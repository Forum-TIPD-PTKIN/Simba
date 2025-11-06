<?php

namespace App\Models;

class HasilSurvey extends Uuid
{
    protected $fillable = [
        'pendaftar_id',
        'aspek',
        'nilai',
        'sesuai'
    ];
}
