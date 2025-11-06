<?php

use App\Api;
use App\Helpers\Field;
use App\Helpers\FormHelper;
use App\Models\FormData;
use App\Models\JadwalKegiatan;
use Carbon\Carbon;
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
            case 3:
                return 'Surveyor';
            default:
                return 'Unknown';
        }
    }
}

if (!function_exists('cek_jadwal')) {
    function cek_jadwal(string $tahun_kegiatan, string $beasiswa, string $role, bool $is_active = true, bool $is_start = false)
    {
        $jadwal = JadwalKegiatan::when($is_active, function ($q) {
            $q->is_active();
        })
            ->when($is_start, function ($q) {
                $q->where('tanggal_mulai', '<=', Carbon::now('Asia/Jakarta'));
            })
            ->where('tahun_kegiatan_id', trim(strip_tags($tahun_kegiatan)))
            ->where('beasiswa_id', trim(strip_tags($beasiswa)))
            ->where('role', $role)
            ->count();

        return $jadwal > 0 ? true : false;
    }
}

if (!function_exists('textInitials')) {
    function textInitials(string $name): string
    {
        $words = preg_split('/\s+/', trim($name));
        $count = count($words);

        if ($count === 0) {
            return '';
        }

        if ($count === 1) {
            // Hanya 1 kata → huruf pertama
            return strtoupper(mb_substr($words[0], 0, 1));
        }

        if ($count === 2) {
            // 2 kata → huruf pertama kata1 + huruf pertama kata2
            return strtoupper(
                mb_substr($words[0], 0, 1) .
                    mb_substr($words[1], 0, 1)
            );
        }

        // 3 kata atau lebih → huruf pertama kata1 + huruf pertama kata3
        return strtoupper(
            mb_substr($words[0], 0, 1) .
                mb_substr($words[2], 0, 1)
        );
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
            case 3:
                return 'Surveyor';
            default:
                return 'Unknown';
        }
    }
}

if (!function_exists('iconFiles')) {
    function iconFiles($extension)
    {
        switch ($extension) {
            case 'pdf':
                return 'pdf.png';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return 'image.png';
            case 'txt':
                return 'txt.png';
            case 'zip':
            case 'rar':
                return 'zip.png';
            case 'doc':
                return 'doc.png';
            case 'docx':
                return 'docx.png';
            case 'xls':
                return 'xls.png';
            case 'xlsx':
                return 'xlsx.png';
            case 'csv':
                return 'csv.png';
            case 'ppt':
                return 'ppt.png';
            case 'pptx':
                return 'pptx.png';
        }
        return 'unknow.png';
    }
}


if (!function_exists('formatDateUpdateAt')) {
    function formatDateUpdateAt($date)
    {
        return Carbon::parse($date)->format('d/m/Y H:i');
    }
}
if (!function_exists('getTimeAgo')) {
    function getTimeAgo($date)
    {
        $time_ago = strtotime($date); // Convert the input to a Unix timestamp
        $current_time = time();
        $time_elapsed = $current_time - $time_ago;

        $seconds = $time_elapsed;
        $minutes = round($time_elapsed / 60);
        $hours = round($time_elapsed / 3600);
        $days = round($time_elapsed / 86400);
        $weeks = round($time_elapsed / 604800);
        $months = round($time_elapsed / 2600640);
        $years = round($time_elapsed / 31207680);

        // Detik
        if ($seconds <= 60) {
            return "skrg";          // sekarang
        }
        // Menit
        else if ($minutes <= 60) {
            return $minutes == 1 ? "1 mnt" : $minutes . " mnt";
        }
        // Jam
        else if ($hours <= 24) {
            return $hours == 1 ? "1 jam" : $hours . " jam";
        }
        // Hari
        else if ($days <= 7) {
            return $days == 1 ? "kmrn" : $days . " hr"; // kmrn = kemarin
        }
        // Minggu
        else if ($weeks <= 4.3) {
            return $weeks == 1 ? "1 mg" : $weeks . " mg"; // mg = minggu
        }
        // Bulan
        else if ($months <= 12) {
            return $months == 1 ? "1 bln" : $months . " bln";
        }
        // Tahun
        else {
            return $years == 1 ? "1 thn" : $years . " thn";
        }
    }
}
