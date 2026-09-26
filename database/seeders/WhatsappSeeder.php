<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WhatsappMessage;
use App\Models\WhatsappSetting;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class WhatsappSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        // Seed initial setting
        WhatsappSetting::firstOrCreate([], [
            'is_connected'          => true, // Logged in by default
            'phone_number'          => '+92 300 9876543',
            'device_name'           => 'School Official WhatsApp Web',
            'connected_at'          => Carbon::now(),
            'auto_attendance_alert' => true,
            'auto_fee_reminder'     => true,
            'auto_exam_result'      => true,
            'auto_visitor_alert'    => true,
        ]);

        $sampleMessages = [
            [
                'recipient_type' => 'Parent',
                'recipient_name' => 'Muhammad Ali (Father of Fatima Ali)',
                'phone_number'   => '+92 301 2345678',
                'message_type'   => 'Automated Alert',
                'template_name' => 'Fee Payment Reminder',
                'message'        => 'Assalamu Alaikum! Dear Parent, kindly note that the fee installment of Rs. 12,500 for Fatima Ali (Class 5-A) is due on 20th August. Thank you, Accounts Dept.',
                'status'         => 'Read',
                'sent_at'        => Carbon::now()->subMinutes(25),
            ],
            [
                'recipient_type' => 'Parent',
                'recipient_name' => 'Tariq Mehmood (Father of Hamza Tariq)',
                'phone_number'   => '+92 321 8765432',
                'message_type'   => 'Automated Alert',
                'template_name' => 'Absence Notification',
                'message'        => 'Respected Parent, your child Hamza Tariq (Class 8-B) was marked ABSENT today (13th Aug 2026). Please contact the administration if this is an error.',
                'status'         => 'Delivered',
                'sent_at'        => Carbon::now()->subHours(2),
            ],
            [
                'recipient_type' => 'Staff',
                'recipient_name' => 'Ustadh Bilal Ahmed (Teacher)',
                'phone_number'   => '+92 333 4567890',
                'message_type'   => 'Direct',
                'template_name' => 'Meeting Invitation',
                'message'        => 'Dear Ustadh Bilal, you are requested to attend the Academic Council Meeting today at 02:00 PM in the Principal Conference Room.',
                'status'         => 'Read',
                'sent_at'        => Carbon::now()->subHours(4),
            ],
            [
                'recipient_type' => 'Broadcast',
                'recipient_name' => 'Class 10 All Parents (45 Contacts)',
                'phone_number'   => '+92 300 0000000',
                'message_type'   => 'Broadcast',
                'template_name' => 'Exam Date Sheet Announcement',
                'message'        => 'Dear Parents, the Mid-Term Examination Date Sheet for Class 10 has been published on the student portal. Exams commence next Monday at 08:30 AM.',
                'status'         => 'Sent',
                'sent_at'        => Carbon::now()->subDays(1),
            ],
        ];

        foreach ($sampleMessages as $m) {
            WhatsappMessage::create(array_merge($m, ['created_by' => $user?->id]));
        }
    }
}
