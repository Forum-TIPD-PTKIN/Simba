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
        Schema::table('surveyors', function (Blueprint $table) {
            $table->uuid('tahun_kegiatan_id')->after('beasiswa_id');
            $table->boolean('bersedia')->nullable()->after('tahun_kegiatan_id');
            $table->text('alasan')->nullable()->after('bersedia');
            $table->string('hp', 15)->nullable()->after('alasan');
            $table->string('alamat')->nullable()->after('hp');

            $table->foreign('tahun_kegiatan_id')
                ->references('id')
                ->on('tahun_kegiatans')
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surveyors', function (Blueprint $table) {

            $table->dropForeign('surveyors_tahun_kegiatan_id_foreign');
            $table->dropColumn('tahun_kegiatan_id');
            $table->dropColumn('bersedia');
            $table->dropColumn('alasan');
            $table->dropColumn('alamat');
            $table->dropColumn('hp');
        });
    }
};
