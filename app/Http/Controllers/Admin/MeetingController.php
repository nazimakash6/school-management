<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingAttendee;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MeetingController extends Controller
{
    public const TYPES = [
        'Staff Meeting',
        'Parent Teacher Meeting (PTM)',
        'Management / Board',
        'Departmental HOD',
        'Academic Council',
        'Emergency / Special',
    ];

    public const MODES = [
        'In-Person',
        'Online (Zoom/Google Meet)',
        'Hybrid',
    ];

    public const STATUSES = [
        'Scheduled',
        'In-Progress',
        'Completed',
        'Postponed',
        'Cancelled',
    ];

    public function index(Request $request)
    {
        $types    = self::TYPES;
        $modes    = self::MODES;
        $statuses = self::STATUSES;

        $query = Meeting::with(['organizer', 'creator', 'attendees'])
            ->latest('meeting_date')
            ->latest('start_time');

        if ($request->filled('type')) {
            $query->where('meeting_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('mode')) {
            $query->where('mode', $request->mode);
        }

        if ($request->filled('date')) {
            $query->whereDate('meeting_date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('target_audience', 'like', "%{$search}%")
                  ->orWhere('agenda', 'like', "%{$search}%");
            });
        }

        $meetings = $query->paginate(15)->appends($request->query());

        // Statistics
        $totalMeetings      = Meeting::count();
        $upcomingCount      = Meeting::where('status', 'Scheduled')->where('meeting_date', '>=', date('Y-m-d'))->count();
        $completedThisMonth = Meeting::where('status', 'Completed')
            ->whereMonth('meeting_date', Carbon::now()->month)
            ->whereYear('meeting_date', Carbon::now()->year)
            ->count();
        $staffMeetingsCount = Meeting::where('meeting_type', 'Staff Meeting')->count();
        $ptmCount           = Meeting::where('meeting_type', 'Parent Teacher Meeting (PTM)')->count();

        return view('pages.admin.meetings.index', compact(
            'meetings', 'types', 'modes', 'statuses',
            'totalMeetings', 'upcomingCount', 'completedThisMonth', 'staffMeetingsCount', 'ptmCount'
        ));
    }

    public function create()
    {
        $types     = self::TYPES;
        $modes     = self::MODES;
        $statuses  = self::STATUSES;
        $staffList = Staff::orderBy('first_name')->get();

        return view('pages.admin.meetings.create', compact('types', 'modes', 'statuses', 'staffList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'meeting_type'    => 'required|string',
            'mode'            => 'required|string',
            'meeting_date'    => 'required|date',
            'start_time'      => 'required',
            'end_time'        => 'nullable',
            'target_audience' => 'required|string|max:255',
            'location'        => 'nullable|string|max:255',
            'meeting_link'    => 'nullable|url|max:255',
            'status'          => 'required|string',
            'agenda'          => 'nullable|string',
            'action_items'    => 'nullable|string',
            'attachment'      => 'nullable|file|mimes:pdf,doc,docx,png,jpg,webp|max:10240',
            'attendees'       => 'nullable|array',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('meeting_docs', 'public');
        }

        $meeting = Meeting::create([
            'title'              => $request->title,
            'meeting_type'       => $request->meeting_type,
            'mode'               => $request->mode,
            'location'           => $request->location,
            'meeting_link'       => $request->meeting_link,
            'meeting_date'       => $request->meeting_date,
            'start_time'         => $request->start_time,
            'end_time'           => $request->end_time,
            'target_audience'    => $request->target_audience,
            'organizer_id'       => Auth::id(),
            'status'             => $request->status,
            'agenda'             => $request->agenda,
            'minutes_of_meeting' => $request->minutes_of_meeting,
            'action_items'       => $request->action_items,
            'attachment_path'    => $attachmentPath,
            'created_by'         => Auth::id(),
        ]);

        // Attach selected staff members
        if ($request->filled('attendees')) {
            $staffMembers = Staff::whereIn('id', $request->attendees)->get();
            foreach ($staffMembers as $st) {
                MeetingAttendee::create([
                    'meeting_id'        => $meeting->id,
                    'attendee_type'     => 'staff',
                    'attendee_id'       => $st->id,
                    'name'              => $st->first_name . ' ' . $st->last_name,
                    'email'             => $st->email,
                    'role'              => $st->designation ?: 'Staff Member',
                    'attendance_status' => 'Invited',
                ]);
            }
        }

        return redirect()->route('meetings.index')
            ->with('success', "Meeting '{$meeting->title}' scheduled successfully!");
    }

    public function show($id)
    {
        $meeting = Meeting::with(['organizer', 'creator', 'attendees'])->findOrFail($id);

        $totalInvited = $meeting->attendees->count();
        $attendedCount = $meeting->attendees->where('attendance_status', 'Attended')->count();
        $excusedCount  = $meeting->attendees->where('attendance_status', 'Excused')->count();
        $absentCount   = $meeting->attendees->where('attendance_status', 'Absent')->count();

        return view('pages.admin.meetings.show', compact(
            'meeting', 'totalInvited', 'attendedCount', 'excusedCount', 'absentCount'
        ));
    }

    public function edit($id)
    {
        $meeting   = Meeting::with('attendees')->findOrFail($id);
        $types     = self::TYPES;
        $modes     = self::MODES;
        $statuses  = self::STATUSES;
        $staffList = Staff::orderBy('first_name')->get();

        return view('pages.admin.meetings.edit', compact('meeting', 'types', 'modes', 'statuses', 'staffList'));
    }

    public function update(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);

        $request->validate([
            'title'           => 'required|string|max:255',
            'meeting_type'    => 'required|string',
            'mode'            => 'required|string',
            'meeting_date'    => 'required|date',
            'start_time'      => 'required',
            'end_time'        => 'nullable',
            'target_audience' => 'required|string|max:255',
            'location'        => 'nullable|string|max:255',
            'meeting_link'    => 'nullable|url|max:255',
            'status'          => 'required|string',
            'agenda'          => 'nullable|string',
            'minutes_of_meeting' => 'nullable|string',
            'action_items'    => 'nullable|string',
            'attachment'      => 'nullable|file|mimes:pdf,doc,docx,png,jpg,webp|max:10240',
        ]);

        $data = [
            'title'              => $request->title,
            'meeting_type'       => $request->meeting_type,
            'mode'               => $request->mode,
            'location'           => $request->location,
            'meeting_link'       => $request->meeting_link,
            'meeting_date'       => $request->meeting_date,
            'start_time'         => $request->start_time,
            'end_time'           => $request->end_time,
            'target_audience'    => $request->target_audience,
            'status'             => $request->status,
            'agenda'             => $request->agenda,
            'minutes_of_meeting' => $request->minutes_of_meeting,
            'action_items'       => $request->action_items,
        ];

        if ($request->hasFile('attachment')) {
            if ($meeting->attachment_path) {
                Storage::disk('public')->delete($meeting->attachment_path);
            }
            $data['attachment_path'] = $request->file('attachment')->store('meeting_docs', 'public');
        }

        $meeting->update($data);

        return redirect()->route('meetings.index')
            ->with('success', 'Meeting record updated successfully!');
    }

    public function destroy($id)
    {
        $meeting = Meeting::findOrFail($id);
        if ($meeting->attachment_path) {
            Storage::disk('public')->delete($meeting->attachment_path);
        }
        $meeting->delete();

        return redirect()->route('meetings.index')
            ->with('success', 'Meeting record deleted successfully!');
    }

    /**
     * Action: Update Minutes of Meeting & Action Items directly
     */
    public function updateMinutes(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);
        $request->validate([
            'minutes_of_meeting' => 'nullable|string',
            'action_items'       => 'nullable|string',
            'status'             => 'nullable|string',
        ]);

        $meeting->update([
            'minutes_of_meeting' => $request->minutes_of_meeting,
            'action_items'       => $request->action_items,
            'status'             => $request->status ?: $meeting->status,
        ]);

        return redirect()->route('meetings.show', $id)
            ->with('success', 'Minutes of Meeting (MoM) & Action Items updated!');
    }

    /**
     * Action: Update attendee status (Attended, Excused, Absent)
     */
    public function updateAttendeeStatus(Request $request, $id, $attendeeId)
    {
        $attendee = MeetingAttendee::where('meeting_id', $id)->findOrFail($attendeeId);
        $request->validate([
            'attendance_status' => 'required|in:Invited,Attended,Excused,Absent',
        ]);

        $attendee->update([
            'attendance_status' => $request->attendance_status,
        ]);

        return redirect()->route('meetings.show', $id)
            ->with('success', "Attendance status updated for {$attendee->name}.");
    }
}
