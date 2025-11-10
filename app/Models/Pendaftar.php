<?php

namespace App\Models;

use App\Models\Scopes\PendaftarStatusLates;

class Pendaftar extends Uuid
{
    protected static $masterStatic = null;
    protected $fillable = [
        'beasiswa_id',
        'tahun_kegiatan_id',
        'user_id'
    ];

    protected $hidden = ['data_survei'];

    protected $appends = ['hasil_survei'];

    public function pemberkasan()
    {
        return $this->hasOne(Pemberkasan::class);
    }

    public function biodata_pendaftar()
    {
        return $this->hasOne(BiodataPendaftar::class);
    }

    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class);
    }

    public function beasiswa()
    {
        return $this->belongsTo(Beasiswa::class);
    }

    public function tahun_kegiatan()
    {
        return $this->belongsTo(TahunKegiatan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pendaftar_status()
    {
        return $this->hasMany(PendaftarStatus::class)->orderByDesc('created_at');
    }

    public function map_ujian()
    {
        return $this->hasOne(MapUjian::class);
    }

    public function surveyor_detail()
    {
        return $this->hasOne(SurveyorDetail::class);
    }

    public function hasil_survey()
    {
        return $this->hasMany(HasilSurvey::class);
    }

    public function latestStatus()
    {
        return $this->hasOne(PendaftarStatus::class)->latestOfMany('created_at');
    }

    public function getHasilSurveiAttribute()
    {
        // kalau sudah ada di cache, kembalikan
        if (!static::$masterStatic) {
            static::$masterStatic = MasterStatis::all();
        }
        if (!$this->attributes['data_survei']) return (object)[
            'nilai' => null,
            'persen' => 0,
            'point' => 0
        ];

        $hasil = json_decode($this->attributes['data_survei']);

        $keys = [
            "ibuNama",
            "ayahKesehatan",
            "ayahNama",
            "kondisiWc",
            "ibuKondisi",
            "lantaiRumah",
            "ibuPekerjaan",
            "kondisiDapur",
            "kondisiRumah",
            "ayahPekerjaan",
            "bangunanRumah",
            "ibuPenghasilan",
            "ayahPenghasilan",
            "kepemilikanRumah",
            "kondisiKamarMandi",
            "kepemilikanListrik",
            "tanggunganKeluarga",
            'berkasGdrive'
        ];
        $keyPoints = [
            'ayahKesehatan',
            'ayahPekerjaan',
            'ayahPenghasilan',
            'bangunanRumah',
            'ibuKondisi',
            'ibuPekerjaan',
            'ibuPenghasilan',
            'kepemilikanListrik',
            'kepemilikanRumah',
            'kondisiDapur',
            'kondisiKamarMandi',
            'kondisiWc',
            'lantaiRumah',
            'tanggunganKeluarga'
        ];
        $isString = ['ibuNama', 'ayahNama', 'kondisiRumah', 'catatan', 'berkasGdrive'];
        $valueAutoScore = ['ayahPekerjaan', 'ibuPekerjaan'];

        $keyMasterStatis = (object)[
            'ayahPekerjaan' => 'pekerjaan',
            'ibuPekerjaan' => 'pekerjaan',
            'ayahPenghasilan' => 'penghasilan',
            'ibuPenghasilan' => 'penghasilan',
            'kepemilikanListrik' => 'kepemilikan_listrik',
            'kepemilikanRumah' => 'kepemilikan_rumah',
            'bangunanRumah' => 'bangunan_rumah',
            'lantaiRumah' => 'lantai_rumah'
        ];

        // // tangani khusus nilai "LAINNYA:" untuk pekerjaan
        foreach ($valueAutoScore as $pekerjaanKey) {
            if (isset($hasil->$pekerjaanKey) && is_string($hasil->$pekerjaanKey)) {
                if (stripos($hasil->$pekerjaanKey, 'LAINNYA:') === 0) {
                    $hasil->$pekerjaanKey = 6.5;
                }
            }
        }
        foreach ($hasil as $key => $value) {
            if (!in_array($key, $isString)) {
                if (is_string($value) && $value !== '') {
                    $nilai = floatval($value);
                    $nd = collect(static::$masterStatic)->where('nama', $keyMasterStatis->{$key} ?? '??')->first();
                    if ($nd && $nd->data) {
                        $nd1 = collect($nd->data)->where('value', $nilai)->first();

                        if ($nd1) {
                            $hasil->$key = (object)[
                                'text' => $nd1['label'],
                                'value' => $nilai
                            ];
                        } else {
                            $hasil->$key = (object)[
                                'text' => '??',
                                'value' => $nilai
                            ];
                        }
                    } else {
                        $nilai2 = 10 - $nilai;
                        $persen = $nilai2 * 10;
                        if ($nilai2 >= 9) {
                            $kategori = 'Sangat Baik';
                        } elseif ($nilai2 >= 7) {
                            $kategori = 'Baik';
                        } elseif ($nilai2 >= 5) {
                            $kategori = 'Cukup';
                        } elseif ($nilai2 >= 3) {
                            $kategori = 'Kurang';
                        } else {
                            $kategori = 'Perlu Perhatian';
                        }
                        $hasil->$key = (object)[
                            'text' => "$kategori ($persen%)",
                            'value' => $nilai
                        ];
                    }
                } elseif ($value === '' || $value === null) {
                    $hasil->$key = null;
                }
            }
        }
        $totalKey = count($keys);
        $filledCount = 0;

        $point = 0;
        foreach ($keys as $key) {
            if (isset($hasil->$key) && $hasil->$key !== '' && $hasil->$key !== null) {
                $filledCount++;
            }
            if (in_array($key, $keyPoints)) {
                $point += $hasil->$key->value;
            }
        }

        $persen = ($filledCount / $totalKey) * 100;

        return (object)[
            'nilai' => $hasil,
            'persen' => round($persen, 2),
            'point' => $point
        ];
    }

    public function getLatestStatusAttribute($val)
    {
        return json_decode($val);
    }

    public function scopeSomeStatus($query, $status)
    {
        return $query->when($status, function ($query, $status) {
            $query->whereHas('latestStatus', fn($q) => $q->where('status', $status));
        });
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new PendaftarStatusLates);
    }
}
