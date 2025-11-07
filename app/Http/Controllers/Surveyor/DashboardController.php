<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa;
use App\Models\Pendaftar;
use App\Models\Surveyor;
use App\Models\TahunKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahun_kegiatan = TahunKegiatan::orderBy('tahun', 'desc')->get();
        $beasiswa = Beasiswa::where('status', 1)->get();

        $responden = $this->data(count($tahun_kegiatan) ? $tahun_kegiatan[0]->id : null, count($beasiswa) ? $beasiswa[0]->id : null);
        $view_daftar_responden = view('surveyor.dashboard.daftar-responden', [
            'responden' => $responden
        ])->render();

        return view('surveyor.dashboard', [
            'tahun_kegiatan' => $tahun_kegiatan,
            'beasiswa' => $beasiswa,
            'view_daftar_responden' => $view_daftar_responden
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
    public function show(string $tahun, string $beasiswa)
    {
        $responden = $this->data($tahun, $beasiswa);

        $view_daftar_responden = view('surveyor.dashboard.daftar-responden', [
            'responden' => $responden
        ])->render();

        return $view_daftar_responden;
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

    public static function data($tahun, $beasiswa)
    {
        $baseQuery = Pendaftar::with(['mahasiswa', 'beasiswa', 'tahun_kegiatan', 'biodata_pendaftar'])
            ->where('tahun_kegiatan_id', $tahun)
            ->where('beasiswa_id', $beasiswa)
            ->whereHas('pendaftar_status', function ($q) {
                $q->where('status', 'LOLOS TPA');
            })
            ->whereHas('surveyor_detail', function ($query) {
                $query->where('surveyor_id', Surveyor::where('user_id', Auth::id())->pluck('id'))
                    ->whereHas('surveyor', function ($surveyorQuery) {
                        $surveyorQuery->where('publish', '1');
                    });
            });

        $total_responden = (clone $baseQuery)->count();
        $sudah_disurvei = (clone $baseQuery)->whereHas('hasil_survey')->count();
        $belum_disurvei = $total_responden - $sudah_disurvei;

        return [
            'total_responden' => $total_responden,
            'sudah_disurvei' => $sudah_disurvei,
            'belum_disurvei' => $belum_disurvei
        ];
    }
}