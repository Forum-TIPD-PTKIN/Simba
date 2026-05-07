<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Beasiswa;
use App\Models\Pendaftar;
use App\Models\HasilSurvey;
use App\Models\MasterStatis;
use Illuminate\Http\Request;
use App\Models\TahunKegiatan;
use App\Models\SurveyorDetail;
use App\Models\PendaftarStatus;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Admin\HasilSurveiExport;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\Admin\PesertaSurveiExport;
use App\Imports\Admin\ImporPelulusanPesertaSurvei;
use App\Exports\Admin\TemplatePelulusanPesertaSurvei;

class SeleksiAkhirController extends Controller
{
    public function index(Request $request)
    {
        $tahun_kegiatan = TahunKegiatan::orderBy('tahun', 'desc')->get();
        $beasiswa = Beasiswa::where('beasiswas.status', 1)
            ->orderByActiveRegistration()
            ->get();
        $surveyor = User::whereLike('access', '%3%')
            ->whereHas('surveyor', function ($query) use ($request, $tahun_kegiatan, $beasiswa) {
                $query->where('bersedia', 1)
                    ->where('publish', 1)
                    ->where('tahun_kegiatan_id', isset($request->flt_tahun) ? $request->flt_tahun : (count($tahun_kegiatan) ? $tahun_kegiatan[0]->id : null))
                    ->where('beasiswa_id', isset($request->flt_beasiswa) ? $request->flt_beasiswa : (count($beasiswa) ? $beasiswa[0]->id : null));
            })
            ->orderBy('name')
            ->get();

        if ($request->ajax()) return response()->json($surveyor);

        return view('admin.seleksi-akhir', [
            'tahun_kegiatan' => $tahun_kegiatan,
            'beasiswa' => $beasiswa,
            'surveyor' => $surveyor
        ]);
    }

    public function hasil(Request $request)
    {
        $tahun_kegiatan = TahunKegiatan::orderBy('tahun', 'desc')->get();
        $beasiswa = Beasiswa::select('beasiswas.*')
            ->join('jadwal_kegiatans', function ($db) {
                $db->on('jadwal_kegiatans.beasiswa_id', '=', 'beasiswas.id')
                    ->where('jadwal_kegiatans.role', 'SELEKSI_ADMINISTRASI');
            })
            ->where('beasiswas.status', 1)
            ->orderBy('jadwal_kegiatans.tanggal_mulai', 'desc')
            ->orderBy('beasiswas.nama')
            ->get();
        $surveyor = User::whereLike('access', '%3%')
            ->whereHas('surveyor', function ($query) use ($request, $tahun_kegiatan, $beasiswa) {
                $query->where('bersedia', 1)
                    ->where('publish', 1)
                    ->where('tahun_kegiatan_id', isset($request->flt_tahun) ? $request->flt_tahun : (count($tahun_kegiatan) ? $tahun_kegiatan[0]->id : null))
                    ->where('beasiswa_id', isset($request->flt_beasiswa) ? $request->flt_beasiswa : (count($beasiswa) ? $beasiswa[0]->id : null));
            })
            ->orderBy('name')
            ->get();

        if ($request->ajax()) return response()->json($surveyor);

        return view('admin.hasil-survei', [
            'tahun_kegiatan' => $tahun_kegiatan,
            'beasiswa' => $beasiswa,
            'surveyor' => $surveyor
        ]);
    }

