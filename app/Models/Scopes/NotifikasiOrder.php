<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class NotifikasiOrder implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->orderBy($model->getTable() . '.dibaca', 'asc')
            ->orderBy($model->getTable() . '.created_at', 'desc');
    }
}
