<?php

namespace Database\Factories;

use App\Models\Admission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Admission>
 */
class AdmissionFactory extends Factory
{
    protected $model = Admission::class;

    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        $fatherName = fake()->name('male');

        return [
            'admission_no' => 'ADM-' . fake()->unique()->numberBetween(10000, 99999),
            'admission_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'cnic_bform' => fake()->numerify('35202-#######-#'),
            'date_of_birth' => fake()->dateTimeBetween('-16 years', '-5 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(['Male', 'Female']),
            'blood_group' => fake()->randomElement(['A+', 'B+', 'O+', 'AB+', 'A-', 'B-', 'O-']),
            'religion' => 'Islam',
            'nationality' => 'Pakistani',
            'student_mobile_no' => '03' . fake()->numerify('#########'),
            'student_email' => strtolower($firstName . '.' . $lastName) . '@example.com',
            'father_name' => $fatherName,
            'mother_name' => fake()->name('female'),
            'guardian_name' => $fatherName,
            'guardian_relation' => 'Father',
            'guardian_cnic' => fake()->numerify('35202-#######-#'),
            'guardian_occupation' => fake()->jobTitle(),
            'guardian_primary_mobile_no' => '03' . fake()->numerify('#########'),
            'guardian_address' => fake()->streetAddress() . ', Lahore',
            'class_name' => fake()->randomElement(['Class 1', 'Class 2', 'Class 3', 'Class 4', 'Class 5', 'Class 6', 'Class 7', 'Class 8', 'Class 9', 'Class 10']),
            'section_name' => fake()->randomElement(['A', 'B', 'C']),
            'roll_no' => (string) fake()->unique()->numberBetween(1000, 99999),
            'monthly_fee' => fake()->randomElement([3500, 4500, 5500, 6000, 7500]),
            'current_address' => fake()->streetAddress() . ', Lahore',
            'emergency_contact_name' => $fatherName,
            'emergency_contact_relation' => 'Father',
            'emergency_contact_mobile_no' => '03' . fake()->numerify('#########'),
            'admission_status' => fake()->randomElement(['approved', 'approved', 'approved', 'pending', 'rejected']),
            'is_confirmed' => true,
        ];
    }
}
