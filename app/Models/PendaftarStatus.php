<?php

namespace App\Models;

class PendaftarStatus extends Uuid
{
    protected $fillable = [
        'pendaftar_id',
        'status',
        'deskripsi'
    ];

    protected $appends = ['deskripsi_json'];

    public function getDeskripsiJsonAttribute()
    {
        $value = $this->deskripsi;

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            return $decoded ?? $value;
        }

        return $value;
    }
}
