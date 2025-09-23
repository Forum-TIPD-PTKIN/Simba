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
        Schema::table('pendaftars', function (Blueprint $table) {

            $table->uuid("tahun_kegiatan_id")->after("beasiswa_id");
            $table
                ->foreign("tahun_kegiatan_id")
                ->references("id")
                ->on("tahun_kegiatans")
                ->onUpdate("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftars', function (Blueprint $table) {
            $table->dropForeign(['tahun_kegiatan_id']);
            $table->dropColumn('tahun_kegiatan_id');
        });
    }
};
