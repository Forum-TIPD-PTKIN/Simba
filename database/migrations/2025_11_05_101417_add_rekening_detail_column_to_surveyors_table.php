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
            $table->json('rekening_bank')->after('alamat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('surveyors', 'rekening_bank')) {
            Schema::table('surveyors', function (Blueprint $table) {
                $table->dropColumn('rekening_bank');
            });
        }
    }
};
