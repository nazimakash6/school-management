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
        Schema::create('student_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('student_class_id')->nullable()->constrained('student_classes')->onDelete('cascade');
            $table->date('evaluation_date');
            $table->string('skill_category')->default('IT & Technology');
            $table->string('skill_name');
            $table->string('assessment_type')->default('Practical Evaluation');
            $table->string('performance_period')->default('monthly');
            $table->decimal('total_score', 8, 2)->default(100.00);
            $table->decimal('obtained_score', 8, 2)->default(0.00);
            $table->decimal('star_rating', 3, 1)->default(0.0);
            $table->string('badge_level')->default('Proficient');
            $table->string('certificate_code')->nullable();
            $table->text('instructor_notes')->nullable();
            $table->string('image_proof')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_skills');
    }
};
