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
        'rekening_bank',
    ];

    protected $appends = [
        'rekening_bank_formatted',
    ];

    public function getRekeningBankFormattedAttribute()
    {
        $value = str_replace('[URL_ORIGIN]', url('file/'), $this->rekening_bank);

        if (is_string($value) && json_decode($value) !== null) {
            $decoded = json_decode($value, true);

            return $decoded ?? $value;
        }

        return $value;
    }

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
