<?php

namespace App\Http\Controllers\Pendaftar;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\Mahasiswa;
use App\Models\Pendaftar;
use App\Models\SiakadMahasiswa;
use App\Models\TahunKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DaftarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $id)
    {
        $user = Auth::user();
        $nim = $user->username;
        $mahasiswa = SiakadMahasiswa::with('prodi.fakultas')
            ->whereNpm($nim)
            ->first();

        $beasiswa = Beasiswa::where('status', 1)
            ->whereHas('jadwal_kegiatan', function ($query) {
                $query->is_active()
                    ->where('role', 'PENDAFTARAN')
                    ->whereHas('tahun_kegiatan', function ($q) {
                        $q->where('status', 1);
                    });
            })
            ->find($id);


        $pendaftar = Pendaftar::whereBeasiswaId($id)
            ->whereHas('tahun_kegiatan', function ($db) {
                $db->whereStatus(1);
            })
            ->whereUserId($user->id)
            ->first();


        if (!$beasiswa) {
            return view('pendaftar.no-page', [
                'message' => 'Beasiswa yang dimaksud tidak tersedia',
                'title' => 'Opz..',
                'bg' => 'danger'
            ]);
        }

        $register = $pendaftar ? true : false;
        $step = intval(request()->get('step') ?? '1');
        if ($step > 2 && !$pendaftar) {
            session()->flash('error_register', 'Sebelum melanjutkan, silahkan konfirmasi terlebih dahulu pendaftaran anda!');
            return redirect()->to(route('pendaftar.daftar', ['id' => $id]) . '?step=2');
        }
        if ($step < 1) $step = 1;
        else if ($step > 3) $step = 3;
        $jalur = null;

        if ($step == 2) {
            $key_pmb = env('PMB_KEY_API');
            $_jalur = api()->get("https://pmb.uinmadura.ac.id/api/info/jalur/{$nim}?key={$key_pmb}");
            if ($_jalur->status) {
                $jalur = $_jalur->data;
            }
        }
        return view('pendaftar.daftar.index', compact(
            'beasiswa',
            'step',
            'mahasiswa',
            'register',
            'jalur'
        ));
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
    public function store(Request $request, $id)
    {

        $beasiswa = Beasiswa::where('status', 1)
            ->whereHas('jadwal_kegiatan', function ($query) {
                $query->is_active()
                    ->where('role', 'PENDAFTARAN')
                    ->whereHas('tahun_kegiatan', function ($q) {
                        $q->where('status', 1);
                    });
            })
            ->find($id);

        $pendaftar = Pendaftar::whereBeasiswaId($id)
            ->whereHas('tahun_kegiatan', function ($db) {
                $db->whereStatus(1);
            })
            ->whereUserId(Auth::id())
            ->first();


        if (!$beasiswa) {
            return view('pendaftar.no-page', [
                'message' => 'Beasiswa yang dimaksud tidak tersedia',
                'title' => 'Opz..',
                'bg' => 'danger'
            ]);
        }

        if ($pendaftar) {
            return view('pendaftar.no-page', [
                'message' => 'Anda telah melakukan pendaftaran',
                'title' => 'Opz..',
                'bg' => 'danger'
            ]);
        }

        $kegiatan = TahunKegiatan::whereStatus(1)->first();
        if (!$kegiatan) {
            return view('pendaftar.no-page', [
                'message' => 'Tahun kegiatan tidak ada yang aktif',
                'title' => 'Opz..',
                'bg' => 'danger'
            ]);
        }

        /* PROSES CEK VALIDASI PENDAFTARAN */

        $user = Auth::user();

        $pendafar = new Pendaftar();
        $pendafar->user_id = $user->id;
        $pendafar->beasiswa_id = $beasiswa->id;
        $pendafar->tahun_kegiatan_id = $kegiatan->id;
        $pendafar->save();

        $mahasiswa_api = SiakadMahasiswa::with('prodi.fakultas')
            ->whereNpm($user->username)
            ->first();

        $mahasiswa = new Mahasiswa();
        $mahasiswa->pendaftar_id = $pendafar->id;
        $mahasiswa->nim = $mahasiswa_api->npm;
        $mahasiswa->nama = $mahasiswa_api->nama_mahasiswa;
        $mahasiswa->fakultas = $mahasiswa_api->prodi->fakultas->id_fakultas . '|' . $mahasiswa_api->prodi->fakultas->nama_fakultas;
        $mahasiswa->prodi = $mahasiswa_api->prodi->id_prodi . '|' . $mahasiswa_api->prodi->singkatan;
        $mahasiswa->save();

        return redirect()->to(route('pendaftar.daftar', ['id' => $beasiswa->id]) . '?step=3');
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
