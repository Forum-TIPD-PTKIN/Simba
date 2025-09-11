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
        Schema::create('jadwal_kegiatans', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->charset = "utf8";
            $table->collation = "utf8_unicode_ci";

            $table->uuid("id")->unique();
            $table->uuid("tahun_kegiatan_id");
            $table->uuid("beasiswa_id");
            $table->enum("role", [
                'PENDAFTARAN',
                'SELEKSI_ADMINISTRASI',
                'PENGUMUMAN_SELEKSI_ADMINISTRASI',
                'SANGGAH_SELEKSI_ADMINISTRASI',
                'PENGUMUMAN_PASCA_SANGGAH_SELEKSI_ADMINISTRASI',
                'TES_POTENSI_AKADEMIK',
                'SURVEI_LOKASI',
                'PENGUMUMAN_AKHIR'
            ]);
            $table->string("nama", 100);
            $table->datetime("tanggal_mulai");
            $table->datetime("tanggal_selesai");
            $table->text("deskripsi")->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('jadwal_kegiatans');
    }
};