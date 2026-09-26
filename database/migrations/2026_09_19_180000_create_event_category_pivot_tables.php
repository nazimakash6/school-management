<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_event_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('event_category_id')->constrained('event_categories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['event_id', 'event_category_id']);
        });

        Schema::create('event_activity_event_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_activity_id')->constrained('event_activities')->cascadeOnDelete();
            $table->foreignId('event_category_id')->constrained('event_categories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['event_activity_id', 'event_category_id'], 'activity_category_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_activity_event_category');
        Schema::dropIfExists('event_event_category');
    }
};
