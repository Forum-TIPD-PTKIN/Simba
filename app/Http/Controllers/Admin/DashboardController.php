<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\Pendaftar;
use App\Models\PendaftarStatus;
use App\Models\TahunKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $view_rekap_status = view('admin.dashboard.status-pendaftar', [
            'rekap_status' => $rekap_status
        ])->render();

        $rekap_prodi = $this->rekap_data('filter_prodi', count($tahun_kegiatan) ? $tahun_kegiatan[0]->id : null, count($beasiswa) ? $beasiswa[0]->id : null);
        $view_rekap_prodi = view('admin.dashboard.prodi-pendaftar', [
            'rekap_prodi' => $rekap_prodi
        ])->render();

        return view('admin.dashboard', [
            'tahun_kegiatan' => $tahun_kegiatan,
            'beasiswa' => $beasiswa,
            'view_rekap_status' => $view_rekap_status,
            'view_rekap_prodi' => $view_rekap_prodi
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
                return view('admin.dashboard.status-pendaftar', [
                    'rekap_status' => $rekap
                ])->render();
                break;

            case 'filter_prodi':
                return view('admin.dashboard.prodi-pendaftar', [
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
        $statusOrder = [
            'DAFTAR' => 0,
            'PENGAJUAN' => 1,
            'GAGAL ADMINISTRASI' => 2,
            'LOLOS ADMINISTRASI' => 3,
            'GAGAL TPA' => 4,
            'LOLOS TPA' => 5,
            'TIDAK LOLOS PENERIMA' => 6,
            'LOLOS PENERIMA' => 7,
        ];

        switch ($label_filter) {
            case 'filter_status':
                $rekap = PendaftarStatus::selectRaw('status, COUNT(status) AS jumlah')
                    ->whereHas('pendaftar', function ($query) use ($tahun, $beasiswa) {
                        $query->where('tahun_kegiatan_id', $tahun)
                            ->where('beasiswa_id', $beasiswa);
                    })
                    ->groupBy('status')
                    ->get()
                    ->sortBy(function ($item) use ($statusOrder) {
                        return $statusOrder[$item->status] ?? PHP_INT_MAX;
                    })
                    ->values();
                break;

            case 'filter_prodi':
                $rekap = PendaftarStatus::selectRaw('mahasiswas.prodi, pendaftar_statuses.status, COUNT(*) as jumlah')
                    ->join('pendaftars', 'pendaftars.id', '=', 'pendaftar_statuses.pendaftar_id')
                    ->join('mahasiswas', 'mahasiswas.pendaftar_id', '=', 'pendaftars.id')
                    ->whereHas('pendaftar', function ($query) use ($tahun, $beasiswa) {
                        $query->where('tahun_kegiatan_id', $tahun)
                            ->where('beasiswa_id', $beasiswa);
                    })
                    ->groupBy('mahasiswas.prodi', 'pendaftar_statuses.status')
                    ->orderBy('mahasiswas.prodi')
                    ->get()
                    ->sortBy(function ($item) use ($statusOrder) {
                        return [
                            $item->prodi,
                            $statusOrder[$item->status] ?? PHP_INT_MAX
                        ];
                    })
                    ->groupBy('prodi');
                break;

            default:
                # code...
                break;
        }

        return $rekap;
    }
}
