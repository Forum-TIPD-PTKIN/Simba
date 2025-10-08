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
                ->rawColumns(['access', 'action'])
                ->make(true);
        }
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