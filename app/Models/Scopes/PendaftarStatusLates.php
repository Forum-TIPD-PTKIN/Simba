<?php

namespace App\Models\Scopes;

use App\Models\PendaftarStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

class PendaftarStatusLates implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->addSelect([
            'latest_status' => PendaftarStatus::selectRaw("
                JSON_OBJECT(
                    'id', pendaftar_statuses.id,
                    'status', pendaftar_statuses.status,
                    'deskripsi', COALESCE(pendaftar_statuses.deskripsi, null)
                )
            ")
                ->whereColumn('pendaftar_statuses.pendaftar_id', $model->getTable() . '.id')
                ->orderByDesc('pendaftar_statuses.created_at')
                ->limit(1)
        ])
            ->addSelect(DB::raw("(
                SELECT JSON_OBJECTAGG(h.aspek, NULLIF(h.nilai, ''))
                FROM hasil_surveys h
                WHERE h.pendaftar_id = {$model->getTable()}.id
            ) AS data_survei"))
            ->orderBy($model->getTable() . '.created_at');
    }
}
