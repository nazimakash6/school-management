<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('event_type');
                $table->string('action');
                $table->text('description');
                $table->string('route')->nullable();
                $table->string('method', 16)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->json('context')->nullable();
                $table->timestamps();
            });

            return;
        }

        Schema::table('audit_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('audit_logs', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->after('id');
            }

            if (! Schema::hasColumn('audit_logs', 'event_type')) {
                $table->string('event_type')->nullable()->after('user_id');
            }

            if (! Schema::hasColumn('audit_logs', 'action')) {
                $table->string('action')->nullable()->after('event_type');
            }

            if (! Schema::hasColumn('audit_logs', 'description')) {
                $table->text('description')->nullable()->after('action');
            }

            if (! Schema::hasColumn('audit_logs', 'route')) {
                $table->string('route')->nullable()->after('description');
            }

            if (! Schema::hasColumn('audit_logs', 'method')) {
                $table->string('method', 16)->nullable()->after('route');
            }

            if (! Schema::hasColumn('audit_logs', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('method');
            }

            if (! Schema::hasColumn('audit_logs', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }

            if (! Schema::hasColumn('audit_logs', 'context')) {
                $table->json('context')->nullable()->after('user_agent');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};