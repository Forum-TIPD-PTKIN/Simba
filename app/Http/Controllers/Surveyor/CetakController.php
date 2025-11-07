<?php

namespace App\Http\Controllers\Surveyor;

use App\Models\Beasiswa;
use App\Models\Surveyor;
use App\Models\Pendaftar;
use Illuminate\Http\Request;
use App\Models\TahunKegiatan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Barryvdh\Snappy\Facades\SnappyPdf;

class CetakController extends Controller
{
    public function peserta_survei(Request $request)
    {
        if (env('APP_ENV') === 'production') {
            $style = base_path('../assets/admin/css/style.css');
        } else {
            $style = public_path('assets/admin/css/style.css');
        }

        $peserta = Pendaftar::with(['mahasiswa', 'biodata_pendaftar', 'beasiswa', 'tahun_kegiatan'])
            ->select('pendaftars.*')
            ->join('mahasiswas', 'pendaftars.id', 'mahasiswas.pendaftar_id')
            ->whereHas('surveyor_detail', function ($query) use ($request) {
                $query->whereHas('surveyor', function ($q) use ($request) {
                    $q->where('user_id', Auth::id())
                        ->where('tahun_kegiatan_id', $request->tahun)
                        ->where('beasiswa_id', $request->beasiswa)
                        ->where('publish', '1')
                        ->where('bersedia', '1');
                });
            })
            ->where('tahun_kegiatan_id', $request->tahun)
            ->where('beasiswa_id', $request->beasiswa)
            ->orderBy('mahasiswas.nama')
            ->get();

        $surveyor = Surveyor::with(['user'])
            ->where('user_id', Auth::id())
            ->where('tahun_kegiatan_id', $request->tahun)
            ->where('beasiswa_id', $request->beasiswa)
            ->first();

        if (!count($peserta)) return response()->json('Data peserta survei tidak ditemukan', 404);

        $beasiswa = Beasiswa::where('id', $request->beasiswa)->pluck('nama')->first();
        $tahun = TahunKegiatan::where('id', $request->tahun)->pluck('tahun')->first();

        $filename = 'DAFTAR_PESERTA_SURVEI_BEASISWA_' . strtoupper(str_replace(' ', '_', $beasiswa)) . '_' . $tahun . '_' . strtoupper(str_replace(' ', '_', $surveyor->user?->name)) . '.pdf';

        set_time_limit(300);
        $html = view('surveyor.cetak.peserta-survei', [
            'peserta' => $peserta,
            'surveyor' => $surveyor,
            'beasiswa' => $beasiswa,
            'tahun' => $tahun,
            'style' => $style,
        ])->render();

        $pdf = SnappyPdf::loadHTML($html)
            ->setOption('page-width', '215mm')
            ->setOption('page-height', '330mm')
            ->setOption('orientation', 'Landscape')
            ->setOption('no-background', false)
            ->setOption('print-media-type', true)
            ->setOption('encoding', 'UTF-8')
            ->setOption('disable-smart-shrinking', true);
        return $pdf->download($filename);
    }

    public function instrumen_survei(Request $request)
    {
        if (env('APP_ENV') === 'production') {
            $style = base_path('../assets/admin/css/style.css');
        } else {
            $style = public_path('assets/admin/css/style.css');
        }

        $peserta = Pendaftar::with(['mahasiswa', 'biodata_pendaftar', 'beasiswa', 'tahun_kegiatan'])
            ->select('pendaftars.*')
            ->join('mahasiswas', 'pendaftars.id', 'mahasiswas.pendaftar_id')
            ->whereHas('surveyor_detail', function ($query) use ($request) {
                $query->whereHas('surveyor', function ($q) use ($request) {
                    $q->where('user_id', Auth::id())
                        ->where('tahun_kegiatan_id', $request->tahun)
                        ->where('beasiswa_id', $request->beasiswa)
                        ->where('publish', '1')
                        ->where('bersedia', '1');
                });
            })
            ->where('tahun_kegiatan_id', $request->tahun)
            ->where('beasiswa_id', $request->beasiswa)
            ->orderBy('mahasiswas.nama')
            ->get();

        $surveyor = Surveyor::with(['user'])
            ->where('user_id', Auth::id())
            ->where('tahun_kegiatan_id', $request->tahun)
            ->where('beasiswa_id', $request->beasiswa)
            ->first();

        if (!count($peserta)) return response()->json('Data peserta survei tidak ditemukan', 404);

        $beasiswa = Beasiswa::where('id', $request->beasiswa)->pluck('nama')->first();
        $tahun = TahunKegiatan::where('id', $request->tahun)->pluck('tahun')->first();

        $filename = 'INSTRUMEN_SURVEI_BEASISWA_' . strtoupper(str_replace(' ', '_', $beasiswa)) . '_' . $tahun . '_' . strtoupper(str_replace(' ', '_', $surveyor->user?->name)) . '.pdf';

        set_time_limit(300);
        $html = view('surveyor.cetak.instrumen-survei', [
            'peserta' => $peserta,
            'surveyor' => $surveyor,
            'beasiswa' => $beasiswa,
            'tahun' => $tahun,
            'style' => $style,
        ])->render();

        $pdf = SnappyPdf::loadHTML($html)
            ->setOption('page-width', '215mm')
            ->setOption('page-height', '330mm')
            ->setOption('no-background', false)
            ->setOption('print-media-type', true)
            ->setOption('encoding', 'UTF-8')
            ->setOption('disable-smart-shrinking', true);
        return $pdf->download($filename);
    }
}