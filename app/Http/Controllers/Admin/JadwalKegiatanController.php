<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\JadwalKegiatan;
use App\Models\TahunKegiatan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class JadwalKegiatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahun_kegiatan = TahunKegiatan::orderBy('tahun', 'desc')->get();
        $beasiswa = Beasiswa::where('beasiswas.status', 1)
            ->orderByActiveRegistration()
            ->get();
        $role_kegiatan = getEnumValues((new JadwalKegiatan())->getTable(), 'role');

        return view('admin.jadwal-kegiatan', [
            'tahun_kegiatan' => $tahun_kegiatan,
            'beasiswa' => $beasiswa,
            'role_kegiatan' => $role_kegiatan
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $data = JadwalKegiatan::with(['tahun_kegiatan', 'beasiswa'])
                ->where(function ($db) use ($request) {
                    if ($request->flt_tahun) {
                        return $db->where('tahun_kegiatan_id', $request->flt_tahun);
                    }

                    return $db->whereHas('tahun_kegiatan', function ($query) {
                        return $query->where('status', 1);
                    });
                })
                ->where(function ($db) use ($request) {
                    if ($request->flt_beasiswa) {
                        return $db->where('beasiswa_id', $request->flt_beasiswa);
                    }

                    return $db->whereHas('beasiswa', function ($query) {
                        return $query->whereRaw('1')->limit(1);
                    });
                })
                ->orderBy('tanggal_mulai', 'asc')
                ->get();

            return DataTables::of($data)
                ->editColumn('beasiswa', function ($data) {
                    return "
                        <div class='flex-grow-1'>
                          <div class='row g-1'>
                            <div class='col-12'>
                              <h6 class='mb-0'>{$data->beasiswa?->nama}</h6>
                              <p class='text-muted mb-0'><small>{$data->tahun_kegiatan?->tahun}</small></p>
                            </div>
                          </div>
                        </div>";
                })
                ->editColumn('tanggal_mulai', function ($data) {
                    return Carbon::parse($data->tanggal_mulai)->translatedFormat('d-m-Y H:i');
                })
                ->editColumn('tanggal_selesai', function ($data) {
                    return Carbon::parse($data->tanggal_selesai)->translatedFormat('d-m-Y H:i');
                })
                ->editColumn('deskripsi', function ($data) {
                    return '<p>' . Str::words(strip_tags($data->deskripsi), 10, '...') . '</p>';
                })
                ->addColumn('action', function ($data) {
                    return view('admin.template._action_button_table', [
                        'data' => $data,
                        'title' => 'Jadwal',
                        'showTitle' => false,
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
                ->rawColumns(['beasiswa', 'deskripsi', 'action'])
                ->make(true);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tahun'           => 'required|regex:/^[0-9]+$/',
            'beasiswa'        => 'required',
            'role'            => 'required',
            'nama'            => 'required',
            'tanggal_mulai'   => 'required',
            'tanggal_selesai' => 'required'
        ], [
            'tahun.required'           => 'Tahun kegiatan harus diisi',
            'tahun.regex'              => 'Tahun kegiatan harus angka',
            'beasiswa.required'        => 'Beasiswa harus dipilih',
            'role.required'            => 'Role kegiatan harus dipilih',
            'nama.required'            => 'Nama kegiatan harus diisi',
            'tanggal_mulai.required'   => 'Tanggal mulai harus diisi',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi'
        ]);

        try {
            $jadwal = new JadwalKegiatan();
            $jadwal->tahun_kegiatan_id = TahunKegiatan::where('tahun', trim(strip_tags($request->tahun)))->pluck('id')->first();
            $jadwal->beasiswa_id = trim(strip_tags($request->beasiswa));
            $jadwal->role = trim(strip_tags($request->role));
            $jadwal->nama = strtoupper(trim(strip_tags($request->nama)));
            $jadwal->tanggal_mulai = date('Y-m-d H:i:s', strtotime(str_ireplace('/', '-', trim(strip_tags($request->tanggal_mulai)))));
            $jadwal->tanggal_selesai = date('Y-m-d H:i:s', strtotime(str_ireplace('/', '-', trim(strip_tags($request->tanggal_selesai)))));
            $jadwal->deskripsi = $request->deskripsi;
            $jadwal->save();

            $data = array(
                'icon' => 'success',
                'title' => 'Berhasil',
                'message' => 'Jadwal kegiatan berhasil ditambahkan'
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
        $data = JadwalKegiatan::with(['beasiswa', 'tahun_kegiatan'])
            ->find(Crypt::decryptString($id));

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'tahun'           => 'required|regex:/^[0-9]+$/',
            'beasiswa'        => 'required',
            'role'            => 'required',
            'nama'            => 'required',
            'tanggal_mulai'   => 'required',
            'tanggal_selesai' => 'required'
        ], [
            'tahun.required'           => 'Tahun kegiatan harus diisi',
            'tahun.regex'              => 'Tahun kegiatan harus angka',
            'beasiswa.required'        => 'Beasiswa harus dipilih',
            'role.required'            => 'Role kegiatan harus dipilih',
            'nama.required'            => 'Nama kegiatan harus diisi',
            'tanggal_mulai.required'   => 'Tanggal mulai harus diisi',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi'
        ]);

        try {
            $jadwal = JadwalKegiatan::find(Crypt::decryptString($id));
            $jadwal->tahun_kegiatan_id = TahunKegiatan::where('tahun', trim(strip_tags($request->tahun)))->pluck('id')->first();
            $jadwal->beasiswa_id = trim(strip_tags($request->beasiswa));
            $jadwal->role = trim(strip_tags($request->role));
            $jadwal->nama = strtoupper(trim(strip_tags($request->nama)));
            $jadwal->tanggal_mulai = date('Y-m-d H:i:s', strtotime(str_ireplace('/', '-', trim(strip_tags($request->tanggal_mulai)))));
            $jadwal->tanggal_selesai = date('Y-m-d H:i:s', strtotime(str_ireplace('/', '-', trim(strip_tags($request->tanggal_selesai)))));
            $jadwal->deskripsi = $request->deskripsi;
            $jadwal->update();

            $data = array(
                'icon' => 'success',
                'title' => 'Berhasil',
                'message' => 'Jadwal kegiatan berhasil disunting'
            );

            return response()->json($data);
        } catch (\Illuminate\Database\QueryException $e) {
            $error = $e->errorInfo;
            $data['message'] = $error[2] ?: 'Ada kesalahan saat menyunting data';

            return response()->json($data, 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jadwal = JadwalKegiatan::find(Crypt::decryptString($id));

        $data = array();
        try {
            $proc = $jadwal->delete();
            if ($proc) {
                $data['icon'] = 'success';
                $data['title'] = 'Berhasil';
                $data['message'] = 'Data jadwal berhasil dihapus';
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $error = $e->errorInfo;
            $data['message'] = str_contains($error[2], 'constraint') ? 'Data tidak dapat dihapus, masih digunakan' : 'Ada Kesalahan saat menghapus data';

            return response()->json($data, 422);
        }

        return response()->json($data);
    }
}
