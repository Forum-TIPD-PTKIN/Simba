<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\Pendaftar;
use App\Models\Surveyor;
use App\Models\TahunKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahun_kegiatan = TahunKegiatan::orderBy('tahun', 'desc')->get();
        $beasiswa = Beasiswa::where('status', 1)->get();

        $responden = Pendaftar::with(['mahasiswa', 'beasiswa', 'tahun_kegiatan', 'biodata_pendaftar'])
            ->where('tahun_kegiatan_id', count($tahun_kegiatan) ? $tahun_kegiatan[0]->id : null)
            ->where('beasiswa_id', count($beasiswa) ? $beasiswa[0]->id : null)
            ->whereHas('pendaftar_status', function ($q) {
                $q->where('status', 'LOLOS TPA');
            })
            ->whereHas('surveyor_detail', function ($query) {
                $query->where('surveyor_id', Surveyor::where('user_id', Auth::id())->pluck('id'));
            })
            ->get();
        $view_daftar_responden = view('surveyor.dashboard.daftar-responden', [
            'responden' => $responden
        ])->render();

        return view('surveyor.dashboard', [
            'tahun_kegiatan' => $tahun_kegiatan,
            'beasiswa' => $beasiswa,
            'view_daftar_responden' => $view_daftar_responden
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
    public function show(string $tahun, string $beasiswa)
    {
        $responden = Pendaftar::with(['mahasiswa', 'beasiswa', 'tahun_kegiatan', 'biodata_pendaftar'])
            ->where('tahun_kegiatan_id', $tahun)
            ->where('beasiswa_id', $beasiswa)
            ->whereHas('pendaftar_status', function ($q) {
                $q->where('status', 'LOLOS TPA');
            })
            ->whereHas('surveyor_detail', function ($query) {
                $query->where('surveyor_id', Surveyor::where('user_id', Auth::id())->pluck('id'));
            })
            ->get();

        $view_daftar_responden = view('surveyor.dashboard.daftar-responden', [
            'responden' => $responden
        ])->render();

        return $view_daftar_responden;
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