<?php

namespace App\Models;

class Surveyor extends Uuid
{
    protected $fillable = [
        'user_id',
        'beasiswa_id',
        'tahun_kegiatan_id',
        'bersedia',
        'publish',
        'alasan',
        'hp',
        'alamat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function beasiswa()
    {
        return $this->belongsTo(Beasiswa::class);
    }

    public function tahun_kegiatan()
    {
        return $this->belongsTo(TahunKegiatan::class);
    }

    public function surveyor_detail()
    {
        return $this->hasMany(SurveyorDetail::class);
    }
}
