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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('fis_turu');
            $table->string('aciklama')->nullable;
            $table->date('yevmiye_tarih');
            $table->string('oda_birimi');
            $table->string('yevmiye_no');
            $table->string('makbuz_no');
            $table->string('islemi_yapan');
            $table->date('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
