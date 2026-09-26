<?php

namespace App\Support;

class WhatsAppHelper
{
    /**
     * Clean and format phone number to international format (Defaulting to Pakistan 92 if leading 0).
     */
    public static function formatPhoneNumber(?string $phone): string
    {
        if (!$phone) {
            return '';
        }

        // Strip non-numeric characters except +
        $cleaned = preg_replace('/[^\d]/', '', $phone);

        if (empty($cleaned)) {
            return '';
        }

        // If local 11-digit number starting with 0 (e.g. 03001234567), replace 0 with 92
        if (str_starts_with($cleaned, '0') && strlen($cleaned) === 11) {
            $cleaned = '92' . substr($cleaned, 1);
        }

        return $cleaned;
    }

    /**
     * Generate direct WhatsApp web/app URL with pre-filled message text.
     */
    public static function makeUrl(?string $phone, string $message): string
    {
        $cleanPhone = self::formatPhoneNumber($phone);
        $encodedMsg = rawurlencode($message);

        if (empty($cleanPhone)) {
            return 'https://api.whatsapp.com/send?text=' . $encodedMsg;
        }

        return "https://api.whatsapp.com/send?phone={$cleanPhone}&text={$encodedMsg}";
    }

    /**
     * Preset Message Templates
     */
    public static function getTemplates(): array
    {
        return [
            'absence' => [
                'name' => 'Absence Notification',
                'body' => "Respected Parent, your child {student_name} (Class: {class_name}) was marked ABSENT today ({date}). Please contact administration for clarification.",
            ],
            'fee_reminder' => [
                'name' => 'Fee Payment Reminder',
                'body' => "Assalamu Alaikum! Dear Parent, the monthly fee installment of Rs. {amount} for {student_name} (Voucher #{voucher_no}) is due on {due_date}. Thank you, Accounts Dept.",
            ],
            'exam_result' => [
                'name' => 'Exam Result Card Released',
                'body' => "Dear Parent, the Exam Result Card for {student_name} ({exam_title}) has been published. Marks: {obtained_marks}/{total_marks} ({percentage}%). Grade: {grade}.",
            ],
            'admission_welcome' => [
                'name' => 'Admission Confirmation Notice',
                'body' => "Warm Welcome to EduCore School! Admission for {student_name} in Class {class_name} (Reg No: {reg_no}) is confirmed. Thank you for trusting us.",
            ],
            'general_notice' => [
                'name' => 'General School Announcement',
                'body' => "Dear Parents & Guardians, {notice_text} - EduCore School Administration.",
            ],
        ];
    }
}
