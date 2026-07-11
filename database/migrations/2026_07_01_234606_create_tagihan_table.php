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
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->cascadeOnDelete();
            $table->foreignId('catatan_meter_id')->unique()->constrained('catatan_meter')->cascadeOnDelete();
            $table->foreignId('periode_id')->constrained('periode_pencatatan')->restrictOnDelete();
            $table->decimal('total_pemakaian', 10, 2)->default(0);
            $table->json('breakdown_tarif');
            $table->decimal('biaya_air', 12, 2)->default(0);
            $table->decimal('biaya_admin', 10, 2)->default(0);
            $table->decimal('total_tagihan', 12, 2)->default(0);
            $table->enum('status', [
                'belum_dibayar', 'menunggu_verifikasi', 'lunas',
            ])->default('belum_dibayar');
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->timestamps();

            $table->index('pelanggan_id');
            $table->index('periode_id');
            $table->index('status');
            $table->index('tanggal_jatuh_tempo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
