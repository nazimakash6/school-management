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
        Schema::create('AcademicPlanning', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('plan_type')->default('annual');
            $table->string('class_name')->nullable();
            $table->string('subject_name')->nullable();
            $table->string('academic_session')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('objectives')->nullable();
            $table->text('topics_covered')->nullable();
            $table->text('teaching_methodology')->nullable();
            $table->text('assessment_plan')->nullable();
            $table->string('status')->default('active');
            $table->string('attachment')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('AcademicPlanning');
    }
};
