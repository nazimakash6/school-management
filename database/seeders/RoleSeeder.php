<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Full administrative access to all system modules, security, and global settings.',
                'is_system' => true,
                'permissions' => ['*'],
            ],
            [
                'name' => 'Principal',
                'slug' => 'principal',
                'description' => 'Full academic oversight, staff monitoring, student records, reports, and noticeboard.',
                'is_system' => true,
                'permissions' => [
                    'dashboard.view', 'students.view', 'admissions.view', 'admissions.create', 'students.promote',
                    'teachers.manage', 'staff.manage', 'school_info.manage', 'academic_sessions.manage',
                    'classes.manage', 'subjects.manage', 'groups.manage', 'houses.manage',
                    'attendance.manage', 'homework.manage', 'classwork.manage', 'examinations.manage',
                    'events.manage', 'discipline.manage', 'quran.manage', 'inventory.manage',
                    'library.manage', 'transport.manage', 'certificates.manage', 'id_cards.manage',
                    'meetings.manage', 'visitors.manage', 'reports.view'
                ],
            ],
            [
                'name' => 'Teacher',
                'slug' => 'teacher',
                'description' => 'Access to assigned classes, attendance marking, homework, classwork, and exam grading.',
                'is_system' => true,
                'permissions' => [
                    'dashboard.view', 'students.view', 'attendance.manage', 'homework.manage',
                    'classwork.manage', 'examinations.manage', 'discipline.manage', 'quran.manage', 'meetings.manage'
                ],
            ],
            [
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Operational staff access for fee collections, payroll, inventory, library, and visitors.',
                'is_system' => true,
                'permissions' => [
                    'dashboard.view', 'students.view', 'teachers.manage', 'staff.manage', 'staff_advances.manage',
                    'payroll.manage', 'fees.manage', 'fee_statement.view', 'inventory.manage', 'library.manage',
                    'transport.manage', 'skills_institute.manage', 'certificates.manage', 'id_cards.manage',
                    'whatsapp.manage', 'meetings.manage', 'visitors.manage'
                ],
            ],
            [
                'name' => 'Student',
                'slug' => 'student',
                'description' => 'Student portal for personal attendance, assigned homework, exam results, and timetable.',
                'is_system' => true,
                'permissions' => [
                    'dashboard.view', 'attendance.manage', 'homework.manage', 'classwork.manage', 'examinations.manage'
                ],
            ],
            [
                'name' => 'Parent',
                'slug' => 'parent',
                'description' => 'Parent portal to track children performance, attendance, fee vouchers, and school notices.',
                'is_system' => true,
                'permissions' => [
                    'dashboard.view', 'attendance.manage', 'homework.manage', 'examinations.manage',
                    'fees.manage', 'fee_statement.view', 'meetings.manage'
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }
    }
}
