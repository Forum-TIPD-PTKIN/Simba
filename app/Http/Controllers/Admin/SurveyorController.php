<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\Pendaftar;
use App\Models\Surveyor;
use App\Models\SurveyorDetail;
use App\Models\TahunKegiatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class SurveyorController extends Controller
{
    public function detail(Request $request, $id)
    {
        $detailSurveyor = Surveyor::with(['user', 'beasiswa', 'tahun_kegiatan', 'surveyor_detail.pendaftar.mahasiswa', 'surveyor_detail.pendaftar.biodata_pendaftar'])->where('id', $id)->first();
        return view('admin.surveyor.detail', compact('detailSurveyor'));
    }

    public function publishAll(Request $request)
    {
        $status = $request->status ?? 0;
        $beasiswa = $request->beasiswa ?? null;
        $tahun = $request->tahun ?? null;

        Surveyor::whereBeasiswaId($beasiswa)
            ->whereTahunKegiatanId($tahun)
            ->update(['publish' => $status]);
        return response()->json([
            'icon' => 'success',
            'title' => 'Berhasil',
            'message' => 'Surveyor berhasil dipublish',
        ]);
    }

    public function publish(Request $request, $id)
    {
        $status = $request->status ?? 0;

        Surveyor::find($id)->update(['publish' => $status]);
        return response()->json([
            'icon' => 'success',
            'title' => 'Berhasil',
            'message' => 'Surveyor berhasil dipublish',
        ]);
    }

    public function rekap(Request $request)
    {
        $status_surveyor = ['Publish', 'Draft'];

        $master_tahun = TahunKegiatan::where('status', 1)
            ->orderBy('tahun', 'desc')
            ->get()
            ->makeVisible(['id']);
        $master_beasiswa = Beasiswa::where('status', 1)
            ->get()
            ->makeVisible(['id']);

        if ($request->beasiswa) {
            $kip_select = Beasiswa::find($request->beasiswa);
        } else {
            $kip_select = $master_beasiswa->first();
        }
        if ($request->tahun) {
            $tahun_select = TahunKegiatan::find($request->tahun);
        } else {
            $tahun_select = $master_tahun->first();
        }

        if ($request->ajax()) {
            $data = Surveyor::with(['user', 'beasiswa', 'tahun_kegiatan'])
                ->where('bersedia', 1)
                ->whereBeasiswaId($request->beasiswa ?? $kip_select->id)
                ->whereTahunKegiatanId($request->tahun ?? $tahun_select->id)
                ->when($request->status, function ($query, $status) {
                    $query->where('publish', $status == 'Publish' ? 1 : 0);
                })
                ->withCount('surveyor_detail as details_count')
                ->withCount(['surveyor_detail as selesai_count' => function ($query) {
                    $query->whereHas('pendaftar.latestStatus', function ($q) {
                        $q->where('status', 'SUDAH SURVEY');
                    });
                }]);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->user->name;
                })
                ->addColumn('beasiswa', function ($row) {
                    return $row->beasiswa->nama . ' ' . $row->tahun_kegiatan->tahun;
                })
                ->addColumn('belum_selesai_count', function ($row) {
                    return $row->details_count - $row->selesai_count;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button onclick="openDetal(event)" data-id="' . $row->id . '" class="btn btn-info btn-sm"><i class="ti ti-eye"></i></button>';
                    return $btn;
                })
                ->addColumn('status', function ($row) {
                    if (!$row->publish)
                        return '<button onclick="publishSurveyor(event, 1)" data-id="' . $row->id . '" class="badge btn  btn-sm small text-bg-warning p-1 btn-sm">Draft</button>';
                    else return '<button onclick="publishSurveyor(event, 0)" data-id="' . $row->id . '" class="badge btn btn-sm small text-bg-success p-1 btn-sm">Publish</button>';
                })
                ->rawColumns(['action', 'status', 'name'])
                ->make(true);
        }

        return view('admin.surveyor.rekap', compact('master_tahun', 'master_beasiswa', 'status_surveyor', 'kip_select', 'tahun_select'));
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $master_pegawai = collect(api()->get("https://api.iainmadura.ac.id/api/pegawai?limit=1000")->data?->data)
            ->map(function ($pegawai) {
                return (object)[
                    'id' => $pegawai->kode,
                    'nama' => $pegawai->nama,
                    'nip' => $pegawai->nip
                ];
            })->toArray();

        $master_tahun = TahunKegiatan::where('status', 1)
            ->orderBy('tahun', 'desc')
            ->get();
        $master_beasiswa = Beasiswa::where('status', 1)
            ->get();

        if ($request->beasiswa) {
            $kip_select = Beasiswa::find($request->beasiswa);
        } else {
            $kip_select = $master_beasiswa->first();
        }
        if ($request->tahun) {
            $tahun_select = TahunKegiatan::find($request->tahun);
        } else {
            $tahun_select = $master_tahun->first();
        }

        $surveyor = Surveyor::with(['user', 'surveyor_detail.pendaftar.mahasiswa', 'surveyor_detail.pendaftar.biodata_pendaftar'])
            ->whereBeasiswaId($request->beasiswa ?? $kip_select->id)
            ->whereTahunKegiatanId($request->tahun ?? $tahun_select->id)
            ->get();

        $pendaftar = Pendaftar::with(['user', 'mahasiswa', 'biodata_pendaftar'])
            ->someStatus('LOLOS TPA')
            ->whereBeasiswaId($kip_select->id)
            ->whereTahunKegiatanId($tahun_select->id)
            ->get();

        return view('admin.surveyor.index', compact('surveyor', 'kip_select', 'tahun_select', 'master_tahun', 'master_beasiswa', 'master_pegawai', 'pendaftar'));
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

    public function plotMultiMahasiswa(Request $request)
    {
        $plots = $request->input('pendaftar_ids');
        if (empty($plots) || !is_array($plots)) {
            return response()->json([
                'icon' => 'error',
                'title' => 'Gagal',
                'message' => 'Data mahasiswa yang akan di-plot tidak valid.',
            ], 400); // 400 Bad Request
        }

        $dataToInsert = [];
        $pendaftarIds = $plots;

        // 3. Cek pendaftar_id mana saja yang sudah ada untuk surveyor_id yang bersangkutan
        // Lakukan pengecekan secara batch.
        $existingPlots = SurveyorDetail::whereIn('pendaftar_id', $pendaftarIds)
            ->whereSurveyorId($request->surveyor_id)
            ->get(['surveyor_id', 'pendaftar_id']);

        $existingCombinations = $existingPlots->map(function ($item) {
            return $item->surveyor_id . '-' . $item->pendaftar_id;
        })->toArray();


        $now = now();
        $insertedCount = 0;

        foreach ($plots as $plot) {
            $surveyorId = $request->surveyor_id ?? null;
            $pendaftarId = $plot ?? null;

            if (!$surveyorId || !$pendaftarId) {
                // Lompati data yang tidak lengkap
                continue;
            }

            $combination = $surveyorId . '-' . $pendaftarId;

            if (!in_array($combination, $existingCombinations)) {
                $dataToInsert[] = [
                    'id' => Str::uuid()->toString(),
                    'surveyor_id' => $surveyorId,
                    'pendaftar_id' => $pendaftarId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $existingCombinations[] = $combination;
            }
        }

        if (!empty($dataToInsert)) {
            SurveyorDetail::insert($dataToInsert);
            $insertedCount = count($dataToInsert);
            return response()->json([
                'icon' => 'success',
                'title' => 'Berhasil',
                'message' => "**{$insertedCount}** mahasiswa berhasil ditambahkan. (**" . (count($plots) - $insertedCount) . "** duplikat diabaikan).",
                'surveyor_detail' => SurveyorDetail::with(['pendaftar.user', 'pendaftar.mahasiswa', 'pendaftar.biodata_pendaftar'])->whereSurveyorId($request->surveyor_id)
                    ->get(),
            ]);
        }
        SurveyorDetail::whereHas('surveyor', function ($query) {
            $query->where('bersedia', '!=', 1);
        })
            ->whereIn('id', collect($pendaftarIds)->pluck('id')->toArray())
            ->delete();
        return response()->json([
            'icon' => 'warning',
            'title' => 'Perhatian',
            'message' => 'Tidak ada mahasiswa baru yang ditambahkan, semua data sudah ada.',
        ]);
    }

    public function plotMahasiswa(Request $request)
    {
        $cek = SurveyorDetail::whereId($request->surveyor_id)
            ->wherePendaftarId($request->pendaftar_id)
            ->first();

        if (!$cek) {
            $surveyor = new SurveyorDetail();
            $surveyor->surveyor_id = $request->surveyor_id;
            $surveyor->pendaftar_id = $request->pendaftar_id;
            $surveyor->save();
            $surf = SurveyorDetail::with(['pendaftar.user', 'pendaftar.mahasiswa', 'pendaftar.biodata_pendaftar'])->find($surveyor->id);

            return response()->json([
                'icon' => 'success',
                'title' => 'Berhasil',
                'message' => 'Mahasiswa berhasil ditambahkan',
                'data' => $surf
            ]);
        }
    }
    public function removeMahasiswa(Request $request)
    {
        SurveyorDetail::destroy($request->surveyor_detail_id);
        return response()->json([
            'icon' => 'success',
            'title' => 'Berhasil',
            'message' => 'Mahasiswa berhasil dihapus',
        ]);
    }

    public function assign(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $existing = Surveyor::select('surveyors.*', 'users.username')
                    ->whereBeasiswaId($request->beasiswa_id)
                    ->whereTahunKegiatanId($request->tahun_kegiatan_id)
                    ->join('users', 'surveyors.user_id', '=', 'users.id')
                    ->get();

                $users = User::whereIn('username', $request->selected_surveyors)->get();
                $kip = Beasiswa::where('id', $request->beasiswa_id)->first();
                $tahun = TahunKegiatan::where('id', $request->tahun_kegiatan_id)->first();

                $userMaster = collect(api()->get("https://api.iainmadura.ac.id/api/pegawai?group_kode=" . implode(",", $request->selected_surveyors) . "&limit=1000")->data?->data);

                $inserted = [];
                $insertNotifikasi = [];
                $kegiatan = $kip->nama . ' tahun ' . $tahun->tahun;

                foreach ($request->selected_surveyors as $key => $value) {
                    $cek = collect($existing)->where('username', $value)->first();
                    if (!$cek) {
                        $user = collect($users)->where('username', $value)->first();
                        if (!$user) {
                            $findUser = collect($userMaster)->where('kode', $value)->first();
                            $user = new User();
                            $user->name = $findUser->nama;
                            $user->username = $value;
                            $user->access = '3';
                            $user->save();
                        } else {
                            if (!in_array('3', $user->access)) {
                                $user->access = implode(',', $user->access) . ',3';
                                $user->save();
                            }
                        }

                        $idSurveyor = Str::uuid()->toString();
                        array_push($inserted, [
                            'id' => $idSurveyor,
                            'beasiswa_id' => $request->beasiswa_id,
                            'tahun_kegiatan_id' => $request->tahun_kegiatan_id,
                            'user_id' => $user->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        array_push($insertNotifikasi, [
                            'id' => Str::uuid()->toString(),
                            'key' => 'ASSIGN_SURVEYOR',
                            'user_id' => $user->id,
                            'pesan' => "Kami mengundang Anda untuk bergabung sebagai surveyor dalam kegiatan beasiswa $kegiatan.\nSilakan konfirmasi kesediaan Anda terlebih dahulu sebelum kami lanjutkan ke tahap berikutnya.\n\nApakah Anda bersedia?",
                            'referensi' => route('surveyor.persetujuan.show', ['persetujuan' => $idSurveyor]),
                            'dibaca' => 0,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
                DB::table('surveyors')->insert($inserted);
                DB::table('notifikasis')->insert($insertNotifikasi);
            });
            return response()->json([
                'icon' => 'success',
                'title' => 'Berhasil',
                'message' => 'Surveyor berhasil ditambahkan',
            ]);
        } catch (\Illuminate\Database\QueryException $th) {
            return response()->json($th->errorInfo[2], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $master_tahun = TahunKegiatan::orderBy('tahun', 'desc')
            ->get();
        $master_beasiswa = Beasiswa::where('status', 1)
            ->get();
        $status_surveyor = [
            'u' => 'Belum Merespon',
            't' => 'Tidak Bersedia',
            'y' => 'Bersedia',
        ];

        if ($request->ajax()) {
            $data = Surveyor::with(['user', 'beasiswa', 'tahun_kegiatan'])
                ->select('surveyors.*')
                ->join('users', 'surveyors.user_id', '=', 'users.id')
                ->whereBeasiswaId($request->beasiswa ?? (count($master_beasiswa) ? $master_beasiswa[0]->id : null))
                ->whereTahunKegiatanId($request->tahun ?? (count($master_tahun) ? $master_tahun[0]->id : null))
                ->when($request->status, function ($query) use ($request) {
                    if ($request->status === 'u') {
                        $query->whereNull('bersedia');
                    } else {
                        $query->where('bersedia', ($request->status == 'y' ? 1 : 0));
                    }
                });

            return DataTables::of($data)
                ->addIndexColumn()
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
                ->editColumn('status', function ($row) {
                    if ($row->bersedia === 1) return '<span class="badge bg-success">Bersedia</span>';
                    else if ($row->bersedia === 0) return '<span class="badge bg-danger">Tidak Bersedia</span>';
                    else return '<span class="badge bg-warning">Belum Merespon</span>';
                })
                ->editColumn('rekening_bank', function ($row) {
                    if ($row->rekening_bank_formatted == null) {
                        return "-";
                    }

                    return '<dl class="row mb-0">
                                <dt class="col-sm-6">No. Rekening</dt>
                                <dd class="col-sm-6">' . $row->rekening_bank_formatted['no_rekening'] . '</dd>

                                <dt class="col-sm-6">Nama di Rekening</dt>
                                <dd class="col-sm-6">' . $row->rekening_bank_formatted['nama_rekening'] . '</dd>

                                <dt class="col-sm-6">Nama Bank</dt>
                                <dd class="col-sm-6">' . $row->rekening_bank_formatted['nama_bank'] . '</dd>

                                <dt class="col-sm-6">File Buku Rekening</dt>
                                <dd class="col-sm-6"><a href="' . $row->rekening_bank_formatted['file_rekening'] . '" target="_blank">Link</a></dd>
                        </dl>';
                })
                ->rawColumns(['beasiswa', 'status', 'rekening_bank'])
                ->make(true);
        }

        return view('admin.surveyor.data', compact('master_tahun', 'master_beasiswa', 'status_surveyor'));
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
