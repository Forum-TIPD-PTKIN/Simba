<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PenggunaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $access = [
            ['code' => 0, 'access' => 'Administrator'],
            ['code' => 1, 'access' => 'Verifikator'],
            ['code' => 3, 'access' => 'Surveyor']
        ];
        return view('admin.pengguna', compact('access'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $dt_access = [
                ['code' => 0, 'access' => 'Administrator'],
                ['code' => 1, 'access' => 'Verifikator'],
                ['code' => 3, 'access' => 'Surveyor']
            ];
            $data = User::whereNull('access')
                ->where('id', '!=', 1)
                ->orWhereNotLike('access', '%2%')
                ->get();

            return DataTables::of($data)
                ->editColumn('access', function ($data) use ($dt_access) {
                    $access = '<div class="d-flex flex-wrap justify-content-center gap-1">';
                    foreach ($data->access as $key => $value) {
                        $filter_access = array_filter($dt_access, fn($i) => $i['code'] === $value);
                        $selected_access = reset($filter_access);
                        if ($selected_access) {
                            $access .= "<span class='badge bg-primary' style='font-size:10px;'>{$selected_access['access']}</span>";
                        }
                    }
                    $access .= '</div>';

                    return $access;
                })
                ->addColumn('action', function ($data) {
                    return view('admin.template._action_button_table', [
                        'data' => $data,
                        'title' => 'Pengguna',
                        'buttons' => [
                            'edit' => [
                                'title' => 'Sunting',
                                'icon' => 'ti ti-edit-circle',
                                'btn-class' => 'btn btn-primary',
                                'encrypted_id' => $data->id,
                            ],
                            'delete' => [
                                'title' => 'Hapus',
                                'icon' => 'ti ti-trash',
                                'btn-class' => 'btn btn-danger',
                                'encrypted_id' => $data->id,
                            ]
                        ]
                    ])
                        ->render();
                })
                ->rawColumns(['access', 'action'])
                ->make(true);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pegawai' => 'required',
            'akses'   => 'required|array|min:1',
            'akses.*' => 'in:0,1,3'
        ], [
            'pegawai.required' => 'Pegawai belum dipilih',
            'akses.required' => 'Pilih minimal 1 akses',
            'akses.*.in' => 'Akses yang dipilih tidak valid'
        ]);

        try {
            $dt_pegawai = collect(api()->get("https://api.iainmadura.ac.id/api/pegawai?kode={$request->pegawai}")->data?->data)->first();

            $pengguna = new User();
            $pengguna->name = $dt_pegawai?->nama;
            $pengguna->username = $dt_pegawai?->kode;
            $pengguna->access = implode(',', $request->akses);
            $pengguna->save();

            return response()->json([
                'icon' => 'success',
                'title' => 'Berhasil',
                'message' => "{$dt_pegawai?->nama} berhasil ditambahkan"
            ]);
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
        $pengguna = User::findOrFail($id);

        return response()->json($pengguna);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'pegawai' => 'required',
            'akses'   => 'required|array|min:1',
            'akses.*' => 'in:0,1,3'
        ], [
            'pegawai.required' => 'Pegawai belum dipilih',
            'akses.required' => 'Pilih minimal 1 akses',
            'akses.*.in' => 'Akses yang dipilih tidak valid'
        ]);

        try {
            $dt_pegawai = collect(api()->get("https://api.iainmadura.ac.id/api/pegawai?kode={$request->pegawai}")->data?->data)->first();

            $pengguna = User::findOrFail($id);
            $pengguna->name = $dt_pegawai?->nama;
            $pengguna->username = $dt_pegawai?->kode;
            $pengguna->access = implode(',', $request->akses);
            $pengguna->update();

            return response()->json([
                'icon' => 'success',
                'title' => 'Berhasil',
                'message' => "{$dt_pegawai?->nama} berhasil disunting"
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            $error = $e->errorInfo;
            $data['message'] = $error[2] ?: 'Ada kesalahan saat menyimpan data';

            return response()->json($data, 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pengguna = User::findOrFail($id);

        $data = array();
        try {
            $proc = $pengguna->delete();
            if ($proc) {
                $data['icon'] = 'success';
                $data['title'] = 'Berhasil';
                $data['message'] = 'Pengguna berhasil dihapus';
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $error = $e->errorInfo;
            $data['message'] = str_contains($error[2], 'constraint') ? 'Data tidak dapat dihapus, masih digunakan' : 'Ada Kesalahan saat menghapus data';

            return response()->json($data, 422);
        }

        return response()->json($data);
    }
}