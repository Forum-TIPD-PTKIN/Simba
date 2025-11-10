<?php

namespace App\Http\Controllers\Surveyor;

use App\Models\Beasiswa;
use App\Models\Surveyor;
use App\Models\Pendaftar;
use App\Models\HasilSurvey;
use App\Models\MasterStatis;
use Illuminate\Http\Request;
use App\Models\TahunKegiatan;
use App\Models\SurveyorDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $surveyor = Surveyor::with('tahun_kegiatan', 'beasiswa', 'user')
            ->whereUserId(Auth::id())
            ->orderByDesc(
                TahunKegiatan::select('tahun')
                    ->whereColumn('tahun_kegiatans.id', 'surveyors.tahun_kegiatan_id')
            )
            ->where('bersedia', 1)
            ->where('publish', 1)
            ->limit(7) // 7 tahun terakhir
            ->get();

        if (request()->ajax()) {
            $data = SurveyorDetail::with(['pendaftar.user', 'pendaftar.mahasiswa', 'pendaftar.biodata_pendaftar'])
                ->whereHas('surveyor', function ($query) {
                    $query->where('bersedia', 1)
                        ->where('publish', 1)
                        ->whereUserId(Auth::id());
                })
                ->whereSurveyorId($request->surveyor_id)
                ->get()
                ->sortByDesc(fn($item) => $item->pendaftar->hasil_survei->point ?? 0);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('surveyor.survey.show', ['id' => $row->id]) . '" class="btn btn-info btn-sm"><i class="ti ti-eye"></i></a>';
                    return $btn;
                })
                ->addColumn('fakultas', function ($row) {
                    $fak = explode('|', $row->pendaftar->mahasiswa->fakultas);
                    $pro = explode('|', $row->pendaftar->mahasiswa->prodi);
                    return $fak[1] . ' - ' . $pro[1];
                })
                ->addColumn('progress', function ($row) {
                    $percentage = $row->pendaftar->hasil_survei->persen ?? 0;
                    $colorClass = 'bg-light-danger'; // default red
                    if ($percentage > 75) {
                        $colorClass = 'bg-light-success'; // green
                    } elseif ($percentage > 50) {
                        $colorClass = 'bg-light-info'; // blue
                    } elseif ($percentage > 25) {
                        $colorClass = 'bg-light-warning'; // yellow
                    }
                    return '<span class="badge ' . $colorClass . '">' . $percentage . '%</span>';
                })
                ->addColumn('point', function ($row) {
                    return $row->pendaftar->hasil_survei->point;
                })
                ->rawColumns(['action', 'fakultas', 'progress', 'point'])
                ->make(true);
        }

        $master = collect($surveyor)->map(function ($item) {
            return (object)[
                'id' => $item->id,
                'tahun' => $item->tahun_kegiatan->tahun,
                'beasiswa' => $item->beasiswa->nama,
            ];
        })->toArray();

        $selectMasterDefault = $surveyor->first();

        return view('surveyor.survey.index', compact('master', 'selectMasterDefault'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
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
            ->where('id', $id)
            ->whereHas('surveyor', function ($query) {
                $query->where('bersedia', 1)
                    ->where('publish', 1)
                    ->whereUserId(Auth::id());
            })
            ->first();

        if (!$pendaftar) {
            return redirect()->route('surveyor.survey');
        }
        $masterPendaftar = SurveyorDetail::with(['pendaftar.user', 'pendaftar.mahasiswa', 'pendaftar.biodata_pendaftar'])
            ->whereHas('surveyor', function ($query) {
                $query->where('bersedia', 1)
                    ->where('publish', 1)
                    ->whereUserId(Auth::id());
            })
            ->whereSurveyorId($pendaftar->surveyor_id)
            ->get();
        $nilai = HasilSurvey::where('pendaftar_id', $pendaftar->pendaftar->id)->get();

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


        return view('surveyor.survey.detail', compact(
            'masterPenghasilan',
            'masterPekerjaan',
            'masterKepemilikanRumah',
            'masterBangunanRumah',
            'masterLantaiRumah',
            'masterKepemilikanListrik',
            'pendaftar',
            'nilaiSurvey',
            'pendaftar',
            'masterPendaftar',
            'id'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function update_skor(Request $request)
    {
        try {
            $key = $request->key;
            $nilai = $request->data;
            $pendaftar = $request->pendaftar;
            $sesuai = $request->sesuai ?? null;

            $cek = DB::table('pendaftars')
                ->whereRaw("
                exists(
                    select 1 from surveyor_details sd
                    where sd.pendaftar_id = ? and exists(
                        select 1 from surveyors s 
                        where s.id = sd.surveyor_id
                        and s.user_id = ? and s.bersedia = 1 and s.publish = 1
                    )
                ) 
            ", [$pendaftar, Auth::id()])->count();

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

    public function reset_skor(Request $request)
    {
        try {
            $key = $request->key;
            $nilai = $request->data;
            $pendaftar = $request->pendaftar;
            $sesuai = $request->sesuai ?? null;

            $cek = DB::table('pendaftars')
                ->whereRaw("
                exists(
                    select 1 from surveyor_details sd
                    where sd.pendaftar_id = ? and exists(
                        select 1 from surveyors s 
                        where s.id = sd.surveyor_id
                        and s.user_id = ? and s.bersedia = 1 and s.publish = 1
                    )
                ) 
            ", [$pendaftar, Auth::id()])->count();

            if ($cek == 0) {
                return response()->json([
                    'error' => 'Pendaftar Tidak ditemukan'
                ], 422);
            }

            HasilSurvey::where('pendaftar_id', $pendaftar)
                ->delete();

            return response()->json([
                'date' => formatDateUpdateAt(now())
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function peserta_survei(Request $request)
    {
        $tahun_kegiatan = TahunKegiatan::orderBy('tahun', 'desc')->get();
        $beasiswa = Beasiswa::where('status', 1)->get();

        $responden = Pendaftar::with(['mahasiswa', 'beasiswa', 'tahun_kegiatan', 'biodata_pendaftar'])
            ->where('tahun_kegiatan_id', isset($request->tahun) && $request->tahun ? $request->tahun : (count($tahun_kegiatan) ? $tahun_kegiatan[0]->id : null))
            ->where('beasiswa_id', isset($request->beasiswa) && $request->beasiswa ? $request->beasiswa : (count($beasiswa) ? $beasiswa[0]->id : null))
            ->whereHas('pendaftar_status', function ($q) {
                $q->where('status', 'LOLOS TPA');
            })
            ->whereHas('surveyor_detail', function ($query) {
                $query->where('surveyor_id', Surveyor::where('user_id', Auth::id())->pluck('id'))
                    ->whereHas('surveyor', function ($surveyorQuery) {
                        $surveyorQuery->where('publish', '1');
                    });
            })
            ->get();

        $view_daftar_responden = view('surveyor.data-peserta-survei', [
            'responden' => $responden
        ])->render();

        if ($request->ajax()) return $view_daftar_responden;

        return view('surveyor.peserta-survei', [
            'tahun_kegiatan' => $tahun_kegiatan,
            'beasiswa' => $beasiswa,
            'view_daftar_responden' => $view_daftar_responden
        ]);
    }

    public function berkas_pendaftar(string $id)
    {
        $pendaftar = Pendaftar::with('mahasiswa', 'biodata_pendaftar', 'pemberkasan')
            ->where('id', $id)
            ->whereHas('surveyor_detail', function ($query) {
                $query->where('surveyor_id', Surveyor::where('user_id', Auth::id())->pluck('id'))
                    ->whereHas('surveyor', function ($surveyorQuery) {
                        $surveyorQuery->where('publish', '1')
                            ->where('bersedia', '1');
                    });
            })
            ->first();

        if (!$pendaftar) return response()->json('Pendaftar tidak ditemukan', 404);

        return view('surveyor.modal-berkas-pendaftar', [
            'data' => $pendaftar,
        ])->render();
    }

    public function cetak_hasil_survei(string $id)
    {
        $data = Pendaftar::with('mahasiswa', 'biodata_pendaftar', 'pemberkasan')
            ->where('id', $id)
            ->whereHas('surveyor_detail', function ($query) {
                $query->where('surveyor_id', Surveyor::where('user_id', Auth::id())->pluck('id'))
                    ->whereHas('surveyor', function ($surveyorQuery) {
                        $surveyorQuery->where('publish', '1')
                            ->where('bersedia', '1');
                    });
            })
            ->first();

        if (!$data) return response()->json('Pendaftar tidak ditemukan', 404);

        return view('surveyor.survey.hasil-survei', [
            'data' => $data
        ]);
    }
}
