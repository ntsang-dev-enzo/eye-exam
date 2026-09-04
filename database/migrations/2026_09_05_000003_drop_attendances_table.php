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
        Schema::dropIfExists('attendances');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->date('attendance_date');
            $table->string('status')->default('present');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['class_id', 'student_id', 'attendance_date']);
        });
    }
};
