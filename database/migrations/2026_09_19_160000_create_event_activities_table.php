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
        Schema::create('event_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('name');
            $table->string('category')->default('Sports'); // Sports, Academic, Cultural, Literary, Other
            $table->date('activity_date')->nullable();
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->string('venue')->nullable();
            $table->json('house_ids')->nullable(); // Array of house IDs
            $table->string('status')->default('Scheduled'); // Scheduled, Ongoing, Completed, Cancelled
            $table->foreignId('winner_house_id')->nullable()->constrained('houses')->onDelete('set null');
            $table->foreignId('runner_up_house_id')->nullable()->constrained('houses')->onDelete('set null');
            $table->foreignId('third_place_house_id')->nullable()->constrained('houses')->onDelete('set null');
            $table->text('rules_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_activities');
    }
};
