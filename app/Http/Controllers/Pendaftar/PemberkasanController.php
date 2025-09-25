<?php

namespace App\Http\Controllers\Pendaftar;

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
        $pendaftar = Pendaftar::with('beasiswa', 'tahun_kegiatan', 'pendaftar_status')
            ->whereHas('tahun_kegiatan', function ($db) {
                $db->whereStatus(1);
            })
            ->whereUserId(Auth::id())
            ->first();
        $berkas = FormData::whereBeasiswaId($pendaftar->beasiswa->id)
            ->whereJenis('FORM PENDAFTARAN')
            ->orderBy('indexed')
            ->get();

        return $berkas;

        return $pendaftar;

        return view('pendaftar.pemberkasan');
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
