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

class SurveyorController extends Controller
{
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

    public function plotMutliMahasiswa(Request $request)
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
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
