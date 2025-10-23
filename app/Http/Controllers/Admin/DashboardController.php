<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\Pendaftar;
use App\Models\TahunKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\Return_;

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
        ];

        switch ($label_filter) {
            case 'filter_status':
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
                break;

            case 'filter_prodi':
                $rekap = Pendaftar::with(['latestStatus', 'mahasiswa'])
                    ->where('tahun_kegiatan_id', $tahun)
                    ->where('beasiswa_id', $beasiswa)
                    ->get()
                    ->filter(fn($p) => $p->latest_status->status && $p->mahasiswa)
                    ->groupBy(function ($p) {
                        return implode('|', [
                            $p->mahasiswa->prodi,
                            $p->latest_status?->status,
                            $p->beasiswa_id,
                            $p->tahun_kegiatan_id
                        ]);
                    })
                    ->map(function ($group, $key) {
                        [$prodi, $prodi_name, $status] = explode('|', $key);
                        return [
                            'prodi' => $prodi,
                            'prodi_name' => $prodi_name,
                            'status' => $status,
                            'jumlah' => $group->count()
                        ];
                    })
                    ->sortBy(fn($item) => [
                        (int) $item['prodi'],
                        $statusOrder[$item['status']] ?? PHP_INT_MAX
                    ])
                    ->groupBy('prodi_name')
                    ->all();
                break;

            default:
                # code...
                break;
        }

        return $rekap;
    }
}