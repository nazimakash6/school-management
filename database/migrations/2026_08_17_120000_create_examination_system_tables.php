<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create Exam Types table
        Schema::create('exam_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // Seed default Exam Types requested by user
        $defaultTypes = [
            ['name' => 'Daily Test', 'code' => 'DAILY', 'description' => 'Daily quick evaluation tests'],
            ['name' => '3rd Day Test', 'code' => '3DAY', 'description' => '3rd day cumulative test'],
            ['name' => 'Weekly Test', 'code' => 'WEEKLY', 'description' => 'Weekly assessment test'],
            ['name' => 'Monthly Test', 'code' => 'MONTHLY', 'description' => 'Monthly comprehensive test'],
            ['name' => 'First Mid Test', 'code' => 'MID1', 'description' => 'First mid-term assessment'],
            ['name' => 'Second Term Test', 'code' => 'TERM2', 'description' => 'Second term assessment'],
            ['name' => 'Annual Exam', 'code' => 'ANNUAL', 'description' => 'Final annual examination'],
            ['name' => 'Re Board Exam', 'code' => 'REBOARD', 'description' => 'Re-board practice exam'],
        ];

        $now = now();
        foreach ($defaultTypes as $type) {
            DB::table('exam_types')->insert([
                'name' => $type['name'],
                'code' => $type['code'],
                'description' => $type['description'],
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 2. Drop basic examinations table if exists and re-create with full schema
        Schema::dropIfExists('examinations');
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('exam_type_id')->constrained('exam_types')->onDelete('cascade');
            $table->foreignId('academic_session_id')->nullable()->constrained('academic_sessions')->onDelete('set null');
            $table->text('class_name');
            $table->string('section_name')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('total_marks', 8, 2)->default(100);
            $table->decimal('pass_marks', 8, 2)->default(40);
            $table->string('status')->default('scheduled'); // scheduled, ongoing, completed, published
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 3. Create Exam Schedules (Subject setup for each exam)
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examination_id')->constrained('examinations')->onDelete('cascade');
            $table->string('subject_name');
            $table->decimal('max_marks', 8, 2)->default(100);
            $table->decimal('pass_marks', 8, 2)->default(40);
            $table->date('exam_date')->nullable();
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->string('room_no')->nullable();
            $table->timestamps();
        });

        // 4. Create Exam Marks table
        Schema::create('exam_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examination_id')->constrained('examinations')->onDelete('cascade');
            $table->foreignId('admission_id')->constrained('admissions')->onDelete('cascade');
            $table->string('subject_name');
            $table->decimal('marks_obtained', 8, 2)->nullable();
            $table->decimal('total_marks', 8, 2)->default(100);
            $table->decimal('pass_marks', 8, 2)->default(40);
            $table->boolean('is_absent')->default(false);
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->unique(['examination_id', 'admission_id', 'subject_name'], 'exam_student_subject_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_marks');
        Schema::dropIfExists('exam_schedules');
        Schema::dropIfExists('examinations');
        Schema::dropIfExists('exam_types');
    }
};
