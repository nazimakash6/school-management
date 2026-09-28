<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->json('permissions')->nullable();
                $table->boolean('is_system')->default(false);
                $table->timestamps();
            });

            // Seed default roles
            $defaultRoles = [
                [
                    'name' => 'Director / Owner',
                    'slug' => 'director-owner',
                    'description' => 'Full administrative access to all system modules and settings.',
                    'is_system' => true,
                    'permissions' => json_encode(['*']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Principal',
                    'slug' => 'principal',
                    'description' => 'Full control over academic, student, teacher, and operational activities.',
                    'is_system' => true,
                    'permissions' => json_encode([
                        'dashboard.view', 'students.view', 'students.create', 'students.edit', 'students.promote',
                        'teachers.manage', 'staff.manage', 'attendance.manage', 'homework.manage', 'classwork.manage',
                        'examinations.manage', 'discipline.manage', 'quran.manage', 'inventory.manage', 'library.manage',
                        'transport.manage', 'certificates.manage', 'whatsapp.manage', 'events.manage', 'meetings.manage',
                        'visitors.manage', 'reports.view'
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Teacher',
                    'slug' => 'teacher',
                    'description' => 'Access to student list, attendance, classwork, homework, and examination grading.',
                    'is_system' => true,
                    'permissions' => json_encode([
                        'dashboard.view', 'students.view', 'attendance.manage', 'homework.manage',
                        'classwork.manage', 'examinations.manage', 'quran.manage', 'events.manage'
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Staff',
                    'slug' => 'staff',
                    'description' => 'General staff access for inventory, visitors, and daily school operations.',
                    'is_system' => true,
                    'permissions' => json_encode([
                        'dashboard.view', 'visitors.manage', 'inventory.manage', 'library.manage', 'events.manage'
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Student',
                    'slug' => 'student',
                    'description' => 'Student portal access for attendance, homework, exam results, and fee status.',
                    'is_system' => true,
                    'permissions' => json_encode([
                        'dashboard.view', 'homework.view', 'classwork.view', 'examinations.view', 'attendance.view'
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Parent',
                    'slug' => 'parent',
                    'description' => 'Parent portal access to monitor children attendance, fees, and progress.',
                    'is_system' => true,
                    'permissions' => json_encode([
                        'dashboard.view', 'students.view', 'attendance.view', 'fees.view', 'homework.view'
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Accountant',
                    'slug' => 'accountant',
                    'description' => 'Access to financial modules, fee collections, and payroll processing.',
                    'is_system' => true,
                    'permissions' => json_encode([
                        'dashboard.view', 'fees.manage', 'payroll.manage', 'reports.view'
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::table('roles')->insert($defaultRoles);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
