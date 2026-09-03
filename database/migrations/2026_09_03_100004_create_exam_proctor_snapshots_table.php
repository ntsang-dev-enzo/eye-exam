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
        Schema::create('exam_proctor_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('exam_attempts')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->string('image_path');
            $table->enum('status', ['normal', 'warning', 'violation'])->default('normal');
            $table->json('violations')->nullable();
            $table->json('detections')->nullable(); // bounding boxes, labels, confidence
            $table->decimal('face_similarity', 5, 2)->nullable();
            $table->text('details')->nullable();
            $table->timestamp('captured_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_proctor_snapshots');
    }
};
