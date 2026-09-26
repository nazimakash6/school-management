<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public const MESSAGE_TEMPLATES = [
        'Absence Notification'       => "Dear Parent,\n\nWe wish to inform you that your child {student_name} (Class: {class_name}) was marked ABSENT today ({date}).\n\nPlease contact the school administration for further clarification.\n\nBest Regards,\nSchool Administration",
        'Fee Payment Reminder'       => "Dear Parent,\n\nThis is a reminder that the monthly fee installment of Rs. {amount} for {student_name} (Voucher #{voucher_no}) is due on {due_date}.\n\nKindly deposit the payment at your earliest convenience to avoid any late charges.\n\nThank you,\nAccounts Department",
        'Exam Result Announcement'   => "Dear Parent,\n\nThe Examination Result for {student_name} has been published.\nMarks: {obtained_marks}/{total_marks} ({percentage}%)\nGrade: {grade}\n\nFor detailed result card, please contact the school.\n\nRegards,\nExamination Department",
        'Staff Attendance Alert'     => "Dear {recipient_name},\n\nThis is to inform you that staff member {staff_name} ({designation}) attendance status for {date} has been recorded as: {status}.\n\nPlease review and take necessary action if required.\n\nRegards,\nSchool Administration",
        'General School Announcement'=> "Dear Parents & Guardians,\n\n{notice_text}\n\nThank you for your continued support.\n\nRegards,\nSchool Administration",
    ];

    public function index(Request $request)
    {
        $query = EmailLog::with('creator')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('recipient_type')) {
            $query->where('recipient_type', $request->recipient_type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('recipient_name', 'like', "%{$search}%")
                  ->orWhere('recipient_email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20)->appends($request->query());

        $totalToday     = EmailLog::whereDate('created_at', Carbon::today())->count();
        $totalSent      = EmailLog::where('status', 'Sent')->count();
        $totalFailed    = EmailLog::where('status', 'Failed')->count();
        $totalMessages  = EmailLog::count();

        $successRate = $totalMessages > 0 ? round(($totalSent / $totalMessages) * 100, 1) : 100.0;

        $templates = self::MESSAGE_TEMPLATES;

        return view('pages.admin.email.index', compact(
            'logs', 'totalToday', 'totalSent', 'totalFailed', 'successRate', 'templates'
        ));
    }

    public function send(Request $request)
    {
        $request->validate([
            'recipient_type'  => 'required|string',
            'recipient_name'  => 'required|string|max:255',
            'recipient_email' => 'required|email|max:255',
            'subject'         => 'required|string|max:500',
            'message'         => 'required|string',
            'message_type'    => 'nullable|string',
            'template_name'   => 'nullable|string',
        ]);

        $status       = 'Sent';
        $errorMessage = null;

        try {
            Mail::html(nl2br(e($request->message)), function ($mail) use ($request) {
                $mail->to($request->recipient_email, $request->recipient_name)
                     ->subject($request->subject);
            });
        } catch (\Throwable $e) {
            $status       = 'Failed';
            $errorMessage = $e->getMessage();
        }

        $log = EmailLog::create([
            'recipient_type'  => $request->recipient_type,
            'recipient_name'  => $request->recipient_name,
            'recipient_email' => $request->recipient_email,
            'message_type'    => $request->message_type ?: 'Direct',
            'template_name'   => $request->template_name,
            'subject'         => $request->subject,
            'message'         => $request->message,
            'status'          => $status,
            'error_message'   => $errorMessage,
            'sent_at'         => Carbon::now(),
            'created_by'      => Auth::id(),
        ]);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => $status === 'Sent',
                'message' => $status === 'Sent'
                    ? 'Email sent successfully!'
                    : 'Email failed to send: ' . $errorMessage,
                'log_id'  => $log->id,
                'status'  => $status,
            ]);
        }

        if ($status === 'Sent') {
            return redirect()->back()->with('success', 'Email sent successfully to ' . $request->recipient_email);
        }
        return redirect()->back()->with('error', 'Email failed: ' . $errorMessage);
    }

    public function destroy($id)
    {
        $log = EmailLog::findOrFail($id);
        $log->delete();

        return redirect()->route('email.index')->with('success', 'Email log entry removed.');
    }
}
