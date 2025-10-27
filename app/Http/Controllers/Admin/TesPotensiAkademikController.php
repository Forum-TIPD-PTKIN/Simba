<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\JadwalCbt;
use App\Models\JenisTesCbt;
use App\Models\MapUjian;
use App\Models\Pendaftar;
use App\Models\PesertaCbt;
use App\Models\SiakadMahasiswa;
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
                ->selectRaw('map_ujians.*')
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
        $tahun = TahunKegiatan::orderBy('tahun', 'desc')->get();
        $beasiswa = Beasiswa::where('status', 1)->get();
        $sesi = MapUjian::selectRaw('sesi')
            ->groupBy('sesi')
            ->pluck('sesi');
        $ruang = MapUjian::selectRaw('ruang')
            ->groupBy('ruang')
            ->pluck('ruang');

        $peserta = MapUjian::with(['pendaftar.mahasiswa', 'pendaftar.beasiswa'])
            ->whereHas('pendaftar', function ($query) use ($request, $tahun, $beasiswa) {
                $query->where(function ($q) use ($request, $tahun, $beasiswa) {
                    if ($request->flt_tahun) {
                        $q->where('tahun_kegiatan_id', $request->flt_tahun);
                    } else {
                        $q->where('tahun_kegiatan_id', count($tahun) ? $tahun[0]->id : null);
                    }

                    if ($request->flt_beasiswa) {
                        $q->where('beasiswa_id', $request->flt_beasiswa);
                    } else {
                        $q->where('beasiswa_id', count($beasiswa) ? $beasiswa[0]->id : null);
                    }
                });
            })
            ->where(function ($query) use ($request, $sesi) {
                if ($request->flt_sesi) {
                    $query->where('sesi', $request->flt_sesi);
                } else {
                    $query->where('sesi', count($sesi) ? $sesi[0] : null);
                }
            })
            ->where(function ($query) use ($request, $ruang) {
                if ($request->flt_ruang) {
                    $query->where('ruang', $request->flt_ruang);
                } else {
                    $query->where('ruang', count($ruang) ? $ruang[0] : null);
                }
            })
            ->orderBy('sesi')
            ->orderBy('ruang')
            ->get();
        return dd($peserta);
    }
}