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
        Schema::table('pemberkasan_items', function (Blueprint $table) {
            $table->string("link_direct")->nullable()->after("pemberkasan_id");
            $table->string('key')->nullable()->change();
            $table->string('name')->nullable()->change();
            $table->string('path')->nullable()->change();
            $table->string('md5')->nullable()->change();
            $table->integer('size')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemberkasan_items', function (Blueprint $table) {
            $table->dropColumn('link_direct');
            $table->string('key')->nullable()->change();
            $table->string('name')->nullable(false)->change();
            $table->string('path')->nullable(false)->change();
            $table->string('md5')->nullable(false)->change();
            $table->integer('size')->nullable(false)->change();
        });
    }
};
