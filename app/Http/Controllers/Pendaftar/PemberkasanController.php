<?php

namespace App\Http\Controllers\Pendaftar;

use App\Helpers\FormField;
use App\Helpers\FormHelper;
use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\FormData;
use App\Models\Pemberkasan;
use App\Models\Pendaftar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemberkasanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $generated_form = [];
        $data = Pendaftar::with(['mahasiswa', 'beasiswa', 'tahun_kegiatan', 'pemberkasan', 'pendaftar_status'])
            ->whereHas('tahun_kegiatan', function ($db) {
                $db->whereStatus(1);
            })
            // ->whereHas('beasiswa', function ($db) {
            //     $db->is_active(1);
            // })
            ->whereUserId(Auth::id())
            ->first();
        $master_form = FormData::whereBeasiswaId($data?->beasiswa_id)
            ->whereTahunKegiatanId($data?->tahun_kegiatan_id)
            ->orderBy('jenis')
            ->orderBy('indexed')
            ->get();
        $jenis_form = $master_form->pluck('jenis')->unique();
        foreach ($jenis_form as $jenis) {
            $form = form($jenis, $data?->beasiswa_id, $data?->tahun_kegiatan_id);
            array_push($generated_form, [
                'jenis' => $jenis,
                'form' => $form->render()
            ]);
        }

        return view('pendaftar.pemberkasan', [
            'data' => $data,
            'beasiswa' => $data->beasiswa,
            'generated_form' => $generated_form
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
