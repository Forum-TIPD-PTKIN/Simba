<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\FormData;
use App\Models\TahunKegiatan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FormDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = [
            'text' => 'Text',
            'number' => 'Number',
            'email' => 'Email',
            'textarea' => 'Textarea',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio',
            'select' => 'Select',
            'password' => 'Password',
            'date' => 'Date',
            'hidden' => 'Hidden',
            'file' => 'File',
        ];

        $tahun_kegiatan = TahunKegiatan::orderBy('tahun', 'desc')
            ->limit(10)
            ->get();
        $beasiswa = Beasiswa::orderBy('nama', 'asc')->get();

        $jenis = FormData::select('jenis')
            ->where('tahun_kegiatan_id', count($tahun_kegiatan) ? $tahun_kegiatan[0]->id : null)
            ->where('beasiswa_id', count($beasiswa) ? $beasiswa[0]->id : null)
            ->groupBy('jenis')
            ->orderBy('jenis', 'asc')
            ->pluck('jenis')
            ->toArray();

        return view('admin.form-data', [
            'master_type'  => $types,
            'tahun_kegiatan' => $tahun_kegiatan,
            'beasiswa' => $beasiswa,
            'curr_tahun_kegiatan' => count($tahun_kegiatan) ? $tahun_kegiatan[0]->id : '',
            'curr_beasiswa' => count($beasiswa) ? $beasiswa[0]->id : '',
            'master_jenis' => $jenis,
            'jenis'        => count($jenis) ? $jenis[0] : '',
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
        $request->validate(
            [
                'tahun_kegiatan' => 'required',
                'beasiswa' => 'required',
                'nama' => 'required',
                'jenis' => 'required',
                'type' => 'required',
                'indexed' => 'required|numeric',
            ],
            [
                'tahun_kegiatan' => 'Tahun kegiatan belum dipilih',
                'beasiswa' => 'Beasiswa belum dipilih',
                'nama.required' => 'Nama data harus diisi',
                'jenis.required' => 'Jenis form harus diisi',
                'type.required' => 'Type form harus diisi',
                'indexed.required' => 'Sisipkan setelah/sebelum harus ditentukan',
                'indexed.numeric' => 'Format indexed harus angka',
            ]
        );
        if ($request->newform) {
            $ind = FormData::where('jenis', $request->newform)
                ->where('tahun_kegiatan_id', $request->tahun_kegiatan)
                ->where('beasiswa_id', $request->beasiswa)
                ->count();
            if ($ind > 0) {
                return response()->json(['message' => 'Nama jenis form sudah ada, gunakan nama lainnya!'], 422);
            }
            $valid = [];
            if ($request->validators) {
                foreach ($request->validators as $key => $value) {
                    $valid[$value['validator']] = $value['message'];
                }
            }
            $form = new FormData();
            $form->tahun_kegiatan_id = $request->tahun_kegiatan;
            $form->beasiswa_id = $request->beasiswa;
            $form->jenis = strtoupper(trim(strip_tags($request->newform)));
            $form->deskripsi = $request->deskripsi;
            $form->indexed = 0;
            $form->config = json_encode([
                'title' => ucwords(trim(strip_tags($request->nama)), ' '),
                'name' => $request->name ?? md5(Carbon::now()->timestamp),
                'validator' => $valid,
                'option' => $request->options ?? [],
                'type' => $request->type,
            ]);
            $form->save();
            return response()->json($request->newform);
        } else {
            $ind = FormData::where('jenis', $request->jenis)
                ->where('tahun_kegiatan_id', $request->tahun_kegiatan)
                ->where('beasiswa_id', $request->beasiswa)
                ->max('indexed');
            $valid = [];
            if ($request->validators) {
                foreach ($request->validators as $key => $value) {
                    $valid[$value['validator']] = $value['message'];
                }
            }
            $this->updateIndexed($request, $request->indexed, 1);
            $form = new FormData();
            $form->tahun_kegiatan_id = $request->tahun_kegiatan;
            $form->beasiswa_id = $request->beasiswa;
            $form->jenis = strtoupper(trim(strip_tags($request->jenis)));
            $form->deskripsi = $request->deskripsi;
            $form->indexed = $request->indexed;
            $form->config = json_encode([
                'title' => ucwords(trim(strip_tags($request->nama)), ' '),
                'name' => $request->name ?? md5(Carbon::now()->timestamp),
                'validator' => $valid,
                'option' => $request->options ?? [],
                'type' => $request->type,
            ]);
            $form->save();
            return response()->json($this->getRaw($request));
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
    public function edit(Request $request, string $id)
    {
        FormData::where('jenis', $id)
            ->where('tahun_kegiatan_id', $request->tahun_kegiatan)
            ->where('beasiswa_id', $request->beasiswa)
            ->update([
                'jenis' => strtoupper(trim(strip_tags($request->nama)))
            ]);
        $request->merge([
            'jenis' => $request->nama,
        ]);
        return response()->json($this->getRaw($request));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate(
            [
                'tahun_kegiatan' => 'required',
                'beasiswa' => 'required',
                'nama' => 'required',
                'jenis' => 'required',
                'type' => 'required',
            ],
            [
                'tahun_kegiatan.required' => 'Tahun kegiatan belum dipilih',
                'beasiswa.required' => 'Beasiswa belum dipilih',
                'nama.required' => 'Nama data harus diisi',
                'jenis.required' => 'Jenis form harus diisi',
                'type.required' => 'Type form harus diisi',
            ]
        );

        $valid = [];
        if ($request->validators) {
            foreach ($request->validators as $key => $value) {
                $valid[$value['validator']] = $value['message'];
            }
        }
        $form = FormData::find($id);

        $this->updateIndexed($request, $request->indexed, $request->indexed != $form->indexed ? 1 : 0);
        $form->tahun_kegiatan_id = $request->tahun_kegiatan;
        $form->beasiswa_id = $request->beasiswa;
        $form->jenis = strtoupper(trim(strip_tags($request->jenis)));
        $form->deskripsi = $request->deskripsi;
        $form->indexed = $request->indexed;
        $form->config = json_encode([
            'title' => ucwords(trim(strip_tags($request->nama)), ' '),
            'name' => $request->name ?? md5(Carbon::now()->timestamp),
            'validator' => $valid,
            'option' => $request->options ?? [],
            'type' => $request->type,
        ]);
        $form->update();
        return response()->json($this->getRaw($request));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $ind = FormData::find($id)->indexed;
        $data = FormData::where('id', $id)
            ->where('jenis', $request->jenis)
            ->where('tahun_kegiatan_id', $request->tahun_kegiatan)
            ->where('beasiswa_id', $request->beasiswa)
            ->delete();
        $this->updateIndexed($request, $ind > 0 ? $ind - 1 : 0);
        return response()->json($this->getRaw($request));
    }

    public function destroy_master(Request $request)
    {
        FormData::where('jenis', $request->jenis)
            ->where('tahun_kegiatan_id', $request->tahun_kegiatan)
            ->where('beasiswa_id', $request->beasiswa)
            ->delete();
        return response()->json('Data berhasil dihapus');
    }

    public function copy(Request $request)
    {
        $request->validate(
            [
                'nama' => 'required',
            ],
            [
                'nama.required' => 'Nama master form harus diisi',
            ]
        );

        $data = $this->getRaw($request, true);
        foreach ($data as $key => $value) {
            $form = new FormData();
            $form->tahun_kegiatan_id = $value->tahun_kegiatan_id;
            $form->beasiswa_id = $value->beasiswa_id;
            $form->jenis      = strtoupper(trim(strip_tags($request->nama)));
            $form->deskripsi  = $value->deskripsi;
            $form->indexed    = $value->indexed;
            $form->config     = $value->config;
            $form->save();
        }
        $request->merge([
            'jenis' => $request->nama,
        ]);
        return response()->json($this->getRaw($request));
    }

    public function detail(Request $request)
    {
        $master_jenis = FormData::select('jenis')
            ->where('tahun_kegiatan_id', $request->tahun_kegiatan)
            ->where('beasiswa_id', $request->beasiswa)
            ->groupBy('jenis')
            ->orderBy('jenis', 'asc')
            ->pluck('jenis')
            ->toArray();

        if ($request->reset === 'true') {
            $request->merge([
                'jenis' => count($master_jenis) ? $master_jenis[0] : '',
            ]);
        }

        return response()->json([
            'data' => $this->getRaw($request),
            'master_jenis' => $master_jenis
        ]);
    }

    private function updateIndexed($data, $from, $forward = 0)
    {
        $data = FormData::where('jenis', $data->jenis)
            ->where('tahun_kegiatan_id', $data->tahun_kegiatan)
            ->where('beasiswa_id', $data->beasiswa)
            ->whereRaw('indexed >= ?', [$from])
            ->orderBy('indexed', 'asc')
            ->get();

        foreach ($data as $key => $value) {
            $f = FormData::find($value->id);
            $f->indexed = $from + $forward;
            $f->save();
            $from++;
        }
    }

    private function getRaw($data, $db = false)
    {
        if ($db) {
            $data = FormData::where('jenis', $data->jenis)
                ->where('tahun_kegiatan_id', $data->tahun_kegiatan)
                ->where('beasiswa_id', $data->beasiswa)
                ->orderBy('indexed', 'asc')
                ->get();
        } else {
            $data = FormData::where('jenis', $data->jenis)
                ->where('tahun_kegiatan_id', $data->tahun_kegiatan)
                ->where('beasiswa_id', $data->beasiswa)
                ->orderBy('indexed', 'asc')
                ->get();
            $data = collect($data)->map(function ($item) {
                $config = $item->config_json ?: null;
                if (!isset($config->validator)) {
                    $config->validator = (object) [];
                }

                if (!isset($config->option)) {
                    $config->option = [];
                }

                $item->config_json = $config;
                return $item;
            });
        }
        return $data;
    }
}