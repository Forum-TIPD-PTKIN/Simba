<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\MapUjian;
use App\Models\TahunKegiatan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TesPotensiAkademikController extends Controller
{
    public function index()
    {
        $tahun_kegiatan = TahunKegiatan::orderBy('tahun', 'desc')->get();
        $beasiswa = Beasiswa::orderBy('nama', 'asc')->get();

        return view('admin.tes-potensi-akademik', [
            'tahun_kegiatan' => $tahun_kegiatan,
            'beasiswa' => $beasiswa,
        ]);
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $data = MapUjian::with(['pendaftar', 'pendaftar.mahasiswa', 'pendaftar.beasiswa'])
                ->selectRaw('pendaftars.*')
                ->join('pendaftars', 'pendaftars.id', 'map_ujians.pendaftar_id')
                ->join('mahasiswas', 'mahasiswas.pendaftar_id', 'pendaftars.id')
                ->whereHas('pendaftar', function ($query) use ($request) {
                    $query->where('tahun_kegiatan_id', $request->flt_tahun)
                        ->where('beasiswa_id', $request->flt_beasiswa);
                });

            return DataTables::of($data)
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
                ->editColumn('prodi', function ($data) {
                    return "
                        <div class='flex-grow-1'>
                          <div class='row g-1'>
                            <div class='col-12'>
                              <h6 class='mb-0'>{$data->pendaftar?->mahasiswa?->prodi_name}</h6>
                              <p class='text-muted mb-0'><small>{$data->pendaftar?->mahasiswas?->fakultas_name}</small></p>
                            </div>
                          </div>
                        </div>";
                })
                ->editColumn('tanggal_ujian', function ($data) {
                    return "
                        <div class='flex-grow-1'>
                          <div class='row g-1'>
                            <div class='col-12'>
                              <h6 class='mb-0'>" . Carbon::parse($data->tanggal_mulai)->translatedFormat('d-m-Y H:i') . "</h6>
                              <p class='text-muted mb-0'><small>" . Carbon::parse($data->tanggal_selesai)->translatedFormat('d-m-Y H:i') . "</small></p>
                            </div>
                          </div>
                        </div>";
                })
                ->rawColumns(['beasiswa'])
                ->make(true);
        }
    }
}
