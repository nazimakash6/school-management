<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\Student;
use App\Models\Transport;
use App\Models\TransportStudent;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransportSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Drivers into Staff Table if not already existing
        $drivers = [
            [
                'staff_id'                   => 'DRV-101',
                'first_name'                 => 'Tariq',
                'last_name'                  => 'Mehmood',
                'gender'                     => 'Male',
                'dob'                        => '1985-05-12',
                'cnic'                       => '35202-1234567-1',
                'marital_status'             => 'Married',
                'mobile_no'                  => '0300-4567891',
                'email'                      => 'tariq.driver@school.edu',
                'current_address'            => 'House 14, Block C, Model Town, Lahore',
                'permanent_address'          => 'House 14, Block C, Model Town, Lahore',
                'emergency_contact_name'     => 'Asma Mehmood',
                'emergency_contact_number'   => '0300-1112233',
                'emergency_contact_relation' => 'Wife',
                'department'                 => 'Transport',
                'designation'                => 'Driver',
                'qualification'              => 'Matriculation / HTV License Holder',
                'employment_type'            => 'full_time',
                'joining_date'               => '2023-01-15',
                'driving_license_number'     => 'HTV-LHR-884920',
                'driving_license_expiry'     => '2028-06-30',
                'vehicle_type'               => 'Coaster / Bus',
                'status'                     => 'active',
                'salary'                     => 45000.00,
            ],
            [
                'staff_id'                   => 'DRV-102',
                'first_name'                 => 'Rashid',
                'last_name'                  => 'Ali',
                'gender'                     => 'Male',
                'dob'                        => '1990-08-20',
                'cnic'                       => '35202-7654321-3',
                'marital_status'             => 'Married',
                'mobile_no'                  => '0321-9876543',
                'email'                      => 'rashid.driver@school.edu',
                'current_address'            => 'St 4, Johar Town, Lahore',
                'permanent_address'          => 'St 4, Johar Town, Lahore',
                'emergency_contact_name'     => 'Fatima Rashid',
                'emergency_contact_number'   => '0321-4455667',
                'emergency_contact_relation' => 'Wife',
                'department'                 => 'Transport',
                'designation'                => 'Driver',
                'qualification'              => 'Intermediate / LTV License Holder',
                'employment_type'            => 'full_time',
                'joining_date'               => '2023-03-01',
                'driving_license_number'     => 'LTV-LHR-551029',
                'driving_license_expiry'     => '2027-11-15',
                'vehicle_type'               => 'Toyota HiAce Van',
                'status'                     => 'active',
                'salary'                     => 40000.00,
            ],
            [
                'staff_id'                   => 'DRV-103',
                'first_name'                 => 'Ghulam',
                'last_name'                  => 'Murtaza',
                'gender'                     => 'Male',
                'dob'                        => '1982-11-04',
                'cnic'                       => '35202-9988776-5',
                'marital_status'             => 'Married',
                'mobile_no'                  => '0333-1122334',
                'email'                      => 'ghulam.murtaza@school.edu',
                'current_address'            => 'Sector Y, DHA Phase 3, Lahore',
                'permanent_address'          => 'Sector Y, DHA Phase 3, Lahore',
                'emergency_contact_name'     => 'Kamran Murtaza',
                'emergency_contact_number'   => '0333-7788990',
                'emergency_contact_relation' => 'Brother',
                'department'                 => 'Transport',
                'designation'                => 'Driver',
                'qualification'              => 'Matriculation / Senior Bus Driver',
                'employment_type'            => 'full_time',
                'joining_date'               => '2024-02-10',
                'driving_license_number'     => 'HTV-LHR-904321',
                'driving_license_expiry'     => '2029-04-20',
                'vehicle_type'               => 'School Bus',
                'status'                     => 'active',
                'salary'                     => 48000.00,
            ],
            [
                'staff_id'                   => 'DRV-104',
                'first_name'                 => 'Mukhtar',
                'last_name'                  => 'Ahmad',
                'gender'                     => 'Male',
                'dob'                        => '1988-03-15',
                'cnic'                       => '35202-3344556-7',
                'marital_status'             => 'Married',
                'mobile_no'                  => '0301-7766554',
                'email'                      => 'mukhtar.rickshaw@school.edu',
                'current_address'            => 'Garhi Shahu, Lahore',
                'permanent_address'          => 'Garhi Shahu, Lahore',
                'emergency_contact_name'     => 'Zainab Mukhtar',
                'emergency_contact_number'   => '0301-2233445',
                'emergency_contact_relation' => 'Wife',
                'department'                 => 'Transport',
                'designation'                => 'Driver',
                'qualification'              => 'Rickshaw Driving License',
                'employment_type'            => 'full_time',
                'joining_date'               => '2024-01-10',
                'driving_license_number'     => '3W-LHR-771122',
                'driving_license_expiry'     => '2028-09-20',
                'vehicle_type'               => 'Auto Ricksha',
                'status'                     => 'active',
                'salary'                     => 35000.00,
            ],
            [
                'staff_id'                   => 'DRV-105',
                'first_name'                 => 'Sajjad',
                'last_name'                  => 'Hussain',
                'gender'                     => 'Male',
                'dob'                        => '1986-07-22',
                'cnic'                       => '35202-6677889-1',
                'marital_status'             => 'Married',
                'mobile_no'                  => '0345-8899001',
                'email'                      => 'sajjad.chandigari@school.edu',
                'current_address'            => 'Shadbagh, Lahore',
                'permanent_address'          => 'Shadbagh, Lahore',
                'emergency_contact_name'     => 'Farzana Sajjad',
                'emergency_contact_number'   => '0345-1122334',
                'emergency_contact_relation' => 'Wife',
                'department'                 => 'Transport',
                'designation'                => 'Driver',
                'qualification'              => 'LTV Driving License',
                'employment_type'            => 'full_time',
                'joining_date'               => '2024-04-01',
                'driving_license_number'     => 'LTV-LHR-443322',
                'driving_license_expiry'     => '2029-01-10',
                'vehicle_type'               => 'Chandi Gari',
                'status'                     => 'active',
                'salary'                     => 38000.00,
            ]
        ];

        $driverStaffIds = [];
        foreach ($drivers as $dData) {
            $staff = Staff::updateOrCreate(['staff_id' => $dData['staff_id']], $dData);
            $driverStaffIds[] = $staff->id;
        }

        // 2. Seed Transport Routes & Vehicles
        $routes = [
            [
                'route_code'       => 'TR-RTE-101',
                'route_title'      => 'Route 1 - Gulberg & Model Town Express',
                'vehicle_number'   => 'LEA-4892',
                'vehicle_model'    => 'Toyota Coaster 2023 (AC Air-Conditioned)',
                'vehicle_type'     => 'Coaster',
                'vehicle_capacity' => 28,
                'driver_id'        => $driverStaffIds[0] ?? null,
                'driver_name'      => 'Tariq Mehmood',
                'driver_contact'   => '0300-4567891',
                'driver_license'   => 'HTV-LHR-884920',
                'fare_amount'      => 150.00,
                'pickup_stops'     => "1. Model Town Park Main Gate (07:15 AM)\n2. Gulberg Main Blvd Stop 3 (07:30 AM)\n3. Liberty Roundabout Stop (07:45 AM)\n4. School Main Campus Arrival (08:00 AM)",
                'status'           => 'Active',
                'note'             => 'Morning & Evening Shuttle route for Gulberg and Model Town residents.',
            ],
            [
                'route_code'       => 'TR-RTE-102',
                'route_title'      => 'Route 2 - DHA Phase 1-5 & Cantt Line',
                'vehicle_number'   => 'LHR-7810',
                'vehicle_model'    => 'Isuzu School Bus 2022 (36 Seater)',
                'vehicle_type'     => 'Bus',
                'vehicle_capacity' => 36,
                'driver_id'        => $driverStaffIds[2] ?? null,
                'driver_name'      => 'Ghulam Murtaza',
                'driver_contact'   => '0333-1122334',
                'driver_license'   => 'HTV-LHR-904321',
                'fare_amount'      => 180.00,
                'pickup_stops'     => "1. DHA Phase 3 Y-Block Commercial (07:10 AM)\n2. DHA Phase 5 Lalik Jan Chowk (07:25 AM)\n3. Cantt Saddar Bazaar Stop (07:40 AM)\n4. School Main Campus Arrival (08:00 AM)",
                'status'           => 'Active',
                'note'             => 'Primary bus for DHA and Cantt sector students.',
            ],
            [
                'route_code'       => 'TR-RTE-103',
                'route_title'      => 'Route 3 - Johar Town & Faisal Town Shuttle',
                'vehicle_number'   => 'LES-9021',
                'vehicle_model'    => 'Toyota HiAce Grand Cabin Van 2024',
                'vehicle_type'     => 'Van',
                'vehicle_capacity' => 15,
                'driver_id'        => $driverStaffIds[1] ?? null,
                'driver_name'      => 'Rashid Ali',
                'driver_contact'   => '0321-9876543',
                'driver_license'   => 'LTV-LHR-551029',
                'fare_amount'      => 140.00,
                'pickup_stops'     => "1. Johar Town Doctor Hospital Stop (07:20 AM)\n2. Faisal Town Moon Market (07:35 AM)\n3. Garden Town Main Gate (07:48 AM)\n4. School Main Campus Arrival (08:00 AM)",
                'status'           => 'Active',
                'note'             => 'Express van service for Johar Town students.',
            ],
            [
                'route_code'       => 'TR-RSH-104',
                'route_title'      => 'Route 4 - Garhi Shahu Auto Ricksha Pick & Drop',
                'vehicle_number'   => 'RIC-6620',
                'vehicle_model'    => 'Sazgar 9-Seater Auto Ricksha 2024',
                'vehicle_type'     => 'Auto Ricksha',
                'vehicle_capacity' => 6,
                'driver_id'        => $driverStaffIds[3] ?? null,
                'driver_name'      => 'Mukhtar Ahmad',
                'driver_contact'   => '0301-7766554',
                'driver_license'   => '3W-LHR-771122',
                'fare_amount'      => 90.00,
                'pickup_stops'     => "1. Garhi Shahu Chowk (07:30 AM)\n2. Railway Station Gate 2 (07:45 AM)\n3. School Campus (08:00 AM)",
                'status'           => 'Active',
                'note'             => 'Local Auto Ricksha service for narrow street pickups.',
            ],
            [
                'route_code'       => 'TR-CG-105',
                'route_title'      => 'Route 5 - Shadbagh Chandi Gari Shuttle',
                'vehicle_number'   => 'CG-9011',
                'vehicle_model'    => 'Chandi Gari (Open Canopy Qingqi Loader Shuttle)',
                'vehicle_type'     => 'Chandi Gari',
                'vehicle_capacity' => 10,
                'driver_id'        => $driverStaffIds[4] ?? null,
                'driver_name'      => 'Sajjad Hussain',
                'driver_contact'   => '0345-8899001',
                'driver_license'   => 'LTV-LHR-443322',
                'fare_amount'      => 80.00,
                'pickup_stops'     => "1. Shadbagh Roundabout (07:25 AM)\n2. Tajpura Market (07:40 AM)\n3. School Gate (07:55 AM)",
                'status'           => 'Active',
                'note'             => 'Traditional Chandi Gari shuttle for neighborhood students.',
            ],
        ];

        $studentIds = Student::pluck('id')->toArray();

        foreach ($routes as $index => $rData) {
            $transport = Transport::updateOrCreate(['route_code' => $rData['route_code']], $rData);

            // 3. Seed Passenger Allocations
            if (!empty($studentIds)) {
                $allocatedStudents = array_slice($studentIds, $index * 2, 2);
                foreach ($allocatedStudents as $stIdx => $stId) {
                    TransportStudent::updateOrCreate(
                        [
                            'transport_id' => $transport->id,
                            'student_id'   => $stId,
                        ],
                        [
                            'stop_name'    => "Stop #" . ($stIdx + 1) . " (" . $transport->route_title . ")",
                            'pickup_time'  => '07:25 AM',
                            'drop_time'    => '02:30 PM',
                            'monthly_fare' => $transport->fare_amount,
                            'status'       => 'Active',
                            'joining_date' => Carbon::now()->subMonths(2)->format('Y-m-d'),
                        ]
                    );
                }
            }
        }
    }
}
