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
        Schema::table('exams', function (Blueprint $table) {
            $table->integer('max_attempts')->nullable()->default(1)->change();
            $table->boolean('enable_anti_cheat')->default(true)->after('allow_review');
            $table->boolean('require_fullscreen')->default(true)->after('enable_anti_cheat');
            $table->boolean('prevent_tab_switch')->default(true)->after('require_fullscreen');
            $table->boolean('prevent_copy_paste')->default(true)->after('prevent_tab_switch');
            $table->boolean('prevent_right_click')->default(true)->after('prevent_copy_paste');
            $table->boolean('prevent_screen_capture')->default(true)->after('prevent_right_click');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn([
                'enable_anti_cheat',
                'require_fullscreen',
                'prevent_tab_switch',
                'prevent_copy_paste',
                'prevent_right_click',
                'prevent_screen_capture',
            ]);
        });
    }
};
