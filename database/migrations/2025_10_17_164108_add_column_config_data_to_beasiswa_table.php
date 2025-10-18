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
        Schema::table('beasiswas', function (Blueprint $table) {
            $table->json('config_data')->after('deskripsi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('beasiswas', 'config_data')) {
            Schema::table('beasiswas', function (Blueprint $table) {
                $table->dropColumn('config_data');
            });
        }
    }
};