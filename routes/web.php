<?php

use App\Http\Controllers\Admin\{
    BeasiswaController,
    FormDataController,
    JadwalKegiatanController,
    TahunKegiatanController,
};
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;


Route::get("/login/sso", [LoginController::class, 'login_sso'])->name('login.sso');

Route::group(['prefix' => 'administrator'], function () {
    Route::get('/', function () {
        return view('admin.index');
    })->name('admin.dashboard');

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
});
