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
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->charset = "utf8";
            $table->collation = "utf8_unicode_ci";
            $table->uuid("id")->unique();
            $table->string("key", 32);
            $table->bigInteger('user_id')->unsigned();
            $table->text("pesan");
            $table->string("referensi")->nullable();
            $table->boolean("dibaca");
            $table->timestamps();
        });


        /* 
        
        Notifikasi::create([
            'user_id' => $atasan->atasan->user_id,
            'key' => 'APPROVAL_ACCEPTED',
            'pesan'   => 'Pengajuan atasan anda diterima',
            'dibaca' => 0,
            'referensi' => 'user/skp/atasan/approval/' . $atasan->id
        ]);


        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
};
