<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class TahunKegiatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $curr_year = TahunKegiatan::where('tahun', date('Y'))
            ->first();

        if (!$curr_year) {
            TahunKegiatan::where('tahun', '!=', date('Y'))
                ->update(['status' => 0]);

            $tahun = new TahunKegiatan();
            $tahun->tahun = date('Y');
            $tahun->status = 1;
            $tahun->save();
        }

        $tahun = TahunKegiatan::orderBy('tahun', 'desc')
            ->get();

        return view('admin.tahun-kegiatan', [
            'tahun' => $tahun
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
        $id = Crypt::decryptString($request->tahun);
        try {
            $tahun = TahunKegiatan::find($id);
            $tahun->update([
                'status' => 1
            ]);

            TahunKegiatan::where('id', '!=', $id)
                ->update([
                    'status' => 0
                ]);
            $data = array(
                'icon' => 'success',
                'title' => 'Berhasil',
                'message' => "Tahun {$tahun->tahun} Aktif",
            );

            return response()->json($data);
        } catch (\Illuminate\Database\QueryException $e) {
            $error = $e->errorInfo;
            $data['message'] = $error[2] ?: 'Ada kesalahan saat menyimpan data';

            return response()->json($data, 422);
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