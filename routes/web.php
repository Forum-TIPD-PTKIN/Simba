<?php

use App\Http\Controllers\Admin\{
    BeasiswaController,
    TahunKegiatanController
};
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'administrator', 'middleware' => []], function () {
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
            'index' => 'admin.beasiswa',
            'store' => 'admin.beasiswa.store',
            'create' => 'admin.beasiswa.create',
            'edit' => 'admin.beasiswa.edit',
            'update' => 'admin.beasiswa.update',
            'destroy' => 'admin.beasiswa.destroy'
        ]
    ]);
});