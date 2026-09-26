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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('subject_code', 50);
            $table->string('subject_name');
            $table->string('subject_type')->nullable(); // Legacy code/name fallback
            $table->foreignId('subject_type_id')->nullable()->constrained('subject_types')->onDelete('set null');
            $table->string('class_name')->nullable();
            $table->foreignId('student_class_id')->nullable()->constrained('student_classes')->onDelete('set null');
            $table->foreignId('staff_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->string('status')->default('active'); // active, inactive
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
