<?php

namespace App\Http\Controllers;

use App\Models\Beasiswa;
use App\Models\BiodataPendaftar;
use App\Models\Pendaftar;
use App\Models\TahunKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use ZipArchive;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ToolsController extends Controller
{
    public function generateCode(Request $request)
    {
        $year = TahunKegiatan::orderBy('tahun', 'desc')->get();
        $scholarship = Beasiswa::where('status', 1)->orderBy('nama', 'asc')->get();

        return view('tools.generate-code', [
            'year' => $year,
            'scholarship' => $scholarship
        ]);
    }

    public function getCandidates(Request $request)
    {
        $year = $request->year;
        $scholarship = $request->scholarship;

        $candidates = Pendaftar::with(['user', 'mahasiswa', 'biodata_pendaftar', 'pemberkasan'])
            ->selectRaw('pendaftars.*')
            ->join('mahasiswas', 'mahasiswas.pendaftar_id', 'pendaftars.id')
            ->where('tahun_kegiatan_id', $year)
            ->where('beasiswa_id', $scholarship)
            ->whereHas(
                'latestStatus',
                fn($q) => $q->where('status', 'LOLOS ADMINISTRASI')
            )
            ->orderBy('mahasiswas.nama')
            ->get();

        return response()->json($candidates);
    }

    public function getCandidateCode(Request $request)
    {
        $year = $request->year;
        $scholarship = $request->scholarship;
        $candidate = $request->candidate;

        $candidates = Pendaftar::with(['user', 'mahasiswa', 'biodata_pendaftar', 'pemberkasan'])
            ->where('tahun_kegiatan_id', $year)
            ->where('beasiswa_id', $scholarship)
            ->where('id', $candidate)
            ->whereHas(
                'latestStatus',
                fn($q) => $q->where('status', 'LOLOS ADMINISTRASI')
            )
            ->first();

        [$id_prodi, $nm_prodi] = explode('|', $candidates->mahasiswa?->prodi);
        $prodi_id = "0{$id_prodi}";
        $get_prodi = api()->get("https://api.uinmadura.ac.id/api/prodi?id={$prodi_id}");
        $prodi_long = $get_prodi->data?->data[0]->long ?? '';

        $candidates->mahasiswa->setAttribute('prodi_long_name', $prodi_long);

        return response()->json($candidates);
    }

    public function changeBiodata(Request $request)
    {
        BiodataPendaftar::with(['pendaftar.user'])
            ->whereHas('pendaftar.beasiswa', fn($q) => $q->where('nama', 'Program Pendidikan Kebanksentralan (PPK) BI'))
            ->chunk(50, function ($pendaftarBatch) {
                foreach ($pendaftarBatch as $value) {
                    $biodata = $value->data;
                    $mahasiswa = \App\Models\SiakadMahasiswa::with('prodi.fakultas')
                        ->whereNpm($value->pendaftar->user->username)
                        ->first();

                    $get_ipk = api()->get("https://tipd.dev/api/public/api/akademik/ipk-sementara/{$mahasiswa->npm}");
                    $ipk = $get_ipk->data?->profil?->ipk_akumulatif ?? 0;

                    $biodata->biodata->ipk->value = $ipk;
                    $biodata->biodata->ttl->value = $mahasiswa->tempat_lahir . ', ' . \Carbon\Carbon::parse($mahasiswa->tanggal_lahir)->translatedFormat('d/m/Y');
                    $biodata->biodata->nama_ibu->value = $mahasiswa->ibu_nama;
                    $biodata->biodata->nama_bapak->value = $mahasiswa->ayah_nama;

                    $value->update(['data' => json_encode($biodata)]);
                }
            });

        echo 'Selesai';
    }

    public function downloadZip(Request $request, string $pendaftarId)
    {
        $downloadExcept = [
            'sc_follow_ig_bi',
            'sc_follow_yt',
            'sc_follow_tiktok',
            'sc_follow_ig_genbi',
            'checklist_berkas',
            'super_tdk_sanksi_kode_etik',
            'suket_baca_quran'
        ];

        $pendaftar = Pendaftar::with(['biodata_pendaftar', 'pemberkasan', 'mahasiswa'])
            ->whereHas(
                'latestStatus',
                fn($q) => $q->where('status', 'LOLOS ADMINISTRASI')
            )
            ->whereId($pendaftarId)
            ->first();

        if (!$pendaftar) {
            return 'Data pendaftar tidak ditemukan.';
        }

        $filePaths = [];
        $newNameFiles = [];
        foreach ($pendaftar->pemberkasan->data->pemberkasan as $key => $value) {
            if (!in_array($key, $downloadExcept) && isset($value->value->path)) {
                $filePaths[] = $value->value->path;
                $newNameFiles[] = $key;
            }
        }

        $zip = new ZipArchive;

        // PERBAIKAN 1: Bersihkan nama dari karakter ilegal
        $safeNama = Str::slug($pendaftar->mahasiswa->nama, '_');
        $fileName = $pendaftar->mahasiswa->nim . '_' . $safeNama . '.zip';

        // PERBAIKAN 2: Simpan di folder storage agar tidak kena error hak akses di Ubuntu
        $zipPath = storage_path('app/' . $fileName);

        $filesAdded = 0; // Buat tracker untuk mengecek apakah ada file yang dimasukkan

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($filePaths as $key => $path) {
                $fullPath = storage_path('app/' . $path);

                // PERBAIKAN 3: Cara aman mengambil ekstensi
                $exten = pathinfo($path, PATHINFO_EXTENSION);

                // PERBAIKAN 4: Bersihkan karakter ilegal (seperti /) pada nama file di dalam zip
                $safeInnerName = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $newNameFiles[$key]);

                $newName = ($key + 1) . '. ' . $safeInnerName . '.' . $exten;

                if (file_exists($fullPath)) {
                    $zip->addFile($fullPath, $newName);
                    $filesAdded++; // Tambah counter
                }
            }
            $zip->close();
        }

        // PERBAIKAN 5: Validasi jika tidak ada file yang berhasil di-zip
        if ($filesAdded === 0) {
            return 'Gagal membuat file ZIP karena file berkas (PDF/JPG) tidak ditemukan secara fisik di server.';
        }

        if (!file_exists($zipPath)) {
            return 'Gagal membuat file ZIP atau file fisik tidak ditemukan di storage.';
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function getCandidateFiles(Request $request)
    {
        $downloads = [
            'sc_follow_ig_bi',
            'sc_follow_yt',
            'sc_follow_tiktok',
            'sc_follow_ig_genbi',
            'checklist_berkas'
        ];
        // $pendaftarId = 'fadd676c-d134-464b-85c8-9f6e9fb26261';
        $pendaftarId = $request->candidate;

        $pendaftar = Pendaftar::with(['biodata_pendaftar', 'pemberkasan', 'mahasiswa'])
            ->whereHas(
                'latestStatus',
                fn($q) => $q->where('status', 'LOLOS ADMINISTRASI')
            )
            ->whereId($pendaftarId)
            ->first();

        if (!$pendaftar) {
            return response()->json('Data pendaftar tidak ditemukan.', 422);
        }

        $filePaths = [];
        foreach ($pendaftar->pemberkasan->data->pemberkasan as $key => $value) {
            if (in_array($key, $downloads) && isset($value->value->path)) {
                $filePaths[] = [
                    'url' => route('download.file', ['path' => Crypt::encrypt($value->value->path)]),
                    'name' => $key
                ];
            }
        }

        $filePaths[] = [
            'url' => route('get.candidates.zip', ['scholarship' => $pendaftarId]),
            'name' => $pendaftar->mahasiswa->nim . '_' . $pendaftar->mahasiswa->nama . '.zip'
        ];

        return response()->json($filePaths);
    }
}
