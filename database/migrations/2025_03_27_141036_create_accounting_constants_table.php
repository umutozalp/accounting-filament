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
        Schema::create('accounting_constants', function (Blueprint $table) {
            $table->id();
            $table->string('hesap_kod');
            $table->bigInteger('hesap_kod_numeric');
            $table->string('hesap_ad');
            $table->string('hesap_turu');
            $table->decimal('bakiye',10,2);
            $table->date('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_constants');
    }
};
