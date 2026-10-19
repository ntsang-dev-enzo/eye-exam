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
        // 1. faculties
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 255);
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });

        // 2. departments
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained('faculties')->onDelete('restrict');
            $table->string('code', 50)->unique();
            $table->string('name', 255);
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });

        // 3. semesters
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 50); // VD: Học kỳ 1, Học kỳ 2
            $table->string('academic_year', 20); // VD: 2025-2026
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->timestamps();

            $table->unique(['name', 'academic_year'], 'unique_name_academic_year');
        });

        // 4. Update subjects
        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('id')->constrained('departments')->onDelete('restrict');
        });

        // 5. subject_teacher (normalized name for qualification)
        if (!Schema::hasTable('subject_teacher')) {
            Schema::create('subject_teacher', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
                $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['subject_id', 'teacher_id']);
            });
        }

        // 6. Update classes
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->after('name')->constrained('subjects')->onDelete('restrict');
            $table->foreignId('semester_id')->nullable()->after('subject_id')->constrained('semesters')->onDelete('restrict');
            $table->string('room', 100)->nullable()->after('teacher_id');
            $table->unsignedInteger('max_students')->default(60)->after('room');
        });

        // 7. Update class_students
        Schema::table('class_students', function (Blueprint $table) {
            if (!Schema::hasColumn('class_students', 'enrolled_at')) {
                $table->timestamp('enrolled_at')->nullable()->after('student_id');
            }
            if (!Schema::hasColumn('class_students', 'status')) {
                $table->string('status', 20)->default('enrolled')->after('enrolled_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_students', function (Blueprint $table) {
            if (Schema::hasColumn('class_students', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('class_students', 'enrolled_at')) {
                $table->dropColumn('enrolled_at');
            }
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['semester_id']);
            $table->dropColumn(['subject_id', 'semester_id', 'room', 'max_students']);
        });

        Schema::dropIfExists('subject_teacher');

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });

        Schema::dropIfExists('semesters');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('faculties');
    }
};
