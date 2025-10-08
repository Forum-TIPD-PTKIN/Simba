<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\JadwalKegiatan;
use App\Models\Pendaftar;
use App\Models\TahunKegiatan;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LaporanController extends Controller
{
    public function verifikasi(Request $request)
    {

        $tahun_kegiatan = TahunKegiatan::orderBy('tahun', 'desc')
            ->get();
        $beasiswa = Beasiswa::where('status', 1)
            ->orderBy('nama')
            ->get();
        $jadwal_kegiatan = JadwalKegiatan::where('tahun_kegiatan_id', count($tahun_kegiatan) ? $tahun_kegiatan[0]->id : null)
            ->where('beasiswa_id', count($beasiswa) ? $beasiswa[0]->id : null)
            ->where('role', 'SELEKSI_ADMINISTRASI')
            ->first();
        return view('admin.laporan.verifikasi', [
            'tahun_kegiatan' => $tahun_kegiatan,
            'beasiswa' => $beasiswa,
            'jadwal_kegiatan' => $jadwal_kegiatan
        ]);
    }
    public function data(Request $request)
    {
        if ($request->ajax()) {
            $dt_pendaftar = Pendaftar::with(['mahasiswa'])
                ->when($request->flt_tahun, function ($q) use ($request) {
                    return $q->where('tahun_kegiatan_id', $request->flt_tahun);
                })
                ->when($request->flt_beasiswa, function ($q) use ($request) {
                    return $q->where('beasiswa_id', $request->flt_beasiswa);
                })
                ->whereHas('pemberkasan')
                ->whereHas(
                    'latestStatus',
                    fn($q) => $q->where('status', 'PENGAJUAN')
                )
                ->get();

            $is_jadwal_verifikasi = cek_jadwal($request->flt_tahun, $request->flt_beasiswa, 'SELEKSI_ADMINISTRASI', is_active: true); // return true atau false

            return DataTables::of($dt_pendaftar)
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

    public function jadwal(Request $request)
    {
        if (request()->ajax()) {
            $jadwal_kegiatan = JadwalKegiatan::with(['tahun_kegiatan', 'beasiswa'])
                ->where('tahun_kegiatan_id', trim(strip_tags($request->tahun)))
                ->where('beasiswa_id', trim(strip_tags($request->beasiswa)))
                ->where('role', 'SELEKSI_ADMINISTRASI')
                ->first();

            return response()->json($jadwal_kegiatan);
        }
    }
}
