<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappMessage;
use App\Models\WhatsappSetting;
use App\Support\WhatsAppHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhatsappController extends Controller
{
    public const MESSAGE_TEMPLATES = [
        'Absence Notification' => "Respected Parent, your child {student_name} (Class {class_name}) was marked ABSENT today. Please contact administration for clarification.",
        'Fee Payment Reminder' => "Assalamu Alaikum! Dear Parent, the monthly fee installment of Rs. {amount} for {student_name} is due on {due_date}. Thank you, Accounts Dept.",
        'Exam Result Card Released' => "Dear Parent, the Mid-Term Examination Result Card for {student_name} has been published. Position: {position}. Grade: {grade}.",
        'Parent-Teacher Meeting Notice' => "Dear Parents, you are cordially invited to attend the PTM consultation scheduled on {date} at {time} in the Auditorium.",
        'Emergency School Announcement' => "Important Notice: School will remain closed tomorrow due to weather advisory. Online classes will be conducted as per timetable.",
    ];

    public function index(Request $request)
    {
        $settings  = WhatsappSetting::getSingleton();
        $templates = self::MESSAGE_TEMPLATES;

        $query = WhatsappMessage::with('creator')->latest();

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
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(15)->appends($request->query());

        // Statistics
        $totalSentToday = WhatsappMessage::whereDate('created_at', Carbon::today())->count();
        $totalBroadcast = WhatsappMessage::where('message_type', 'Broadcast')->count();
        $deliveredCount = WhatsappMessage::whereIn('status', ['Sent', 'Delivered', 'Read'])->count();
        $totalMessages  = WhatsappMessage::count();

        $readRate = $totalMessages > 0 ? round(($deliveredCount / $totalMessages) * 100, 1) : 100.0;

        return view('pages.admin.whatsapp.index', compact(
            'settings', 'templates', 'messages',
            'totalSentToday', 'totalBroadcast', 'readRate'
        ));
    }

    public function pairDevice(Request $request)
    {
        $settings = WhatsappSetting::getSingleton();
        $settings->update([
            'is_connected' => true,
            'connected_at' => Carbon::now(),
            'phone_number' => $request->phone_number ?: '+92 300 9876543',
            'device_name'  => 'School Official WhatsApp Web',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success'      => true,
                'message'      => 'WhatsApp Web session active.',
                'phone_number' => $settings->phone_number,
                'device_name'  => $settings->device_name,
                'connected_at' => $settings->connected_at->format('M d, Y g:i A'),
            ]);
        }

        return redirect()->route('whatsapp.index')
            ->with('success', 'WhatsApp status set to active.');
    }

    public function disconnectDevice(Request $request)
    {
        $settings = WhatsappSetting::getSingleton();
        $settings->update([
            'is_connected' => false,
            'connected_at' => null,
        ]);

        return redirect()->route('whatsapp.index')
            ->with('warning', 'WhatsApp Web session has been reset.');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'recipient_type' => 'required|string',
            'recipient_name' => 'required|string|max:255',
            'phone_number'   => 'required|string|max:30',
            'message'        => 'required|string',
            'message_type'   => 'nullable|string',
            'template_name'  => 'nullable|string',
            'attachment'     => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:10240',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('whatsapp_attachments', 'public');
        }

        $formattedPhone = WhatsAppHelper::formatPhoneNumber($request->phone_number);
        $whatsappUrl    = WhatsAppHelper::makeUrl($formattedPhone, $request->message);

        $msg = WhatsappMessage::create([
            'recipient_type'  => $request->recipient_type,
            'recipient_name'  => $request->recipient_name,
            'phone_number'    => $formattedPhone ?: $request->phone_number,
            'message_type'    => $request->message_type ?: 'Direct',
            'template_name'   => $request->template_name,
            'message'         => $request->message,
            'attachment_path' => $attachmentPath,
            'status'          => 'Sent',
            'sent_at'         => Carbon::now(),
            'created_by'      => Auth::id(),
        ]);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success'      => true,
                'message'      => 'WhatsApp message logged successfully!',
                'whatsapp_url' => $whatsappUrl,
                'log_id'       => $msg->id,
            ]);
        }

        return redirect()->away($whatsappUrl);
    }

    public function updateSettings(Request $request)
    {
        $settings = WhatsappSetting::getSingleton();
        $settings->update([
            'auto_attendance_alert' => $request->boolean('auto_attendance_alert'),
            'auto_fee_reminder'     => $request->boolean('auto_fee_reminder'),
            'auto_exam_result'      => $request->boolean('auto_exam_result'),
            'auto_visitor_alert'    => $request->boolean('auto_visitor_alert'),
        ]);

        return redirect()->route('whatsapp.index')
            ->with('success', 'Automated WhatsApp notification triggers updated successfully!');
    }

    public function destroyMessage($id)
    {
        $msg = WhatsappMessage::findOrFail($id);
        $msg->delete();

        return redirect()->route('whatsapp.index')
            ->with('success', 'Message log entry removed.');
    }
}
