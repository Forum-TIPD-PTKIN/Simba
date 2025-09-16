<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\FormData;
use App\Models\TahunKegiatan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->groupBy('jenis')
            ->orderBy('jenis', 'asc')
            ->pluck('jenis')
            ->toArray();

        return view('admin.form-data', [
            'master_type'  => $types,
            'tahun_kegiatan' => $tahun_kegiatan,
            'beasiswa' => $beasiswa,
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
                'nama' => 'required',
                'jenis' => 'required',
                'type' => 'required',
                'indexed' => 'required|numeric',
            ],
            [
                'nama.required' => 'Nama data harus diisi',
                'jenis.required' => 'Jenis form harus diisi',
                'type.required' => 'Type form harus diisi',
                'indexed.required' => 'Sisipkan setelah/sebelum harus ditentukan',
                'indexed.numeric' => 'Format indexed harus angka',
            ]
        );
        if ($request->newform) {
            $ind = FormData::where('jenis', $request->newform)->count();
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
            $form->jenis = strtoupper(trim(strip_tags($request->newform)));
            $form->deskripsi = $request->deskripsi;
            $form->indexed = 0;
            $form->config = json_encode([
                'title' => $request->nama,
                'name' => $request->name ?? md5(Carbon::now()->timestamp),
                'validator' => $valid,
                'option' => $request->options ?? [],
                'type' => $request->type,
            ]);
            $form->save();
            return response()->json($request->newform);
        } else {
            $ind = FormData::where('jenis', $request->jenis)->max('indexed');
            $valid = [];
            if ($request->validators) {
                foreach ($request->validators as $key => $value) {
                    $valid[$value['validator']] = $value['message'];
                }
            }
            $this->updateIndexed($request->jenis, $request->indexed, 1);
            $form = new FormData();
            $form->jenis = strtoupper(trim(strip_tags($request->jenis)));
            $form->deskripsi = $request->deskripsi;
            $form->indexed = $request->indexed;
            $form->config = json_encode([
                'title' => $request->nama,
                'name' => $request->name ?? md5(Carbon::now()->timestamp),
                'validator' => $valid,
                'option' => $request->options ?? [],
                'type' => $request->type,
            ]);
            $form->save();
            return response()->json($this->getRaw($request->jenis));
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

    public function detail(Request $request)
    {
        return response()->json($this->getRaw($request->jenis));
    }

    private function updateIndexed($jenis, $from, $forward = 0)
    {
        $data = FormData::where('jenis', $jenis)
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

    private function getRaw($jenis, $db = false)
    {
        if ($db) {
            $data = DB::table('master_forms')->where('jenis', $jenis)
                ->orderBy('indexed', 'asc')
                ->get();
        } else {
            $data = FormData::where('jenis', $jenis)
                ->orderBy('indexed', 'asc')
                ->get();
            $data = collect($data)->map(function ($item) {
                $config = $item->config ? json_decode($item->config) : null;
                if (!isset($config->validator)) {
                    $config->validator = (object) [];
                }

                if (!isset($config->option)) {
                    $config->option = [];
                }

                $item->config = $config;
                return $item;
            });
        }
        return $data;
    }
}