    public function data_peserta(Request $request)
    {
        if ($request->ajax()) {
            $data = Pendaftar::with('mahasiswa', 'beasiswa', 'tahun_kegiatan', 'biodata_pendaftar', 'surveyor_detail.surveyor.user')
                ->select('pendaftars.*')
                ->join('mahasiswas', 'mahasiswas.pendaftar_id', 'pendaftars.id')
                ->join('biodata_pendaftars', 'biodata_pendaftars.pendaftar_id', 'pendaftars.id')
                ->join('surveyor_details', 'surveyor_details.pendaftar_id', 'pendaftars.id')
                ->join('surveyors', 'surveyors.id', 'surveyor_details.surveyor_id')
                ->join('users', 'users.id', 'surveyors.user_id')
                ->where('pendaftars.tahun_kegiatan_id', $request->flt_tahun)
                ->where('pendaftars.beasiswa_id', $request->flt_beasiswa)
                ->whereHas('pendaftar_status', fn($query) => $query->where('status', 'LOLOS TPA'))
                ->whereHas('surveyor_detail', function ($query) use ($request) {
                    $query->whereHas('surveyor', fn($q) => $q->when($request->flt_surveyor, fn($qr) => $qr->where('user_id', $request->flt_surveyor)));
                });

            return DataTables::of($data)
                ->editColumn('beasiswa', function ($data) {
                    return "
                        <div class='flex-grow-1'>
                          <div class='row g-1'>
                            <div class='col-12'>
                              <h6 class='mb-0'>{$data->beasiswa?->nama}</h6>
                              <p class='text-muted mb-0'><small>{$data->tahun_kegiatan?->tahun}</small></p>
                            </div>
                          </div>
                        </div>";
                })
                ->editColumn('prodi', function ($data) {
                    return "
                        <div class='flex-grow-1'>
                          <div class='row g-1'>
                            <div class='col-12'>
                              <h6 class='mb-0'>{$data->mahasiswa?->prodi_name}</h6>
                              <p class='text-muted mb-0'><small>{$data->mahasiswa?->fakultas_name}</small></p>
                            </div>
                          </div>
                        </div>";
                })
                ->editColumn('alamat', function ($data) {
                    return $data->biodata_pendaftar?->data?->biodata?->alamat_ktp?->value;
                })
                ->rawColumns(['prodi', 'beasiswa'])
                ->make(true);
        }
    }

    public function data_hasil_survei(Request $request)
    {
        if ($request->ajax()) {
            $data = Pendaftar::with([
                'mahasiswa',
                'beasiswa',
                'tahun_kegiatan',
                'biodata_pendaftar',
                'surveyor_detail.surveyor.user'
            ])
                ->select('pendaftars.*')
                ->join('mahasiswas', 'mahasiswas.pendaftar_id', 'pendaftars.id')
                ->join('biodata_pendaftars', 'biodata_pendaftars.pendaftar_id', 'pendaftars.id')
                ->join('surveyor_details', 'surveyor_details.pendaftar_id', 'pendaftars.id')
                ->join('surveyors', 'surveyors.id', 'surveyor_details.surveyor_id')
                ->join('users', 'users.id', 'surveyors.user_id')
                ->where('pendaftars.tahun_kegiatan_id', $request->flt_tahun)
                ->where('pendaftars.beasiswa_id', $request->flt_beasiswa)
                ->whereHas('pendaftar_status', fn($query) => $query->where('status', 'LOLOS TPA'))
                ->whereHas('surveyor_detail', function ($query) use ($request) {
                    $query->whereHas('surveyor', fn($q) => $q->when(!empty($request->flt_surveyor), fn($qr) => $qr->where('user_id', $request->flt_surveyor)));
                });

            return DataTables::of($data)
                ->editColumn('beasiswa', function ($data) {
                    return "
                        <div class='flex-grow-1'>
                          <div class='row g-1'>
                            <div class='col-12'>
                              <h6 class='mb-0'>{$data->beasiswa?->nama}</h6>
                              <p class='text-muted mb-0'><small>{$data->tahun_kegiatan?->tahun}</small></p>
                            </div>
                          </div>
                        </div>";
                })
                ->editColumn('prodi', function ($data) {
                    return "
                        <div class='flex-grow-1'>
                          <div class='row g-1'>
                            <div class='col-12'>
                              <h6 class='mb-0'>{$data->mahasiswa?->prodi_name}</h6>
                              <p class='text-muted mb-0'><small>{$data->mahasiswa?->fakultas_name}</small></p>
                            </div>
                          </div>
                        </div>";
                })
                ->editColumn('nilai_akhir', function ($data) {
                    return "<div class='flex-grow-1'>
                          <div class='row g-1'>
                            <div class='col-12'>
                              <h6 class='mb-0'>{$data->hasil_survei?->point}</h6>
                              <p class='badge bg-secondary'><small>{$data->hasil_survei?->persen}%</small></p>
                            </div>
                          </div>
                        </div>";
                })
                ->addColumn('action', function ($data) {
                    return view('admin.template._action_button_table', [
                        'data' => $data,
                        'title' => 'Nilai Survei',
                        'showTitle' => false,
                        'buttons' => [
                            'view' => [
                                'title' => 'Lihat',
                                'icon' => 'ti ti-eye',
                                'btn-class' => 'btn btn-primary',
                                'encrypted_id' => $data->id,
                            ]
                        ]
                    ])
                        ->render();
                })
                ->rawColumns(['prodi', 'beasiswa', 'nilai_akhir', 'action'])
                ->make(true);
        }
    }

