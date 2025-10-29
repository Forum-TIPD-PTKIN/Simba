<?php

namespace App\Models;

class SurveyorDetail extends Uuid
{
    protected $fillable = [
        'surveyor_id',
        'pendaftar_id',
    ];

    public function surveyor()
    {
        return $this->belongsTo(Surveyor::class);
    }

    public function pendaftar()
    {
        return $this->belongsTo(Pendaftar::class);
    }
}
