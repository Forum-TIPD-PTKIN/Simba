<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Admin\PesertaSurveiExport;
use App\Models\User;
use App\Models\Beasiswa;
use App\Models\Pendaftar;
use Illuminate\Http\Request;
use App\Models\TahunKegiatan;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SeleksiAkhirController extends Controller
{
    public function index(Request $request)
    {
        $tahun_kegiatan = TahunKegiatan::orderBy('tahun', 'desc')->get();
        $beasiswa = Beasiswa::orderBy('nama', 'asc')->get();
        $surveyor = User::whereLike('access', '%3%')
            ->whereHas('surveyor', function ($query) use ($request, $tahun_kegiatan, $beasiswa) {
                $query->where('bersedia', 1)
                    ->where('publish', 1)
                    ->where('tahun_kegiatan_id', isset($request->flt_tahun) ? $request->flt_tahun : (count($tahun_kegiatan) ? $tahun_kegiatan[0]->id : null))
                    ->where('beasiswa_id', isset($request->flt_beasiswa) ? $request->flt_beasiswa : (count($beasiswa) ? $beasiswa[0]->id : null));
            })
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
        $beasiswa = Beasiswa::orderBy('nama', 'asc')->get();
        $surveyor = User::whereLike('access', '%3%')
            ->whereHas('surveyor', function ($query) use ($request, $tahun_kegiatan, $beasiswa) {
                $query->where('bersedia', 1)
                    ->where('publish', 1)
                    ->where('tahun_kegiatan_id', isset($request->flt_tahun) ? $request->flt_tahun : (count($tahun_kegiatan) ? $tahun_kegiatan[0]->id : null))
                    ->where('beasiswa_id', isset($request->flt_beasiswa) ? $request->flt_beasiswa : (count($beasiswa) ? $beasiswa[0]->id : null));
            })
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
}