    public function data_hasil_survei_detail(string $id)
    {
        $data = Pendaftar::with([
            'mahasiswa',
            'beasiswa',
            'tahun_kegiatan',
            'biodata_pendaftar',
            'surveyor_detail.surveyor.user'
        ])
            ->select('pendaftars.*')
            ->join('mahasiswas', 'mahasiswas.pendaftar_id', 'pendaftars.id')
            ->join('biodata_pendaftars', 'biodata_pendaftars.pendaftar_id', 'pendaftars.id')
            ->join('surveyor_details', 'surveyor_details.pendaftar_id', 'pendaftars.id')
            ->join('surveyors', 'surveyors.id', 'surveyor_details.surveyor_id')
            ->join('users', 'users.id', 'surveyors.user_id')
            ->where('pendaftars.id', $id)
            ->whereHas('pendaftar_status', fn($query) => $query->where('status', 'LOLOS TPA'))
            ->first();

        return view('admin.modal-detail-nilai-survei', [
            'data' => $data
        ])->render();
    }

    public function pelulusan()
    {
        $tahun_kegiatan = TahunKegiatan::orderBy('tahun', 'desc')
            ->get();
        $beasiswa = Beasiswa::where('beasiswas.status', 1)
            ->orderByActiveRegistration()
            ->get();
        $status = ['LOLOS PENERIMA', 'TIDAK LOLOS PENERIMA'];

        return view('admin.laporan.pelulusan-akhir', [
            'tahun_kegiatan' => $tahun_kegiatan,
            'beasiswa' => $beasiswa,
            'status' => $status
        ]);
    }

