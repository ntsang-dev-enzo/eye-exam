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
            $table->boolean('require_face_verification')->default(true)->after('prevent_screen_capture');
            $table->boolean('enable_proctor_camera')->default(true)->after('require_face_verification');
            $table->integer('proctor_interval_seconds')->default(180)->after('enable_proctor_camera'); // default ~3 mins (180s)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn([
                'require_face_verification',
                'enable_proctor_camera',
                'proctor_interval_seconds',
            ]);
        });
    }
};
