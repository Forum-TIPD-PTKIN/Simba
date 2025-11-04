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
        $data = Pendaftar::with(['pemberkasan', 'biodata_pendaftar', 'mahasiswa', 'beasiswa', 'tahun_kegiatan', 'pendaftar_status'])
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

        $pendaftar = Pendaftar::find($request->pendaftar_id);
        if (!$pendaftar) return response()->json('Pendaftar tidak ditemukan', 404);

        $is_jadwal_verifikasi = cek_jadwal($pendaftar->tahun_kegiatan_id, $pendaftar->beasiswa_id, 'SELEKSI_ADMINISTRASI', is_active: true); // return true atau false
        $is_jadwal_sanggah = cek_jadwal($pendaftar->tahun_kegiatan_id, $pendaftar->beasiswa_id, 'SANGGAH_SELEKSI_ADMINISTRASI', is_active: true); // return true atau false

        if (!$is_jadwal_verifikasi && !$is_jadwal_sanggah) return response()->json('Tidak dapat melakukan seleksi administrasi', 419);

        $is_valid_form = [];
        foreach ($request->verifikasi as $key => $value) {
            array_push($is_valid_form, [$key => $value == 1 ? 'Valid' : 'Invalid']);
        }

        try {
            $status_pendaftar = PendaftarStatus::where('pendaftar_id', $pendaftar->id)
                ->orderBy('created_at', 'desc')
                ->first();
            if ($pendaftar->latest_status?->status === 'PENGAJUAN' && $status_pendaftar->status === 'PENGAJUAN') {
                $status_pendaftar = new PendaftarStatus();
            } else if (in_array($status_pendaftar->status, ['LOLOS ADMINISTRASI', 'GAGAL ADMINISTRASI']) && $status_pendaftar->deskripsi_json['verifikator'] !== Auth::user()->name) {
                return response()->json('Pendaftar sudah diverifikasi oleh verifikator lain', 419);
            }

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
                ->selectRaw('pendaftars.*, mahasiswas.nim, mahasiswas.nama as nama_mahasiswa')
                ->join('mahasiswas', 'mahasiswas.pendaftar_id', 'pendaftars.id')
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
                ->orderBy('mahasiswas.fakultas')
                ->orderBy('mahasiswas.prodi')
                ->orderBy('mahasiswas.nim');

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
                    return "<div id='status_{$data->id}'><span class='badge bg-primary'>{$data->latest_status?->status}</span></div>";
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

    public function rekap()
    {
        $tahun_kegiatan = TahunKegiatan::orderBy('tahun', 'desc')
            ->get();
        $beasiswa = Beasiswa::where('status', 1)
            ->orderBy('nama')
            ->get();
        $status = ['LOLOS ADMINISTRASI', 'GAGAL ADMINISTRASI'];

        return view('verifikator.laporan.seleksi-administrasi', [
            'tahun_kegiatan' => $tahun_kegiatan,
            'beasiswa' => $beasiswa,
            'status' => $status
        ]);
    }

    public function rekap_data(Request $request)
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
                        fn($q) => $q->where('status', 'LOLOS ADMINISTRASI')
                            ->orWhere('status', 'GAGAL ADMINISTRASI')
                    );
                })
                ->orderBy('mahasiswas.fakultas')
                ->orderBy('mahasiswas.prodi')
                ->orderBy('mahasiswas.nim');

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
                    $status_seleksi_administrasi = collect($data->pendaftar_status)->filter(fn($item) => in_array($item->status, ['LOLOS ADMINISTRASI', 'GAGAL ADMINISTRASI']))->first();
                    return "<span class='badge bg-primary'>{$status_seleksi_administrasi?->status}</span>";
                })
                ->editColumn('verifikator', function ($data) {
                    $status_seleksi_administrasi = collect($data->pendaftar_status)->filter(fn($item) => in_array($item->status, ['LOLOS ADMINISTRASI', 'GAGAL ADMINISTRASI']))->first();
                    $deskripsi = json_decode($status_seleksi_administrasi?->deskripsi);
                    return $deskripsi?->verifikator;
                })
                ->addColumn('action', function ($data) use ($is_jadwal_verifikasi, $is_jadwal_sanggah) {
                    $status_seleksi_administrasi = collect($data->pendaftar_status)->filter(fn($item) => in_array($item->status, ['LOLOS ADMINISTRASI', 'GAGAL ADMINISTRASI']))->first();
                    $deskripsi = json_decode($status_seleksi_administrasi?->deskripsi);
                    if (Auth::user()->name === $deskripsi?->verifikator && ($is_jadwal_verifikasi || $is_jadwal_sanggah)) {
                        return view('admin.template._action_button_table', [
                            'data' => $data,
                            'title' => 'Status Seleksi',
                            'buttons' => [
                                'ubahVerifikasi' => [
                                    'title' => 'Sunting',
                                    'icon' => 'ti ti-edit-circle',
                                    'btn-class' => 'btn btn-warning',
                                    'encrypted_id' => $data->id,
                                    'is_disabled' => !$is_jadwal_verifikasi && !$is_jadwal_sanggah
                                ]
                            ]
                        ])
                            ->render();
                    }
                })
                ->rawColumns(['beasiswa', 'status', 'action'])
                ->make(true);
        }
    }
}