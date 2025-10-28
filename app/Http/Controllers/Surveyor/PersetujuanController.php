<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\Surveyor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersetujuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('surveyor.persetujuan');
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
        $detailSurveyor = Surveyor::with(['beasiswa', 'tahun_kegiatan'])->where('id', $id)->whereUserId(Auth::id())->first();
        if ($detailSurveyor->bersedia !== null) {
            return redirect()->route('surveyor.persetujuan');
        }

        $titleKegiatan = $detailSurveyor->beasiswa->nama . ' tahun ' . $detailSurveyor->tahun_kegiatan->tahun;


        return view('surveyor.persetujuan-detail', compact('titleKegiatan', 'detailSurveyor'));
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
        $request->validate([
            'status' => 'required|boolean',
            'alamat' => 'required_if:status,1|string|nullable',
            'no_wa' => 'required_if:status,1|string|nullable',
            'alasan' => 'string|nullable',
        ]);

        try {
            $surveyor = Surveyor::findOrFail($id);

            // Prevent re-submission
            if ($surveyor->bersedia !== null) {
                return response()->json(['message' => 'Anda sudah mengirimkan tanggapan sebelumnya.'], 422);
            }

            $surveyor->bersedia = $request->status;
            if ($request->status == 1) {
                $surveyor->alamat = $request->alamat;
                $surveyor->hp = $request->no_wa;
                $surveyor->alasan = null;
            } else {
                $surveyor->alasan = $request->alasan;
                $surveyor->alamat = null;
                $surveyor->hp = null;
            }

            $surveyor->save();

            return response()->json(['message' => 'Data persetujuan berhasil disimpan.']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Data surveyor tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
