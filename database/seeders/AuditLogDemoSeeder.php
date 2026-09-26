<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AuditLogs;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuditLogDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $userId = $user ? $user->id : 1;

        $sampleLogs = [
            [
                'user_id' => $userId,
                'event_type' => 'auth',
                'action' => 'user_login',
                'description' => 'User Administrator logged in successfully.',
                'route' => '/login',
                'method' => 'POST',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
                'context' => json_encode(['email' => 'admin@school.com', 'status' => 'success']),
                'created_at' => now()->subMinutes(5),
            ],
            [
                'user_id' => $userId,
                'event_type' => 'examination',
                'action' => 'exam_created',
                'description' => 'Created new examination: Monthly Assessment Test August 2026 for Class 1.',
                'route' => '/examination',
                'method' => 'POST',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
                'context' => json_encode(['exam_title' => 'Monthly Assessment Test August 2026', 'class' => 'Class 1', 'total_marks' => 100]),
                'created_at' => now()->subMinutes(25),
            ],
            [
                'user_id' => $userId,
                'event_type' => 'examination',
                'action' => 'marks_updated',
                'description' => 'Updated bulk student examination marks for Class 1 (35 students processed).',
                'route' => '/examination/1/marks',
                'method' => 'POST',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
                'context' => json_encode(['exam_id' => 1, 'students_count' => 35, 'subjects' => ['English', 'Mathematics', 'Science']]),
                'created_at' => now()->subHours(1),
            ],
            [
                'user_id' => $userId,
                'event_type' => 'admission',
                'action' => 'admission_created',
                'description' => 'Admitted new student: Muhammad Ali (Adm No: ADM-2026-098) into Class 5.',
                'route' => '/admission',
                'method' => 'POST',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
                'context' => json_encode(['student_name' => 'Muhammad Ali', 'admission_no' => 'ADM-2026-098', 'class' => 'Class 5']),
                'created_at' => now()->subHours(3),
            ],
            [
                'user_id' => $userId,
                'event_type' => 'fee',
                'action' => 'payment_recorded',
                'description' => 'Recorded tuition fee payment Rs. 15,500 for Student ADM-2026-042.',
                'route' => '/fee-management/1/payment',
                'method' => 'POST',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
                'context' => json_encode(['amount' => 15500, 'payment_mode' => 'Cash', 'receipt_no' => 'REC-88910']),
                'created_at' => now()->subHours(5),
            ],
            [
                'user_id' => $userId,
                'event_type' => 'security',
                'action' => 'password_changed',
                'description' => 'Administrator updated account security password.',
                'route' => '/password/manage',
                'method' => 'PUT',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
                'context' => json_encode(['forced' => false, 'ip' => '127.0.0.1']),
                'created_at' => now()->subDays(1),
            ],
            [
                'user_id' => $userId,
                'event_type' => 'settings',
                'action' => 'settings_updated',
                'description' => 'Updated Custom System Settings (School Name, Contact Phone, Late Fee Fine).',
                'route' => '/custom-settings',
                'method' => 'POST',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
                'context' => json_encode(['updated_keys' => ['school_name', 'school_phone', 'late_fee_fine']]),
                'created_at' => now()->subDays(2),
            ],
            [
                'user_id' => $userId,
                'event_type' => 'security',
                'action' => 'failed_login_warning',
                'description' => 'Multiple failed login attempts detected from IP 192.168.1.105.',
                'route' => '/login',
                'method' => 'POST',
                'ip_address' => '192.168.1.105',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/119.0.0.0',
                'context' => json_encode(['attempts' => 4, 'target_email' => 'admin@school.com']),
                'created_at' => now()->subDays(3),
            ],
        ];

        foreach ($sampleLogs as $log) {
            DB::table('audit_logs')->insert(array_merge($log, ['updated_at' => $log['created_at']]));
        }
    }
}
