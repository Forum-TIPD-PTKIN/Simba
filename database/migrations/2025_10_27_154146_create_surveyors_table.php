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
        Schema::create('surveyors', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->charset = "utf8";
            $table->collation = "utf8_unicode_ci";

            $table->uuid('id')->unique();
            $table->bigInteger('user_id')->unsigned();
            $table->uuid('beasiswa_id');
            $table->timestamps();

            $table->foreign("user_id")
                ->references("id")
                ->on("users")
                ->cascadeOnUpdate();
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
        Schema::dropIfExists('surveyors');
    }
};
