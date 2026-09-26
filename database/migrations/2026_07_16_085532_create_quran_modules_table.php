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
        Schema::create('quran_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('cascade');
            $table->foreignId('student_class_id')->nullable()->constrained('student_classes')->onDelete('set null');
            $table->date('entry_date')->nullable();
            $table->string('category')->default('Nazra'); // Qaida, Nazra, Hifz, Tajweed, Hadith, Dua
            $table->string('status')->default('In Progress'); // In Progress, Completed, Needs Improvement, Passed, Excellent
            $table->string('teacher_name')->nullable();
            $table->integer('para_no')->nullable();
            $table->string('surah_name')->nullable();
            $table->integer('ayah_from')->nullable();
            $table->integer('ayah_to')->nullable();
            $table->string('lesson_name')->nullable();
            $table->text('sabaq')->nullable();
            $table->text('sabqi')->nullable();
            $table->text('manzil')->nullable();
            $table->integer('total_parahs_memorized')->default(0);
            $table->decimal('score', 5, 2)->default(100.00);
            $table->integer('mistakes_count')->default(0);
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_modules');
    }
};
