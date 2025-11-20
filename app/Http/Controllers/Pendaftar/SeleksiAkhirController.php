<?php

namespace App\Http\Controllers\Pendaftar;

use App\Models\Beasiswa;
use App\Models\Pendaftar;
use Illuminate\Http\Request;
use App\Models\TahunKegiatan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SeleksiAkhirController extends Controller
{
    public function index(Request $request)
    {
        $master_beasiswa = Beasiswa::select('beasiswas.id', 'beasiswas.nama')
            ->whereHas('pendaftar', function ($query) {
                $query->where('user_id', Auth::id())
                    ->whereHas('tahun_kegiatan', function ($q) {
                        $q->where('status', 1);
                    });
            })
            ->get();
        $master_tahun = TahunKegiatan::whereHas('pendaftar', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->orderBy('tahun', 'desc')
            ->get();
        $pendaftar = Pendaftar::where('user_id', Auth::user()->id)
            ->where(function ($query) use ($request, $master_tahun) {
                if ($request->flt_tahun) return $query->where('tahun_kegiatan_id', $request->flt_tahun);

                return $query->where('tahun_kegiatan_id', count($master_tahun) ? $master_tahun[0]->id : null);
            })
            ->where(function ($query) use ($request, $master_beasiswa) {
                if ($request->flt_beasiswa) return $query->where('beasiswa_id', $request->flt_beasiswa);

                return $query->where('beasiswa_id', count($master_beasiswa) ? $master_beasiswa[0]->id : null);
            })
            ->first();

        if (!$pendaftar) return view('pendaftar.no-page', [
            'message' => 'Data pendaftar tidak ditemukan',
            'title' => 'Opz..',
            'bg' => 'danger'
        ]);

        $is_pengumuman_seleksi_akhir = cek_jadwal($pendaftar->tahun_kegiatan_id, $pendaftar->beasiswa_id, 'PENGUMUMAN_AKHIR', false, true);

        $hasil_seleksi = view('pendaftar.hasil-seleksi-akhir', [
            'pendaftar' => $pendaftar,
            'is_pengumuman_seleksi_akhir' => $is_pengumuman_seleksi_akhir,
        ])->render();

        if ($request->ajax()) return $hasil_seleksi;

        return view('pendaftar.seleksi-akhir', [
            'master_beasiswa' => $master_beasiswa,
            'master_tahun' => $master_tahun,
            'hasil_seleksi' => $hasil_seleksi
        ]);
    }
}
