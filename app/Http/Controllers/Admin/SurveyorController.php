<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\Surveyor;
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
    public function index()
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
        $kip_select = $master_beasiswa->first();
        $tahun_select = $master_tahun->first();

        $surveyor = Surveyor::with(['user', 'surveyor_detail'])->whereBeasiswaId($kip_select->id)
            ->whereTahunKegiatanId($tahun_select->id)
            ->get();


        return view('admin.surveyor.index', compact('surveyor', 'kip_select', 'tahun_select', 'master_tahun', 'master_beasiswa', 'master_pegawai'));
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
                            'referensi' => route('surveyor.persetujuan', ['id' => $idSurveyor]),
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
