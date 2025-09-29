<?php

namespace App\Models;

use Illuminate\Support\Str;

class FormData extends Uuid
{
    protected $hidden = ['config'];
    protected $appends = ['config_json', 'kode'];
    public function getConfigJsonAttribute()
    {
        $value = $this->config;

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            return $decoded ?? $value;
        }

        return $value;
    }

    public function getKodeAttribute()
    {
        return Str::snake(strtolower($this->jenis));
    }
}
