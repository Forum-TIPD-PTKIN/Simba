<?php

namespace App\Http\Controllers\Surveyor;

use App\Models\Beasiswa;
use App\Models\Surveyor;
use App\Models\Pendaftar;
use Illuminate\Http\Request;
use App\Models\TahunKegiatan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CetakController extends Controller
{
    public function peserta_survei(Request $request)
    {
        $request->merge([
            'tahun' => '0b299747-d905-45bd-a9cd-be6b31cc37d7',
            'beasiswa' => '8fec4e7a-d72e-4e5f-8f7f-94f43bbda3ce'
        ]);

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
        return view('surveyor.cetak.peserta-survei', [
            'peserta' => $peserta,
            'surveyor' => $surveyor,
            'beasiswa' => $beasiswa,
            'tahun' => $tahun,
            'style' => $style,
        ]);
        $tanggal_ujian = collect($data)->map(fn($value) => \Carbon\Carbon::parse($value['tanggal_mulai'])->format('Y-m-d'))
            ->unique()->first();
        $sesi = collect($data)->pluck('sesi')->unique()->first();
        $ruang = collect($data)->pluck('ruang')->unique()->first();

        $filename = 'DAFTAR_HADIR_PESERTA_TES_POTENSI_AKADEMIK_BEASISWA_' . strtoupper(str_replace(' ', '_', $beasiswa)) . '_' . $tahun . '_' . $tanggal_ujian . '_' . $sesi . '_' . $ruang . '.pdf';

        set_time_limit(300);
        $html = view('admin.cetak.daftar-hadir', [
            'data' => $data,
            'beasiswa' => $beasiswa,
            'tahun' => $tahun,
            'tanggal_ujian' => $tanggal_ujian,
            'sesi' => $sesi,
            'ruang' => $ruang,
            'style' => $style,
        ])->render();

        $pdf = SnappyPdf::loadHTML($html)
            ->setOption('page-width', '215mm')
            ->setOption('page-height', '330mm')
            ->setOption('margin-bottom', '20mm')
            ->setOption('no-background', false)
            ->setOption('print-media-type', true)
            ->setOption('encoding', 'UTF-8')
            ->setOption('disable-smart-shrinking', true);
        return $pdf->download($filename);
    }
}
