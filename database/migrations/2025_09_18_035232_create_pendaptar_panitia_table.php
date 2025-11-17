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
        Schema::create('pendaptar_panitia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('event')->onDelete('cascade');
            $table->string('kode_panitia')->unique()->nullable();
            $table->string('nama');
            $table->string('NIM')->length(16);
            $table->string('email');
            $table->string('nomor_whatapp')->length(14);
            $table->string('kelas');
            $table->string('angkatan')->length(4)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('ukuran_kaos')->nullable();
            $table->string('nomor_darurat')->length(14)->nullable();
            $table->string('tipe_nomor_darurat')->nullable();
            $table->string('riwayat_penyakit')->nullable();
            $table->string('divisi')->nullable();
            $table->enum('komitmen1', ['ya', 'tidak'])->nullable();
            $table->enum('komitmen2', ['ya', 'tidak'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaptar_panitia');
    }
};
