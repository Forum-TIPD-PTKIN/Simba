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
        Schema::create('hasil_surveys', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->charset = "utf8";
            $table->collation = "utf8_unicode_ci";

            $table->uuid('id')->unique();
            $table->uuid('pendaftar_id');
            $table->string('aspek', 32);
            $table->boolean('sesuai')->nullable();
            $table->string('nilai');
            $table->timestamps();

            $table->foreign("pendaftar_id")
                ->references("id")
                ->on("pendaftars")
                ->cascadeOnUpdate();
        });

        Schema::table('pendaftars', function (Blueprint $table) {
            $table->float('persen_survey')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_surveys');
        Schema::table('pendaftars', function (Blueprint $table) {
            $table->dropColumn('persen_survey');
        });
    }
};
