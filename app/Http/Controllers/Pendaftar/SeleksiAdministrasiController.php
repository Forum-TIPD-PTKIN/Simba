<?php

namespace App\Http\Controllers\Pendaftar;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\JadwalKegiatan;
use App\Models\Pendaftar;
use App\Models\TahunKegiatan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SeleksiAdministrasiController extends Controller
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

        $is_pengumuman_seleksi_administrasi = cek_jadwal($pendaftar->tahun_kegiatan_id, $pendaftar->beasiswa_id, 'PENGUMUMAN_SELEKSI_ADMINISTRASI', false, true);
        $jadwal_tpa = JadwalKegiatan::whereTahunKegiatanId($pendaftar->tahun_kegiatan_id)
            ->whereBeasiswaId($pendaftar->beasiswa_id)
            ->whereRole('TES_POTENSI_AKADEMIK')
            ->first();
        // ! Jadwal cetak kartu sehari sebelum TPA dan tanggal setelahnya
        $is_jadwal_cetak_kartu = $jadwal_tpa ? Carbon::now()->gte(
            Carbon::parse($jadwal_tpa->tanggal_mulai)->copy()->subDay()
        ) : false;
        $jadwal_pengumuman_seleksi_admnistrasi = JadwalKegiatan::whereTahunKegiatanId($pendaftar->tahun_kegiatan_id)
            ->whereBeasiswaId($pendaftar->beasiswa_id)
            ->whereRole('PENGUMUMAN_SELEKSI_ADMINISTRASI')
            ->first();

        $hasil_seleksi = view('pendaftar.hasil-seleksi-administrasi', [
            'pendaftar' => $pendaftar,
            'is_pengumuman_seleksi_administrasi' => $is_pengumuman_seleksi_administrasi,
            'is_jadwal_cetak_kartu' => $is_jadwal_cetak_kartu,
            'jadwal_pengumuman_seleksi_administrasi' => $jadwal_pengumuman_seleksi_admnistrasi
        ])->render();

        if ($request->ajax()) return $hasil_seleksi;

        return view('pendaftar.seleksi-administrasi', [
            'master_beasiswa' => $master_beasiswa,
            'master_tahun' => $master_tahun,
            'hasil_seleksi' => $hasil_seleksi
        ]);
    }
}
