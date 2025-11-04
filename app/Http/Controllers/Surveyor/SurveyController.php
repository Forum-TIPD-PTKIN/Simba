<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\Surveyor;
use App\Models\SurveyorDetail;
use App\Models\TahunKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $surveyor = Surveyor::with('tahun_kegiatan', 'beasiswa', 'user')
            ->whereUserId(Auth::id())
            ->orderByDesc(
                TahunKegiatan::select('tahun')
                    ->whereColumn('tahun_kegiatans.id', 'surveyors.tahun_kegiatan_id')
            )
            ->where('bersedia', 1)
            ->where('publish', 1)
            ->limit(7)
            ->get();

        if (request()->ajax()) {
            $data = SurveyorDetail::with(['pendaftar.user', 'pendaftar.mahasiswa', 'pendaftar.biodata_pendaftar'])
                ->whereHas('surveyor', function ($query) {
                    $query->where('bersedia', 1)
                        ->where('publish', 1)
                        ->whereUserId(Auth::id());
                })
                ->whereSurveyorId($request->surveyor_id);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('surveyor.survey.show', ['id' => $row->id]) . '" class="btn btn-info btn-sm"><i class="ti ti-eye"></i></a>';
                    return $btn;
                })
                ->addColumn('fakultas', function ($row) {
                    $fak = explode('|', $row->pendaftar->mahasiswa->fakultas);
                    $pro = explode('|', $row->pendaftar->mahasiswa->prodi);
                    return $fak[1] . ' - ' . $pro[1];
                })
                ->addColumn('progress', function ($row) {
                    $percentage = $row->progress ?? 0;
                    $colorClass = 'bg-light-danger'; // default red
                    if ($percentage > 75) {
                        $colorClass = 'bg-light-success'; // green
                    } elseif ($percentage > 50) {
                        $colorClass = 'bg-light-info'; // blue
                    } elseif ($percentage > 25) {
                        $colorClass = 'bg-light-warning'; // yellow
                    }
                    return '<span class="badge ' . $colorClass . '">' . $percentage . '%</span>';
                })
                ->rawColumns(['action', 'fakultas', 'progress'])
                ->make(true);
        }

        $master = collect($surveyor)->map(function ($item) {
            return (object)[
                'id' => $item->id,
                'tahun' => $item->tahun_kegiatan->tahun,
                'beasiswa' => $item->beasiswa->nama,
            ];
        })->toArray();

        $selectMasterDefault = $surveyor->first();

        return view('surveyor.survey.index', compact('master', 'selectMasterDefault'));
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
        return view('surveyor.survey.detail');
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
