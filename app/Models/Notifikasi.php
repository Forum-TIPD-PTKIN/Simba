<?php

namespace App\Models;

use App\Models\Scopes\LatestCreated;
use App\Models\Scopes\NotifikasiOrder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Uuid
{
    protected $fillable = [
        'user_id',
        'key',
        'pesan',
        'referensi',
        'dibaca',
    ];

    protected $appends = ['time_ago', 'tag', 'title'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTitleAttribute()
    {
        switch ($this->key) {
            case 'ASSIGN_SURVEYOR':
                return 'Undangan Surveyor';
            default:
                return $this->key;
        }
    }

    public function getTagAttribute()
    {
        return substr($this->pesan, 0, 60) . '...';
    }

    public function getTimeAgoAttribute()
    {
        $time_ago = strtotime($this->created_at->format('Y:m:d H:i:s')); // Convert the input to a Unix timestamp
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

    protected static function booted(): void
    {
        static::addGlobalScope(new NotifikasiOrder);
    }
}
