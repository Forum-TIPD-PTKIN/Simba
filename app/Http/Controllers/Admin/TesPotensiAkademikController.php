<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Admin\PesertaCBTExport;
use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\JadwalCbt;
use App\Models\JenisTesCbt;
use App\Models\MapUjian;
use App\Models\Pendaftar;
use App\Models\PesertaCbt;
use App\Models\SiakadMahasiswa;
use App\Models\TahunKegiatan;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class TesPotensiAkademikController extends Controller
{
    public function index(Request $request)
    {
        $tahun_kegiatan = TahunKegiatan::orderBy('tahun', 'desc')->get();
        $beasiswa = Beasiswa::orderBy('nama', 'asc')->get();

        $tahun_selected = isset($request->flt_tahun) && $request->flt_tahun ? $request->flt_tahun : $tahun_kegiatan->first()->id;
        $beasiswa_selected = isset($request->flt_beasiswa) && $request->flt_beasiswa ? $request->flt_beasiswa : $beasiswa->first()->id;

        $tanggal_ujian = MapUjian::selectRaw('DATE(tanggal_mulai) AS tanggal')
            ->whereHas('pendaftar', function ($query) use ($tahun_selected, $beasiswa_selected) {
                $query->where('tahun_kegiatan_id', $tahun_selected)
                    ->where('beasiswa_id', $beasiswa_selected);
            })
            ->groupBy('tanggal')
            ->pluck('tanggal');
        $sesi = MapUjian::selectRaw('sesi')
            ->whereHas('pendaftar', function ($query) use ($tahun_selected, $beasiswa_selected) {
                $query->where('tahun_kegiatan_id', $tahun_selected)
                    ->where('beasiswa_id', $beasiswa_selected);
            })
            ->groupBy('sesi')
            ->pluck('sesi');
        $ruang = MapUjian::selectRaw('ruang')
            ->whereHas('pendaftar', function ($query) use ($tahun_selected, $beasiswa_selected) {
                $query->where('tahun_kegiatan_id', $tahun_selected)
                    ->where('beasiswa_id', $beasiswa_selected);
            })
            ->groupBy('ruang')
            ->pluck('ruang');

        if ($request->ajax()) {
            return response()->json([
                'tanggal_ujian' => $tanggal_ujian,
                'sesi' => $sesi,
                'ruang' => $ruang
            ]);
        }

        return view('admin.tes-potensi-akademik', [
            'tahun_kegiatan' => $tahun_kegiatan,
            'beasiswa' => $beasiswa,
            'tanggal_ujian' => $tanggal_ujian,
            'sesi' => $sesi,
            'ruang' => $ruang
        ]);
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->getDataPesertaTes(
                $request->flt_tahun,
                $request->flt_beasiswa,
                $request->flt_tanggal_ujian,
                $request->flt_sesi,
                $request->flt_ruang,
                true, // jika true, tidak get() langsung
            );

            return DataTables::of($data)
                ->editColumn('beasiswa', function ($data) {
                    return "
                        <div class='flex-grow-1'>
                          <div class='row g-1'>
                            <div class='col-12'>
                              <h6 class='mb-0'>{$data->pendaftar?->beasiswa?->nama}</h6>
                              <p class='text-muted mb-0'><small>{$data->pendaftar?->tahun_kegiatan?->tahun}</small></p>
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
                              <h6 class='mb-0'>" . Carbon::parse($data->tanggal_mulai)->translatedFormat('d-m-Y') . "</h6>
                              <p class='text-muted mb-0'><small>" .
                        Carbon::parse($data->tanggal_mulai)->translatedFormat('H:i') . ' - ' .
                        Carbon::parse($data->tanggal_selesai)->translatedFormat('H:i') .
                        "</small></p>
                            </div>
                          </div>
                        </div>";
                })
                ->rawColumns(['prodi', 'beasiswa', 'tanggal_ujian'])
                ->make(true);
        }
    }

    public function show(string $tahun, string $beasiswa)
    {
        $pendaftar = $this->getDataPendaftar($tahun, $beasiswa);

        return view('admin.modal-data-pendaftar', [
            'tahun' => $tahun,
            'beasiswa' => $beasiswa,
            'pendaftar' => $pendaftar,
            'count_pendaftar' => count($pendaftar)
        ])
            ->render();
    }

    public function store(Request $request)
    {
        set_time_limit(60 * 60);

        $request->validate([
            'tahun' => 'required',
            'beasiswa' => 'required',
            'start' => 'required|numeric',
            'per_process' => 'required|numeric',
        ], [
            'tahun.required' => 'Tahun kegiatan harus diisi',
            'beasiswa.required' => 'Beasiswa harus diisi',
            'start.required' => 'Start harus diisi',
            'start.numeric' => 'Start harus berupa angka',
            'per_process.required' => 'Per process harus diisi',
            'per_process.numeric' => 'Per process harus berupa angka',
        ]);

        $data = $this->getDataPendaftar(
            $request->tahun,
            $request->beasiswa,
            is_numeric($request->start) ? (int) $request->start : null,
            is_numeric($request->per_process) ? (int) $request->per_process : null
        );

        foreach ($data as $key => $value) {
            // Cek data pendaftar di map_ujian dan peserta_cbt
            $dt_map_ujian = MapUjian::find($value->id);
            $dt_peserta_cbt =  PesertaCbt::where('no_test', $value->mahasiswa?->nim)
                ->first();
            if ($dt_map_ujian && $dt_peserta_cbt) continue;

            $tgl_lahir = SiakadMahasiswa::whereNpm($value->mahasiswa?->nim)
                ->pluck('tanggal_lahir')
                ->first();

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
            $peserta =  PesertaCbt::where('no_test', $value->mahasiswa?->nim)
                ->first();

            // Jika peserta masih belum ada
            if (!$peserta) {
                // Cek jadwal yang tersedia (sesi dan ruang)
                foreach ($jenis_tes->jadwal as $key => $item) {
                    // Jika pada jadwal kuota masih ada
                    if ($item->isi < $item->kuota) {
                        // Menambah peserta CBT baru
                        $new_peserta = new PesertaCbt();

                        $new_peserta->no_test = $value->mahasiswa?->nim;
                        $new_peserta->password = md5(str_replace('-', '', $tgl_lahir ?? '0000-00-00'));
                        $new_peserta->nama = $value->mahasiswa?->nama;
                        $new_peserta->prodi = $value->mahasiswa?->prodi_name;
                        $new_peserta->id_jenis_tes = $jenis_tes->id_jenis_tes;
                        $new_peserta->id_jadwal = $item->id_jadwal;
                        $new_peserta->lama_simulasi = 0;

                        if ($new_peserta->save()) {
                            // Update isi peserta pada tabel jadwal
                            $jadwal = JadwalCbt::where('id_jadwal', $item->id_jadwal)
                                ->first();

                            $jadwal->isi = $jadwal->isi + 1;
                            $jadwal->update();

                            // Menambah atau ubah data peserta pada tabel Map Ujian (DB PMB)
                            $map_ujian = new MapUjian();
                            $map_ujian->pendaftar_id = $value->id;
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
            ], 419);
        }
    }

    public function daftar_hadir(Request $request)
    {
        $request->validate([
            'tahun' => 'required',
            'beasiswa' => 'required',
            'tanggal_ujian' => 'required',
            'sesi' => 'required',
            'ruang' => 'required'
        ], [
            'tahun.required' => 'Tahun kegiatan tidak ditentukan',
            'beasiswa.required' => 'Beasiswa tidak ditentukan',
            'tanggal_ujian.required' => 'Tanggal ujian tidak ditentukan',
            'sesi.required' => 'Sesi tidak ditentukan',
            'ruang.required' => 'Ruang tidak ditentukan'
        ]);

        $style = public_path('assets/admin/css/style.css');

        $data = $this->getDataPesertaTes(
            $request->tahun,
            $request->beasiswa,
            $request->tanggal_ujian,
            $request->sesi,
            $request->ruang
        );

        if (!count($data)) return response()->json('Data peserta tes tidak ditemukan', 404);

        $beasiswa = Beasiswa::where('id', $request->beasiswa)->pluck('nama')->first();
        $tahun = TahunKegiatan::where('id', $request->tahun)->pluck('tahun')->first();
        $tanggal_ujian = collect($data)->map(fn($value) => \Carbon\Carbon::parse($value['tanggal_mulai'])->format('Y-m-d'))
            ->unique()->first();
        $sesi = collect($data)->pluck('sesi')->unique()->first();
        $ruang = collect($data)->pluck('ruang')->unique()->first();

        $filename = 'DAFTAR_HADIR_PESERTA_TES_POTENSI_AKADEMIK_BEASISWA_' . strtoupper(str_replace(' ', '_', $beasiswa)) . '_' . $tahun . '.pdf';

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
            ->setOption('no-background', false)
            ->setOption('print-media-type', true);
        return $pdf->download($filename);
    }

    public function unduh(Request $request)
    {
        set_time_limit(60 * 60);

        $tahun = $request->tahun;
        $beasiswa = $request->beasiswa;

        $dt_tahun = TahunKegiatan::where('id', $tahun)->pluck('tahun')->first();
        $dt_beasiswa = Beasiswa::where('id', $beasiswa)->pluck('nama')->first();

        return Excel::download(new PesertaCBTExport($tahun, $beasiswa), "Data Peserta Test Potensi Akademik Beasiswa {$dt_beasiswa} Tahun {$dt_tahun}.xlsx");
    }

    public static function getDataPendaftar(string $tahun, string $beasiswa, ?int $skip = null, ?int $take = null)
    {
        $query = Pendaftar::with(['mahasiswa', 'beasiswa'])
            ->select('pendaftars.*')
            ->join('mahasiswas', 'pendaftars.id', 'mahasiswas.pendaftar_id')
            ->where('tahun_kegiatan_id', $tahun)
            ->where('beasiswa_id', $beasiswa)
            ->whereHas(
                'latestStatus',
                fn($q) => $q->where('status', 'LOLOS ADMINISTRASI')
            )
            ->whereDoesntHave('map_ujian')
            ->orderBy('mahasiswas.nama');

        if (!is_null($skip) && !is_null($take)) $query->skip($skip)->take($take);

        return $query->get();
    }

    public static function getDataPesertaTes(string $tahun, string $beasiswa, ?string $tanggal_ujian, ?string $sesi, ?string $ruang, bool $is_query = false)
    {
        $query = MapUjian::with(['pendaftar.mahasiswa', 'pendaftar.beasiswa'])
            ->selectRaw('map_ujians.*')
            ->join('pendaftars', 'pendaftars.id', 'map_ujians.pendaftar_id')
            ->join('mahasiswas', 'mahasiswas.pendaftar_id', 'pendaftars.id')
            ->whereHas('pendaftar', function ($query) use ($tahun, $beasiswa) {
                $query->whereHas(
                    'latestStatus',
                    fn($q) => $q->where('status', 'LOLOS ADMINISTRASI')
                )
                    ->where(function ($q) use ($tahun, $beasiswa) {
                        $q->where('tahun_kegiatan_id', $tahun)
                            ->where('beasiswa_id', $beasiswa);
                    });
            })
            ->whereRaw('DATE(tanggal_mulai) = ?', [$tanggal_ujian])
            ->where('sesi', $sesi)
            ->where('ruang', $ruang)
            ->orderBy('mahasiswas.nama');

        if (!$is_query) $query = $query->get();

        return $query;
    }
}