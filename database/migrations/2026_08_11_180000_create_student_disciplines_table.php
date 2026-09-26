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
        Schema::create('student_disciplines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('student_class_id')->nullable()->constrained('student_classes')->onDelete('cascade');
            $table->date('entry_date');
            $table->string('category')->default('General Behavior'); // Behavior, Character, Punctuality, Uniform, Conduct, Warning, Praise
            $table->json('category_ratings')->nullable(); // Multi-category ratings array
            $table->string('performance_period')->default('daily'); // daily
            $table->string('title')->nullable();
            $table->decimal('total_score', 8, 2)->default(100.00);
            $table->decimal('obtained_score', 8, 2)->default(100.00);
            $table->decimal('star_rating', 3, 1)->default(5.0); // 1.0 to 5.0 stars
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
        Schema::dropIfExists('student_disciplines');
    }
};
