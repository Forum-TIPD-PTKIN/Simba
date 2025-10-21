<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaCbt extends Model
{
    protected $table = 'peserta';
    protected $connection = 'cbt';
    protected $primaryKey = 'no_test';
    public $timestamps = false;

    public function jenis_cbt()
    {
        return $this->belongsTo(JenisTesCbt::class, 'id_jenis_tes', 'id_jenis_tes');
    }

    public function jadwal_cbt()
    {
        return $this->belongsTo(JadwalCbt::class, 'id_jadwal', 'id_jadwal');
    }
}
