<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\JadwalKegiatan;
use App\Models\Notifikasi;
use App\Models\Pendaftar;
use App\Models\PendaftarStatus;
use App\Models\SiakadMahasiswa;
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
        $beasiswa = Beasiswa::select('beasiswas.*')
            ->join('jadwal_kegiatans', function ($db) {
                $db->on('jadwal_kegiatans.beasiswa_id', '=', 'beasiswas.id')
                    ->where('jadwal_kegiatans.role', 'SELEKSI_ADMINISTRASI')
                    ->whereExists(function ($db) {
                        $db->select(DB::raw('1'))
                            ->from('tahun_kegiatans as ta')
                            ->whereColumn('ta.id', 'jadwal_kegiatans.tahun_kegiatan_id')
                            ->where('ta.status', 1);
                    });
            })
            ->where('beasiswas.status', 1)
            ->orderBy('jadwal_kegiatans.tanggal_mulai', 'desc')
            ->orderBy('beasiswas.nama')
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

        $key_pmb = env('PMB_KEY_API');
        $req_pmb = api()->get("https://pmb.uinmadura.ac.id/api/info/jalur/{$data->mahasiswa?->nim}?key={$key_pmb}");
        if ($req_pmb->status) $data_pmb = $req_pmb->data;

        return view('verifikator.modal-seleksi-administrasi', [
            'data' => $data,
            'data_pmb' => $data_pmb ?? null
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
            $status_pendaftar->status = trim(strip_tags($request->status_verval)) === 'success' ? 'LOLOS ADMINISTRASI' : 'GAGAL ADMINISTRASI';
            $status_pendaftar->deskripsi = json_encode([
                'valid_form' => $is_valid_form,
                'catatan' => $request->catatan,
                'verifikator' => Auth::user()->name,
            ]);
            $status_pendaftar->save();

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
                    fn($q) => $q->where('status', 'PENGAJUAN')
                )
                ->get();

            $is_jadwal_verifikasi = cek_jadwal($request->flt_tahun, $request->flt_beasiswa, 'SELEKSI_ADMINISTRASI', is_active: true); // return true atau false
            $is_jadwal_sanggah = cek_jadwal($request->flt_tahun, $request->flt_beasiswa, 'SANGGAH_SELEKSI_ADMINISTRASI', is_active: true); // return true atau false

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
                ->addColumn('action', function ($data) use ($is_jadwal_verifikasi, $is_jadwal_sanggah) {
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
                                'is_disabled' => !$is_jadwal_verifikasi && !$is_jadwal_sanggah
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