    public function pelulusan_data(Request $request)
    {
        if ($request->ajax()) {
            $dt_pendaftar = Pendaftar::with(['mahasiswa'])
                ->selectRaw('pendaftars.*')
                ->join('mahasiswas', 'pendaftars.id', '=', 'mahasiswas.pendaftar_id')
                ->join(
                    DB::raw('(
                    SELECT *
                    FROM pendaftar_statuses AS ps1
                    WHERE created_at = (
                        SELECT MAX(created_at)
                        FROM pendaftar_statuses AS ps2
                        WHERE ps2.pendaftar_id = ps1.pendaftar_id
                    )
                ) as ps'),
                    'pendaftars.id',
                    '=',
                    'ps.pendaftar_id'
                )
                ->when($request->flt_tahun, function ($q) use ($request) {
                    return $q->where('tahun_kegiatan_id', $request->flt_tahun);
                })
                ->when($request->flt_beasiswa, function ($q) use ($request) {
                    return $q->where('beasiswa_id', $request->flt_beasiswa);
                })
                // ->whereHas('surveyor_detail')
                // ->whereHas('hasil_survey')
                ->where(function ($query) use ($request) {
                    if ($request->flt_status) {
                        return $query->whereHas('pendaftar_status', fn($q) => $q->where('status', $request->flt_status));
                    }

                    return $query->whereHas(
                        'pendaftar_status',
                        fn($q) => $q->whereIn('status', ['LOLOS PENERIMA', 'TIDAK LOLOS PENERIMA'])
                    );
                })
                ->orderBy('mahasiswas.fakultas')
                ->orderBy('mahasiswas.prodi')
                ->orderBy('mahasiswas.nim');

            return DataTables::of($dt_pendaftar)
                ->editColumn('beasiswa', function ($data) {
                    return "
                            <div class='flex-grow-1'>
                              <div class='row g-1'>
                                <div class='col-12'>
                                  <h6 class='mb-0'>{$data->beasiswa?->nama}</h6>
                                  <p class='text-muted mb-0'><small>{$data->tahun_kegiatan?->tahun}</small></p>
                                </div>
                              </div>
                            </div>";
                })
                ->editColumn('status', function ($data) {
                    $status_seleksi_tpa = collect($data->pendaftar_status)
                        ->filter(fn($item) => in_array($item->status, ['LOLOS PENERIMA', 'TIDAK LOLOS PENERIMA']))
                        ->first();
                    return "<span class='badge " . ($status_seleksi_tpa?->status === 'LOLOS PENERIMA' ? 'bg-success' : 'bg-danger') . "'>{$status_seleksi_tpa?->status}</span>";
                })
                ->addColumn('action', function ($data) {
                    $status_seleksi_tpa = collect($data->pendaftar_status)
                        ->filter(fn($item) => in_array($item->status, ['LOLOS PENERIMA', 'TIDAK LOLOS PENERIMA']))
                        ->first();
                    return view('admin.template._action_button_table', [
                        'data' => $data,
                        'title' => 'Status Seleksi Akhir',
                        'showTitle' => false,
                        'buttons' => [
                            'lolos' => [
                                'title' => 'Lolos',
                                'icon' => 'ti ti-circle-check',
                                'btn-class' => 'btn btn-success',
                                'encrypted_id' => $status_seleksi_tpa?->id
                            ],
                            'gagal' => [
                                'title' => 'Gagal',
                                'icon' => 'ti ti-circle-x',
                                'btn-class' => 'btn btn-danger',
                                'encrypted_id' => $status_seleksi_tpa?->id
                            ]
                        ]
                    ])
                        ->render();
                })
                ->rawColumns(['beasiswa', 'status', 'action'])
                ->make(true);
        }
    }

    public function pelulusan_template(Request $request)
    {
        set_time_limit(60 * 60);

        $tahun = $request->tahun;
        $beasiswa = $request->beasiswa;

        $dt_tahun = TahunKegiatan::where('id', $tahun)->pluck('tahun')->first();
        $dt_beasiswa = Beasiswa::where('id', $beasiswa)->pluck('nama')->first();
        $filename = "template_pelulusan_akhir_beasiswa_" . strtolower(str_replace(' ', '_', $dt_beasiswa)) . "_" . $dt_tahun . ".xlsx";

        return Excel::download(new TemplatePelulusanPesertaSurvei($tahun, $beasiswa), $filename);
    }

    public function impor(Request $request)
    {
        $request->validate([
            'tahun_kegiatan' => 'required',
            'beasiswa'       => 'required',
            'file_impor' => 'required|file|mimes:xls,xlsx|max:2048'
        ], [
            'tahun_kegiatan.requierd' => 'Tahun kegiatan belum dipilih',
            'beasiswa.required' => 'Beasiswa belum dipilih',
            'file_impor.required' => 'File template harus dipilih',
            'file_impor.mimes' => 'Format harus xls/xlsx',
            'file_impor.max' => 'Ukuran file tidak boleh lebih dari 2 MB'
        ]);

        try {
            set_time_limit(60 * 60);
            $import = new ImporPelulusanPesertaSurvei($request->tahun_kegiatan, $request->beasiswa);
            Excel::import($import, $request->file('file_impor'));

            $data = array(
                'icon' => 'success',
                'title' => 'Berhasil',
                'message' => $import->getRowCount() . ' data peserta survei berhasil disimpan status lulusnya',
                'jalur' => $request->jalur_masuk
            );
            return response()->json($data);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];

            foreach ($failures as $failure) {
                $errors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values(),
                ];
            }

