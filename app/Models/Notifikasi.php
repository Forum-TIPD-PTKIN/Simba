<?php

namespace App\Models;

use App\Models\Scopes\NotifikasiOrder;

class Notifikasi extends Uuid
{
    protected $fillable = [
        'user_id',
        'key',
        'pesan',
        'referensi',
        'dibaca',
    ];

    protected $appends = ['time_ago', 'tag', 'title'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTitleAttribute()
    {
        switch ($this->key) {
            case 'ASSIGN_SURVEYOR':
                return 'Undangan Surveyor';
            default:
                return $this->key;
        }
    }

    public function getTagAttribute()
    {
        return substr($this->pesan, 0, 60) . '...';
    }

    public function getTimeAgoAttribute()
    {
        return getTimeAgo($this->created_at);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new NotifikasiOrder);
    }
}
