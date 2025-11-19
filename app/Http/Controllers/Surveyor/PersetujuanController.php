<?php

namespace App\Http\Controllers\Surveyor;

use App\Helpers\UploadFileHelper;
use App\Http\Controllers\Controller;
use App\Models\Surveyor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class PersetujuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('surveyor.persetujuan');
    }

    public function data()
    {
        $query = Surveyor::with(['beasiswa', 'tahun_kegiatan'])
            ->where('user_id', Auth::id());

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                if (is_null($row->bersedia)) {
                    return '<span class="badge bg-light-warning">Menunggu Persetujuan</span>';
                }
                if ($row->bersedia == 1) {
                    return '<span class="badge bg-light-success">Bersedia</span>';
                }
                return '<span class="badge bg-light-danger">Tidak Bersedia</span>';
            })
            ->addColumn('action', function ($row) {
                if (is_null($row->bersedia)) {
                    return '<a href="' . route('surveyor.persetujuan.show', ['persetujuan' => $row->id]) . '" class="btn btn-primary btn-sm">Detail</a>';
                } else if ($row->bersedia === 0 && $row->alasan) {
                    return '<i><strong>Alasan : </strong> ' . $row->alasan . '</i>';
                }
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
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
            'bersedia' => 'required|boolean',
            'alamat' => 'required_if:bersedia,1|string|nullable',
            'no_wa' => 'required_if:bersedia,1|string|nullable',
            'alasan' => 'string|nullable',
        ]);

        try {
            $surveyor = Surveyor::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

            // Prevent re-submission
            if ($surveyor->bersedia !== null && !$request->has('update_rekening')) {
                return response()->json(['message' => 'Anda sudah mengirimkan tanggapan sebelumnya.'], 422);
            }

            $surveyor->bersedia = $request->bersedia;
            if ($request->bersedia == 1) {
                $no_rekening = $request->input('no_rekening');
                $nama_rekening = $request->input('nama_rekening');
                $nama_bank = $request->input('nama_bank');
                $file_rekening = UploadFileHelper::upload($request->file_rekening, 'rekening_bank/surveyor');
                $rekening_bank_data = [
                    'no_rekening' => $no_rekening,
                    'nama_rekening' => $nama_rekening,
                    'nama_bank' => $nama_bank,
                    'file_rekening' => '[URL_ORIGIN]/' . $file_rekening,
                ];

                $surveyor->alamat = $request->alamat;
                $surveyor->hp = $request->no_wa;
                $surveyor->rekening_bank = json_encode($rekening_bank_data);
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
        } catch (\Illuminate\Database\QueryException $e) {
            $error = $e->errorInfo;
            return response()->json(['message' => 'Terjadi kesalahan pada server: ' . $error[2]], 500);
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
