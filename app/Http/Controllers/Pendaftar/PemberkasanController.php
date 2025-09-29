<?php

namespace App\Http\Controllers\Pendaftar;

use App\Helpers\FormField;
use App\Helpers\FormHelper;
use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\FormData;
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
                $db->is_active()
                    ->whereStatus(1);
            })
            ->whereHas('beasiswa', function ($db) {
                $db->is_active(1);
            })
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
        $generated_form = [];
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

        return response()->json($jenis_form, 422);
        foreach ($jenis_form as $jenis) {
            $form = form($jenis, $pendaftar?->beasiswa_id, $pendaftar?->tahun_kegiatan_id);
            $validator = $form->execValidator();
            $file = $form->getInstance();
            // return response()->json($file, 422);
            // // $contoh = request()->file('file_ktp');

            // $file = request()->file('form_pendaftaran_file_ktp');

            // if ($file) {
            //     return response()->json([
            //         'original_name' => $file->getClientOriginalName(), // nama file asli
            //         'extension'     => $file->getClientOriginalExtension(), // ekstensi
            //         'mime'          => $file->getMimeType(), // mime type
            //         'size'          => $file->getSize(), // ukuran byte
            //         'tmp_path'      => $file->getPathname(), // path sementara
            //     ]);
            // }

            // return response()->json(['message' => 'tidak ada file terkirim']);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            // array_push($generated_form, [
            //     'jenis' => $jenis,
            //     'names' => $form->getInstance()
            // ]);
        }

        return redirect()->back();

        return $generated_form;
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