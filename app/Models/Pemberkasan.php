<?php

namespace App\Models;

class Pemberkasan extends Uuid
{
    protected $fillable = [
        'pendaftar_id',
        'data'
    ];

    public function getDataAttribute($val)
    {
        // $val = str_replace('[URL_ORIGIN]', url('file/'), $val);
        // return json_decode($val);

        $data = is_string($val) ? json_decode($val, true) : $val;

        // Pastikan $data array valid
        if (!is_array($data)) {
            return $data;
        }

        // Loop semua form (form_pendaftaran, form_keluarga, dll)
        foreach ($data as $formKey => $formValue) {
            if (is_array($formValue)) {
                // Jika di dalamnya ada banyak field (file_ktp, file_ijazah, dll)
                $data[$formKey] = collect($formValue)
                    ->sortBy(fn($item) => $item['index'] ?? 0)
                    ->all(); // pakai all() biar key aslinya tetap
            }
        }

        return json_decode(json_encode($data));
    }
}