<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = ['access_active', 'status_label', 'status_badge_class'];

    public function getAccessAttribute($akses)
    {
        if (!$akses) return [0];

        return array_map('intval', explode(',', $akses));
    }

    public function getAccessActiveAttribute()
    {
        if (session()->has('level')) {
            return session()->get('level');
        }
        return null;
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            0 => 'danger',
            1 => 'success',
            default => 'success',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            0 => 'Non Aktif',
            1 => 'Aktif',
            default => 'Aktif',
        };
    }

    public function surveyor()
    {
        return $this->hasMany(Surveyor::class);
    }
}
