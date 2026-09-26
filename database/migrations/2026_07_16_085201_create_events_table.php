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
        Schema::dropIfExists('events');

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('event_type')->default('Academic'); // Academic, Sports, Cultural, Islamic / Religious, Parent-Teacher, Holiday / Vacation, Administrative, Other
            $table->string('target_audience')->default('All Students'); // All Students, Parents & Guardians, Staff & Faculty, Primary Section, Secondary Section
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_all_day')->default(false);
            $table->string('location')->default('School Auditorium');
            $table->string('organizer')->nullable();
            $table->string('status')->default('Upcoming'); // Upcoming, In-Progress, Completed, Postponed, Cancelled
            $table->decimal('budget_pkr', 12, 2)->nullable();
            $table->string('banner_image')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
