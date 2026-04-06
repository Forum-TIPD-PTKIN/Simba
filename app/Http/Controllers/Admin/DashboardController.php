<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\PendaftarStatus;
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
        $beasiswa = Beasiswa::where('beasiswas.status', 1)
            ->orderByActiveRegistration()
            ->get();

        $rekap = $this->rekap_data(count($tahun_kegiatan) ? $tahun_kegiatan[0]->id : null, count($beasiswa) ? $beasiswa[0]->id : null);
        $view_rekap = view('admin.dashboard.rekap', [
            'rekap_status' => $rekap['status'],
            'rekap_prodi' => $rekap['prodi']
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

        return view('admin.dashboard.rekap', [
            'rekap_status' => $rekap['status'],
            'rekap_prodi' => $rekap['prodi']
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
            'GAGAL TPA' => 4,
            'LOLOS TPA' => 5,
            'TIDAK LOLOS PENERIMA' => 6,
            'LOLOS PENERIMA' => 7,
        ];

        $rekap_status = PendaftarStatus::selectRaw('status, COUNT(status) AS jumlah')
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

        $rekap_prodi = PendaftarStatus::selectRaw('mahasiswas.prodi, pendaftar_statuses.status, COUNT(*) as jumlah')
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

        return [
            'status' => $rekap_status,
            'prodi' => $rekap_prodi
        ];
    }
}
