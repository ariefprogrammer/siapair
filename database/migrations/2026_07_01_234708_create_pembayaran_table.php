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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->unique()->constrained('tagihan')->cascadeOnDelete();
            $table->enum('metode', ['tunai', 'qris'])->default('tunai');
            $table->decimal('jumlah_bayar', 12, 2);

            // Kolom khusus TUNAI
            $table->foreignId('teller_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nomor_nota', 50)->nullable()->unique();

            // Kolom khusus QRIS
            $table->string('bukti_bayar_path', 255)->nullable();
            $table->enum('status_verifikasi', ['pending', 'disetujui', 'ditolak'])->nullable();
            $table->foreignId('admin_verifikasi_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamp('tanggal_verifikasi')->nullable();

            // Bersama
            $table->timestamp('tanggal_bayar')->useCurrent();
            $table->timestamps();

            $table->index('teller_id');
            $table->index('admin_verifikasi_id');
            $table->index('status_verifikasi');
            $table->index('metode');
            $table->index('tanggal_bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
