<?php

namespace App\Http\Controllers\Pendaftar;

use App\Helpers\FormField;
use App\Helpers\FormHelper;
use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\FormData;
use App\Models\Mahasiswa;
use App\Models\Notifikasi;
use App\Models\Pemberkasan;
use App\Models\Pendaftar;
use App\Models\PendaftarStatus;
use App\Models\SiakadMahasiswa;
use App\Models\TahunKegiatan;
use App\Models\User;
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

        $pendaftar = Pendaftar::with('pendaftar_status', 'tahun_kegiatan')->whereBeasiswaId($id)
            ->whereHas('tahun_kegiatan', function ($db) {
                $db->whereStatus(1);
            })
            ->whereUserId($user->id)
            ->first();

        if (!$beasiswa) {
            return view('pendaftar.no-page', [
                'message' => 'Beasiswa yang dimaksud tidak tersedia',
                'title' => 'Opz..',
                'bg' => 'danger'
            ]);
        }

        $register = $pendaftar ? true : false;
        $readOnly = false;
        $step = intval(request()->get('step') ?? '1');
        if ($step > 2 && !$pendaftar) {
            session()->flash('error_register', 'Sebelum melanjutkan, silahkan konfirmasi terlebih dahulu pendaftaran Anda!');
            return redirect()->to(route('pendaftar.daftar', ['id' => $id]) . '?step=2');
        }
        if ($step < 1) $step = 1;
        else if ($step > 4) $step = 4;
        $jalur = null;
        $generated_form = [];

        if ($step == 2) {
            $key_pmb = env('PMB_KEY_API');
            $_jalur = api()->get("https://pmb.uinmadura.ac.id/api/info/jalur/{$nim}?key={$key_pmb}");
            if ($_jalur->status) {
                $jalur = $_jalur->data;
            }
        } else if ($step == 3 && ($pendaftar && $pendaftar->latest_status?->status === 'DAFTAR')) {
            $master_form = FormData::whereBeasiswaId($pendaftar?->beasiswa_id)
                ->whereTahunKegiatanId($pendaftar?->tahun_kegiatan_id)
                ->orderBy('jenis')
                ->orderBy('indexed')
                ->get();
            $jenis_form = $master_form->pluck('jenis')->unique();

            $berkas = Pemberkasan::wherePendaftarId($pendaftar->id)->first();

            $masterTemplate = [
                'file_surat_pernyataan_1' => url('file/template/surat_pernyataan_1.docx'),
                'file_surat_pernyataan_2' => url('file/template/surat_pernyataan_2.docx'),
                'file_pakta_integritas' => url('file/template/pakta_integritas.docx'),
            ];

            foreach ($jenis_form as $jenis) {
                $form = form($jenis, $pendaftar?->beasiswa_id, $pendaftar?->tahun_kegiatan_id);
                if ($berkas) {
                    if (isset($berkas->data->{$form->getCode()})) {
                        $berkasdata = $berkas->data->{$form->getCode()};
                        foreach ($form->getType() as $name => $type) {
                            if ($type === 'file') {
                                $url_temp = isset($masterTemplate[$name]) ? $masterTemplate[$name] : null;
                                $extension = $berkasdata->{$name}->value->extension;
                                $url = $berkasdata->{$name}->value->url;
                                $text = $berkasdata->{$name}->text;
                                if ($url_temp) {
                                    $form->setLabel($name, "$text (<a href='$url_temp' target='_blank'>Download Template</a>)");
                                }
                                $form->setDescription($name, "<div class='alert alert-info mt-1 mb-0'><div class='text-success fst-italic'>{$text} telah diunggah, biarkan kosong apabila tidak ingin diganti</div>File saat ini: <strong><a href='javascript:void(0);' data-extension='$extension' data-url='$url' data-type='$text' class='fw-bold text-decoration-underline base-berkas' onclick='viewControl(this)' class='btn btn-link p-0 fw-bold text-primary'>{$berkasdata->{$name}->value->name}</a></strong></div>");
                                $form->removeValidator($name, 'required');
                                $form->appendField(new FormField(
                                    name: 'old_' . $name,
                                    type: 'hidden'
                                ));
                                $form->setValue('old_' . $name, $berkasdata->{$name}->value->name);
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
            'generated_form'
        ));
    }

    private function validateFinalisasiBerkas($pendaftar)
    {
        $berkas = Pemberkasan::wherePendaftarId($pendaftar->id)->first();
        $master_form = FormData::whereBeasiswaId($pendaftar?->beasiswa_id)
            ->whereTahunKegiatanId($pendaftar?->tahun_kegiatan_id)
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
        $config = [
            [
                'beasiswa' => 'KIP',
                'setting' => [
                    'tahun_lulus' => [2023, 2024, 2025],
                    'tahun_masuk' => 2025
                ]
            ]
        ];

        $beasiswa = Beasiswa::where('status', 1)
            ->whereHas('jadwal_kegiatan', function ($query) {
                $query->is_active()
                    ->where('role', 'PENDAFTARAN')
                    ->whereHas('tahun_kegiatan', function ($q) {
                        $q->where('status', 1);
                    });
            })
            ->find($id);

        $matches = array_filter($config, function ($c) use ($beasiswa) {
            $configBeasiswa = $c['beasiswa'];
            return stripos($beasiswa->nama, $configBeasiswa) !== false
                || stripos($configBeasiswa, $beasiswa->nama) !== false;
        });
        $config_matches = count($matches) ? $matches[0] : null;

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
            /* kembalikan ke step 2 dan tampilkan pesan kesalahan  */
            session()->flash('error_register', 'Sebelum melanjutkan, silahkan konfirmasi terlebih dahulu pendaftaran Anda!');
            return redirect()->to(route('pendaftar.daftar', ['id' => $beasiswa->id]) . '?step=2');
        }
        if ($config_matches && !in_array($request->tahun_lulus, $config_matches['setting']['tahun_lulus'] ?? [])) {
            return response()->json('Tahun lulus tidak sesuai dengan ketentuan pendaftaran', 422);
        }
        if ($config_matches && ($request->tahun_masuk != $config_matches['setting']['tahun_masuk'] ?? 0)) {
            return response()->json('Tahun masuk tidak sesuai dengan ketentuan pendaftaran', 422);
        }
        /* =============================== */

        $user = Auth::user();

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

        $mahasiswa_api = SiakadMahasiswa::with('prodi.fakultas')
            ->whereNpm($user->username)
            ->first();

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
            'redirect' => route('pendaftar.daftar', ['id' => $beasiswa->id]) . '?step=3'
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

        if ($pendaftar->latest_status?->status !== 'FINALISASI') {
            PendaftarStatus::create([
                'pendaftar_id' => $pendaftar->id,
                'status' => 'FINALISASI'
            ]);
        }

        $users = User::whereRaw("FIND_IN_SET(1, access)")->get();
        foreach ($users as $key => $value) {
            Notifikasi::create([
                'key' => 'PENDAFTARAN',
                'user_id' => $value->id,
                'pesan' => "{$pendaftar->mahasiswa->nama} ({$pendaftar->mahasiswa->nim}) berhasil memfinalisasi pendaftaran beasiswa {$beasiswa->nama} tahun {$pendaftar->tahun_kegiatan->tahun}",
                'referensi' => NULL,
                'dibaca' => 0
            ]);
        }
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