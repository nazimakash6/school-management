<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\StaffAdvance;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StaffAdvanceSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('Seeding Staff Advances Demo Data...');

        $staffMembers = Staff::all();

        if ($staffMembers->isEmpty()) {
            $this->call(StaffSeeder::class);
            $staffMembers = Staff::all();
        }

        $reasons = [
            'Medical emergency for family member',
            'House maintenance and repair work',
            'Children annual school fee payment',
            'Motorcycle purchase / vehicle repair',
            'Marriage ceremony expenses in family',
            'Advance against festival expenses',
            'Emergency personal financial commitment',
            'Laptops / IT equipment purchase',
            'Home rental advance deposit',
            'Higher education fees for self/dependents',
        ];

        $paymentMethods = ['cash', 'bank_transfer', 'cheque'];

        // Preset sample advance records for realistic financial overview
        $sampleData = [
            [
                'staff_index' => 0,
                'advance_amount' => 50000.00,
                'repaid_amount' => 20000.00,
                'monthly_installment' => 10000.00,
                'advance_date' => Carbon::now()->subMonths(3)->format('Y-m-d'),
                'payment_method' => 'bank_transfer',
                'status' => 'partially_repaid',
                'reason' => 'Medical emergency for family member',
                'notes' => 'Deducting Rs. 10,000 monthly from salary starting from last 2 months.',
            ],
            [
                'staff_index' => 1,
                'advance_amount' => 30000.00,
                'repaid_amount' => 30000.00,
                'monthly_installment' => 10000.00,
                'advance_date' => Carbon::now()->subMonths(4)->format('Y-m-d'),
                'payment_method' => 'bank_transfer',
                'status' => 'fully_repaid',
                'reason' => 'Children annual school fee payment',
                'notes' => 'Fully cleared through 3 consecutive monthly salary deductions.',
            ],
            [
                'staff_index' => 2,
                'advance_amount' => 25000.00,
                'repaid_amount' => 0.00,
                'monthly_installment' => 5000.00,
                'advance_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'payment_method' => 'cash',
                'status' => 'approved',
                'reason' => 'Motorcycle purchase / vehicle repair',
                'notes' => 'Approved by Finance Dept. Monthly deduction starting next payroll cycle.',
            ],
            [
                'staff_index' => 3,
                'advance_amount' => 40000.00,
                'repaid_amount' => 0.00,
                'monthly_installment' => 8000.00,
                'advance_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'payment_method' => 'cheque',
                'status' => 'pending',
                'reason' => 'House maintenance and repair work',
                'notes' => 'Application submitted and awaiting Principal approval.',
            ],
            [
                'staff_index' => 4,
                'advance_amount' => 60000.00,
                'repaid_amount' => 45000.00,
                'monthly_installment' => 15000.00,
                'advance_date' => Carbon::now()->subMonths(5)->format('Y-m-d'),
                'payment_method' => 'bank_transfer',
                'status' => 'partially_repaid',
                'reason' => 'Marriage ceremony expenses in family',
                'notes' => 'Rs. 15,000 monthly deduction agreed.',
            ],
            [
                'staff_index' => 5,
                'advance_amount' => 15000.00,
                'repaid_amount' => 15000.00,
                'monthly_installment' => 5000.00,
                'advance_date' => Carbon::now()->subMonths(2)->format('Y-m-d'),
                'payment_method' => 'cash',
                'status' => 'fully_repaid',
                'reason' => 'Advance against festival expenses',
                'notes' => 'Cleared completely.',
            ],
            [
                'staff_index' => 6,
                'advance_amount' => 35000.00,
                'repaid_amount' => 0.00,
                'monthly_installment' => 7000.00,
                'advance_date' => Carbon::now()->subDays(15)->format('Y-m-d'),
                'payment_method' => 'bank_transfer',
                'status' => 'rejected',
                'reason' => 'Emergency personal financial commitment',
                'notes' => 'Rejected due to existing active loan balance.',
            ],
            [
                'staff_index' => 7,
                'advance_amount' => 75000.00,
                'repaid_amount' => 25000.00,
                'monthly_installment' => 12500.00,
                'advance_date' => Carbon::now()->subMonths(2)->format('Y-m-d'),
                'payment_method' => 'cheque',
                'status' => 'partially_repaid',
                'reason' => 'Home rental advance deposit',
                'notes' => 'Approved under special staff welfare scheme.',
            ],
            [
                'staff_index' => 8,
                'advance_amount' => 20000.00,
                'repaid_amount' => 0.00,
                'monthly_installment' => 5000.00,
                'advance_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'payment_method' => 'cash',
                'status' => 'pending',
                'reason' => 'Laptops / IT equipment purchase',
                'notes' => 'Under review by HR committee.',
            ],
            [
                'staff_index' => 9,
                'advance_amount' => 50000.00,
                'repaid_amount' => 50000.00,
                'monthly_installment' => 10000.00,
                'advance_date' => Carbon::now()->subMonths(6)->format('Y-m-d'),
                'payment_method' => 'bank_transfer',
                'status' => 'fully_repaid',
                'reason' => 'Higher education fees for self/dependents',
                'notes' => 'Successfully repaid in full.',
            ],
        ];

        $createdCount = 0;

        foreach ($sampleData as $data) {
            $staff = $staffMembers->get($data['staff_index'] % $staffMembers->count());

            StaffAdvance::create([
                'staff_id' => $staff->id,
                'advance_amount' => $data['advance_amount'],
                'repaid_amount' => $data['repaid_amount'],
                'monthly_installment' => $data['monthly_installment'],
                'advance_date' => $data['advance_date'],
                'payment_method' => $data['payment_method'],
                'status' => $data['status'],
                'reason' => $data['reason'],
                'notes' => $data['notes'],
            ]);
            $createdCount++;
        }

        // Add additional random realistic advances for broader staff representation
        for ($i = 10; $i < min(25, $staffMembers->count()); $i++) {
            $staff = $staffMembers->get($i);
            $amount = rand(15, 60) * 1000;
            $repaid = rand(0, 2) === 0 ? 0 : (rand(0, 1) === 0 ? $amount : rand(1, floor($amount / 1000) - 1) * 1000);
            $installment = min($amount, rand(3, 10) * 1000);
            $status = $repaid >= $amount ? 'fully_repaid' : ($repaid > 0 ? 'partially_repaid' : ['pending', 'approved', 'rejected'][rand(0, 2)]);
            
            StaffAdvance::create([
                'staff_id' => $staff->id,
                'advance_amount' => $amount,
                'repaid_amount' => $repaid,
                'monthly_installment' => $installment,
                'advance_date' => Carbon::now()->subDays(rand(1, 120))->format('Y-m-d'),
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'status' => $status,
                'reason' => $reasons[array_rand($reasons)],
                'notes' => 'Automated demo seed entry.',
            ]);
            $createdCount++;
        }

        $this->command?->info("✓ Total {$createdCount} staff advance records seeded successfully!");
    }
}
