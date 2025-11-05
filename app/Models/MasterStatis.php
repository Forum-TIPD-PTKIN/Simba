<?php

namespace App\Models;

class MasterStatis extends Uuid
{
    protected $fillable = ['nama', 'data'];

    public function getDataAttribute()
    {
        $raw = $this->attributes['data'] ?? null;
        $d = is_string($raw) ? json_decode($raw, true) : $raw;
        return $d;
    }
}
