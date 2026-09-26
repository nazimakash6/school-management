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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id')->unique();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('gender');
            $table->string('dob');
            $table->string('cnic');
            $table->string('marital_status');
            $table->string('blood_group')->nullable();
            $table->string('religion')->nullable();
            $table->string('nationality')->nullable();
            $table->string('mobile_no');
            $table->string('alternate_mobile_no')->nullable();
            $table->string('email');
            $table->string('current_address');
            $table->string('permanent_address');
            $table->string('joining_date');
            $table->string('leaving_date')->nullable();
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_number');
            $table->string('emergency_contact_relation');
            $table->string('department');
            $table->string('designation');
            $table->string('qualification');
            $table->string('experience')->nullable();
            $table->string('employment_type');
            $table->string('shift')->nullable();
            $table->string('salary');
            $table->string('salary_type')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_title')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('iban')->nullable();
            $table->string('cv')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('status')->default('active');
            $table->string('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
