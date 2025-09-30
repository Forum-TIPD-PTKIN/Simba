<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\JadwalKegiatan;
use App\Models\Notifikasi;
use App\Models\Pendaftar;
use App\Models\PendaftarStatus;
use App\Models\TahunKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SeleksiAdministrasiController extends Controller
{
    public function index()
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

        return view('verifikator.seleksi-administrasi', [
            'tahun_kegiatan' => $tahun_kegiatan,
            'beasiswa' => $beasiswa,
            'jadwal_kegiatan' => $jadwal_kegiatan
        ]);
    }

    public function edit(string $id)
    {
        $data = Pendaftar::with(['pemberkasan', 'mahasiswa', 'beasiswa', 'tahun_kegiatan', 'pendaftar_status'])
            ->find($id);

        return view('verifikator.modal-seleksi-administrasi', [
            'data' => $data
        ])->render();
    }

    public function store(Request $request)
    {
        $request->validate([
            'status_verval' => 'required'
        ], [
            'status_verval.required' => 'Status verifikasi dan validasi belum dipilih'
        ]);

        $is_valid_form = [];
        foreach ($request->verifikasi as $key => $value) {
            array_push($is_valid_form, [$key => $value == 1 ? 'Valid' : 'Invalid']);
        }

        try {
            $status_pendaftar = new PendaftarStatus();
            $status_pendaftar->pendaftar_id = trim(strip_tags($request->pendaftar_id));
            $status_pendaftar->status = trim(strip_tags($request->status_verval)) === 'success' ? 'LOLOS ADMINISTRASI' : (trim(strip_tags($request->status_verval)) === 'sanggah' ? 'SANGGAH ADMINISTRASI' : 'GAGAL ADMINISTRASI');
            $status_pendaftar->deskripsi = json_encode([
                'valid_form' => $is_valid_form,
                'catatan' => $request->catatan,
                'verifikator' => "Verifikator : " . Auth::user()->name,
            ]);
            $status_pendaftar->save();

            $pendaftar = Pendaftar::with(['beasiswa', 'tahun_kegiatan'])
                ->find($request->pendaftar_id);

            Notifikasi::create([
                'user_id' => $pendaftar?->user_id,
                'key' => $request->status_verval === 'success' ? 'LOLOS_ADMINISTRASI' : ($request->status_verval === 'sanggah' ? 'SANGGAH_ADMINISTRASI' : 'GAGAL ADMINISTRASI'),
                'pesan'   => 'Berdasarkan hasil verifikasi dan validasi oleh tim verifikator, Anda dinyatakan ' . ($request->status_verval === 'success' ? 'Lolos Seleksi Administrasi' : ($request->status_verval !== 'sanggah' ? 'Tidak Lolos Seleksi Administrasi' : 'Tidak Lolos Seleksi Administrasi, tapi Anda berhak untuk menyanggah hasil seleksi.')),
                'dibaca' => 0,
                'referensi' => 'pendaftar/riwayat'
            ]);

            $data = array(
                'icon' => 'success',
                'title' => 'Berhasil',
                'message' => 'Berhasil verifikasi dan validasi pendaftar'
            );

            return response()->json($data);
        } catch (\Illuminate\Database\QueryException $e) {
            $error = $e->errorInfo;
            $data['message'] = $error[2] ?: 'Ada kesalahan saat menyimpan data';

            return response()->json($data, 422);
        }
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
                    fn($q) => $q->where('status', 'DAFTAR')
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
                ->addColumn('action', function ($data) use ($is_jadwal_verifikasi) {
                    return view('verifikator.template._action_button_table', [
                        'data' => $data,
                        'title' => 'Pendaftar',
                        'showTitle' => true,
                        'buttons' => [
                            'verifikasi' => [
                                'title' => 'Verifikasi',
                                'icon' => 'ti ti-checkbox',
                                'btn-class' => 'btn btn-primary',
                                'encrypted_id' => $data->id,
                                'is_disabled' => !$is_jadwal_verifikasi
                            ]
                        ]
                    ])
                        ->render();
                })
                ->rawColumns(['beasiswa', 'status', 'action'])
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