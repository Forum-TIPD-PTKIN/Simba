<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BeasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.beasiswa');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $data = Beasiswa::all();

            return DataTables::of($data)
                ->editColumn('deskripsi', function ($data) {
                    return '<p>' . Str::words(strip_tags($data->deskripsi), 20, '...') . '</p>';
                })
                ->editColumn('status', function ($data) {
                    return '<span class="badge bg-' . ($data->status === 1 ? 'success' : 'danger') . '" style="font-size: 10px;">' . ($data->status === 1 ? 'Aktif' : 'Non Aktif') . '</span>';
                })
                ->addColumn('action', function ($data) {
                    return view('admin.template._action_button_table', [
                        'data' => $data,
                        'title' => 'Beasiswa',
                        'buttons' => [
                            'edit' => [
                                'title' => 'Sunting',
                                'icon' => 'ti ti-edit-circle',
                                'btn-class' => 'btn btn-primary',
                            ],
                            'delete' => [
                                'title' => 'Hapus',
                                'icon' => 'ti ti-trash',
                                'btn-class' => 'btn btn-danger',
                            ]
                        ]
                    ])
                        ->render();
                })
                ->rawColumns(['deskripsi', 'status', 'action'])
                ->make(true);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'status' => 'required'
        ], [
            'nama.required' => 'Nama beasiswa harus diisi',
            'status.required' => 'Status beasiswa harus dipilih'
        ]);

        try {
            $beasiswa = new Beasiswa();
            $beasiswa->nama = trim(strip_tags($request->nama));
            $beasiswa->deskripsi = $request->deskripsi;
            $beasiswa->status = $request->status === 'on' ? 1 : 0;
            $beasiswa->save();

            $data = array(
                'icon' => 'success',
                'title' => 'Berhasil',
                'message' => "Beasiswa {$beasiswa->nama} berhasil ditambahkan"
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
        $data = Beasiswa::find(Crypt::decryptString($id));

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama' => 'required',
            'status' => 'required'
        ], [
            'nama.required' => 'Nama beasiswa harus diisi',
            'status.required' => 'Status beasiswa harus dipilih'
        ]);

        $beasiswa = Beasiswa::find(Crypt::decryptString($id));
        try {
            $beasiswa->nama = trim(strip_tags($request->nama));
            $beasiswa->deskripsi = $request->deskripsi;
            $beasiswa->status = $request->status === 'on' ? 1 : 0;
            $beasiswa->update();

            $data = array(
                'icon' => 'success',
                'title' => 'Berhasil',
                'message' => "Data beasiswa {$beasiswa->nama} berhasil disunting"
            );

            return response()->json($data);
        } catch (\Illuminate\Database\QueryException $e) {
            $error = $e->errorInfo;
            $data['message'] = $error[2] ?: 'Ada kesalahan saat sunting data';

            return response()->json($data, 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $beasiswa = Beasiswa::find(Crypt::decryptString($id));

        $data = array();
        try {
            $proc = $beasiswa->delete();
            if ($proc) {
                $data['icon'] = 'success';
                $data['title'] = 'Berhasil';
                $data['message'] = 'Data beasiswa berhasil dihapus';
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $error = $e->errorInfo;
            $data['message'] = str_contains($error[2], 'constraint') ? 'Data tidak dapat dihapus, masih digunakan' : 'Ada Kesalahan saat menghapus data';

            return response()->json($data, 422);
        }

        return response()->json($data);
    }
}