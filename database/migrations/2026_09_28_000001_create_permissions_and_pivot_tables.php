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
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('module');
                $table->string('label');
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('permission_role')) {
            Schema::create('permission_role', function (Blueprint $table) {
                $table->id();
                $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
                $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['permission_id', 'role_id']);
            });
        }

        if (!Schema::hasTable('permission_user')) {
            Schema::create('permission_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->boolean('is_granted')->default(true); // true = direct allow, false = explicit deny
                $table->timestamps();
                $table->unique(['permission_id', 'user_id']);
            });
        }

        if (!Schema::hasTable('role_user')) {
            Schema::create('role_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['role_id', 'user_id']);
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'student_id')) {
                $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'staff_id')) {
                $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'father_cnic')) {
                $table->string('father_cnic', 30)->nullable()->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'father_cnic')) {
                $table->dropColumn('father_cnic');
            }
            if (Schema::hasColumn('users', 'staff_id')) {
                $table->dropForeign(['staff_id']);
                $table->dropColumn('staff_id');
            }
            if (Schema::hasColumn('users', 'student_id')) {
                $table->dropForeign(['student_id']);
                $table->dropColumn('student_id');
            }
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            }
        });

        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permission_user');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
    }
};
