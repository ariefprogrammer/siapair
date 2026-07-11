<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('config_general', function (Blueprint $table) {
            $table->string('app_name')->nullable()->after('id');
            $table->string('app_logo')->nullable()->after('app_name');
            $table->decimal('biaya_beban', 15, 2)->default(0)->after('ppn');
        });
    }

    public function down(): void
    {
        Schema::table('config_general', function (Blueprint $table) {
            $table->dropColumn(['app_name', 'app_logo', 'biaya_beban']);
        });
    }
};
