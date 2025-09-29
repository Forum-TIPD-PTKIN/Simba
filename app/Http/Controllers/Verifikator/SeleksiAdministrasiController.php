<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\Pendaftar;
use App\Models\TahunKegiatan;
use Illuminate\Http\Request;

class SeleksiAdministrasiController extends Controller
{
    public function index(Request $request)
    {
        $tahun_kegiatan = TahunKegiatan::orderBy('tahun', 'desc')
            ->get();
        $beasiswa = Beasiswa::where('status', 1)
            ->orderBy('nama')
            ->get();

        $dt_pendaftar = Pendaftar::when(count($tahun_kegiatan), function ($q) use ($tahun_kegiatan) {
            return $q->where('tahun_kegiatan_id', $tahun_kegiatan[0]->id);
        })
            ->when(count($beasiswa), function ($q) use ($beasiswa) {
                return $q->where('beasiswa_id', $beasiswa[0]->id);
            })
            ->whereHas('pemberkasan', function ($q) {
                return $q->where('lengkap', 1);
            })
            ->get();

        return view('verifikator.seleksi-administrasi', [
            'tahun_kegiatan' => $tahun_kegiatan,
            'beasiswa' => $beasiswa,
            'dt_pendaftar' => $dt_pendaftar
        ]);
    }
}
