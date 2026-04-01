<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Admin\RekapPendaftarExport;
use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\Pendaftar;
use App\Models\TahunKegiatan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class RekapController extends Controller
{
    public function index()
    {
        $master_tahun = TahunKegiatan::orderBy('tahun', 'desc')
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
        $status_pendaftar = [
            'DAFTAR',
            'PENGAJUAN',
            'LOLOS ADMINISTRASI',
            'GAGAL ADMINISTRASI',
            'LOLOS TPA',
            'GAGAL TPA',
            'LOLOS PENERIMA',
            'TIDAK LOLOS PENERIMA',
        ];

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
                    $query->whereHas('pendaftar_status', function ($query) use ($status) {
                        $query->where('status', $status);
                    });
                })
                ->orderBy('mahasiswas.fakultas')
                ->orderBy('mahasiswas.prodi')
                ->orderBy('mahasiswas.nim');

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

    public function unduh(Request $request)
    {
        set_time_limit(60 * 60);

        $tahun = $request->tahun;
        $beasiswa = $request->beasiswa;
        $status = $request->status;

        $dt_tahun = TahunKegiatan::where('id', $tahun)->pluck('tahun')->first();
        $dt_beasiswa = Beasiswa::where('id', $beasiswa)->pluck('nama')->first();

        return Excel::download(new RekapPendaftarExport($tahun, $beasiswa, $status), "Rekapitulasi Data Pendaftar Beasiswa {$dt_beasiswa} Tahun {$dt_tahun}.xlsx");
    }
}
