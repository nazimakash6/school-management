<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $grouped = Role::availablePermissionsGrouped();
        $permissionMap = [];

        // 1. Seed Permissions
        foreach ($grouped as $moduleName => $perms) {
            foreach ($perms as $permName => $permLabel) {
                $permission = Permission::firstOrCreate(
                    ['name' => $permName],
                    [
                        'module'      => $moduleName,
                        'label'       => $permLabel,
                        'description' => "Allows user to perform {$permLabel} in {$moduleName}",
                    ]
                );
                $permissionMap[$permName] = $permission->id;
            }
        }

        // 2. Define Default Roles and their Permission Mapping
        $rolesConfig = [
            'Admin' => [
                'description' => 'Full administrative access to all system modules and settings.',
                'is_system'   => true,
                'permissions' => array_keys($permissionMap),
            ],
            'Operator' => [
                'description' => 'School office operator with permissions to handle student records, admissions, fees, and attendance.',
                'is_system'   => true,
                'permissions' => [
                    'dashboard.view',
                    'students.view', 'students.create', 'students.edit',
                    'admissions.view', 'admissions.create', 'admissions.edit',
                    'classes.view', 'sections.view',
                    'fees.view', 'fees.create',
                    'attendance.view', 'attendance.create',
                    'visitors.view', 'visitors.manage',
                    'events.view',
                ],
            ],
            'Director / Owner' => [
                'description' => 'Executive owner access to all system modules and financial reports.',
                'is_system'   => true,
                'permissions' => array_keys($permissionMap),
            ],
            'Principal' => [
                'description' => 'Full operational control over academic, student, teacher, and administrative activities.',
                'is_system'   => true,
                'permissions' => [
                    'dashboard.view',
                    'students.view', 'students.create', 'students.edit', 'students.promote',
                    'admissions.view', 'admissions.create', 'admissions.edit',
                    'teachers.view', 'teachers.create', 'teachers.edit',
                    'staff.view', 'staff.create', 'staff.edit',
                    'classes.view', 'sections.view', 'subjects.view',
                    'fees.view', 'fees.create', 'payroll.view', 'staff_advances.view',
                    'attendance.view', 'attendance.create', 'attendance.edit',
                    'homework.view', 'homework.manage',
                    'classwork.view', 'classwork.manage',
                    'examinations.view', 'examinations.manage',
                    'discipline.view', 'discipline.manage',
                    'quran.view', 'quran.manage',
                    'inventory.view', 'inventory.manage',
                    'library.view', 'library.manage',
                    'transport.view', 'transport.manage',
                    'certificates.view', 'certificates.manage',
                    'id_cards.view', 'id_cards.manage',
                    'whatsapp.view', 'whatsapp.manage',
                    'events.view', 'events.manage',
                    'meetings.view', 'meetings.manage',
                    'visitors.view', 'visitors.manage',
                    'reports.view', 'reports.export',
                ],
            ],
            'Teacher' => [
                'description' => 'Teacher access for student viewing, attendance marking, homework, classwork, and examination marks entry.',
                'is_system'   => true,
                'permissions' => [
                    'dashboard.view',
                    'students.view',
                    'classes.view', 'sections.view', 'subjects.view',
                    'attendance.view', 'attendance.create', 'attendance.edit',
                    'homework.view', 'homework.manage',
                    'classwork.view', 'classwork.manage',
                    'examinations.view', 'examinations.manage',
                    'discipline.view', 'discipline.manage',
                    'quran.view', 'quran.manage',
                    'events.view',
                ],
            ],
            'Staff' => [
                'description' => 'General staff access for inventory, visitors, and daily school operations.',
                'is_system'   => true,
                'permissions' => [
                    'dashboard.view',
                    'visitors.view', 'visitors.manage',
                    'inventory.view', 'inventory.manage',
                    'library.view', 'library.manage',
                    'events.view',
                ],
            ],
            'Student' => [
                'description' => 'Student portal access for attendance, homework, exam results, timetable, and fee status.',
                'is_system'   => true,
                'permissions' => [
                    'dashboard.view',
                    'homework.view',
                    'classwork.view',
                    'examinations.view',
                    'attendance.view',
                    'fees.view',
                    'events.view',
                    'quran.view',
                    'skills_institute.view',
                ],
            ],
            'Parent' => [
                'description' => 'Parent portal access to monitor children attendance, fee vouchers, and exam results.',
                'is_system'   => true,
                'permissions' => [
                    'dashboard.view',
                    'students.view',
                    'attendance.view',
                    'fees.view',
                    'homework.view',
                    'examinations.view',
                    'events.view',
                ],
            ],
            'Accountant' => [
                'description' => 'Access to financial modules, fee collections, payroll, and financial reports.',
                'is_system'   => true,
                'permissions' => [
                    'dashboard.view',
                    'fees.view', 'fees.create', 'fees.edit', 'fees.delete',
                    'payroll.view', 'payroll.create', 'payroll.edit',
                    'staff_advances.view', 'staff_advances.manage',
                    'reports.view', 'reports.export',
                ],
            ],
        ];

        // 3. Seed Roles and Sync Permissions
        foreach ($rolesConfig as $roleName => $config) {
            $role = Role::firstOrCreate(
                ['slug' => Str::slug($roleName)],
                [
                    'name'        => $roleName,
                    'description' => $config['description'],
                    'is_system'   => $config['is_system'],
                    'permissions' => $config['permissions'],
                ]
            );

            $role->update([
                'name'        => $roleName,
                'description' => $config['description'],
                'permissions' => $config['permissions'],
            ]);

            // Sync permission_role pivot IDs
            $permIds = Permission::whereIn('name', $config['permissions'])->pluck('id')->toArray();
            $role->permissionsRelation()->sync($permIds);
        }

        // 4. Clean up duplicate/obsolete roles (Teachers -> Teacher, Staff Users -> Staff)
        $teacherRole = Role::where('name', 'Teacher')->first();
        $staffRole   = Role::where('name', 'Staff')->first();

        $obsoleteRoles = Role::whereIn('slug', ['teachers', 'staff-users'])
            ->orWhereIn('name', ['Teachers', 'Staff Users'])
            ->get();

        foreach ($obsoleteRoles as $obsolete) {
            if ($obsolete->name === 'Teacher' || $obsolete->name === 'Staff') {
                continue;
            }

            $target = ($obsolete->slug === 'teachers' || $obsolete->name === 'Teachers') ? $teacherRole : $staffRole;

            if ($target) {
                User::where('role_id', $obsolete->id)
                    ->orWhere('role', $obsolete->name)
                    ->update([
                        'role'    => $target->name,
                        'role_id' => $target->id,
                    ]);
            }

            $obsolete->permissionsRelation()->detach();
            $obsolete->delete();
        }

        // 5. Update Existing Users role_id column based on role string
        $allRoles = Role::all()->keyBy(fn($r) => strtolower(trim($r->name)));

        User::chunk(50, function ($users) use ($allRoles) {
            foreach ($users as $user) {
                $userRole = strtolower(trim($user->role ?? ''));
                
                // Map legacy string roles to matched Role ID
                $matchedRole = $allRoles->get($userRole);
                if (!$matchedRole) {
                    if (in_array($userRole, ['admin', 'director', 'director / owner', 'super admin', 'owner'])) {
                        $matchedRole = $allRoles->get('admin') ?? $allRoles->get('director / owner');
                    } elseif (in_array($userRole, ['operator', 'receptionist'])) {
                        $matchedRole = $allRoles->get('operator');
                    } elseif (in_array($userRole, ['teacher', 'teachers'])) {
                        $matchedRole = $allRoles->get('teacher');
                    } elseif (in_array($userRole, ['staff', 'staff users'])) {
                        $matchedRole = $allRoles->get('staff');
                    } elseif (in_array($userRole, ['student', 'students'])) {
                        $matchedRole = $allRoles->get('student');
                    } elseif (in_array($userRole, ['parent', 'parents', 'guardian'])) {
                        $matchedRole = $allRoles->get('parent');
                    }
                }

                if ($matchedRole && ($user->role_id !== $matchedRole->id || $user->role !== $matchedRole->name)) {
                    $user->role = $matchedRole->name;
                    $user->role_id = $matchedRole->id;
                    $user->save();
                }
            }
        });
    }
}
