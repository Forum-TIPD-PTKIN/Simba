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
        Schema::table('form_data', function (Blueprint $table) {
            $table->uuid("tahun_kegiatan_id")->after("id");
            $table->uuid("beasiswa_id")->after("tahun_kegiatan_id");

            $table
                ->foreign("tahun_kegiatan_id")
                ->references("id")
                ->on("tahun_kegiatans")
                ->onUpdate("cascade");
            $table->foreign("beasiswa_id")
                ->references("id")
                ->on("beasiswas")
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('form_data', 'tahun_kegiatan_id')) {
            Schema::table('form_data', function (Blueprint $table) {
                $table->dropForeign(['tahun_kegiatan_id']);
                $table->dropColumn('tahun_kegiatan_id');
            });
        }

        if (Schema::hasColumn('form_data', 'beasiswa_id')) {
            Schema::table('form-data', function (Blueprint $table) {
                $table->dropForeign(['beasiswa_id']);
                $table->dropColumn('beasiswa_id');
            });
        }
    }
};