            return response()->json([
                'icon' => 'error',
                'title' => 'Gagal Mengimpor',
                'message' => 'Terdapat kesalahan pada file Excel.',
                'errors' => $errors
            ], 422);
        }
    }

    public function pelulusan_update(Request $request)
    {
        $request->validate([
            'status_id' => 'required',
            'status' => 'required|in:Lolos,Tidak Lolos'
        ], [
            'status_id.required' => 'ID status tidak ditemukan',
            'status.required' => 'Status tidak boleh kosong',
            'status.in' => 'Status tidak valid'
        ]);

        try {
            $status = PendaftarStatus::find($request->status_id);
            $status->status = $request->status == 'Lolos' ? 'LOLOS PENERIMA' : 'TIDAK LOLOS PENERIMA';
            $status->update();

            return response()->json([
                'status' => true,
                'icon' => 'success',
                'title' => 'Sukses!',
                'message' => 'Status kelulusan peserta survei berhasil diupdate'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            $error = $e->errorInfo;

            return response()->json($error[2], 422);
        }
    }

    public function unduh_peserta(Request $request)
    {
        set_time_limit(60 * 60);

        $tahun = $request->tahun;
        $beasiswa = $request->beasiswa;
        $surveyor = $request->surveyor;

        $dt_tahun = TahunKegiatan::where('id', $tahun)->pluck('tahun')->first();
        $dt_beasiswa = Beasiswa::where('id', $beasiswa)->pluck('nama')->first();
        $dt_surveyor = User::where('id', $surveyor)->pluck('name')->first();
        $filename = "Data Peserta Survei";
        if ($dt_surveyor) $filename .= " {$dt_surveyor}";
        $filename .= " Beasiswa {$dt_beasiswa} Tahun {$dt_tahun}.xlsx";

        return Excel::download(new PesertaSurveiExport($tahun, $beasiswa, $surveyor), $filename);
    }

    public function unduh_hasil_survei(Request $request)
    {
        set_time_limit(60 * 60);

        $tahun = $request->tahun;
        $beasiswa = $request->beasiswa;
        $surveyor = $request->surveyor;

        $dt_tahun = TahunKegiatan::where('id', $tahun)->pluck('tahun')->first();
        $dt_beasiswa = Beasiswa::where('id', $beasiswa)->pluck('nama')->first();
        $dt_surveyor = User::where('id', $surveyor)->pluck('name')->first();
        $filename = "Hasil Survei";
        if ($dt_surveyor) $filename .= " {$dt_surveyor}";
        $filename .= " Beasiswa {$dt_beasiswa} Tahun {$dt_tahun}.xlsx";

        return Excel::download(new HasilSurveiExport($tahun, $beasiswa, $surveyor), $filename);
    }

    public function survei(Request $request)
    {
        $list_tahun = TahunKegiatan::orderBy('tahun', 'desc')->get();
        $list_beasiswa = Beasiswa::orderBy('nama', 'asc')->get();
        $tahun = isset($request->flt_tahun) && !empty($request->flt_tahun) ? $request->flt_tahun : (count($list_tahun) ? $list_tahun[0]->id : null);
        $beasiswa = isset($request->flt_beasiswa) && !empty($request->flt_beasiswa) ? $request->flt_beasiswa : (count($list_beasiswa) ? $list_beasiswa[0]->id : null);

        $list_peserta = Pendaftar::selectRaw('pendaftars.id, mahasiswas.nama')
            ->join('mahasiswas', 'mahasiswas.pendaftar_id', 'pendaftars.id')
            ->where('pendaftars.tahun_kegiatan_id', $tahun)
            ->where('pendaftars.beasiswa_id', $beasiswa)
            ->whereHas('pendaftar_status', fn($query) => $query->where('status', 'LOLOS TPA'))
            ->get();
        $id = isset($request->id) && !empty($request->id) ? $request->id : (count($list_peserta) ? $list_peserta[0]->id : null);

        $masters = MasterStatis::whereIn('nama', [
            'penghasilan',
            'pekerjaan',
            'kepemilikan_rumah',
            'bangunan_rumah',
            'lantai_rumah',
            'kepemilikan_listrik'
        ])->get();

        $masterPenghasilan = $masters->where('nama', 'penghasilan')->first()->data;
        $masterPekerjaan = $masters->where('nama', 'pekerjaan')->first()->data;
        $masterKepemilikanRumah = $masters->where('nama', 'kepemilikan_rumah')->first()->data;
        $masterBangunanRumah = $masters->where('nama', 'bangunan_rumah')->first()->data;
        $masterLantaiRumah = $masters->where('nama', 'lantai_rumah')->first()->data;
        $masterKepemilikanListrik = $masters->where('nama', 'kepemilikan_listrik')->first()->data;

        $pendaftar = SurveyorDetail::with('pendaftar.mahasiswa', 'pendaftar.pemberkasan', 'pendaftar.biodata_pendaftar')
            ->where('pendaftar_id', $id)
            ->first();

        if (!$pendaftar) return redirect()->route('admin.seleksi-akhir');

        $nilai = HasilSurvey::where('pendaftar_id', $pendaftar->pendaftar?->id)->get();

        $nilaiSurvey = (object)[
            'ayahNama' => '',
            'ayahKesehatan' => 1,
            'ayahKesehatanUpdateAt' => null,
            'ayahNamaStatus' => '',
            'ayahNamaUpdateAt' => null,
            'ibuNama' => '',
            'ibuKondisi' => 1,
            'ibuKondisiUpdateAt' => null,
            'ibuNamaStatus' => '',
            'ibuNamaUpdateAt' => null,
            'ayahPekerjaan' => '""',
            'ayahPekerjaanUpdateAt' => null,
            'ayahPekerjaanLainnya' => '',
            'ayahPenghasilan' => '""',
            'ayahPenghasilanUpdateAt' => null,
            'ibuPekerjaan' => '""',
            'ibuPekerjaanUpdateAt' => null,
            'ibuPekerjaanLainnya' => '',
            'ibuPenghasilan' => '""',
            'ibuPenghasilanUpdateAt' => null,
            'tanggunganKeluarga' => '""',
            'tanggunganKeluargaUpdateAt' => null,
            'tanggunganKeluargaStatus' => '',
            'kepemilikanRumah' => '""',
            'kepemilikanRumahUpdateAt' => null,
            'kepemilikanRumahStatus' => '',
            'bangunanRumah' => '""',
            'bangunanRumahUpdateAt' => null,
            'bangunanRumahStatus' => '',
            'lantaiRumah' => '""',
            'lantaiRumahUpdateAt' => null,
            'lantaiRumahStatus' => '',
            'kepemilikanListrik' => '""',
            'kepemilikanListrikUpdateAt' => null,
            'kepemilikanListrikStatus' => '',
            'kondisiRumahStatus' => '',
            'kondisiRumahUpdateAt' => null,
            'kondisiDapurStatus' => '',
            'kondisiDapur' => '""',
            'kondisiDapurUpdateAt' => null,
            'kondisiKamarMandiStatus' => '',
            'kondisiKamarMandi' => '""',
            'kondisiKamarMandiUpdateAt' => null,
            'kondisiWcStatus' => '',
            'kondisiWc' => '""',
            'kondisiWcUpdateAt' => null,
            'catatan' => '',
            'catatanUpdateAt' => null,
            'berkasGdrive' => '',
            'berkasGdriveUpdateAt' => null,
        ];

        foreach ($nilai as $key => $value) {
            $nilaiSurvey->{$value->aspek} = $value->nilai;
            $nilaiSurvey->{$value->aspek . 'UpdateAt'} = $value->created_at;

            $autoStatus = [
                'ayahNama',
                'ibuNama',
                'tanggunganKeluarga',
                'kepemilikanRumah',
                'bangunanRumah',
                'lantaiRumah',
                'kepemilikanListrik',
                'kondisiRumah',
                'kondisiDapur',
                'kondisiKamarMandi',
                'kondisiWc',
            ];
            if (in_array($value->aspek, $autoStatus)) {
                $nilaiSurvey->{$value->aspek . 'Status'} = $value->sesuai ? 'sesuai' : 'tidak';
            } else if ($value->aspek == 'ayahPekerjaan') {
                $cek = strpos($value->nilai, 'LAINNYA:');
                if ($cek === 0) {
                    $nilaiSurvey->{'ayahPekerjaanLainnya'} = str_replace('LAINNYA:', '', $value->nilai);
                    $nilaiSurvey->{'ayahPekerjaan'} = 'LAINNYA';
                } else {
                    $nilaiSurvey->{'ayahPekerjaan'} = $value->nilai;
                }
            } else if ($value->aspek == 'ibuPekerjaan') {
                $cek = strpos($value->nilai, 'LAINNYA:');
                if ($cek === 0) {
                    $nilaiSurvey->{'ibuPekerjaanLainnya'} = str_replace('LAINNYA:', '', $value->nilai);
                    $nilaiSurvey->{'ibuPekerjaan'} = 'LAINNYA';
                } else {
                    $nilaiSurvey->{'ibuPekerjaan'} = $value->nilai;
                }
            }
        }

        return view('admin.survey', compact(
            'masterPenghasilan',
            'masterPekerjaan',
            'masterKepemilikanRumah',
            'masterBangunanRumah',
            'masterLantaiRumah',
            'masterKepemilikanListrik',
            'pendaftar',
            'nilaiSurvey',
            'pendaftar',
            'list_tahun',
            'list_beasiswa',
            'list_peserta',
            'tahun',
            'beasiswa',
            'id'
        ));
    }

    public function peserta_survei(Request $request)
    {
        $list_peserta = Pendaftar::selectRaw('pendaftars.id, mahasiswas.nama')
            ->join('mahasiswas', 'mahasiswas.pendaftar_id', 'pendaftars.id')
            ->where('pendaftars.tahun_kegiatan_id', $request->flt_tahun)
            ->where('pendaftars.beasiswa_id', $request->flt_beasiswa)
            ->whereHas('pendaftar_status', fn($query) => $query->where('status', 'LOLOS TPA'))
            ->get();

        return response()->json($list_peserta);
    }

    public function survei_post(Request $request)
    {
        try {
            $key = $request->key;
            $nilai = $request->data;
            $pendaftar = $request->pendaftar;
            $sesuai = $request->sesuai ?? null;

            $cek = DB::table('pendaftars')
                ->where('id', $pendaftar)
                ->count();

            if ($cek == 0) {
                return response()->json([
                    'error' => 'Pendaftar Tidak ditemukan'
                ], 422);
            }

            $dtNilai = HasilSurvey::where('pendaftar_id', $pendaftar)
                ->where('aspek', $key)
                ->first();

            if (!$dtNilai) {
                $dtNilai = new HasilSurvey();
            }

            $dtNilai->pendaftar_id = $pendaftar;
            $dtNilai->aspek = $key;
            $dtNilai->sesuai = $sesuai == 'sesuai' ? true : ($sesuai == 'tidak' ? false : null);
            $dtNilai->nilai = $nilai ?? '';
            $dtNilai->save();

            return response()->json([
                'date' => formatDateUpdateAt(now())
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}