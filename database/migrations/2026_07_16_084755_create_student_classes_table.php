<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_table_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->string('name')->unique();
            $table->string('level');
            $table->string('group')->nullable();
            $table->unsignedInteger('section_count')->default(0);
            $table->json('section_names')->nullable();
            $table->string('status')->default('active');
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_classes');
    }
};
