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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('face_registered')->default(false)->after('status');
            $table->longText('face_embedding')->nullable()->after('face_registered');
            $table->json('face_images')->nullable()->after('face_embedding');
            $table->timestamp('face_registered_at')->nullable()->after('face_images');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'face_registered',
                'face_embedding',
                'face_images',
                'face_registered_at',
            ]);
        });
    }
};
