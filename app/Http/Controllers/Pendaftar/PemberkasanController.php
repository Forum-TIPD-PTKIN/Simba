<?php

namespace App\Http\Controllers\Pendaftar;

use App\Helpers\FormHelper;
use App\Http\Controllers\Controller;
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
        $data = Pendaftar::with(['mahasiswa', 'beasiswa', 'tahun_kegiatan', 'pemberkasan'])
            ->whereUserId(Auth::id())
            ->first();
        $master_form = FormData::whereBeasiswaId($data?->beasiswa_id)
            ->whereTahunKegiatanId($data?->tahun_kegiatan_id)
            ->orderBy('jenis')
            ->orderBy('indexed')
            ->get();
        $jenis_form = $master_form->pluck('jenis')->unique();
        foreach ($jenis_form as $jenis) {
            $form = new FormHelper($jenis, $data?->beasiswa_id, $data?->tahun_kegiatan_id);
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
        //
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