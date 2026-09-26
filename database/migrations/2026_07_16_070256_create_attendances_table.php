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
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->time('school_open_time')->default('07:30:00');
            $table->time('school_start_time')->default('08:00:00');
            $table->time('school_break_time')->default('12:00:00');
            $table->time('school_end_time')->default('14:00:00');
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('user_type'); // 'student' or 'staff'
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->string('session1_status')->nullable();
            $table->time('session1_time')->nullable();
            $table->string('session1_remarks')->nullable();
            $table->string('session2_status')->nullable();
            $table->time('session2_time')->nullable();
            $table->string('session2_remarks')->nullable();
            $table->string('final_status')->nullable();
            $table->timestamps();

            $table->unique(['user_type', 'user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('attendance_settings');
    }
};
