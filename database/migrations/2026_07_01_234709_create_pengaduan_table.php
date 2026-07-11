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
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->cascadeOnDelete();
            $table->enum('kategori', ['teknis', 'administrasi', 'lainnya'])->default('lainnya');
            $table->text('deskripsi');
            $table->string('lampiran_path', 255)->nullable();
            $table->enum('status', ['masuk', 'diproses', 'selesai'])->default('masuk');
            $table->text('respons_admin')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_respons')->nullable();
            $table->timestamps();

            $table->index('pelanggan_id');
            $table->index('status');
            $table->index('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};
