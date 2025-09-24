<?php

namespace App\Http\Controllers\Pendaftar;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\Pendaftar;
use App\Models\SiakadMahasiswa;
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


        $notif = null;
        $register = $pendaftar ? true : false;
        $step = intval(request()->get('step') ?? '1');
        if ($step > 2 && !$pendaftar) {
            $step = 2;
            $notif = 'Silahkan konfirmasi terlebih dahulu pendaftaran anda!';
        }
        if ($step < 1) $step = 1;
        else if ($step > 3) $step = 3;
        return view('pendaftar.daftar.index', compact(
            'beasiswa',
            'step',
            'mahasiswa',
            'notif',
            'register'
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
    public function store(Request $request)
    {
        //
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
