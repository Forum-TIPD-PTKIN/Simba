<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisTesCbt extends Model
{
    protected $table = 'jenis_tes';
    protected $connection = 'cbt';
    protected $primaryKey = 'id_jenis_tes';
    public $incrementing = false;
    protected $keyType = 'string';

    public function jadwal()
    {
        return $this->hasMany(JadwalCbt::class, 'id_jenis_tes', 'id_jenis_tes');
    }
}
