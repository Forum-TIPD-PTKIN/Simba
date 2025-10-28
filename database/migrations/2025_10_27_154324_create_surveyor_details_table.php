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
        Schema::create('surveyor_details', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->charset = "utf8";
            $table->collation = "utf8_unicode_ci";

            $table->uuid('id')->unique();
            $table->uuid('surveyor_id');
            $table->uuid('pendaftar_id');
            $table->timestamps();

            $table->foreign("surveyor_id")
                ->references("id")
                ->on("surveyors")
                ->cascadeOnUpdate();
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
        Schema::dropIfExists('surveyor_details');
    }
};
