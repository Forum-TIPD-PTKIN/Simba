<?php

namespace App\Http\Controllers\Pendaftar;

use App\Helpers\FormField;
use App\Helpers\FormHelper;
use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\FormData;
use App\Models\JadwalKegiatan;
use App\Models\Pemberkasan;
use App\Models\Pendaftar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PemberkasanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $beasiswa = Beasiswa::select('beasiswas.id', 'beasiswas.nama')
            ->whereExists(function ($db) {
                $db->select(DB::raw('1'))
                    ->from('pendaftars as p')
                    ->whereColumn('p.beasiswa_id', 'beasiswas.id')
                    ->where('p.user_id', Auth::id())
                    ->whereExists(function ($db) {
                        $db->select(DB::raw('1'))
                            ->from('tahun_kegiatans as tk')
                            ->whereColumn('tk.id', 'p.tahun_kegiatan_id')
                            ->where('tk.status', 1);
                    });
            })
            ->get();

        if (!count($beasiswa)) {
            return view('pendaftar.no-page', [
                'message' => 'Tidak ada beasiswa yang aktif untuk pengisian berkas',
                'title' => 'Opz..',
                'bg' => 'danger'
            ]);
        }

        $beasiswa_select = $request->get('beasiswa') ?? $beasiswa[0]->id;
        $request->mergeIfMissing(['beasiswa' => $beasiswa_select]);
        $generated_form = [];
        $data = Pendaftar::with(['mahasiswa', 'beasiswa', 'tahun_kegiatan', 'pemberkasan', 'pendaftar_status'])
            ->whereHas('tahun_kegiatan', function ($db) {
                $db->whereStatus(1);
            })
            ->whereHas('beasiswa', function ($db) use ($beasiswa_select) {
                $db->whereId($beasiswa_select);
            })
            ->whereUserId(Auth::id())
            ->first();
        $is_jadwal_daftar = cek_jadwal($data->tahun_kegiatan_id, $data->beasiswa_id, 'PENDAFTARAN', is_active: true); // return true atau false
        $master_form = FormData::whereBeasiswaId($data?->beasiswa_id)
            ->whereTahunKegiatanId($data?->tahun_kegiatan_id)
            ->orderBy('jenis')
            ->orderBy('indexed')
            ->get();
        $jenis_form = $master_form->pluck('jenis')->unique();

        $berkas = Pemberkasan::wherePendaftarId($data->id)->first();

        foreach ($jenis_form as $jenis) {
            $form = form($jenis, $data?->beasiswa_id, $data?->tahun_kegiatan_id);
            if ($berkas) {
                if (isset($berkas->data->{$form->getCode()})) {
                    $berkasdata = $berkas->data->{$form->getCode()};
                    foreach ($form->getType() as $name => $type) {
                        if ($type === 'file') {
                            $form->setValue($name, "<div class='alert alert-info mt-1 mb-0'><div class='text-success fst-italic'>{$form->getLabel($name)} telah diunggah, biarkan kosong apabila tidak ingin diganti</div>File saat ini: <strong><a class='btn btn-link p-0 fw-bold text-primary'>{$berkasdata->{$name}->value->name}</a></strong></div>");
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

        return view('pendaftar.pemberkasan', [
            'data' => $data,
            'filter_beasiswa' => $beasiswa,
            'beasiswa' => $data->beasiswa,
            'generated_form' => $generated_form,
            'is_jadwal_daftar' => $is_jadwal_daftar
        ]);
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
    public function store(Request $request)
    {
        $beasiswa = Beasiswa::where('status', 1)
            ->whereHas('jadwal_kegiatan', function ($query) {
                $query->is_active()
                    ->where('role', 'PENDAFTARAN')
                    ->whereHas('tahun_kegiatan', function ($q) {
                        $q->where('status', 1);
                    });
            })
            ->whereId($request->beasiswa)
            ->first();

        $pendaftar = Pendaftar::with('pendaftar_status')->whereBeasiswaId($beasiswa->id)
            ->whereHas('tahun_kegiatan', function ($db) {
                $db->whereStatus(1);
            })
            ->whereUserId(Auth::id())
            ->first();

        $master_form = FormData::whereBeasiswaId($pendaftar?->beasiswa_id)
            ->whereTahunKegiatanId($pendaftar?->tahun_kegiatan_id)
            ->orderBy('jenis')
            ->orderBy('indexed')
            ->get();

        $jenis_form = $master_form->pluck('jenis')->unique();
        foreach ($jenis_form as $jenis) {
            $form = form($jenis, $pendaftar?->beasiswa_id, $pendaftar?->tahun_kegiatan_id);
            $kode = $form->getCode();
            $formtype = $form->getType();
            foreach ($formtype as $name => $type) {
                $old_name = $kode . '_old_' . $name;
                if ($request->has($old_name)) {
                    $form->removeValidator($name, 'required');
                }
            }

            $validator = $form->execValidator();
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
        }

        $berkasGroup = new \stdClass();
        $berkas = Pemberkasan::wherePendaftarId($pendaftar->id)->first();

        foreach ($jenis_form as $jenis) {
            $form = form($jenis, $pendaftar?->beasiswa_id, $pendaftar?->tahun_kegiatan_id);
            $formtype = $form->getType();
            $berkasGroup->{$form->getCode()} = new \stdClass();
            foreach ($formtype as $name => $type) {
                if ($type === 'file') {
                    $val = $form->saveFile($name, 'pendaftar/' . $name);
                    if (!$val) {
                        // tidak ada unggahan baru
                        if ($berkas) {
                            $val = $berkas->data->{$form->getCode()}->{$name}->value;
                        }
                    } else {
                        if ($berkas) {
                            if (isset($berkas->data->{$form->getCode()}->{$name}->value->path)) {
                                $path = storage_path("app/{$berkas->data->{$form->getCode()}->{$name}->value->path}");
                                if (file_exists($path)) {
                                    try {
                                        unlink($path);
                                    } catch (\Throwable $th) {
                                        return 'error';
                                    }
                                }
                            }
                        }
                        $val->url = '[URL_ORIGIN]/' . $val->path;
                    }
                } else {
                    return $form->getOption();
                    $val = $form->getValue($name);
                }
                $berkasGroup->{$form->getCode()}->{$name} = [
                    'text' => $form->getLabel($name),
                    'type' => $form->getType($name),
                    'value' => $val
                ];
            }
        }

        if ($berkas) {
            Pemberkasan::whereId($berkas->id)->update([
                'pendaftar_id' => $pendaftar->id,
                'data' => json_encode($berkasGroup)
            ]);
        } else {
            Pemberkasan::create([
                'pendaftar_id' => $pendaftar->id,
                'data' => json_encode($berkasGroup)
            ]);
        }

        return response()->json([
            'icon' => 'success',
            'title' => 'Berhasil',
            'message' => "Pengisian Pemberkasan beasiswa {$beasiswa->nama} berhasil",
            'redirect' => route('pendaftar.daftar', ['id' => $beasiswa->id]) . '?step=3'
        ]);
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