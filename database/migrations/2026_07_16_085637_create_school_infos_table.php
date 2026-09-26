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
        Schema::create('school_infos', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->default('The Academy School');
            $table->string('school_code')->nullable()->default('TAS-001');
            $table->string('tagline')->nullable()->default('Excellence in Education');
            $table->string('email')->nullable()->default('info@academyschool.edu.pk');
            $table->string('phone')->nullable()->default('+92 300 1234567');
            $table->string('alternate_phone')->nullable();
            $table->string('website')->nullable()->default('https://academyschool.edu.pk');
            $table->text('address')->nullable();
            $table->string('city')->nullable()->default('Lahore');
            $table->string('state')->nullable()->default('Punjab');
            $table->string('postal_code')->nullable()->default('54000');
            $table->string('established_year')->nullable()->default('1998');
            $table->string('affiliation_board')->nullable()->default('BISE Lahore');
            $table->string('registration_no')->nullable()->default('REG-2026-LHR');
            $table->string('principal_name')->nullable()->default('Dr. Muhammad Usman');
            $table->string('currency_symbol')->nullable()->default('Rs.');
            $table->string('logo_path')->nullable();
            $table->string('stamp_path')->nullable();
            $table->string('social_facebook')->nullable();
            $table->string('social_instagram')->nullable();
            $table->string('social_twitter')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_infos');
    }
};
