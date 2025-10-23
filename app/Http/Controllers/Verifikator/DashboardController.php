<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\Pendaftar;
use App\Models\TahunKegiatan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahun_kegiatan = TahunKegiatan::orderBy('tahun', 'desc')->get();
        $beasiswa = Beasiswa::where('status', 1)->get();

        $rekap_status = $this->rekap_data('filter_status', count($tahun_kegiatan) ? $tahun_kegiatan[0]->id : null, count($beasiswa) ? $beasiswa[0]->id : null);
        $view_rekap_status = view('verifikator.dashboard.status-pendaftar', [
            'rekap_status' => $rekap_status
        ])->render();

        return view('verifikator.dashboard', [
            'tahun_kegiatan' => $tahun_kegiatan,
            'beasiswa' => $beasiswa,
            'view_rekap_status' => $view_rekap_status
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
        $rekap = $this->rekap_data($request->filter, $tahun, $beasiswa);

        switch ($request->filter) {
            case 'filter_status':
                return view('verifikator.dashboard.status-pendaftar', [
                    'rekap_status' => $rekap
                ])->render();
                break;

            case 'filter_prodi':
                return view('verifikator.dashboard.prodi-pendaftar', [
                    'rekap_prodi' => $rekap
                ])->render();
                break;

            default:
                # code...
                break;
        }
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

    public static function rekap_data($label_filter, $tahun, $beasiswa)
    {
        switch ($label_filter) {
            case 'filter_status':
                $rekap = Pendaftar::with(['latestStatus'])
                    ->where('tahun_kegiatan_id', $tahun)
                    ->where('beasiswa_id', $beasiswa)
                    ->whereHas('latestStatus', function ($query) {
                        $query->whereIn('status', ['PENGAJUAN', 'LOLOS ADMINISTRASI', 'GAGAL ADMINISTRASI']);
                    })
                    ->get()
                    ->map(function ($item) {
                        return [
                            'label' => ($item->latest_status?->status === 'GAGAL ADMINISTRASI' or $item->latest_status?->status === 'LOLOS ADMINISTRASI') ? 'SUDAH DIVERIFIKASI' : 'BELUM DIVERIFIKASI',
                            'data' => $item->id
                        ];
                    })
                    ->groupBy('label')
                    ->map(function ($group) {
                        return [
                            'label' => $group[0]['label'],
                            'jumlah' => count($group),
                        ];
                    })
                    ->sortBy('label')
                    ->values();
                break;

            case 'filter_prodi':
                # code ...
                break;

            default:
                # code...
                break;
        }

        return $rekap;
    }
}