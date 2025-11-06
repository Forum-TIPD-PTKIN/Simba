<?php

namespace App\Http\Controllers\Pendaftar;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\BiodataPendaftar;
use App\Models\FormData;
use App\Models\Pendaftar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BiodataPendaftarController extends Controller
{
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
            ->where(function ($query) {
                $query->whereJenis('BIODATA')
                    ->orWhere('jenis', 'FORM BIODATA');
            })
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

        $biodataGroup = new \stdClass();
        $biodata = BiodataPendaftar::wherePendaftarId($pendaftar->id)->first();

        foreach ($jenis_form as $jenis) {
            $form = form($jenis, $pendaftar?->beasiswa_id, $pendaftar?->tahun_kegiatan_id);
            $formtype = $form->getType();
            $biodataGroup->{$form->getCode()} = new \stdClass();
            foreach ($formtype as $name => $type) {
                if ($type === 'file') {
                    $val = $form->saveFile($name, 'pendaftar/' . $name);
                    if (!$val) {
                        // tidak ada unggahan baru
                        if ($biodata) {
                            // $val = $biodata?->data?->{$form->getCode()}?->{$name}?->value;
                            $data = $biodata?->data;
                            $section = $form->getCode();

                            // Ambil key fallback dari properti yang tersedia
                            $fallbackKey = array_keys((array) $data)[0] ?? null;

                            // Tentukan active section
                            $activeSection = property_exists($data, $section)
                                ? $section
                                : $fallbackKey;

                            // Akses value
                            $val = $data?->{$activeSection}?->{$name}?->value ?? null;
                        }
                    } else {
                        if ($biodata) {
                            if (isset($biodata->data->{$form->getCode()}->{$name}->value->path)) {
                                $path = storage_path("app/{$biodata->data->{$form->getCode()}->{$name}->value->path}");
                                if (file_exists($path)) {
                                    try {
                                        unlink($path);
                                    } catch (\Throwable $th) {
                                        return abort(500, 'Proses gagal');
                                    }
                                }
                            }
                        }
                        $val->url = '[URL_ORIGIN]/' . $val->path;
                    }
                } else {
                    $val = $form->getValue($name);
                    $valOption =  collect($form->getOption($name))->filter(function ($i) use ($val) {
                        return $i->value === $val;
                    })->map(function ($i) {
                        return $i->text;
                    })->first();
                }
                $biodataGroup->{$form->getCode()}->{$name} = [
                    'index' => $form->getIndex($name),
                    'text' => $form->getLabel($name),
                    'type' => $form->getType($name),
                    'value' => in_array($type, ['text', 'textarea']) ? strtoupper($val) : $val,
                    'valOption' => $type !== 'file' ? $valOption : null
                ];
            }
        }

        if ($biodata) {
            BiodataPendaftar::whereId($biodata->id)->update([
                'pendaftar_id' => $pendaftar->id,
                'data' => json_encode($biodataGroup)
            ]);
        } else {
            BiodataPendaftar::create([
                'pendaftar_id' => $pendaftar->id,
                'data' => json_encode($biodataGroup)
            ]);
        }

        return response()->json([
            'icon' => 'success',
            'title' => 'Berhasil',
            'message' => "Pengisian Biodata beasiswa {$beasiswa->nama} berhasil",
            'redirect' => route('pendaftar.daftar', ['id' => $beasiswa->id]) . '?step=3'
        ]);
    }
}
