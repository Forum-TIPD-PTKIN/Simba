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
        Schema::create('map_ujians', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->charset = "utf8";
            $table->collation = "utf8_unicode_ci";

            $table->uuid('id')->unique();
            $table->uuid('pendaftar_id');
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai');
            $table->string('sesi', 5);
            $table->string('ruang', 5);
            $table->decimal('nilai', 5, 2)->nullable();
            $table->timestamps();

            $table->foreign("pendaftar_id")
                ->references("id")
                ->on("pendaftars")
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('map_ujians');
    }
};
