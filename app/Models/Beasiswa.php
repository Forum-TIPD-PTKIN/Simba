<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Crypt;

class Beasiswa extends Uuid
{
    protected $fillable = ['nama', 'deskripsi', 'status'];
    protected $hidden = ['id'];
    protected $appends = ['encrypted_id', 'config_data_json'];

    public function getEncryptedIdAttribute()
    {
        return Crypt::encryptString($this->id);
    }

    public function getConfigDataJsonAttribute()
    {
        if (is_string($this->config_data) && json_decode($this->config_data) !== null) {
            return json_decode($this->config_data, true);
        }
        return [];
    }

    public function scopeDescriptId($query, $enc)
    {
        $id = Crypt::decryptString($enc);
        $query->whereId($id);
    }

    public function jadwal_kegiatan()
    {
        return $this->hasMany(JadwalKegiatan::class);
    }

    public function pendaftar()
    {
        return $this->hasMany(Pendaftar::class);
    }

    public function scopeOrderByActiveRegistration(Builder $query)
    {
        return $query->orderByDesc(
            JadwalKegiatan::selectRaw('COUNT(*)')
                ->whereColumn('beasiswa_id', 'beasiswas.id')
                ->whereHas('tahun_kegiatan', function ($query) {
                    $query->where('status', 1);
                })
                // ->where('role', 'PENDAFTARAN')
                ->where('tanggal_mulai', '<=', now())
                ->where('tanggal_selesai', '>=', now())
        )->orderBy(
            JadwalKegiatan::select('tanggal_mulai')
                ->whereColumn('beasiswa_id', 'beasiswas.id')
                ->whereHas('tahun_kegiatan', function ($query) {
                    $query->where('status', 1);
                })
                // ->where('role', 'PENDAFTARAN')
                ->orderBy('tanggal_mulai', 'asc')
                ->limit(1)
        );
    }
}
