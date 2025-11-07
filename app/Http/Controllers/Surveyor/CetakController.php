<?php

namespace App\Http\Controllers\Surveyor;

use App\Models\Beasiswa;
use App\Models\Surveyor;
use App\Models\Pendaftar;
use Illuminate\Http\Request;
use App\Models\TahunKegiatan;
use App\Http\Controllers\Controller;
use Barryvdh\Snappy\Facades\SnappyPdf;
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

    public function instrumen_survei(Request $request)
    {
        $grouper = [
            (object)[
                'rowspan' => 16,
                'range' => [0, 15],
                'text' => 'BIODATA'
            ],
            (object)[
                'rowspan' => 7,
                'range' => [16, 23],
                'text' => 'EKONOMI'
            ],
            (object)[
                'rowspan' => 6,
                'range' => [23, 28],
                'text' => 'TEMPAT TINGGAL'
            ]
        ];
        $butiran = [
            ['NIM', '24383021023', 1],
            ['Nama', 'DESTRA DWI CAHYO', 2],
            ['Prodi', 'Perbankan Syariah', 3],
            ['Alamat (Dusun sesuai KTP)', 'MURTAJIH', 4],
            ['RT/RW (sesuai KTP)', '002/001', 5],
            ['Desa/Kelurahan (sesuai KTP)', 'DS. MURTAJIH', 6],
            ['Kecamatan (sesuai KTP)', 'PADEMAWU', 7],
            ['Kabupaten/Kota (sesuai KTP)', 'PAMEKASAN', 8],
            ['Provinsi (sesuai KTP)', 'JAWA TIMUR', 9],
            ['Nomor KTP', '3528012012050003', 10],
            ['Nomor Ijazah SMA', 'DN-05/M-SMA/K13/24/0046747', 11],
            ['Nilai Transkrip SMA', '89,65', 12],
            ['Nomor Ponsel', '81915509797', 13],
            ['Nomor Kartu Keluarga', '3528022404066396', 14],
            ['Nama Ayah', 'MOH. SAYURI', 15],
            ['Nama Ibu', 'FATIMAH', 16],
            ['Kondisi Ayah', 'Sehat', 17],
            ['Pekerjaan Ayah', 'Petani/Nelayan', 18],
            ['Penghasilan Ayah', '< 1 juta', 19],
            ['Kondisi Ibu', 'Sehat', 20],
            ['Pekerjaan Ibu', 'Tidak Bekerja', 21],
            ['Penghasilan Ibu', '< 1 juta', 22],
            ['Tanggungan Keluarga', '3 orang', 23],
            ['Status Kepemilikan Rumah', 'Milik sendiri', 24],
            ['Bangunan Rumah', 'Permanen', 25],
            ['Lantai Rumah', 'Keramik', 26],
            ['Kepemilikan Listrik', 'Milik sendiri', 27],
            ['Kondisi Dapur', 'Rusak berat', 28],
            ['Kondisi Kamar Mandi & WC', 'Rusak', 29],
            // ['Berkas Persyaratan', 'https://drive.google.com/open?id=1wMJZ1s', 30],
        ];
        return view('surveyor.cetak.instrumen-survei', compact('butiran', 'grouper'));
    }
}
