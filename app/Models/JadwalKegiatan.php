<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class JadwalKegiatan extends Uuid
{
    protected $appends = ['encrypted_id', 'tgl_mulai_formatted', 'tgl_selesai_formatted'];

    public function getEncryptedIdAttribute()
    {
        return Crypt::encryptString($this->id);
    }

    public function getTglMulaiFormattedAttribute()
    {
        return Carbon::parse($this->tanggal_mulai)->translatedFormat('d-m-Y H:i');
    }

    public function getTglSelesaiFormattedAttribute()
    {
        return Carbon::parse($this->tanggal_selesai)->translatedFormat('d-m-Y H:i');
    }

    public function formatTanggal($kolom, $format = 'd/m/Y')
    {
        return $this->{$kolom} ? Carbon::parse($this->{$kolom})->translatedFormat($format) : null;
    }

    public function scopeIs_Active($query)
    {
        return $query->where('tanggal_mulai', '<=', Carbon::now('Asia/Jakarta'))
            ->where('tanggal_selesai', '>=', Carbon::now('Asia/Jakarta'));
    }

    public function scopeIs_NotActive($query)
    {
        return $query->where('tanggal_selesai', '<', Carbon::now('Asia/Jakarta'));
    }

    public function tahun_kegiatan()
    {
        return $this->belongsTo(TahunKegiatan::class);
    }

    public function beasiswa()
    {
        return $this->belongsTo(Beasiswa::class);
    }
}