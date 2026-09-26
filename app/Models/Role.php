<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public static function availablePermissions(): array
    {
        return [
            'Dashboard' => [
                'dashboard.view' => 'Dashboard Access',
            ],
            'Students' => [
                'students.view'     => 'Student List',
                'admissions.view'   => 'Admission List',
                'admissions.create' => 'New Admission Form',
                'students.promote'  => 'Student Promotion',
            ],
            'HR & Finance' => [
                'teachers.manage'       => 'Teachers Management',
                'staff.manage'          => 'Staff Management',
                'staff_advances.manage' => 'Salary Advances',
                'payroll.manage'        => 'Payroll Processing',
                'fees.manage'           => 'Fee Management',
                'fee_statement.view'    => 'Fee Statement Reports',
            ],
            'School Setup' => [
                'school_info.manage'       => 'School Information',
                'academic_sessions.manage' => 'Academic Sessions',
                'classes.manage'           => 'Classes Setup',
                'subjects.manage'          => 'Subjects Setup',
                'groups.manage'            => 'Groups Setup',
                'houses.manage'            => 'Houses Setup',
            ],
            'Academic' => [
                'attendance.manage'   => 'Attendance Marking',
                'homework.manage'     => 'Homework Assignments',
                'classwork.manage'    => 'Class Work Management',
                'examinations.manage' => 'Examination & Marks Entry',
                'events.manage'       => 'Academic Planning & Events',
                'discipline.manage'   => 'Discipline Management',
                'quran.manage'        => 'Quran Hifz Module',
            ],
            'Operations' => [
                'inventory.manage'        => 'Inventory Stock',
                'library.manage'          => 'Library & Books',
                'transport.manage'        => 'Transport System',
                'skills_institute.manage' => 'Skills Institute',
                'certificates.manage'     => 'Certificates Generation',
                'id_cards.manage'         => 'ID Cards Printing',
            ],
            'Communication' => [
                'whatsapp.manage' => 'WhatsApp Integration',
                'meetings.manage' => 'Staff & PTM Meetings',
            ],
            'Administration & Gate' => [
                'visitors.manage' => 'Visitor Gate Management',
            ],
            'Reports & Settings' => [
                'reports.view'         => 'Reports & Analytics',
                'settings.manage'      => 'Custom School Settings',
            ],
            'System Security & Users' => [
                'users.manage'      => 'User Management',
                'roles.manage'      => 'Roles & Permissions',
                'security.manage'   => 'Security Settings',
                'audit_logs.view'   => 'Audit Logs',
            ],
        ];
    }
}
