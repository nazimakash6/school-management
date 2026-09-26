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
        if (!Schema::hasTable('event_categories')) {
            Schema::create('event_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('badge_class')->default('bg-primary text-white');
                $table->string('status')->default('active'); // active, inactive
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'event_category_id')) {
                $table->foreignId('event_category_id')->nullable()->after('event_type')->constrained('event_categories')->onDelete('set null');
            }
        });

        Schema::table('event_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('event_activities', 'event_category_id')) {
                $table->foreignId('event_category_id')->nullable()->after('category')->constrained('event_categories')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'event_category_id')) {
                $table->dropForeign(['event_category_id']);
                $table->dropColumn('event_category_id');
            }
        });

        Schema::table('event_activities', function (Blueprint $table) {
            if (Schema::hasColumn('event_activities', 'event_category_id')) {
                $table->dropForeign(['event_category_id']);
                $table->dropColumn('event_category_id');
            }
        });

        Schema::dropIfExists('event_categories');
    }
};
