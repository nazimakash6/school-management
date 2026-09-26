<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Re-create or alter custom_settings table
        Schema::dropIfExists('custom_settings');
        Schema::create('custom_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general'); // general, academic, finance, email, whatsapp, system
            $table->string('type')->default('text'); // text, textarea, boolean, select, number
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed default custom settings
        $defaultSettings = [
            // General
            ['key' => 'school_name', 'value' => 'Noor Ul Huda Superior School', 'group' => 'general', 'type' => 'text', 'description' => 'Official School Name'],
            ['key' => 'school_tagline', 'value' => 'Discipline | Education | Excellence', 'group' => 'general', 'type' => 'text', 'description' => 'School Motto / Tagline'],
            ['key' => 'school_email', 'value' => 'info@noorulhuda.edu.pk', 'group' => 'general', 'type' => 'text', 'description' => 'Primary Contact Email'],
            ['key' => 'school_phone', 'value' => '+92 300 1234567', 'group' => 'general', 'type' => 'text', 'description' => 'Primary Phone Number'],
            ['key' => 'school_address', 'value' => 'Main Campus, Educational Complex, City', 'group' => 'general', 'type' => 'textarea', 'description' => 'School Physical Address'],
            ['key' => 'currency_symbol', 'value' => 'Rs.', 'group' => 'general', 'type' => 'text', 'description' => 'Currency Symbol'],
            ['key' => 'timezone', 'value' => 'Asia/Karachi', 'group' => 'general', 'type' => 'select', 'description' => 'Default Timezone'],
            ['key' => 'date_format', 'value' => 'd/m/Y', 'group' => 'general', 'type' => 'select', 'description' => 'System Date Format'],

            // Academic & Fee
            ['key' => 'late_fee_fine', 'value' => '500', 'group' => 'academic', 'type' => 'number', 'description' => 'Late Fee Fine Amount'],
            ['key' => 'fee_due_day', 'value' => '10', 'group' => 'academic', 'type' => 'number', 'description' => 'Default Day of Month for Fee Due'],
            ['key' => 'pass_percentage', 'value' => '40', 'group' => 'academic', 'type' => 'number', 'description' => 'Minimum Passing Percentage'],
            ['key' => 'attendance_threshold', 'value' => '75', 'group' => 'academic', 'type' => 'number', 'description' => 'Minimum Attendance Percentage Required'],

            // Email / SMTP
            ['key' => 'smtp_host', 'value' => 'smtp.gmail.com', 'group' => 'email', 'type' => 'text', 'description' => 'SMTP Mail Server Host'],
            ['key' => 'smtp_port', 'value' => '587', 'group' => 'email', 'type' => 'number', 'description' => 'SMTP Mail Server Port'],
            ['key' => 'smtp_username', 'value' => 'notifications@noorulhuda.edu.pk', 'group' => 'email', 'type' => 'text', 'description' => 'SMTP Username'],
            ['key' => 'smtp_password', 'value' => '••••••••••••', 'group' => 'email', 'type' => 'text', 'description' => 'SMTP Password'],
            ['key' => 'smtp_encryption', 'value' => 'tls', 'group' => 'email', 'type' => 'select', 'description' => 'Encryption Type (tls/ssl)'],
            ['key' => 'email_from_name', 'value' => 'Noor Ul Huda School Admin', 'group' => 'email', 'type' => 'text', 'description' => 'Outgoing Mail Sender Name'],

            // WhatsApp Gateway
            ['key' => 'whatsapp_api_key', 'value' => 'wh_live_key_8892109841', 'group' => 'whatsapp', 'type' => 'text', 'description' => 'WhatsApp API Access Key'],
            ['key' => 'whatsapp_phone_id', 'value' => '109823471092834', 'group' => 'whatsapp', 'type' => 'text', 'description' => 'WhatsApp Sender Phone ID'],
            ['key' => 'auto_whatsapp_fee_receipt', 'value' => '1', 'group' => 'whatsapp', 'type' => 'boolean', 'description' => 'Auto-send WhatsApp on Fee Payment'],
            ['key' => 'auto_whatsapp_absent_alert', 'value' => '1', 'group' => 'whatsapp', 'type' => 'boolean', 'description' => 'Auto-send WhatsApp on Student Absence'],

            // System & Maintenance
            ['key' => 'maintenance_mode', 'value' => '0', 'group' => 'system', 'type' => 'boolean', 'description' => 'Enable System Maintenance Mode'],
            ['key' => 'session_timeout', 'value' => '60', 'group' => 'system', 'type' => 'number', 'description' => 'Session Timeout Duration (Minutes)'],
            ['key' => 'max_login_attempts', 'value' => '5', 'group' => 'system', 'type' => 'number', 'description' => 'Max Login Attempts Before Lockout'],
            ['key' => 'require_2fa', 'value' => '0', 'group' => 'system', 'type' => 'boolean', 'description' => 'Require Two-Factor Authentication'],
        ];

        $now = now();
        foreach ($defaultSettings as $st) {
            DB::table('custom_settings')->insert(array_merge($st, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // 2. Create IP Bans Table for Security
        Schema::create('ip_bans', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');
            $table->string('reason')->nullable();
            $table->foreignId('banned_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // 3. Create Security Settings Table
        Schema::create('security_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default security settings
        $secDefaults = [
            '2fa_requirement' => 'optional',
            'session_timeout' => '60',
            'password_min_length' => '8',
            'password_require_number' => '1',
            'password_require_symbol' => '1',
            'password_expiry_days' => '90',
            'max_failed_attempts' => '5',
            'lockout_duration_minutes' => '15',
            'ip_restriction_enabled' => '0',
        ];

        foreach ($secDefaults as $k => $v) {
            DB::table('security_settings')->insert([
                'key' => $k,
                'value' => $v,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('security_settings');
        Schema::dropIfExists('ip_bans');
        Schema::dropIfExists('custom_settings');
    }
};
