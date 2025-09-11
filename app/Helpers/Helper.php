<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('getEnumValues')) {
    function getEnumValues(string $table, string $column): array
    {
        $type = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = ?", [$column])[0]->Type;

        preg_match('/enum\((.*)\)/', $type, $matches);

        return array_map(function ($value) {
            return trim($value, "'");
        }, explode(',', $matches[1]));
    }
}