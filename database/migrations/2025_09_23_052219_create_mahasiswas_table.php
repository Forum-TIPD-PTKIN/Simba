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
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->charset = "utf8";
            $table->collation = "utf8_unicode_ci";

            $table->uuid("id")->unique();
            $table->uuid("pendaftar_id");
            $table->string('nim', 16);
            $table->string('nama', 50);
            $table->string('fakultas', 32);
            $table->string('prodi', 32);
            $table->timestamps();

            $table->foreign("pendaftar_id")
                ->references("id")
                ->on("pendaftars")
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};
