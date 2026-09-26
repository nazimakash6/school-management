<?php

namespace Database\Seeders;

use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('Seeding Staff Members Demo Data...');

        $firstNamesMale = ['Shaheen', 'Tariq', 'Kamran', 'Usman', 'Rashid', 'Bilal', 'Kashif', 'Asim', 'Abdul', 'Hamza', 'Imran', 'Fahad', 'Mohsin', 'Muhammad', 'Zahid', 'Faisal', 'Noman', 'Adeel', 'Waqas', 'Sajid', 'Salman', 'Haroon', 'Omer', 'Danish', 'Zain'];
        $firstNamesFemale = ['Sadia', 'Farzana', 'Saima', 'Nabila', 'Bushra', 'Ayesha', 'Maryam', 'Hina', 'Tahira', 'Samina', 'Rabia', 'Uzma', 'Sobia', 'Khadija', 'Zunaira', 'Fatima', 'Amna', 'Maimoona', 'Nida', 'Sobia', 'Farah', 'Sidra', 'Asma', 'Irum', 'Sumaira'];
        $lastNames = ['Akhtar', 'Mehmood', 'Ali', 'Ghani', 'Minhas', 'Hassan', 'Riaz', 'Raza', 'Rehman', 'Shah', 'Khan', 'Naeem', 'Javed', 'Yaqoob', 'Imran', 'Malik', 'Qureshi', 'Siddiqui', 'Bhatti', 'Chaudhry', 'Sheikh', 'Zafar', 'Iqbal', 'Mirza', 'Abbas'];

        $departments = [
            'Administration' => ['Principal', 'Vice Principal', 'Admin Officer', 'Front Desk Officer', 'HR Executive'],
            'Academics' => [
                'Teacher', 'Senior Teacher', 'Teacher', 'Teacher',
                'Teacher', 'Teacher', 'Teacher', 'Teacher',
                'Teacher', 'Junior Teacher', 'Kindergarten Teacher', 'Art Teacher'
            ],
            'Finance' => ['Head Accountant', 'Assistant Accountant', 'Fee Auditor', 'Accounts Clerk'],
            'IT Department' => ['IT Systems Administrator', 'Computer Lab Manager', 'Network Engineer'],
            'Library' => ['Head Librarian', 'Assistant Librarian', 'Cataloger'],
            'Sports' => ['Head Sports Instructor', 'Physical Education Teacher', 'Cricket Coach'],
            'Security' => ['Head Security Officer', 'Senior Gate Supervisor', 'Shift Security In-charge'],
            'Transport' => ['Transport Fleet Manager', 'Senior Bus Supervisor', 'Route Coordinator'],
        ];

        $qualifications = [
            'Ph.D. in Educational Leadership', 'M.Phil in Education', 'M.Sc. Mathematics', 'M.Sc. Physics',
            'M.Sc. Chemistry', 'M.Sc. Zoology', 'M.A. English Literature', 'M.A. Urdu', 'M.A. Islamiat',
            'BS Computer Science', 'M.Com / ACMA Finalist', 'B.Com / PIPFA', 'M.Sc. Library Sciences',
            'M.P.Ed (Physical Education)', 'B.Ed / M.A. Education', 'B.A. Honors', 'Diploma in Associate Engineering'
        ];

        $banks = ['Meezan Bank', 'Habib Bank Limited', 'Bank Alfalah', 'MCB Bank', 'United Bank Limited', 'Standard Chartered'];

        $staffList = [];
        $idCounter = 1;

        foreach ($departments as $dept => $designations) {
            foreach ($designations as $desig) {
                $gender = (rand(0, 1) === 1 || in_array($desig, ['Front Desk Officer', 'Kindergarten Head Teacher', 'Art & Craft Instructor'])) ? 'Female' : 'Male';
                $firstName = ($gender === 'Female') ? $firstNamesFemale[array_rand($firstNamesFemale)] : $firstNamesMale[array_rand($firstNamesMale)];
                $lastName = $lastNames[array_rand($lastNames)];
                $staffId = 'STF-' . str_pad($idCounter, 3, '0', STR_PAD_LEFT);
                $salary = rand(45, 150) * 1000;
                if (str_contains($desig, 'Principal')) $salary = 180000;
                if (str_contains($desig, 'Lecturer') || str_contains($desig, 'Head')) $salary = rand(80, 120) * 1000;

                $bankName = $banks[array_rand($banks)];

                $staffList[] = [
                    'staff_id' => $staffId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'gender' => $gender,
                    'dob' => Carbon::now()->subYears(rand(24, 52))->subDays(rand(1, 300))->format('Y-m-d'),
                    'cnic' => '35202-' . rand(1000000, 9999999) . '-' . ($gender === 'Male' ? 1 : 2),
                    'marital_status' => rand(0, 1) ? 'Married' : 'Single',
                    'blood_group' => ['A+', 'B+', 'O+', 'AB+'][rand(0, 3)],
                    'religion' => 'Islam',
                    'nationality' => 'Pakistani',
                    'mobile_no' => '03' . rand(0, 4) . rand(10000000, 99999999),
                    'alternate_mobile_no' => '03' . rand(0, 4) . rand(10000000, 99999999),
                    'email' => strtolower($firstName . '.' . Str::slug($desig) . '@educore.edu.pk'),
                    'current_address' => 'House #' . rand(1, 150) . ', Block ' . chr(rand(65, 70)) . ', Gulberg III, Lahore',
                    'permanent_address' => 'House #' . rand(1, 150) . ', Block ' . chr(rand(65, 70)) . ', Gulberg III, Lahore',
                    'joining_date' => Carbon::now()->subYears(rand(1, 8))->subMonths(rand(1, 11))->format('Y-m-d'),
                    'emergency_contact_name' => $lastNames[array_rand($lastNames)] . ' Family Member',
                    'emergency_contact_number' => '0300' . rand(1000000, 9999999),
                    'emergency_contact_relation' => ['Spouse', 'Brother', 'Father', 'Sister'][rand(0, 3)],
                    'department' => $dept,
                    'designation' => $desig,
                    'qualification' => $qualifications[array_rand($qualifications)],
                    'experience' => rand(2, 18) . ' Years',
                    'employment_type' => 'Full-time',
                    'shift' => 'Morning',
                    'salary' => $salary,
                    'salary_type' => 'Monthly',
                    'bank_name' => $bankName,
                    'bank_account_title' => $firstName . ' ' . $lastName,
                    'bank_account_number' => 'PK' . rand(10, 99) . strtoupper(Str::random(4)) . rand(1000000000, 9999999999),
                    'iban' => 'PK' . rand(10, 99) . 'MEZN' . rand(10000000000000, 99999999999999),
                    'status' => 'active',
                ];

                $idCounter++;
            }
        }

        $createdCount = 0;
        foreach ($staffList as $stData) {
            Staff::updateOrCreate(['staff_id' => $stData['staff_id']], $stData);
            $createdCount++;
        }

        $thiscommandInfo = $this->command?->info("✓ Total {$createdCount} staff members seeded across all departments!");
    }
}
