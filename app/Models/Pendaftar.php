<?php

namespace App\Models;

use App\Models\Scopes\PendaftarStatusLast;
use App\Models\Scopes\PendaftarStatusLates;

class Pendaftar extends Uuid
{
    protected $fillable = [
        'beasiswa_id',
        'tahun_kegiatan_id',
        'user_id'
    ];

    public function pemberkasan()
    {
        return $this->hasOne(Pemberkasan::class);
    }

    public function biodata_pendaftar()
    {
        return $this->hasOne(BiodataPendaftar::class);
    }

    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class);
    }

    public function beasiswa()
    {
        return $this->belongsTo(Beasiswa::class);
    }

    public function tahun_kegiatan()
    {
        return $this->belongsTo(TahunKegiatan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pendaftar_status()
    {
        return $this->hasMany(PendaftarStatus::class)->orderByDesc('created_at');
    }

    public function map_ujian()
    {
        return $this->hasOne(MapUjian::class);
    }

    public function latestStatus()
    {
        return $this->hasOne(PendaftarStatus::class)->latestOfMany('created_at');
    }

    public function getLatestStatusAttribute($val)
    {
        return json_decode($val);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new PendaftarStatusLates);
    }
}
