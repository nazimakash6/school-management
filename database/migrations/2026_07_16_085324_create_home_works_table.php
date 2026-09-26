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
        if (!Schema::hasTable('home_works')) {
            Schema::create('home_works', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_class_id')->nullable()->constrained('student_classes')->onDelete('cascade');
                $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->json('subject_tasks')->nullable();
                $table->date('assigned_date')->nullable();
                $table->date('due_date')->nullable();
                $table->string('attachment')->nullable();
                $table->string('status')->default('active');
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        } else {
            Schema::table('home_works', function (Blueprint $table) {
                if (!Schema::hasColumn('home_works', 'subject_tasks')) {
                    $table->json('subject_tasks')->nullable()->after('description');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_works');
    }
};
