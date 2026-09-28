<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_system',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_system'   => 'boolean',
    ];

    public function permissionsRelation(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }

    public static function availablePermissionsGrouped(): array
    {
        return [
            'Dashboard' => [
                'dashboard.view' => 'Dashboard View Access',
            ],
            'Students' => [
                'students.view'         => 'View Students List & Details',
                'students.create'       => 'Create / Add Student',
                'students.edit'         => 'Edit Student Info',
                'students.delete'       => 'Move Student to Trash',
                'students.restore'      => 'Restore Trashed Student',
                'students.force_delete' => 'Permanently Delete Student',
                'students.promote'      => 'Promote Students',
            ],
            'Admissions' => [
                'admissions.view'   => 'View Admissions List',
                'admissions.create' => 'New Student Admission Form',
                'admissions.edit'   => 'Edit Admission Record',
                'admissions.delete' => 'Delete Admission Record',
            ],
            'Teachers' => [
                'teachers.view'   => 'View Teachers List',
                'teachers.create' => 'Add New Teacher',
                'teachers.edit'   => 'Edit Teacher Details',
                'teachers.delete' => 'Delete Teacher Record',
            ],
            'Staff' => [
                'staff.view'   => 'View Staff List',
                'staff.create' => 'Add New Staff',
                'staff.edit'   => 'Edit Staff Details',
                'staff.delete' => 'Delete Staff Record',
            ],
            'Classes' => [
                'classes.view'   => 'View Classes',
                'classes.create' => 'Create Class',
                'classes.edit'   => 'Edit Class',
                'classes.delete' => 'Delete Class',
            ],
            'Sections' => [
                'sections.view'   => 'View Sections',
                'sections.create' => 'Create Section',
                'sections.edit'   => 'Edit Section',
                'sections.delete' => 'Delete Section',
            ],
            'Fees & Payroll' => [
                'fees.view'             => 'View Fee Management & Receipts',
                'fees.create'           => 'Create / Collect Fee Payment',
                'fees.edit'             => 'Edit Fee Records',
                'fees.delete'           => 'Delete / Refund Fee Vouchers',
                'payroll.view'          => 'View Payroll Records',
                'payroll.create'        => 'Generate Payroll / Pay Salaries',
                'payroll.edit'          => 'Edit Payroll Details',
                'payroll.delete'        => 'Delete Payroll Record',
                'staff_advances.view'   => 'View Salary Advances',
                'staff_advances.manage' => 'Manage Salary Advances & Repayments',
            ],
            'Academic & Examination' => [
                'attendance.view'     => 'View Attendance Records',
                'attendance.create'   => 'Mark / Submit Attendance',
                'attendance.edit'     => 'Edit Attendance',
                'attendance.delete'   => 'Delete Attendance Records',
                'homework.view'       => 'View Homework Assignments',
                'homework.manage'     => 'Create / Manage Homework',
                'classwork.view'      => 'View Classwork',
                'classwork.manage'    => 'Create / Manage Classwork',
                'examinations.view'   => 'View Exams & Results',
                'examinations.manage' => 'Manage Exams & Marks Entry',
                'discipline.view'     => 'View Student Discipline',
                'discipline.manage'   => 'Manage Discipline & Ratings',
                'quran.view'          => 'View Quran Module',
                'quran.manage'        => 'Manage Quran Hifz Progress',
                'events.view'         => 'View Events & Calendar',
                'events.manage'       => 'Manage Academic Events',
            ],
            'School Setup & Operations' => [
                'school_info.view'        => 'View School Information',
                'school_info.manage'      => 'Manage School Information',
                'academic_sessions.view'  => 'View Academic Sessions',
                'academic_sessions.manage'=> 'Manage Academic Sessions',
                'subjects.view'           => 'View Subjects',
                'subjects.manage'         => 'Manage Subjects',
                'groups.manage'           => 'Manage Student Groups',
                'houses.manage'           => 'Manage Houses',
                'inventory.view'          => 'View Inventory Stock',
                'inventory.manage'        => 'Manage Inventory Stock',
                'library.view'            => 'View Library & Books',
                'library.manage'          => 'Issue & Return Books',
                'transport.view'          => 'View Transport Routes & Vehicles',
                'transport.manage'        => 'Manage Transport System',
                'skills_institute.view'   => 'View Skills Institute',
                'skills_institute.manage' => 'Manage Skills Courses',
                'certificates.view'       => 'View Certificates',
                'certificates.manage'     => 'Generate & Print Certificates',
                'id_cards.view'           => 'View ID Cards',
                'id_cards.manage'         => 'Print Student & Staff ID Cards',
            ],
            'Communication & Gate' => [
                'whatsapp.view'   => 'View WhatsApp Logs',
                'whatsapp.manage' => 'Send WhatsApp Messages',
                'meetings.view'   => 'View Meetings',
                'meetings.manage' => 'Manage Staff & PTM Meetings',
                'visitors.view'   => 'View Gate Visitors',
                'visitors.manage' => 'Manage Gate Visitors & Check-Out',
            ],
            'Reports & Settings' => [
                'reports.view'    => 'View Reports & Analytics',
                'reports.export'  => 'Export Reports (CSV/PDF)',
                'settings.view'   => 'View Custom Settings',
                'settings.edit'   => 'Edit Custom Settings',
            ],
            'Users, Roles & Security' => [
                'users.view'        => 'View Users List',
                'users.create'      => 'Create User Account',
                'users.edit'        => 'Edit User Account & Status',
                'users.delete'      => 'Delete User Account',
                'roles.view'        => 'View Roles List',
                'roles.create'      => 'Create New Role',
                'roles.edit'        => 'Edit Role & Permissions',
                'roles.delete'      => 'Delete Role',
                'permissions.view'  => 'View Permission Catalog',
                'permissions.assign'=> 'Assign Individual Allow/Deny Permissions',
                'security.view'     => 'View Security Dashboard',
                'security.manage'   => 'Manage IP Banning & Security Settings',
                'audit_logs.view'   => 'View System Audit Logs',
            ],
        ];
    }

    public static function availablePermissions(): array
    {
        return self::availablePermissionsGrouped();
    }
}
