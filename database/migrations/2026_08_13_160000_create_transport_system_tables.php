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
        // Update transports table
        Schema::table('transports', function (Blueprint $table) {
            $table->string('route_code')->nullable()->unique()->after('id');
            $table->string('route_title')->after('route_code');
            $table->string('vehicle_number')->after('route_title');
            $table->string('vehicle_model')->nullable()->after('vehicle_number');
            $table->string('vehicle_type')->default('Bus')->after('vehicle_model');
            $table->string('vehicle_ownership')->nullable()->default('School Owned')->after('vehicle_type');
            $table->integer('vehicle_capacity')->nullable()->default(30)->after('vehicle_ownership');
            $table->foreignId('driver_id')->nullable()->after('vehicle_capacity')->constrained('staff')->onDelete('set null');
            $table->string('driver_name')->nullable()->after('driver_id');
            $table->string('driver_contact')->nullable()->after('driver_name');
            $table->string('driver_license')->nullable()->after('driver_contact');
            $table->decimal('fare_amount', 10, 2)->nullable()->default(0.00)->after('driver_license');
            $table->text('pickup_stops')->nullable()->after('fare_amount');
            $table->string('status')->nullable()->default('Active')->after('pickup_stops');
            $table->text('note')->nullable()->after('status');
            $table->foreignId('created_by')->nullable()->after('note')->constrained('users')->onDelete('set null');
            $table->softDeletes()->after('created_by');
        });

        // Create transport_students table
        Schema::create('transport_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_id')->constrained('transports')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('stop_name')->nullable();
            $table->string('pickup_time')->nullable();
            $table->string('drop_time')->nullable();
            $table->decimal('monthly_fare', 8, 2)->default(0.00);
            $table->string('status')->default('Active'); // Active, Paused, Cancelled
            $table->date('joining_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_students');
        Schema::table('transports', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'route_code',
                'route_title',
                'vehicle_number',
                'vehicle_model',
                'vehicle_type',
                'vehicle_ownership',
                'vehicle_capacity',
                'driver_id',
                'driver_name',
                'driver_contact',
                'driver_license',
                'fare_amount',
                'pickup_stops',
                'status',
                'note',
                'created_by',
                'deleted_at',
            ]);
        });
    }
};
