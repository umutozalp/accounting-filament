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
        Schema::create('voucher_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id');
            $table->foreignId('accounting_constant_id');
            $table->integer('borc_tutar');
            $table->integer('alacak_tutar');
            $table->string('fis_kalemi_aciklama');
            $table->decimal('islem_sonrasi_bakiye')->nullable();
            $table->date('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_items');
    }
};
