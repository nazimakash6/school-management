<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $defaultGroups = [
            [
                'name' => 'Science Group',
                'subjects' => ['Physics', 'Chemistry', 'Biology', 'Mathematics (Science)', 'English Compulsory', 'Urdu Compulsory', 'Islamiat Compulsory', 'Pakistan Studies'],
                'description' => 'Academic group focusing on pre-medical and pre-engineering science curriculum.',
                'status' => 'active',
            ],
            [
                'name' => 'Computer Science Group',
                'subjects' => ['Computer Science', 'Physics', 'Mathematics (Science)', 'English Compulsory', 'Urdu Compulsory', 'Islamiat Compulsory', 'Pakistan Studies'],
                'description' => 'Technical science stream specializing in Computer Hardware, Software, and Programming.',
                'status' => 'active',
            ],
            [
                'name' => 'Arts Group',
                'subjects' => ['General Mathematics', 'General Science', 'Civics', 'Economics', 'Islamic Studies', 'Urdu Literature', 'English Compulsory', 'Pakistan Studies'],
                'description' => 'Humanities and Social Sciences stream for general academic education.',
                'status' => 'active',
            ],
            [
                'name' => 'Commerce Group',
                'subjects' => ['Accounting', 'Principles of Commerce', 'Commercial Geography', 'Business Mathematics', 'English Compulsory', 'Urdu Compulsory'],
                'description' => 'Business, finance, and trade studies group.',
                'status' => 'active',
            ],
            [
                'name' => 'Hifz-e-Quran Group',
                'subjects' => ['Nazra Quran', 'Quran Memorization', 'Tajweed', 'Islamic Studies', 'Urdu', 'Basic Mathematics'],
                'description' => 'Specialized Islamic religious curriculum focused on Quran memorization.',
                'status' => 'active',
            ],
        ];

        foreach ($defaultGroups as $group) {
            Group::updateOrCreate(
                ['name' => $group['name']],
                $group
            );
        }
    }
}
