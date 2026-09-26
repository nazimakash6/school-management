<?php

namespace Database\Seeders;

use App\Models\Meeting;
use App\Models\MeetingAttendee;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MeetingSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $staffMembers = Staff::get();

        $meetings = [
            [
                'title'              => 'Monthly All-Staff General Assembly & Syllabus Review',
                'meeting_type'       => 'Staff Meeting',
                'mode'               => 'In-Person',
                'location'           => 'Main School Auditorium',
                'meeting_link'       => null,
                'meeting_date'       => Carbon::now()->addDays(2)->format('Y-m-d'),
                'start_time'         => '09:00:00',
                'end_time'           => '10:30:00',
                'target_audience'    => 'All Teachers & Administrative Staff',
                'status'             => 'Scheduled',
                'agenda'             => "1. Monthly attendance & discipline performance report.\n2. Review of midterm examination schedule & paper submission deadlines.\n3. Implementation of new digital grading portal.\n4. Staff welfare & annual sports day preparations.",
                'minutes_of_meeting' => null,
                'action_items'       => "• All HODs to submit exam papers by next Friday.\n• Admin officer to finalize sports day venue booking.",
            ],
            [
                'title'              => 'Q2 Midterm Parent-Teacher Conference (PTM)',
                'meeting_type'       => 'Parent Teacher Meeting (PTM)',
                'mode'               => 'Hybrid',
                'location'           => 'Classrooms 101 to 110 & Online Portal',
                'meeting_link'       => 'https://meet.google.com/educore-ptm-q2',
                'meeting_date'       => Carbon::now()->subDays(5)->format('Y-m-d'),
                'start_time'         => '10:00:00',
                'end_time'           => '14:00:00',
                'target_audience'    => 'Parents of Class 5 to Class 10 Students',
                'status'             => 'Completed',
                'agenda'             => "1. One-on-one progress discussion for student academic performance.\n2. Review of student attendance & behavioral discipline ratings.\n3. Distribution of Q2 progress report cards.\n4. Parent feedback on school transport & cafeteria facilities.",
                'minutes_of_meeting' => "Over 85% parent attendance recorded across all junior & senior sections. Parents expressed satisfaction with the new Star Rating discipline system. Requests were made for extra coaching in Mathematics and Quran Hifz acceleration.",
                'action_items'       => "• Math HOD to schedule remedial classes starting next Monday.\n• Transport manager to review Route 4 bus timing adjustments.",
            ],
            [
                'title'              => 'Academic Council & Curriculum Standards Meeting',
                'meeting_type'       => 'Academic Council',
                'mode'               => 'In-Person',
                'location'           => 'Conference Room B (Admin Block)',
                'meeting_link'       => null,
                'meeting_date'       => Carbon::now()->addDays(7)->format('Y-m-d'),
                'start_time'         => '11:30:00',
                'end_time'           => '13:00:00',
                'target_audience'    => 'Academic Coordinators, HODs & Vice Principal',
                'status'             => 'Scheduled',
                'agenda'             => "1. Revision of STEM & Vocational Skills curriculum.\n2. Integration of Quran Tajweed progression milestones.\n3. Evaluation of library book acquisitions for 2026-2027.",
                'minutes_of_meeting' => null,
                'action_items'       => null,
            ],
            [
                'title'              => 'Emergency Staff Briefing: Security & Campus Safety',
                'meeting_type'       => 'Emergency / Special',
                'mode'               => 'In-Person',
                'location'           => 'Staff Common Room',
                'meeting_link'       => null,
                'meeting_date'       => Carbon::now()->subDays(12)->format('Y-m-d'),
                'start_time'         => '14:15:00',
                'end_time'           => '15:00:00',
                'target_audience'    => 'All Security Personnel & Gate Supervisors',
                'status'             => 'Completed',
                'agenda'             => "1. Review visitor entry verification procedure.\n2. Inspection of CCTV camera coverage across entry gates.\n3. Protocol for emergency fire evacuation drills.",
                'minutes_of_meeting' => "All gate staff briefed on strict visitor digital badge verification. Fire drill schedule confirmed for the third week of the month.",
                'action_items'       => "• Security officer to repair Camera #4 at East Gate.\n• Admin to issue updated staff ID badges.",
            ],
            [
                'title'              => 'School Board & Executive Management Review',
                'meeting_type'       => 'Management / Board',
                'mode'               => 'Online (Zoom/Google Meet)',
                'location'           => 'Zoom Executive Room',
                'meeting_link'       => 'https://zoom.us/j/9876543210',
                'meeting_date'       => Carbon::now()->addDays(14)->format('Y-m-d'),
                'start_time'         => '16:00:00',
                'end_time'           => '17:30:00',
                'target_audience'    => 'Board Members, Principal & Director Finance',
                'status'             => 'Scheduled',
                'agenda'             => "1. Quarterly Financial Audit & PKR Fee Revenue Analysis.\n2. Campus infrastructure expansion project proposal.\n3. Staff salary increments and annual performance bonuses.",
                'minutes_of_meeting' => null,
                'action_items'       => null,
            ],
            [
                'title'              => 'Departmental HOD Coordination Meeting',
                'meeting_type'       => 'Departmental HOD',
                'mode'               => 'In-Person',
                'location'           => 'Principal Office Suite',
                'meeting_link'       => null,
                'meeting_date'       => Carbon::now()->subDays(2)->format('Y-m-d'),
                'start_time'         => '08:30:00',
                'end_time'           => '09:30:00',
                'target_audience'    => 'Department Heads (Science, English, Mathematics, Islamic Studies)',
                'status'             => 'Completed',
                'agenda'             => "1. Science Lab inventory replenishment.\n2. Coordination of weekly homework assignments.\n3. Teacher substitute roster management during leaves.",
                'minutes_of_meeting' => "Approved budget for purchasing 15 new microscopes and chemistry glassware for the senior lab. Standardized homework schedule established.",
                'action_items'       => "• Inventory manager to issue PO for lab equipment.\n• English HOD to submit weekly lesson plan template.",
            ],
        ];

        foreach ($meetings as $mData) {
            $meeting = Meeting::create([
                'title'              => $mData['title'],
                'meeting_type'       => $mData['meeting_type'],
                'mode'               => $mData['mode'],
                'location'           => $mData['location'],
                'meeting_link'       => $mData['meeting_link'],
                'meeting_date'       => $mData['meeting_date'],
                'start_time'         => $mData['start_time'],
                'end_time'           => $mData['end_time'],
                'target_audience'    => $mData['target_audience'],
                'organizer_id'       => $user?->id,
                'status'             => $mData['status'],
                'agenda'             => $mData['agenda'],
                'minutes_of_meeting' => $mData['minutes_of_meeting'],
                'action_items'       => $mData['action_items'],
                'created_by'         => $user?->id,
            ]);

            // Add attendees
            if ($staffMembers->isNotEmpty()) {
                foreach ($staffMembers as $st) {
                    MeetingAttendee::create([
                        'meeting_id'        => $meeting->id,
                        'attendee_type'     => 'staff',
                        'attendee_id'       => $st->id,
                        'name'              => $st->first_name . ' ' . $st->last_name,
                        'email'             => $st->email ?: (strtolower($st->first_name) . '@school.edu.pk'),
                        'role'              => $st->designation ?: 'Staff Member',
                        'attendance_status' => $mData['status'] === 'Completed' ? (rand(0, 10) > 2 ? 'Attended' : 'Excused') : 'Invited',
                    ]);
                }
            } else {
                MeetingAttendee::create([
                    'meeting_id'        => $meeting->id,
                    'attendee_type'     => 'user',
                    'attendee_id'       => $user?->id,
                    'name'              => $user?->name ?: 'School Administrator',
                    'email'             => $user?->email ?: 'admin@school.edu.pk',
                    'role'              => 'Meeting Convener',
                    'attendance_status' => 'Attended',
                ]);
            }
        }
    }
}
