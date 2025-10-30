<?php

namespace App\Http\Controllers\Pendaftar;

use App\Http\Controllers\Controller;
use App\Models\JadwalCbt;
use App\Models\JadwalKegiatan;
use App\Models\JenisTesCbt;
use App\Models\MapUjian;
use App\Models\Pendaftar;
use App\Models\PesertaCbt;
use App\Models\SiakadMahasiswa;
use Illuminate\Http\Request;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TesPotensiAkademikController extends Controller
{
    public function generate_kartu(Request $request)
    {
        $style = public_path('assets/admin/css/style.css');
        $style_kartu = public_path('assets/admin/css/style-kartu.css');

        // Ambil data pendaftar
        $pendaftar = Pendaftar::with(['mahasiswa', 'beasiswa', 'user'])
            ->select('pendaftars.*')
            ->join('mahasiswas', 'pendaftars.id', 'mahasiswas.pendaftar_id')
            ->where('tahun_kegiatan_id', $request->flt_tahun)
            ->where('beasiswa_id', $request->flt_beasiswa)
            ->whereHas(
                'latestStatus',
                fn($q) => $q->where('status', 'LOLOS ADMINISTRASI')
            )
            ->where('user_id', Auth::id())
            ->first();

        $tgl_lahir = SiakadMahasiswa::whereNpm($pendaftar->mahasiswa?->nim)
            ->pluck('tanggal_lahir')
            ->first();

        $jadwal_tpa = JadwalKegiatan::whereTahunKegiatanId($pendaftar->tahun_kegiatan_id)
            ->whereBeasiswaId($pendaftar->beasiswa_id)
            ->whereRole('TES_POTENSI_AKADEMIK')
            ->first();
        // Jadwal cetak kartu sehari sebelum TPA
        $is_jadwal_cetak_kartu = Carbon::now()->isSameDay(Carbon::parse($jadwal_tpa->tanggal_mulai)->copy()->subDay());
        if (!$is_jadwal_cetak_kartu) return response()->json('Jadwal cetak kartu sehari sebelum TES POTENSI AKADEMIK', 419);

        if ($request->ajax()) {
            // Ambil data jenis tes yang tersedia, lengkap dengan semua jadwal yang tersedia (DB CBT)
            $jenis_tes = JenisTesCbt::with([
                'jadwal' => function ($query) {
                    $query->orderBy('sesi')
                        ->orderBy('ruang');
                }
            ])
                ->select('jenis_tes.*')
                ->join('jadwal', 'jadwal.id_jenis_tes', 'jenis_tes.id_jenis_tes')
                ->where('aktif', 'Y')
                ->whereExists(function ($query) {
                    $query->select('*')
                        ->from('jadwal')
                        ->whereColumn('jadwal.id_jenis_tes', 'jenis_tes.id_jenis_tes');
                })
                ->where('jenjang', 'S1')
                ->orderBy('jadwal.tgl_ujian', 'desc')
                ->first();

            // Kuota penuh
            $count_limit_kuota_peserta = 0;

            // Cek data peserta (DB CBT)
            $peserta =  PesertaCbt::where('no_test', $pendaftar->mahasiswa?->nim)
                ->first();

            // Jika peserta masih belum ada
            if (!$peserta) {
                // Cek jadwal yang tersedia (sesi dan ruang)
                foreach ($jenis_tes->jadwal as $key => $item) {
                    // Jika pada jadwal kuota masih ada
                    if ($item->isi < $item->kuota) {
                        // Menambah peserta CBT baru
                        $new_peserta = new PesertaCbt();

                        $new_peserta->no_test = $pendaftar->mahasiswa?->nim;
                        $new_peserta->password = md5(str_replace('-', '', $tgl_lahir ?? '0000-00-00'));
                        $new_peserta->nama = $pendaftar->mahasiswa?->nama;
                        $new_peserta->prodi = $pendaftar->mahasiswa?->prodi_name;
                        $new_peserta->id_jenis_tes = $jenis_tes->id_jenis_tes;
                        $new_peserta->id_jadwal = $item->id_jadwal;
                        $new_peserta->lama_simulasi = 0;

                        if ($new_peserta->save()) {
                            // Update isi peserta pada tabel jadwal
                            $jadwal = JadwalCbt::where('id_jadwal', $item->id_jadwal)
                                ->first();

                            $jadwal->isi = $jadwal->isi + 1;
                            $jadwal->update();

                            // Menambah data peserta pada tabel Map Ujian
                            $map_ujian = new MapUjian();
                            $map_ujian->pendaftar_id = $pendaftar->id;
                            $map_ujian->cbt_jenis_tes = $jenis_tes->id_jenis_tes;
                            $map_ujian->tanggal_mulai = date('Y-m-d H:i:s', strtotime($jadwal->tgl_ujian . ' ' . $jadwal->jam_mulai));
                            $map_ujian->tanggal_selesai = date('Y-m-d H:i:s', strtotime($jadwal->tgl_ujian . ' ' . $jadwal->jam_selesai));
                            $map_ujian->sesi = $jadwal->sesi;
                            $map_ujian->ruang = $jadwal->ruang;
                            $map_ujian->save();

                            // Akhiri perulangan jika data sudah dimasukkan ke database
                            break;
                        }
                    } else {
                        $count_limit_kuota_peserta += 1;
                    }
                }
            }

            if ($count_limit_kuota_peserta === count($jenis_tes?->jadwal)) return response()->json([
                'status' => false,
                'message' => 'Kuota peserta per-ruang sudah penuh.'
            ], 404);

            $peserta_cbt =  PesertaCbt::with(['jenis_cbt', 'jadwal_cbt'])
                ->where('no_test', $pendaftar->mahasiswa?->nim)
                ->first();

            if (!$peserta_cbt) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data ujian tidak ditemukan, silakan hubungi panitia untuk mendapatkan informasi lebih lanjut.'
                ], 404);
            }

            $map_ujian =  MapUjian::where('pendaftar_id', $pendaftar->id)
                ->first();

            if (!$map_ujian) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data ujian tidak ditemukan, silakan hubungi panitia untuk mendapatkan informasi lebih lanjut.'
                ], 404);
            }

            $filename = 'KARTU_TES_POTENSI_AKADEMIK_BEASISWA_' . strtoupper(str_replace(' ', '_', $pendaftar->beasiswa?->nama)) . '_' . $pendaftar->mahasiswa?->kode . '_' . strtoupper(str_replace(' ', '_', $pendaftar->mahasiswa?->nama)) . '.pdf';

            set_time_limit(300);
            $html = view('pendaftar.kartu-ujian', [
                'pendaftar' => $pendaftar,
                'map_ujian' => $map_ujian,
                'style' => $style,
                'style_kartu' => $style_kartu,
            ])->render();

            $pdf = SnappyPdf::loadHTML($html)
                ->setOption('page-width', '215mm')
                ->setOption('page-height', '330mm')
                ->setOption('no-background', false)
                ->setOption('print-media-type', true);
            return $pdf->download($filename);
        }
    }
}