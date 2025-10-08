<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\Pendaftar;
use App\Models\TahunKegiatan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RekapController extends Controller
{
    public function index()
    {
        $master_tahun = TahunKegiatan::where('status', 1)
            ->orderBy('tahun', 'desc')
            ->get();
        $master_beasiswa = Beasiswa::where('status', 1)
            ->get();
        // $status_pendaftar = Pendaftar::where('tahun_kegiatan_id', count($master_tahun) ? $master_tahun[0]->id : null)
        //     ->where('beasiswa_id', count($master_beasiswa) ? $master_beasiswa[0]->id : null)
        //     ->get()
        //     ->pluck('latest_status.status')
        //     ->filter()
        //     ->unique()
        //     ->values();
        $status_pendaftar = ['DAFTAR', 'PENGAJUAN', 'LOLOS ADMINISTRASI', 'GAGAL ADMINISTRASI'];

        return view('admin.laporan.rekap-pendaftar', [
            'master_tahun' => $master_tahun,
            'master_beasiswa' => $master_beasiswa,
            'status_pendaftar' => $status_pendaftar
        ]);
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $pendaftar = Pendaftar::with(['mahasiswa'])
                ->select('pendaftars.*')
                ->join('mahasiswas', 'pendaftars.id', 'mahasiswas.pendaftar_id')
                ->when($request->flt_tahun, function ($query, $tahun) {
                    $query->where('tahun_kegiatan_id', $tahun);
                })
                ->when($request->flt_beasiswa, function ($query, $beasiswa) {
                    $query->where('beasiswa_id', $beasiswa);
                })
                ->when($request->flt_status, function ($query, $status) {
                    $query->whereHas(
                        'latestStatus',
                        fn($q) => $q->where('status', $status)
                    );
                })
                ->orderBy('mahasiswas.fakultas')
                ->orderBy('mahasiswas.prodi')
                ->orderBy('mahasiswas.nim')
                ->get();

            return DataTables::of($pendaftar)
                ->editColumn('beasiswa', function ($data) {
                    return "
                            <div class='flex-grow-1'>
                              <div class='row g-1'>
                                <div class='col-12'>
                                  <h6 class='mb-0'>{$data->beasiswa?->nama}</h6>
                                  <p class='text-muted mb-0'><small>{$data->tahun_kegiatan?->tahun}</small></p>
                                </div>
                              </div>
                            </div>";
                })
                ->editColumn('status', function ($data) {
                    return "<span class='badge bg-primary'>{$data->latest_status?->status}</span>";
                })
                ->rawColumns(['beasiswa', 'status'])
                ->make(true);
        }
    }
}