<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Admin\HasilSeleksiAdministrasiExport;
use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\JadwalKegiatan;
use App\Models\Pendaftar;
use App\Models\PendaftarStatus;
use App\Models\TahunKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
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
        $status = ['LOLOS ADMINISTRASI', 'GAGAL ADMINISTRASI'];
        $jadwal_kegiatan = JadwalKegiatan::where('tahun_kegiatan_id', count($tahun_kegiatan) ? $tahun_kegiatan[0]->id : null)
            ->where('beasiswa_id', count($beasiswa) ? $beasiswa[0]->id : null)
            ->where('role', 'SELEKSI_ADMINISTRASI')
            ->first();
        return view('admin.laporan.verifikasi', [
            'tahun_kegiatan' => $tahun_kegiatan,
            'beasiswa' => $beasiswa,
            'jadwal_kegiatan' => $jadwal_kegiatan,
            'status' => $status
        ]);
    }
    public function data(Request $request)
    {
        if ($request->ajax()) {
            $dt_pendaftar = Pendaftar::with(['mahasiswa'])
                ->selectRaw('pendaftars.*')
                ->join('mahasiswas', 'pendaftars.id', '=', 'mahasiswas.pendaftar_id')
                ->join(
                    DB::raw('(
                    SELECT *
                    FROM pendaftar_statuses AS ps1
                    WHERE created_at = (
                        SELECT MAX(created_at)
                        FROM pendaftar_statuses AS ps2
                        WHERE ps2.pendaftar_id = ps1.pendaftar_id
                    )
                ) as ps'),
                    'pendaftars.id',
                    '=',
                    'ps.pendaftar_id'
                )
                ->when($request->flt_tahun, function ($q) use ($request) {
                    return $q->where('tahun_kegiatan_id', $request->flt_tahun);
                })
                ->when($request->flt_beasiswa, function ($q) use ($request) {
                    return $q->where('beasiswa_id', $request->flt_beasiswa);
                })
                ->whereHas('pemberkasan')
                ->where(function ($query) use ($request) {
                    if ($request->flt_status) {
                        return $query->whereHas('pendaftar_status', fn($q) => $q->where('status', $request->flt_status));
                    }

                    return $query->whereHas(
                        'pendaftar_status',
                        fn($q) => $q->where('status', 'LOLOS ADMINISTRASI')->orWhere('status', 'GAGAL ADMINISTRASI')
                    );
                })
                ->orderBy('mahasiswas.fakultas')
                ->orderBy('mahasiswas.prodi')
                ->orderBy('mahasiswas.nim');

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
                    $status_seleksi_administrasi = collect($data->pendaftar_status)
                        ->filter(fn($item) => in_array($item->status, ['LOLOS ADMINISTRASI', 'GAGAL ADMINISTRASI']))
                        ->first();
                    return "<span class='badge bg-primary'>{$status_seleksi_administrasi?->status}</span>";
                })
                ->editColumn('verifikator', function ($data) {
                    $status_seleksi_administrasi = collect($data->pendaftar_status)
                        ->filter(fn($item) => in_array($item->status, ['LOLOS ADMINISTRASI', 'GAGAL ADMINISTRASI']))
                        ->first();
                    $deskripsi = json_decode($status_seleksi_administrasi?->deskripsi);
                    return $deskripsi?->verifikator;
                })
                ->addColumn('action', function ($data) {
                    return view('admin.template._action_button_table', [
                        'data' => $data,
                        'title' => 'Status Seleksi',
                        'buttons' => [
                            'verifikasi' => [
                                'title' => 'Lihat',
                                'icon' => 'ti ti-eye',
                                'btn-class' => 'btn btn-primary',
                                'encrypted_id' => $data->id
                            ]
                        ]
                    ])
                        ->render();
                })
                ->rawColumns(['beasiswa', 'status', 'action'])
                ->make(true);
        }
    }

    public function edit(string $id)
    {
        $data = Pendaftar::with(['pemberkasan', 'mahasiswa', 'beasiswa', 'tahun_kegiatan', 'pendaftar_status', 'latestStatus'])
            ->find($id);

        $is_jadwal_verifikasi = cek_jadwal($data->tahun_kegiatan_id, $data->beasiswa_id, 'SELEKSI_ADMINISTRASI', is_active: true); // return true atau false
        $is_jadwal_sanggah = cek_jadwal($data->tahun_kegiatan_id, $data->beasiswa_id, 'SANGGAH_SELEKSI_ADMINISTRASI', true);

        $key_pmb = env('PMB_KEY_API');
        $req_pmb = api()->get("https://pmb.uinmadura.ac.id/api/info/jalur/{$data->mahasiswa?->nim}?key={$key_pmb}");
        if ($req_pmb->status) $data_pmb = $req_pmb->data;

        return view('admin.laporan.modal-verifikasi', [
            'data' => $data,
            'data_pmb' => $data_pmb ?? null,
            'is_jadwal_verifikasi' => $is_jadwal_verifikasi,
            'is_jadwal_sanggah' => $is_jadwal_sanggah
        ])->render();
    }

    public function update(Request $request, string $id)
    {
        $curr_status = PendaftarStatus::find($id);

        $data = array();
        try {
            if ($curr_status && in_array($curr_status->status, ['LOLOS ADMINISTRASI', 'GAGAL ADMINISTRASI'])) $curr_status->delete();

            $data['icon'] = 'success';
            $data['title'] = 'Berhasil';
            $data['message'] = 'Status seleksi pendaftar berhasil dibatalkan';
        } catch (\Illuminate\Database\QueryException $e) {
            $error = $e->errorInfo;
            $data['message'] = str_contains($error[2], 'constraint') ? 'Data tidak dapat dihapus, masih digunakan' : 'Ada Kesalahan saat menghapus data';

            return response()->json($data, 422);
        }

        return response()->json($data);
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

    public function unduh(Request $request)
    {
        set_time_limit(60 * 60);

        $tahun = $request->tahun;
        $beasiswa = $request->beasiswa;
        $status = $request->status;

        $dt_tahun = TahunKegiatan::where('id', $tahun)->pluck('tahun')->first();
        $dt_beasiswa = Beasiswa::where('id', $beasiswa)->pluck('nama')->first();

        return Excel::download(new HasilSeleksiAdministrasiExport($tahun, $beasiswa, $status), "Data " . ($request->status ?? '') . " Seleksi Administrasi Beasiswa {$dt_beasiswa} Tahun {$dt_tahun}.xlsx");
    }

    public function rekap_verifikator(Request $request)
    {
        $tahun = TahunKegiatan::where('id', $request->tahun)->pluck('tahun')->first();
        $beasiswa = Beasiswa::where('id', $request->beasiswa)->pluck('nama')->first();

        $verifikator = PendaftarStatus::selectRaw("deskripsi->>'$.verifikator' AS verifikator, COUNT(*) AS total")
            ->whereHas('pendaftar', function ($q) use ($request) {
                $q->where('tahun_kegiatan_id', $request->tahun)
                    ->where('beasiswa_id', $request->beasiswa);
            })
            ->whereIn('status', ['LOLOS ADMINISTRASI', 'GAGAL ADMINISTRASI'])
            ->groupBy(DB::raw("deskripsi->>'$.verifikator'"))
            ->orderBy('total', 'desc')
            ->orderBy('verifikator')
            ->get();

        return view('admin.laporan.rekap-verifikator', [
            'tahun' => $tahun,
            'beasiswa' => $beasiswa,
            'verifikator' => $verifikator,
        ])
            ->render();
    }
}
