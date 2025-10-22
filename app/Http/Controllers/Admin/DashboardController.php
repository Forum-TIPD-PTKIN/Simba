<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\Pendaftar;
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

        $rekap = $this->rekap_data(count($tahun_kegiatan) ? $tahun_kegiatan[0]->id : null, count($beasiswa) ? $beasiswa[0]->id : null);
        $view_rekap = view('admin.rekap-data', [
            'rekap' => $rekap
        ])->render();

        return view('admin.dashboard', [
            'tahun_kegiatan' => $tahun_kegiatan,
            'beasiswa' => $beasiswa,
            'view_rekap' => $view_rekap
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
    public function show(Request $request, string $tahun, string $beasiswa)
    {
        $rekap = $this->rekap_data($tahun, $beasiswa);

        return view('admin.rekap-data', [
            'rekap' => $rekap
        ])->render();
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

    public static function rekap_data($tahun, $beasiswa)
    {
        $statusOrder = [
            'DAFTAR' => 0,
            'PENGAJUAN' => 1,
            'GAGAL ADMINISTRASI' => 2,
            'LOLOS ADMINISTRASI' => 3,
        ];
        $rekap = Pendaftar::with('latestStatus')
            ->where('tahun_kegiatan_id', $tahun)
            ->where('beasiswa_id', $beasiswa)
            ->get()
            ->groupBy(fn($p) => $p->latest_status?->status ?? 'BELUM ADA STATUS')
            ->map(function ($group) {
                return [
                    'label' => $group[0]->latest_status?->status ?? 'BELUM ADA STATUS',
                    'value' => count($group)
                ];
            })
            ->sortBy(fn($item) => $statusOrder[$item['label']] ?? PHP_INT_MAX)
            ->values();

        return $rekap;
    }
}
