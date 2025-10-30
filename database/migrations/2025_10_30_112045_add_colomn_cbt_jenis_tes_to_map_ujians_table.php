<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('map_ujians', function (Blueprint $table) {
            $table->string('cbt_jenis_tes', 100)->after('pendaftar_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('map_ujians', 'cbt_jenis_tes')) {
            Schema::table('map_ujians', function (Blueprint $table) {
                $table->dropColumn('cbt_jenis_tes');
            });
        }
    }
};