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
            if (!Schema::hasColumn('exams', 'allow_view_score')) {
                $table->boolean('allow_view_score')->default(true)->after('allow_review');
            }
            if (!Schema::hasColumn('exams', 'semester')) {
                $table->string('semester')->nullable()->after('category_id');
            }
        });

        Schema::table('exam_answers', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_answers', 'text_answer')) {
                $table->text('text_answer')->nullable()->after('answer_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'allow_view_score')) {
                $table->dropColumn('allow_view_score');
            }
            if (Schema::hasColumn('exams', 'semester')) {
                $table->dropColumn('semester');
            }
        });

        Schema::table('exam_answers', function (Blueprint $table) {
            if (Schema::hasColumn('exam_answers', 'text_answer')) {
                $table->dropColumn('text_answer');
            }
        });
    }
};
