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

        Schema::table('pemberkasans', function (Blueprint $table) {
            $table->json('data')->after('pendaftar_id');
            $table->dropColumn('lengkap');
        });
        Schema::dropIfExists('pemberkasan_items');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('pemberkasans', function (Blueprint $table) {
            $table->boolean('lengkap')->default(false);
            $table->dropColumn('data');
        });
        Schema::create('pemberkasan_items', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->charset = "utf8";
            $table->collation = "utf8_unicode_ci";
            $table->uuid('id')->unique();
            $table->uuid('pemberkasan_id');
            $table->string('key');
            $table->string('name');
            $table->string('path');
            $table->string('md5');
            $table->integer('size');
            $table->string('extension', 5)->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
            $table->foreign('pemberkasan_id')
                ->references('id')
                ->on('pemberkasans')
                ->cascadeOnUpdate();
        });
    }
};
