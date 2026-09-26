<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('recipient_type')->default('Parent'); // Parent, Staff, Principal, Admin, Custom
            $table->string('subject');
            $table->text('message');
            $table->string('message_type')->default('Direct'); // Direct, Broadcast
            $table->string('template_name')->nullable();
            $table->string('status')->default('Sent'); // Sent, Failed
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
