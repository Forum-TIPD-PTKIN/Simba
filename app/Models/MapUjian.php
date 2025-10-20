<?php

namespace App\Models;

class MapUjian extends Uuid
{
    public function pendaftar()
    {
        return $this->belongsTo(Pendaftar::class);
    }
}
