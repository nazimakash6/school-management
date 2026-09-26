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
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_settings');

        Schema::create('whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_connected')->default(true);
            $table->string('phone_number')->nullable()->default('+92 300 9876543');
            $table->string('device_name')->nullable()->default('School Official WhatsApp Web');
            $table->timestamp('connected_at')->useCurrent();
            $table->boolean('auto_attendance_alert')->default(true);
            $table->boolean('auto_fee_reminder')->default(true);
            $table->boolean('auto_exam_result')->default(true);
            $table->boolean('auto_visitor_alert')->default(true);
            $table->timestamps();
        });

        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_type')->default('Parent'); // Student, Parent, Staff, Custom
            $table->string('recipient_name');
            $table->string('phone_number');
            $table->string('message_type')->default('Direct'); // Direct, Broadcast, Automated Alert
            $table->string('template_name')->nullable();
            $table->text('message');
            $table->string('attachment_path')->nullable();
            $table->string('status')->default('Sent'); // Sent, Delivered, Read, Failed
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_settings');
    }
};
