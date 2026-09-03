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
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->timestamp('face_verified_at')->nullable()->after('started_at');
            $table->decimal('face_similarity', 5, 2)->nullable()->after('face_verified_at');
            $table->string('verification_image')->nullable()->after('face_similarity');
            $table->string('exam_session_token', 64)->nullable()->unique()->after('verification_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropColumn([
                'face_verified_at',
                'face_similarity',
                'verification_image',
                'exam_session_token',
            ]);
        });
    }
};
