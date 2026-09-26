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
        Schema::dropIfExists('meeting_attendees');
        Schema::dropIfExists('meetings');

        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('meeting_type')->default('Staff Meeting'); // Staff Meeting, Parent Teacher Meeting (PTM), Management / Board, Departmental HOD, Academic Council, Emergency / Special
            $table->string('mode')->default('In-Person'); // In-Person, Online (Zoom/Google Meet), Hybrid
            $table->string('location')->nullable(); // Room 102, Main Auditorium, Online
            $table->string('meeting_link')->nullable(); // Zoom link, Google Meet URL
            $table->date('meeting_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('target_audience')->default('All Teachers & Staff');
            $table->foreignId('organizer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('Scheduled'); // Scheduled, In-Progress, Completed, Postponed, Cancelled
            $table->text('agenda')->nullable();
            $table->text('minutes_of_meeting')->nullable(); // MoM recorded after meeting
            $table->text('action_items')->nullable(); // Tasks assigned & deadlines
            $table->string('attachment_path')->nullable(); // Document / PDF upload
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('meeting_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('meetings')->onDelete('cascade');
            $table->string('attendee_type')->default('staff'); // staff, teacher, parent, user
            $table->unsignedBigInteger('attendee_id')->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('role')->nullable();
            $table->string('attendance_status')->default('Invited'); // Invited, Attended, Excused, Absent
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_attendees');
        Schema::dropIfExists('meetings');
    }
};
