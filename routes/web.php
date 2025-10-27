<?php

use App\Http\Controllers\Admin\{
    DashboardController as DashboardAdmin,
    BeasiswaController,
    FormDataController,
    JadwalKegiatanController,
    LaporanController,
    PenggunaController,
    RekapController,
    TahunKegiatanController,
    TesPotensiAkademikController,
};
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\Pendaftar\{
    BiodataPendaftarController,
    DaftarController,
    DashboardController,
    PemberkasanController,
    RiwayatController,
    SeleksiAdministrasiController as PendaftarSeleksiAdministrasiController,
    TesPotensiAkademikController as PendaftarTesPotensiAkademikController
};
use App\Http\Controllers\Verifikator\{
    DashboardController as DashboardVerifikator,
    SeleksiAdministrasiController
};

use App\Http\Controllers\Penguji\Kip\{
    DashboardController as DashboardPengujiKip,
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

use function Livewire\store;

Route::get('/', function () {
    return redirect()->route('login');
});
Route::get("/login/sso", [LoginController::class, 'login_sso'])->name('login.sso');
Route::get("/login/secret", [LoginController::class, 'login_secret'])->name('login.secret');
Route::get("/login", [LoginController::class, 'login_view'])->name('login');
Route::post("/login", [LoginController::class, 'login'])->name('login.post');
Route::get("/logout", [LoginController::class, 'logout'])->name('logout');
Route::get("/akses/{access}", [LoginController::class, 'change_access'])->middleware(['auth'])->name('akses.ganti');

Route::post("/view-control", [LoginController::class, 'view_control'])->name('view.control');

// administrator
Route::group(['prefix' => 'administrator', 'middleware' => ['auth', 'isAdmin']], function () {
    Route::get('/', [DashboardAdmin::class, 'index'])->name('admin.dashboard');
    Route::get('/show/{tahun}/{beasiswa}', [DashboardAdmin::class, 'show'])->name('admin.dashboard.show');

    Route::resource('/tahun-kegiatan', TahunKegiatanController::class, [
        'names' => [
            'index'   => 'admin.tahun-kegiatan',
            'store'   => 'admin.tahun-kegiatan.store',
            'edit'    => 'admin.tahun-kegiatan.edit',
            'update'  => 'admin.tahun-kegiatan.update',
            'destroy' => 'admin.tahun-kegiatan.destroy'
        ]
    ]);

    Route::resource('/beasiswa', BeasiswaController::class, [
        'names' => [
            'index'   => 'admin.beasiswa',
            'store'   => 'admin.beasiswa.store',
            'create'  => 'admin.beasiswa.create',
            'edit'    => 'admin.beasiswa.edit',
            'update'  => 'admin.beasiswa.update',
            'destroy' => 'admin.beasiswa.destroy'
        ]
    ]);

    Route::resource('/jadwal-kegiatan', JadwalKegiatanController::class, [
        'names' => [
            'index' => 'admin.jadwal-kegiatan',
            'store' => 'admin.jadwal-kegiatan.store',
            'create' => 'admin.jadwal-kegiatan.create',
            'edit' => 'admin.jadwal-kegiatan.edit',
            'update' => 'admin.jadwal-kegiatan.update',
            'destroy' => 'admin.jadwal-kegiatan.destroy'
        ]
    ]);

    Route::post("/form-data/detail", [FormDataController::class, 'detail'])->name('admin.form-data.detail');
    Route::post("/form-data/copy", [FormDataController::class, 'copy'])->name('admin.form-data.copy');;
    Route::delete("/form-data", [FormDataController::class, 'destroy_master'])->name('admin.form-data.destroy.master');;
    Route::resource('/form-data', FormDataController::class, [
        'names' => [
            'index' => 'admin.form-data',
            'store' => 'admin.form-data.store',
            'create' => 'admin.form-data.create',
            'edit' => 'admin.form-data.edit',
            'update' => 'admin.form-data.update',
            'destroy' => 'admin.form-data.destroy'
        ]
    ]);

    Route::resource('/pengguna', PenggunaController::class, [
        'names' => [
            'index' => 'admin.pengguna',
            'store' => 'admin.pengguna.store',
            'create' => 'admin.pengguna.create',
            'edit' => 'admin.pengguna.edit',
            'update' => 'admin.pengguna.update',
            'destroy' => 'admin.pengguna.destroy'
        ]
    ]);

    Route::get('/notifikasi/{id}/show', [NotifikasiController::class, 'show'])->name('admin.notifikasi.show');
    Route::delete('/notifikasi', [NotifikasiController::class, 'destroy'])->name('admin.notifikasi.destroy');

    Route::get('/seleksi-tpa', [TesPotensiAkademikController::class, 'index'])->name('admin.seleksi-tpa');
    Route::post('/seleksi-tpa', [TesPotensiAkademikController::class, 'store'])->name('admin.seleksi-tpa.store');
    Route::post('/seleksi-tpa/data', [TesPotensiAkademikController::class, 'data'])->name('admin.seleksi-tpa.data');
    Route::get('/seleksi-tpa/daftar-hadir', [TesPotensiAkademikController::class, 'daftar_hadir'])->name('admin.seleksi-tpa.daftar-hadir');
    Route::get('/seleksi-tpa/{tahun}/{beasiswa}/show', [TesPotensiAkademikController::class, 'show'])->name('admin.seleksi-tpa.show');

    /* Laporan */
    Route::group(['prefix' => 'laporan'], function () {
        Route::get('/rekap', [RekapController::class, 'index'])->name('admin.laporan.rekap');
        Route::post('/rekap/data', [RekapController::class, 'data'])->name('admin.laporan.rekap.data');

        Route::get('/verifikasi', [LaporanController::class, 'verifikasi'])->name('admin.laporan.verifikasi');
        Route::get('/verifikasi/data', [LaporanController::class, 'data'])->name('admin.laporan.verifikasi.data');
        Route::get('/verifikasi/jadwal', [LaporanController::class, 'jadwal'])->name('admin.laporan.verifikasi.jadwal');
        Route::get('/verifikasi/{id}/edit', [LaporanController::class, 'edit'])->name('admin.laporan.verifikasi.edit');
        Route::put('/verifikasi/{id}', [LaporanController::class, 'update'])->name('admin.laporan.verifikasi.update');
    });
});

// Pendaftar
Route::group(['prefix' => 'pendaftar', 'middleware' => ['auth', 'isMahasiswa']], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('pendaftar.dashboard');
    Route::get('/beasiswa/{id}/detail', [DashboardController::class, 'show'])->name('pendaftar.detail-beasiswa');
    Route::get('/beasiswa/{status}', [DashboardController::class, 'beasiswa'])->name('pendaftar.beasiswa.status');
    Route::get('/jadwal-kegiatan', [DashboardController::class, 'jadwal'])->name('pendaftar.jadwal-kegiatan');

    Route::get('/daftar/{id}', [DaftarController::class, 'index'])->name('pendaftar.daftar');
    Route::post('/daftar/{id}', [DaftarController::class, 'store'])->name('pendaftar.daftar.store');
    Route::post('/daftar/finalisasi/{id}', [DaftarController::class, 'finalisasi'])->name('pendaftar.daftar.finalisasi');

    Route::group(['middleware' => 'zonaPendaftar'], function () {
        Route::get('/riwayat', [RiwayatController::class, 'index'])->name('pendaftar.riwayat');
        Route::post('/biodata', [BiodataPendaftarController::class, 'store'])->name('pendaftar.biodata.store');
        Route::get('/pemberkasan', [PemberkasanController::class, 'index'])->name('pendaftar.pemberkasan');
        Route::post('/pemberkasan', [PemberkasanController::class, 'store'])->name('pendaftar.pemberkasan.store');
        Route::get('/seleksi-administrasi', [PendaftarSeleksiAdministrasiController::class, 'index'])->name('pendaftar.seleksi-administrasi');

        Route::post('/seleksi-tpa/kartu-peserta', [PendaftarTesPotensiAkademikController::class, 'generate_kartu'])->name('pendaftar.seleksi-tpa.kartu-ujian');
    });

    Route::get('/notifikasi/{id}/show', [NotifikasiController::class, 'show'])->name('pendaftar.notifikasi.show');
    Route::delete('/notifikasi', [NotifikasiController::class, 'destroy'])->name('pendaftar.notifikasi.destroy');
});

