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
        Schema::create('tarif_progresif', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('tier')->unique();
            $table->decimal('batas_bawah', 8, 2)->comment('m³ awal tier (inklusif)');
            $table->decimal('batas_atas', 8, 2)->nullable()->comment('m³ akhir tier, NULL = unlimited');
            $table->decimal('harga_per_m3', 10, 2);
            $table->string('keterangan', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarif_progresif');
    }
};
