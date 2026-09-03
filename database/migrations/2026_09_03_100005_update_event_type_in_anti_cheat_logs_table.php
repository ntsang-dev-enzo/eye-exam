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
        Schema::table('anti_cheat_logs', function (Blueprint $table) {
            $table->string('event_type', 50)->change();
            $table->string('snapshot_path')->nullable()->after('event_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anti_cheat_logs', function (Blueprint $table) {
            $table->dropColumn('snapshot_path');
        });
    }
};