// Verifikator
Route::group(['prefix' => 'verifikator', 'middleware' => ['auth', 'isVerifikator']], function () {
    Route::get('/', [DashboardVerifikator::class, 'index'])->name('verifikator.dashboard');
    Route::get('/show/{tahun}/{beasiswa}', [DashboardAdmin::class, 'show'])->name('verifikator.dashboard.show');

    Route::get('/seleksi-administrasi', [SeleksiAdministrasiController::class, 'index'])->name('verifikator.seleksi-administrasi');
    Route::get('/seleksi-administrasi/data', [SeleksiAdministrasiController::class, 'data'])->name('verifikator.seleksi-administrasi.data');
    Route::get('/seleksi-administrasi/jadwal', [SeleksiAdministrasiController::class, 'jadwal'])->name('verifikator.seleksi-administrasi.jadwal');
    Route::get('/seleksi-administrasi/{id}', [SeleksiAdministrasiController::class, 'edit'])->name('verifikator.seleksi-administrasi.edit');
    Route::post('/seleksi-administrasi', [SeleksiAdministrasiController::class, 'store'])->name('verifikator.seleksi-administrasi.store');

    Route::get('/hasil-seleksi-administrasi', [SeleksiAdministrasiController::class, 'rekap'])->name('verifikator.seleksi-administrasi.rekap');
    Route::get('/hasil-seleksi-administrasi/data', [SeleksiAdministrasiController::class, 'rekap_data'])->name('verifikator.seleksi-administrasi.rekap.data');

    Route::get('/notifikasi/{id}/show', [NotifikasiController::class, 'show'])->name('verifikator.notifikasi.show');
    Route::delete('/notifikasi', [NotifikasiController::class, 'destroy'])->name('verifikator.notifikasi.destroy');
});

// penguji
Route::group(['prefix' => 'penguji', 'middleware' => ['auth']], function () {
    Route::group(['prefix' => 'kip'], function () {
        Route::get('/', [DashboardPengujiKip::class, 'index'])->name('penguji.kip.dashboard');
    });
});

Route::get('file/{root}/{berkas}/{filename}', function ($root, $berkas, $filename) {
    $path = "$root/$berkas/" . $filename;
    if (!file_exists(storage_path('app/' . $path))) {
        abort(404, 'File not found');
    }
    return response()->file(storage_path('app/' . $path));
});