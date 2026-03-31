<?php

namespace App\Http\Controllers\Pendaftar;

use App\Helpers\FormField;
use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\BiodataPendaftar;
use App\Models\FormData;
use App\Models\JadwalKegiatan;
use App\Models\Mahasiswa;
use App\Models\Pemberkasan;
use App\Models\Pendaftar;
use App\Models\PendaftarStatus;
use App\Models\SiakadMahasiswa;
use App\Models\TahunKegiatan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DaftarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $id)
    {
        $user = Auth::user();
        $nim = $user->username;
        $mahasiswa = SiakadMahasiswa::with('prodi.fakultas')
            ->whereNpm($nim)
            ->first();

        $beasiswa = Beasiswa::where('status', 1)
            ->whereHas('jadwal_kegiatan', function ($query) {
                $query->is_active()
                    ->where('role', 'PENDAFTARAN')
                    ->whereHas('tahun_kegiatan', function ($q) {
                        $q->where('status', 1);
                    });
            })
            ->find($id);

        $pendaftar = Pendaftar::with('pendaftar_status', 'tahun_kegiatan', 'beasiswa')->whereBeasiswaId($id)
            ->whereHas('tahun_kegiatan', function ($db) {
                $db->whereStatus(1);
            })
            ->whereUserId($user->id)
            ->first();

        if (!$beasiswa && !$pendaftar) {
            return view('pendaftar.no-page', [
                'message' => 'Beasiswa yang dimaksud tidak tersedia',
                'title' => 'Opz..',
                'bg' => 'danger'
            ]);
        }

        if ($pendaftar && $pendaftar->latest_status?->status !== 'DAFTAR') {
            $key_pmb = env('PMB_KEY_API');
            $_pendaftar = api()->get("https://pmb.uinmadura.ac.id/api/info/pendaftar/{$nim}?key={$key_pmb}");
            $jalur = null;
            if ($_pendaftar->status) {
                $jalur = $_pendaftar->data->jalur_masuk;
                $akunpmb = $_pendaftar->data->biodata->kode;
            }

            $kegiatan = JadwalKegiatan::where('tahun_kegiatan_id', $pendaftar?->tahun_kegiatan_id)
                ->where('beasiswa_id', $pendaftar?->beasiswa_id)
                ->where('role', 'PENGUMUMAN_SELEKSI_ADMINISTRASI')
                ->first();

            if (!$kegiatan) {
                return view('pendaftar.no-page', [
                    'message' => '',
                    'title' => 'Opz..',
                    'bg' => 'danger',
                ]);
            }
            $pengumuman_seleksi = Carbon::parse($kegiatan->tanggal_mulai)
                ->locale('id')
                ->translatedFormat('l, j F Y H:i');

            $biodatadata = BiodataPendaftar::wherePendaftarId($pendaftar->id)->first();
            $biodata = [];
            if ($biodatadata) {
                foreach ($biodatadata->data as $key => $value) {
                    foreach ($value as $a => $b) {
                        $isFile = $b->type == 'file' ? true : false;
                        $isSelect = $b->type == 'select' ? true : false;
                        $isRadio = $b->type == 'radio' ? true : false;
                        array_push($biodata, (object)[
                            'text' => $b->text,
                            'url' => $isFile ? $b?->value?->url : null,
                            'value' => $isFile ? null : (($isSelect || $isRadio) ? $b->valOption : $b->value),
                            'extension' => $isFile ? $b?->value?->extension : null
                        ]);
                    }
                }
            }

            $berkasdata = Pemberkasan::wherePendaftarId($pendaftar->id)->first();
            $berkas = [];
            if ($berkasdata) {
                foreach ($berkasdata->data as $key => $value) {
                    foreach ($value as $a => $b) {
                        $isFile = $b->type == 'file' ? true : false;
                        $isSelect = $b->type == 'select' ? true : false;
                        array_push($berkas, (object)[
                            'text' => $b->text,
                            'url' => $isFile ? $b?->value?->url : null,
                            'value' => $isFile ? null : ($isSelect ? $b->valOption : $b->value),
                            'extension' => $isFile ? $b?->value?->extension : null
                        ]);
                    }
                }
            }

            return view('pendaftar.daftar.finalisasi', compact('pendaftar', 'jalur', 'akunpmb', 'pengumuman_seleksi', 'biodata', 'berkas'));
        }

        $register = $pendaftar ? true : false;
        $readOnly =  false;
        $step = intval(request()->get('step') ?? '1');
        if ($step > 1 && !$pendaftar) {
            session()->flash('error_register', 'Sebelum melanjutkan, silahkan konfirmasi terlebih dahulu pendaftaran Anda!');
            return redirect()->to(route('pendaftar.daftar', ['id' => $id]) . '?step=1');
        }
        if ($step < 1) $step = 1;
        else if ($step > 4) $step = 4;
        $jalur = null;
        $generated_form = [];
        $akunpmb = null;

        if ($step == 1) {
            $key_pmb = env('PMB_KEY_API');
            $_jalur = api()->get("https://pmb.uinmadura.ac.id/api/info/pendaftar/{$nim}?key={$key_pmb}");
            if ($_jalur->status) {
                $jalur = $_jalur->data->jalur_masuk;
                $akunpmb = $_jalur->data->biodata->kode;
            }
        } else if ($step == 2 && ($pendaftar && $pendaftar->latest_status?->status === 'DAFTAR')) {
            $master_form = FormData::whereBeasiswaId($pendaftar?->beasiswa_id)
                ->whereTahunKegiatanId($pendaftar?->tahun_kegiatan_id)
                ->where(function ($query) {
                    $query->whereJenis('BIODATA')
                        ->orWhere('jenis', 'FORM BIODATA');
                })
                ->orderBy('jenis')
                ->orderBy('indexed')
                ->get();

            $jenis_form = $master_form->pluck('jenis')->unique();

            $data = BiodataPendaftar::wherePendaftarId($pendaftar->id)->first();

            foreach ($jenis_form as $jenis) {
                $form = form($jenis, $pendaftar?->beasiswa_id, $pendaftar?->tahun_kegiatan_id);

                if ($data) {
                    if (isset($data->data->{$form->getCode()})) {
                        $biodatadata = $data->data->{$form->getCode()};
                        foreach ($form->getType() as $name => $type) {
                            if ($type === 'file') {
                                $extension = $biodatadata->{$name}->value->extension;
                                $url = $biodatadata->{$name}->value->url;
                                $text = $biodatadata->{$name}->text;
                                $form->setDescription($name, "<div class='alert alert-info mt-1 mb-0'><div class='text-success fst-italic'>{$text} telah diunggah, biarkan kosong apabila tidak ingin diganti</div>File saat ini: <strong><a href='javascript:void(0);' data-extension='$extension' data-url='$url' data-type='$text' class='fw-bold text-decoration-underline base-berkas' onclick='viewControl(this)' class='btn btn-link p-0 fw-bold text-primary'>{$biodatadata->{$name}->value->name}</a></strong></div>");
                                $form->removeValidator($name, 'required');
                                $form->appendField(new FormField(
                                    name: 'old_' . $name,
                                    type: 'hidden'
                                ));
                                $form->setValue('old_' . $name, $biodatadata->{$name}->value->name);
                            } else {
                                if (isset($biodatadata->{$name}) && !($biodatadata->{$name}->type === 'file')) {
                                    $form->setValue($name, $biodatadata->{$name}->value);
                                }
                            }
                        }
                    }
                }
                array_push($generated_form, [
                    'jenis' => $jenis,
                    'form' => $form->render()
                ]);
            }
        } else if ($step == 3 && ($pendaftar && $pendaftar->latest_status?->status === 'DAFTAR')) {
            $isi = $this->validateFinalisasiBiodata($pendaftar);
            if (!$isi->status) {
                session()->flash('error_register', $isi->message);
                return redirect()->to(route('pendaftar.daftar', ['id' => $pendaftar?->beasiswa_id]) . '?step=2');
            }

            $master_form = FormData::whereBeasiswaId($pendaftar?->beasiswa_id)
                ->whereTahunKegiatanId($pendaftar?->tahun_kegiatan_id)
                ->where(function ($query) {
                    $query->whereJenis('PEMBERKASAN')
                        ->orWhere('jenis', 'BERKAS');
                })
                ->orderBy('jenis')
                ->orderBy('indexed')
                ->get();
            $jenis_form = $master_form->pluck('jenis')->unique();

            $berkas = Pemberkasan::wherePendaftarId($pendaftar->id)->first();

            $masterTemplate = [
                'file_pendukung' => url('file/template/kip/File_Pendukung.docx'),
                'file_pakta_integritas' => url('file/template/kip/Pakta_Integritas_KIP_Kuliah.docx'),
            ];

            foreach ($jenis_form as $jenis) {
                $form = form($jenis, $pendaftar?->beasiswa_id, $pendaftar?->tahun_kegiatan_id);
                foreach ($form->getType() as $name => $type) {
                    if ($type === 'file') {
                        $url_temp = isset($masterTemplate[$name]) ? $masterTemplate[$name] : null;
                        if ($url_temp) {
                            if ($name === 'file_pendukung') {
                                $form->setLabel($name, "{$form->getLabel($name)} <span class='small'>(Jika kategori yang dipilih <span class='fw-bold'>Mahasiswa tidak mampu atau difabel</span>, <a href='$url_temp' target='_blank'>Download Template</a>)</span>");
                            } else {
                                $form->setLabel($name, "{$form->getLabel($name)} <span class='small'>(<a href='$url_temp' target='_blank'>Download Template</a>)</small>");
                            }
                        }
                    }
                }
                if ($berkas) {
                    if (isset($berkas->data->{$form->getCode()})) {
                        $berkasdata = $berkas->data->{$form->getCode()};
                        foreach ($form->getType() as $name => $type) {
                            if ($type === 'file') {
                                $extension = $berkasdata->{$name}?->value?->extension;
                                $url = $berkasdata->{$name}?->value?->url;
                                $text = $berkasdata->{$name}?->text;
                                if ($extension && $url) $form->setDescription($name, "<div class='alert alert-info mt-1 mb-0'><div class='text-success fst-italic'>{$text} telah diunggah, biarkan kosong apabila tidak ingin diganti</div>File saat ini: <strong><a href='javascript:void(0);' data-extension='$extension' data-url='$url' data-type='$text' class='fw-bold text-decoration-underline base-berkas' onclick='viewControl(this)' class='btn btn-link p-0 fw-bold text-primary'>{$berkasdata->{$name}?->value?->name}</a></strong></div>");
                                $form->removeValidator($name, 'required');
                                $form->appendField(new FormField(
                                    name: 'old_' . $name,
                                    type: 'hidden'
                                ));
                                $form->setValue('old_' . $name, $berkasdata->{$name}?->value?->name);
                            } else {
                                if (isset($berkasdata->{$name}) && !($berkasdata->{$name}->type === 'file')) {
                                    $form->setValue($name, $berkasdata->{$name}->value);
                                }
                            }
                        }
                    }
                }
                array_push($generated_form, [
                    'jenis' => $jenis,
                    'form' => $form->render()
                ]);
            }
        } else if ($step == 4 && ($pendaftar && $pendaftar->latest_status?->status === 'DAFTAR')) {
            $isi = $this->validateFinalisasiBerkas($pendaftar);
            if (!$isi->status) {
                session()->flash('error_register', $isi->message);
                return redirect()->to(route('pendaftar.daftar', ['id' => $pendaftar?->beasiswa_id]) . '?step=3');
            }
        }

        return view('pendaftar.daftar.index', compact(
            'beasiswa',
            'step',
            'mahasiswa',
            'register',
            'jalur',
            'pendaftar',
            'readOnly',
            'generated_form',
            'akunpmb'
        ));
    }

    private function validateFinalisasiBiodata($pendaftar)
    {
        $biodata = BiodataPendaftar::wherePendaftarId($pendaftar->id)->first();
        $master_form = FormData::whereBeasiswaId($pendaftar?->beasiswa_id)
            ->whereTahunKegiatanId($pendaftar?->tahun_kegiatan_id)
            ->where(function ($query) {
                $query->whereJenis('BIODATA')
                    ->orWhere('jenis', 'FORM BIODATA');
            })
            ->orderBy('jenis')
            ->orderBy('indexed')
            ->get();
        $jenis_form = $master_form->pluck('jenis')->unique();

        if (!$biodata) {
            return (object)[
                'status' => false,
                'message' => 'Biodata harus dilengkapi!'
            ];
        }

        foreach ($jenis_form as $jenis) {
            $form = form($jenis, $pendaftar?->beasiswa_id, $pendaftar?->tahun_kegiatan_id);

            $validators = $form->getValidator();
            $biodatadata = $biodata->data->{$form->getCode()} ?? null;

            foreach ($form->getType() as $name => $type) {
                $validator = $validators->rules[$form->getCode() . '_' . $name];
                $_valids = explode('|', $validator);
                if (in_array('required', collect($_valids)->map(function ($v) {
                    return strtolower(trim($v));
                })->toArray())) {
                    if (!$biodatadata) {
                        return (object)[
                            'status' => false,
                            'message' => "{$form->getLabel($name)} harus diisi!"
                        ];
                    }
                }
            }
        }
        return (object)[
            'status' => true
        ];
    }

    private function validateFinalisasiBerkas($pendaftar)
    {
        $berkas = Pemberkasan::wherePendaftarId($pendaftar->id)->first();
        $master_form = FormData::whereBeasiswaId($pendaftar?->beasiswa_id)
            ->whereTahunKegiatanId($pendaftar?->tahun_kegiatan_id)
            ->where(function ($query) {
                $query->whereJenis('PEMBERKASAN')
                    ->orWhere('jenis', 'BERKAS');
            })
            ->orderBy('jenis')
            ->orderBy('indexed')
            ->get();
        $jenis_form = $master_form->pluck('jenis')->unique();

        $berkas = Pemberkasan::wherePendaftarId($pendaftar->id)->first();

        if (!$berkas) {
            return (object)[
                'status' => false,
                'message' => 'Pemberkasan harus dilengkapi!'
            ];
        }

        foreach ($jenis_form as $jenis) {
            $form = form($jenis, $pendaftar?->beasiswa_id, $pendaftar?->tahun_kegiatan_id);

            $validators = $form->getValidator();
            $berkasdata = $berkas->data->{$form->getCode()} ?? null;

            foreach ($form->getType() as $name => $type) {
                $validator = $validators->rules[$form->getCode() . '_' . $name];
                $_valids = explode('|', $validator);
                if (in_array('required', collect($_valids)->map(function ($v) {
                    return strtolower(trim($v));
                })->toArray())) {
                    if (!$berkasdata) {
                        return (object)[
                            'status' => false,
                            'message' => "{$form->getLabel($name)} harus diisi!"
                        ];
                    }
                }
            }
        }
        return (object)[
            'status' => true
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $beasiswa = Beasiswa::where('status', 1)
            ->whereHas('jadwal_kegiatan', function ($query) {
                $query->is_active()
                    ->where('role', 'PENDAFTARAN')
                    ->whereHas('tahun_kegiatan', function ($q) {
                        $q->where('status', 1);
                    });
            })
            ->find($id);

        $pendaftar = Pendaftar::whereBeasiswaId($id)
            ->whereHas('tahun_kegiatan', function ($db) {
                $db->whereStatus(1);
            })
            ->whereUserId(Auth::id())
            ->first();
        $kegiatan = TahunKegiatan::whereStatus(1)->first();

        if (!$beasiswa) return response()->json('Beasiswa yang dimaksud tidak tersedia', 422);
        if ($pendaftar) return response()->json('Anda telah melakukan pendaftaran pada beasiswa ini', 422);
        if (!$kegiatan) return response()->json('Tahun kegiatan tidak ada yang aktif', 422);

        /* PROSES CEK VALIDASI PENDAFTARAN */
        $valid = true;
        if (!$valid) {
            /* kembalikan ke step 1 dan tampilkan pesan kesalahan  */
            session()->flash('error_register', 'Sebelum melanjutkan, silahkan konfirmasi terlebih dahulu pendaftaran Anda!');
            return redirect()->to(route('pendaftar.daftar', ['id' => $beasiswa->id]) . '?step=1');
        }

        $user = Auth::user();

        $mahasiswa_api = SiakadMahasiswa::with('prodi.fakultas')
            ->whereNpm($user->username)
            ->first();

        // Cek Aturan Beasiswa ==================================
        foreach ($beasiswa->config_data_json as $key => $value) {
            foreach ($value as $k => $v) {
                if ($request->input($k)) {
                    if (!($request->input($k) >= $v['min_' . $k] && $request->input($k) <= $v['max_' . $k])) {
                        return response()->json(ucfirst(str_replace('_', ' ', $k)) . ' tidak sesuai dengan ketentuan', 422);
                    }
                }
            }
        }

        // ! KHUSUS BEASISWA GENBI (PPK)
        if ($beasiswa->nama === 'Program Pendidikan Kebanksentralan (PPK) BI') {
            // ^ Cek jenjang harus S1
            if ($mahasiswa_api->prodi?->id_jenjang_pendidikan !== 'S1') return response()->json('Jenjang pendidikan bukan S1', 422);

            // ^ Cek status aktif
            $get_aktif = api()->get("https://tipd.dev/api/public/api/akademik/semester-aktif/{$mahasiswa_api->npm}");
            $aktif = $get_aktif->status_akademik ?? null;
            if ($aktif && $aktif  !== 'AKTIF') return response()->json('Status akademik tidak aktif', 422);

            // ^ Cek umur maksimal 23 tahun
            $tgl_lahir = Carbon::parse($mahasiswa_api->tanggal_lahir)->age;
            if ($tgl_lahir > 23) return response()->json('Usia maksimal 23 tahun', 422);

            // ^ Cek prodi
            $prodi_allowed = [
                'Manajemen Pendidikan Islam',
                'Tadris IPS',
                'Bimbingan dan Konseling Pendidikan Islam',
                'Tadris Matematika',
                'Tadris Ilmu Pengetahuan Alam',
                'Perbankan Syari\'ah',
                'Ekonomi Syari\'ah',
                'Akuntansi Syari\'ah',
                'Manajemen Bisnis Syariah',
                'Komunikasi dan Penyiaran Islam',
                'Psikologi Islam',
                'Hukum Keluarga Islam',
                'Hukum Ekonomi Syari\'ah',
                'Hukum Tata Negara'
            ];

            $nama_prodi = strtolower($mahasiswa_api->prodi?->nama_prodi_id ?? '');
            $nama_prodi = preg_replace('/\s*\(.*?\)\s*/', '', $nama_prodi);

            $is_prodi_allowed = false;
            foreach ($prodi_allowed as $allowed) {
                similar_text($nama_prodi, strtolower($allowed), $percent);
                if ($percent > 90) { // ambang batas kemiripan
                    $is_prodi_allowed = true;
                    break;
                }
            }
            if (!$is_prodi_allowed) return response()->json('Program studi Anda tidak diperbolehkan mengikuti beasiswa ini', 422);

            // ^ Cek IPK minimal 3.30
            $get_ipk = api()->get("https://tipd.dev/api/public/api/akademik/ipk-sementara/{$mahasiswa_api->npm}");
            $ipk = $get_ipk->data?->profil?->ipk_akumulatif ?? 0;
            if ((float) $ipk  < 3.30) return response()->json('IPK minimal 3.30', 422);

            // ^ Cek semester minimal 3
            $get_semester = api()->get("https://tipd.dev/api/public/api/akademik/semester-aktif/{$mahasiswa_api->npm}");
            $semester = $get_semester->semester_ke ?? 0;
            if ($semester  < 3) return response()->json('Minimal semester 3', 422);
        }
        /* =============================== */

        $pendaftar = Pendaftar::where('user_id', $user->id)
            ->where('beasiswa_id', $beasiswa->id)
            ->where('tahun_kegiatan_id', $kegiatan->id)
            ->first();

        if (!$pendaftar) $pendaftar = new Pendaftar();
        $pendaftar->user_id = $user->id;
        $pendaftar->beasiswa_id = $beasiswa->id;
        $pendaftar->tahun_kegiatan_id = $kegiatan->id;
        $pendaftar->save();

        $status = PendaftarStatus::where('pendaftar_id', $pendaftar->id)->first();
        if (!$status) $status = new PendaftarStatus();
        $status->pendaftar_id = $pendaftar->id;
        $status->status = 'DAFTAR';
        $status->save();

        $mahasiswa = Mahasiswa::where('pendaftar_id', $pendaftar->id)->first();
        if (!$mahasiswa) $mahasiswa = new Mahasiswa();
        $mahasiswa->pendaftar_id = $pendaftar->id;
        $mahasiswa->nim = $mahasiswa_api->npm;
        $mahasiswa->nama = $mahasiswa_api->nama_mahasiswa;
        $mahasiswa->fakultas = $mahasiswa_api->prodi->fakultas->id_fakultas . '|' . $mahasiswa_api->prodi->fakultas->nama_fakultas;
        $mahasiswa->prodi = $mahasiswa_api->prodi->id_prodi . '|' . $mahasiswa_api->prodi->singkatan;
        $mahasiswa->save();

        return response()->json([
            'icon' => 'success',
            'title' => 'Berhasil',
            'message' => "Pendaftaran beasiswa {$beasiswa->nama} berhasil",
            'redirect' => route('pendaftar.daftar', ['id' => $beasiswa->id]) . '?step=2'
        ]);
    }

    public function finalisasi(Request $request, $id)
    {
        $beasiswa = Beasiswa::where('status', 1)
            ->whereHas('jadwal_kegiatan', function ($query) {
                $query->is_active()
                    ->where('role', 'PENDAFTARAN')
                    ->whereHas('tahun_kegiatan', function ($q) {
                        $q->where('status', 1);
                    });
            })
            ->find($id);

        $pendaftar = Pendaftar::with('pendaftar_status', 'tahun_kegiatan', 'mahasiswa')->whereBeasiswaId($id)
            ->whereHas('tahun_kegiatan', function ($db) {
                $db->whereStatus(1);
            })
            ->whereUserId(Auth::id())
            ->first();

        if (!$beasiswa) {
            session()->flash('error_register', 'Beasiswa yang dimaksud tidak tersedia');
            return redirect()->to(route('pendaftar.daftar', ['id' => $pendaftar?->beasiswa_id]) . '?step=4');
        }

        $cek = $this->validateFinalisasiBerkas($pendaftar);
        if (!$cek->status) {
            session()->flash('error_register', $cek->message);
            return redirect()->to(route('pendaftar.daftar', ['id' => $pendaftar?->beasiswa_id]) . '?step=4');
        }

        if (in_array($pendaftar->latest_status?->status, [
            'DAFTAR'
        ])) {
            $kegiatan = JadwalKegiatan::where('tahun_kegiatan_id', $pendaftar?->tahun_kegiatan_id)
                ->where('beasiswa_id', $pendaftar?->beasiswa_id)
                ->where('role', 'PENGUMUMAN_SELEKSI_ADMINISTRASI')
                ->first();

            if (!$kegiatan) {
                session()->flash('error_register', 'Tanggal pengumuman seleksi administrasi belum ditentukan oleh admin');
                return redirect()->to(route('pendaftar.daftar', ['id' => $pendaftar?->beasiswa_id]) . '?step=4');
            }
            PendaftarStatus::create([
                'pendaftar_id' => $pendaftar->id,
                'status' => 'PENGAJUAN'
            ]);
        }

        return redirect()->to(route('pendaftar.daftar', ['id' => $pendaftar?->beasiswa_id]));
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}