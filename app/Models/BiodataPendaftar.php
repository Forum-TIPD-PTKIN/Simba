<?php

namespace App\Models;

class BiodataPendaftar extends Uuid
{
    protected $fillable = [
        'pendaftar_id',
        'data'
    ];

    public function getDataAttribute($val)
    {
        $data = is_string($val) ? json_decode($val, true) : $val;

        // Pastikan $data array valid
        if (!is_array($data)) {
            return $data;
        }

        // Loop semua form (form_pendaftaran, form_keluarga, dll)
        foreach ($data as $formKey => $formValue) {
            if (is_array($formValue)) {
                $data[$formKey] = collect($formValue)
                    ->sortBy(fn($item) => $item['index'] ?? 0)
                    ->all(); // pakai all() biar key aslinya tetap
            }
        }

        return json_decode(json_encode($data));
    }

    public function pendaftar()
    {
        return $this->belongsTo(Pendaftar::class);
    }
}
