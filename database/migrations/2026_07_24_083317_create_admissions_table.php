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
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_session_id')->nullable()->constrained('academic_sessions')->onDelete('set null');
            $table->unsignedBigInteger('sibling_id')->nullable();
            // Personal Information
            $table->string('admission_no', 50)->unique();
            $table->date('admission_date');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('cnic_bform')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender');
            $table->string('blood_group')->nullable();
            $table->string('religion')->nullable();
            $table->string('nationality')->nullable();
            $table->string('student_mobile_no')->nullable();
            $table->string('student_email')->nullable();
            $table->string('previous_school')->nullable();
            $table->string('student_medical_notes')->nullable();
            // Guardian Information
            $table->string('father_name');
            $table->string('father_cnic')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_cnic')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_cnic')->nullable();
            $table->string('guardian_occupation')->nullable();
            $table->string('guardian_primary_mobile_no')->nullable();
            $table->string('guardian_secondary_mobile_no')->nullable();
            $table->string('guardian_email')->nullable();
            $table->string('guardian_address')->nullable();
            // Academic Information
            $table->string('class_name');
            $table->string('section_name')->nullable();
            $table->string('group_name')->nullable();
            $table->string('group')->nullable();
            $table->string('roll_no')->nullable();
            $table->string('class_shift')->nullable();
            $table->string('fee_plan')->nullable();
            $table->string('fee_status')->default('pending');
            $table->decimal('registration_fee', 10, 2)->nullable();
            $table->decimal('monthly_fee', 10, 2)->nullable();
            $table->decimal('quarterly_fee', 10, 2)->nullable();
            $table->decimal('six_monthly_fee', 10, 2)->nullable();
            $table->decimal('annual_fee', 10, 2)->nullable();
            $table->string('scholarship_discount')->nullable();
            $table->string('academic_notes')->nullable();
            // Address and transportation
            $table->string('current_address');
            $table->string('permanent_address')->nullable();
            $table->string('transportation_required')->nullable();
            $table->string('transportation_route')->nullable();
            $table->string('hostel')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            $table->string('emergency_contact_mobile_no')->nullable();
            // Documents and reviews
            $table->string('birth_certificate')->nullable();
            $table->string('bform_cnic_copy')->nullable();
            $table->string('guardian_cnic_copy')->nullable();
            $table->string('school_leaving_certificate')->nullable();
            $table->string('student_photo')->nullable();
            $table->json('attached_documents')->nullable();
            $table->string('admission_status')->default('pending');
            $table->text('admission_remarks')->nullable();
            $table->boolean('is_confirmed')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
