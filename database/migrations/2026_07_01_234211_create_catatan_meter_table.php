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
        Schema::create('catatan_meter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->cascadeOnDelete();
            $table->foreignId('operator_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('periode_id')->constrained('periode_pencatatan')->restrictOnDelete();
            $table->decimal('angka_meter_lalu', 10, 2)->default(0);
            $table->decimal('angka_meter_sekarang', 10, 2);
            $table->decimal('pemakaian', 10, 2)->virtualAs('angka_meter_sekarang - angka_meter_lalu');
            $table->enum('status_kondisi', [
                'normal', 'meteran_rusak', 'tidak_terbaca',
                'tidak_tercatat', 'angka_minus', 'laju_tinggi',
            ])->default('normal');
            $table->text('catatan')->nullable();
            $table->string('foto_path', 255)->nullable();
            $table->timestamp('dicatat_at')->useCurrent();
            $table->timestamps();

            $table->unique(['pelanggan_id', 'periode_id']);
            $table->index('operator_id');
            $table->index('periode_id');
            $table->index('status_kondisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatan_meter');
    }
};
