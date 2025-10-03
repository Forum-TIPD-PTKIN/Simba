<?php

namespace App\Http\Controllers\Pendaftar;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $beasiswa = Beasiswa::where('status', 1)
            ->whereHas('jadwal_kegiatan', function ($query) {
                $query->is_active()
                    ->where('role', 'PENDAFTARAN')
                    ->whereHas('tahun_kegiatan', function ($q) {
                        $q->where('status', 1);
                    });
            })
            ->get();
        return view('pendaftar.index', [
            'beasiswa' => $beasiswa
        ]);
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
        $beasiswa = Beasiswa::with(['jadwal_kegiatan' => function ($query) {
            $query->orderBy('tanggal_mulai', 'asc');
        }])
            ->findOrFail($id);
        $tahun_kegiatan_id = count($beasiswa->jadwal_kegiatan) ? $beasiswa->jadwal_kegiatan[0]->tahun_kegiatan_id : null;
        $beasiswa_id = count($beasiswa->jadwal_kegiatan) ? $beasiswa->jadwal_kegiatan[0]->beasiswa_id : null;
        $is_jadwal_daftar = cek_jadwal($tahun_kegiatan_id, $beasiswa_id, 'PENDAFTARAN', true);

        return view('pendaftar.detail-beasiswa', [
            'beasiswa' => $beasiswa,
            'is_jadwal_daftar' => $is_jadwal_daftar
        ]);
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

    public function beasiswa(string $status)
    {
        $beasiswa = Beasiswa::where('status', 1)
            ->whereHas('jadwal_kegiatan', function ($query) use ($status) {
                if ($status === 'all') {
                    return $query->where('role', 'PENDAFTARAN')
                        ->whereHas('tahun_kegiatan', function ($q) {
                            $q->where('status', 1);
                        });
                } else if ($status === 'open') {
                    return $query->is_active()
                        ->where('role', 'PENDAFTARAN')
                        ->whereHas('tahun_kegiatan', function ($q) {
                            $q->where('status', 1);
                        });
                } else if ($status === 'close') {
                    return $query->is_notActive()
                        ->where('role', 'PENDAFTARAN')
                        ->whereHas('tahun_kegiatan', function ($q) {
                            $q->where('status', 1);
                        });
                }
            })
            ->get();

        return view('pendaftar.list-beasiswa', [
            'beasiswa' => $beasiswa
        ]);
    }
}