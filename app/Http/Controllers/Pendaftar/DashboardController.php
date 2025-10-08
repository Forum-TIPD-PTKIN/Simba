<?php

namespace App\Http\Controllers\Pendaftar;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\JadwalKegiatan;
use App\Models\TahunKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $beasiswa = Beasiswa::where('status', 1)
            ->whereHas('jadwal_kegiatan', function ($query) {
                $query->is_active()
                    ->where('role', 'PENDAFTARAN')
                    ->whereHas('tahun_kegiatan', function ($q) {
                        $q->where('status', 1);
                    });
            })
            ->get();
        return view('pendaftar.index', [
            'beasiswa' => $beasiswa
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
    public function show(string $id)
    {
        $beasiswa = Beasiswa::with(['jadwal_kegiatan' => function ($query) {
            $query->orderBy('tanggal_mulai', 'asc');
        }])
            ->findOrFail($id);
        $tahun_kegiatan_id = count($beasiswa->jadwal_kegiatan) ? $beasiswa->jadwal_kegiatan[0]->tahun_kegiatan_id : null;
        $beasiswa_id = count($beasiswa->jadwal_kegiatan) ? $beasiswa->jadwal_kegiatan[0]->beasiswa_id : null;
        $is_jadwal_daftar = cek_jadwal($tahun_kegiatan_id, $beasiswa_id, 'PENDAFTARAN', true);

        return view('pendaftar.detail-beasiswa', [
            'beasiswa' => $beasiswa,
            'is_jadwal_daftar' => $is_jadwal_daftar
        ]);
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

    public function beasiswa(string $status)
    {
        $beasiswa = Beasiswa::where('status', 1)
            ->whereHas('jadwal_kegiatan', function ($query) use ($status) {
                if ($status === 'all') {
                    return $query->where('role', 'PENDAFTARAN')
                        ->whereHas('tahun_kegiatan', function ($q) {
                            $q->where('status', 1);
                        });
                } else if ($status === 'open') {
                    return $query->is_active()
                        ->where('role', 'PENDAFTARAN')
                        ->whereHas('tahun_kegiatan', function ($q) {
                            $q->where('status', 1);
                        });
                } else if ($status === 'close') {
                    return $query->is_notActive()
                        ->where('role', 'PENDAFTARAN')
                        ->whereHas('tahun_kegiatan', function ($q) {
                            $q->where('status', 1);
                        });
                }
            })
            ->get();

        return view('pendaftar.list-beasiswa', [
            'beasiswa' => $beasiswa
        ]);
    }

    public function jadwal(Request $request)
    {
        $master_tahun = TahunKegiatan::whereHas('pendaftar', function ($query) {
            $query->where('user_id', Auth::id());
        })->orderBy('tahun', 'desc')
            ->get();
        $master_beasiswa = Beasiswa::whereHas('pendaftar', function ($query) {
            $query->where('user_id', Auth::id())
                ->whereHas('tahun_kegiatan', function ($q) {
                    $q->where('status', 1);
                });
        })->get();
        $jadwal = JadwalKegiatan::where('tahun_kegiatan_id', count($master_tahun) ? $master_tahun[0]->id : null)
            ->where('beasiswa_id', count($master_beasiswa) ? $master_beasiswa[0]->id : null)
            ->orderBy('tanggal_mulai')
            ->get();

        if ($request->ajax()) {
            return DataTables::of($jadwal)
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
                ->editColumn('tanggal_mulai', function ($data) {
                    if ($data->role === 'SURVEI_LOKASI') return 'Kondisional';
                    return $data->formatTanggal('tanggal_mulai', 'd-m-Y H:i');
                })
                ->editColumn('tanggal_selesai', function ($data) {
                    if ($data->role === 'SURVEI_LOKASI') return 'Kondisional';
                    return $data->formatTanggal('tanggal_selesai', 'd-m-Y H:i');
                })
                ->editColumn('deskripsi', function ($data) {
                    return '<p>' . Str::words(strip_tags($data->deskripsi), 10, '...') . '</p>';
                })
                ->rawColumns(['beasiswa', 'deskripsi'])
                ->make(true);
        }

        return view('pendaftar.jadwal-kegiatan', [
            'master_tahun' => $master_tahun,
            'master_beasiswa' => $master_beasiswa,
            'jadwal' => $jadwal
        ]);
    }
}