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
        Schema::create('permintaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nomor_permintaan')->unique();
            $table->date('tanggal');
            $table->text('catatan')->nullable();
            $table->string('status')->default('Menunggu');
            $table->timestamps();
        });

        Schema::create('permintaan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_id')->constrained('permintaans')->cascadeOnDelete();
            $table->string('nama_barang');
            $table->integer('jumlah')->default(1);
            $table->string('satuan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_items');
        Schema::dropIfExists('permintaans');
    }
};
