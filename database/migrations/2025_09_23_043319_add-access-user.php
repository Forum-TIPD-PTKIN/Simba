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
        Schema::table('users', function (Blueprint $table) {
            /* 
            akses berupa angka
            0 = admin
            1 = verifikator
            2 = mahasiswa

            ** jika banya akses dalam 1 user maka pisah dengan tanda koma, ex: 0,1,2
            ** jika null maka itu adalah admin
            */
            $table->string("access")->nullable()->after("remember_token");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form-users', function (Blueprint $table) {
            $table->dropColumn('access');
        });
    }
};
