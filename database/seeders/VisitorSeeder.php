<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\Student;
use App\Models\User;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VisitorSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $students = Student::orderBy('id')->get();
        $staffMembers = Staff::orderBy('id')->get();

        $sampleVisitors = [
            [
                'visitor_name'   => 'Muhammad Tariq Khan',
                'phone'          => '+92 300 9481234',
                'cnic_id'        => '35202-1948123-1',
                'num_persons'    => 1,
                'purpose'        => 'Student Early Pick-up',
                'meet_type'      => 'Student',
                'person_to_meet' => 'Student Early Departure (Medical Leave)',
                'vehicle_no'     => 'LEA-4912',
                'gate_no'        => 'Main Gate 1',
                'status'         => 'Checked-Out',
                'check_in_sub'   => 180, // minutes ago
                'check_out_sub'  => 120,
                'remarks'        => 'Father arrived to pick up student for dentist appointment.',
            ],
            [
                'visitor_name'   => 'Mrs. Ayesha Bilal',
                'phone'          => '+92 321 4482910',
                'cnic_id'        => '35201-8819203-4',
                'num_persons'    => 2,
                'purpose'        => 'Parent Consultation',
                'meet_type'      => 'Staff',
                'person_to_meet' => 'Class Teacher Consultation',
                'vehicle_no'     => 'LHR-8821',
                'gate_no'        => 'Gate 1',
                'status'         => 'Checked-In',
                'check_in_sub'   => 45, // minutes ago
                'check_out_sub'  => null,
                'remarks'        => 'Meeting with Class 5 teacher regarding Q2 midterm result.',
            ],
            [
                'visitor_name'   => 'Kamran Shah (TCS Express)',
                'phone'          => '+92 333 1192837',
                'cnic_id'        => '35202-7718291-5',
                'num_persons'    => 1,
                'purpose'        => 'Vendor / Delivery',
                'meet_type'      => 'General',
                'person_to_meet' => 'Admin Office (Courier)',
                'vehicle_no'     => 'Honda CD-70 Bike',
                'gate_no'        => 'Gate 2',
                'status'         => 'Checked-Out',
                'check_in_sub'   => 90,
                'check_out_sub'  => 75,
                'remarks'        => 'Delivered official examination paper parcels to main office.',
            ],
            [
                'visitor_name'   => 'Dr. Salman Hashmi',
                'phone'          => '+92 301 5566778',
                'cnic_id'        => '35202-9988112-9',
                'num_persons'    => 1,
                'purpose'        => 'Official Meeting',
                'meet_type'      => 'Staff',
                'person_to_meet' => 'Principal Office',
                'vehicle_no'     => 'LEB-9911',
                'gate_no'        => 'Main Gate 1',
                'status'         => 'Checked-In',
                'check_in_sub'   => 20,
                'check_out_sub'  => null,
                'remarks'        => 'Annual health inspection survey briefing with principal.',
            ],
            [
                'visitor_name'   => 'Rashid Mahmood',
                'phone'          => '+92 345 6789012',
                'cnic_id'        => '35201-1122334-7',
                'num_persons'    => 2,
                'purpose'        => 'Admission Query',
                'meet_type'      => 'General',
                'person_to_meet' => 'Admission Desk',
                'vehicle_no'     => 'Toyota Corolla (LEC-1029)',
                'gate_no'        => 'Gate 1',
                'status'         => 'Checked-Out',
                'check_in_sub'   => 240,
                'check_out_sub'  => 190,
                'remarks'        => 'Inquired for Grade 1 & Grade 4 fresh admission fees & prospectus.',
            ],
            [
                'visitor_name'   => 'Mrs. Nazia Farooq',
                'phone'          => '+92 300 8877665',
                'cnic_id'        => '35202-5544332-1',
                'num_persons'    => 1,
                'purpose'        => 'Fee Payment & Inquiry',
                'meet_type'      => 'Student',
                'person_to_meet' => 'Accounts Office',
                'vehicle_no'     => null,
                'gate_no'        => 'Gate 1',
                'status'         => 'Checked-Out',
                'check_in_sub'   => 300,
                'check_out_sub'  => 270,
                'remarks'        => 'Submitted quarterly tuition fee challan at bank counter.',
            ],
        ];

        foreach ($sampleVisitors as $idx => $vData) {
            $student = $vData['meet_type'] === 'Student' ? $students->shift() : null;
            $staff   = $vData['meet_type'] === 'Staff' ? $staffMembers->shift() : null;

            $checkIn = Carbon::now()->subMinutes($vData['check_in_sub']);
            $checkOut = $vData['check_out_sub'] ? Carbon::now()->subMinutes($vData['check_out_sub']) : null;

            $passCode = 'VSTR-' . date('Y') . '-' . sprintf('%04d', 1001 + $idx);

            Visitor::updateOrCreate(
                ['pass_code' => $passCode],
                [
                    'visitor_name'   => $vData['visitor_name'],
                    'phone'          => $vData['phone'],
                    'cnic_id'        => $vData['cnic_id'],
                    'num_persons'    => $vData['num_persons'],
                    'purpose'        => $vData['purpose'],
                    'meet_type'      => $vData['meet_type'],
                    'student_id'     => $student?->id,
                    'staff_id'       => $staff?->id,
                    'person_to_meet' => $vData['person_to_meet'],
                    'visit_date'     => $checkIn->format('Y-m-d'),
                    'check_in_time'  => $checkIn,
                    'check_out_time' => $checkOut,
                    'vehicle_no'     => $vData['vehicle_no'],
                    'gate_no'        => $vData['gate_no'],
                    'status'         => $vData['status'],
                    'remarks'        => $vData['remarks'],
                    'created_by'     => $user?->id,
                ]
            );
        }
    }
}
