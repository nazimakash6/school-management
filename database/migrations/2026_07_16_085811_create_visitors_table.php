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
        Schema::dropIfExists('visitors');

        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('pass_code')->unique(); // VSTR-2026-8941
            $table->string('visitor_name');
            $table->string('phone');
            $table->string('cnic_id')->nullable(); // CNIC / National ID
            $table->integer('num_persons')->default(1);
            $table->string('purpose')->default('General Inquiry'); // Student Early Pick-up, Fee Payment, Meeting Staff, Vendor, Admission Query, Parent Consultation, Official
            $table->string('meet_type')->default('General'); // Student, Staff, General
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained('staff')->onDelete('cascade');
            $table->string('person_to_meet')->nullable();
            $table->date('visit_date');
            $table->dateTime('check_in_time');
            $table->dateTime('check_out_time')->nullable();
            $table->string('vehicle_no')->nullable(); // Vehicle / Bike plate
            $table->string('gate_no')->default('Main Gate 1');
            $table->string('status')->default('Checked-In'); // Checked-In, Checked-Out, Blocked
            $table->string('id_proof_image')->nullable();
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
        Schema::dropIfExists('visitors');
    }
};
