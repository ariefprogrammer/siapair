<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catatan_meter', function (Blueprint $table) {
            $table->string('status_kondisi_new')->nullable()->after('status_kondisi');
        });

        DB::table('catatan_meter')->update([
            'status_kondisi_new' => DB::raw('status_kondisi'),
        ]);

        Schema::table('catatan_meter', function (Blueprint $table) {
            $table->dropColumn('status_kondisi');
        });

        Schema::table('catatan_meter', function (Blueprint $table) {
            $table->renameColumn('status_kondisi_new', 'status_kondisi');
        });

        DB::table('catatan_meter')
            ->whereNull('status_kondisi')
            ->update(['status_kondisi' => 'normal']);
    }

    public function down(): void
    {
        Schema::table('catatan_meter', function (Blueprint $table) {
            $table->string('status_kondisi_old')->nullable()->after('status_kondisi');
        });

        DB::table('catatan_meter')->update([
            'status_kondisi_old' => DB::raw('status_kondisi'),
        ]);

        Schema::table('catatan_meter', function (Blueprint $table) {
            $table->dropColumn('status_kondisi');
        });

        Schema::table('catatan_meter', function (Blueprint $table) {
            $table->enum('status_kondisi', ['normal', 'meteran_rusak', 'tidak_terbaca', 'tidak_tercatat', 'angka_minus', 'laju_tinggi'])
                ->default('normal')
                ->after('foto_path');
        });

        DB::table('catatan_meter')->update([
            'status_kondisi' => DB::raw('status_kondisi_old'),
        ]);

        Schema::table('catatan_meter', function (Blueprint $table) {
            $table->dropColumn('status_kondisi_old');
        });
    }
};