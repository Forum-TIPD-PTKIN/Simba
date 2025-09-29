<?php

use App\Api;
use App\Helpers\Field;
use App\Helpers\FormHelper;
use App\Models\FormData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

if (!function_exists('getEnumValues')) {
    function getEnumValues(string $table, string $column): array
    {
        $type = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = ?", [$column])[0]->Type;

        preg_match('/enum\((.*)\)/', $type, $matches);

        return array_map(function ($value) {
            return trim($value, "'");
        }, explode(',', $matches[1]));
    }
}

if (!function_exists('api')) {
    function api(): Api
    {
        return new Api();
    }
}

if (!function_exists('form')) {
    function form(string $jenis, string|Field|array|null $beasiswa = null, string|null $tahun_kegiatan = null): FormHelper
    {
        return new FormHelper($jenis, $beasiswa, $tahun_kegiatan);
    }
}


if (!function_exists('userAccessName')) {
    function userAccessName()
    {
        $access = Auth::user()->access_active;
        switch ($access) {
            case 0:
                return 'Administrator';
            case 1:
                return 'Verifikator';
            case 2:
                return 'Mahasiswa';
            default:
                return 'Unknow';
        }
    }
}

if (!function_exists('formPemberkasan')) {
    function formPemberkasan($beasiswaId)
    {
        static $cacheFormPendaftaran = null;

        if ($cacheFormPendaftaran === null) {
            $cacheFormPendaftaran = FormData::all()->groupBy('beasiswa_id');
        }

        $masterForm = [(object)[
            'name' => 'ktp',
            'label' => 'KTP',
            'formData' => (object)[
                'jenis' => 'FORM PENDAFTARAN',
                'name' => 'file_ktp',
            ]
        ]];
        $access = Auth::user()->access_active;
        switch ($access) {
            case 0:
                return 'Administrator';
            case 1:
                return 'Verifikator';
            case 2:
                return 'Mahasiswa';
            default:
                return 'Unknow';
        }
    }
}
