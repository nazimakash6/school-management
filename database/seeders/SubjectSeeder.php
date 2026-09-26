<?php

namespace Database\Seeders;

use App\Models\StudentClass;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        // Seed default classes if none exist
        $classes = ['Class 1', 'Class 2', 'Class 3', 'Class 4', 'Class 5', 'Class 6', 'Class 7', 'Class 8', 'Class 9', 'Class 10'];

        foreach ($classes as $index => $className) {
            StudentClass::firstOrCreate(
                ['name' => $className],
                [
                    'level' => $index < 5 ? 'Primary' : ($index < 8 ? 'Middle' : 'Secondary'),
                    'section_count' => 3,
                    'section_names' => ['A', 'B', 'C'],
                    'capacity' => 120,
                    'status' => 'active',
                ]
            );
        }

        // Primary subjects (Class 1 to Class 5)
        $primaryClasses = ['Class 1', 'Class 2', 'Class 3', 'Class 4', 'Class 5'];
        $primarySubjects = [
            ['code' => 'ENG-101', 'name' => 'English', 'type' => 'core', 'total' => 100, 'pass' => 33],
            ['code' => 'URD-101', 'name' => 'Urdu', 'type' => 'core', 'total' => 100, 'pass' => 33],
            ['code' => 'MATH-101', 'name' => 'Mathematics', 'type' => 'core', 'total' => 100, 'pass' => 33],
            ['code' => 'SCI-101', 'name' => 'General Science', 'type' => 'core', 'total' => 100, 'pass' => 33],
            ['code' => 'ISL-101', 'name' => 'Islamiat', 'type' => 'islamic', 'total' => 100, 'pass' => 33],
            ['code' => 'SST-101', 'name' => 'Social Studies', 'type' => 'core', 'total' => 50, 'pass' => 17],
            ['code' => 'QUR-101', 'name' => 'Quranic Studies', 'type' => 'islamic', 'total' => 50, 'pass' => 17],
        ];

        foreach ($primaryClasses as $cName) {
            $classObj = StudentClass::where('name', $cName)->first();
            foreach ($primarySubjects as $subj) {
                Subject::updateOrCreate(
                    [
                        'subject_code' => $subj['code'] . '-' . str_replace('Class ', 'C', $cName),
                        'class_name'   => $cName,
                    ],
                    [
                        'subject_name'     => $subj['name'],
                        'subject_type'     => $subj['type'],
                        'student_class_id' => $classObj?->id,
                        'total_marks'      => $subj['total'],
                        'passing_marks'    => $subj['pass'],
                        'status'           => 'active',
                        'description'      => "Standard {$subj['name']} curriculum for {$cName}",
                    ]
                );
            }
        }

        // Middle subjects (Class 6 to Class 8)
        $middleClasses = ['Class 6', 'Class 7', 'Class 8'];
        $middleSubjects = [
            ['code' => 'ENG-201', 'name' => 'English Grammar & Literature', 'type' => 'core', 'total' => 100, 'pass' => 33],
            ['code' => 'URD-201', 'name' => 'Urdu Literature', 'type' => 'core', 'total' => 100, 'pass' => 33],
            ['code' => 'MATH-201', 'name' => 'Mathematics', 'type' => 'core', 'total' => 100, 'pass' => 33],
            ['code' => 'SCI-201', 'name' => 'Science', 'type' => 'core', 'total' => 100, 'pass' => 33],
            ['code' => 'ISL-201', 'name' => 'Islamiat', 'type' => 'islamic', 'total' => 100, 'pass' => 33],
            ['code' => 'SST-201', 'name' => 'History & Geography', 'type' => 'core', 'total' => 100, 'pass' => 33],
            ['code' => 'CS-201', 'name' => 'Computer Studies', 'type' => 'elective', 'total' => 50, 'pass' => 17],
            ['code' => 'ART-201', 'name' => 'Arts & Crafts', 'type' => 'optional', 'total' => 50, 'pass' => 17],
        ];

        foreach ($middleClasses as $cName) {
            $classObj = StudentClass::where('name', $cName)->first();
            foreach ($middleSubjects as $subj) {
                Subject::updateOrCreate(
                    [
                        'subject_code' => $subj['code'] . '-' . str_replace('Class ', 'C', $cName),
                        'class_name'   => $cName,
                    ],
                    [
                        'subject_name'     => $subj['name'],
                        'subject_type'     => $subj['type'],
                        'student_class_id' => $classObj?->id,
                        'total_marks'      => $subj['total'],
                        'passing_marks'    => $subj['pass'],
                        'status'           => 'active',
                        'description'      => "Standard {$subj['name']} curriculum for {$cName}",
                    ]
                );
            }
        }

        // High subjects (Class 9 & Class 10)
        $highClasses = ['Class 9', 'Class 10'];
        $highSubjects = [
            ['code' => 'ENG-301', 'name' => 'English Compulsory', 'type' => 'core', 'total' => 75, 'pass' => 25],
            ['code' => 'URD-301', 'name' => 'Urdu Compulsory', 'type' => 'core', 'total' => 75, 'pass' => 25],
            ['code' => 'ISL-301', 'name' => 'Islamiat Compulsory', 'type' => 'islamic', 'total' => 50, 'pass' => 17],
            ['code' => 'PKS-301', 'name' => 'Pakistan Studies', 'type' => 'core', 'total' => 50, 'pass' => 17],
            ['code' => 'MATH-301', 'name' => 'Mathematics (Science)', 'type' => 'core', 'total' => 75, 'pass' => 25],
            ['code' => 'PHY-301', 'name' => 'Physics', 'type' => 'elective', 'total' => 75, 'pass' => 25],
            ['code' => 'CHM-301', 'name' => 'Chemistry', 'type' => 'elective', 'total' => 75, 'pass' => 25],
            ['code' => 'BIO-301', 'name' => 'Biology', 'type' => 'elective', 'total' => 75, 'pass' => 25],
            ['code' => 'CS-301', 'name' => 'Computer Science', 'type' => 'elective', 'total' => 75, 'pass' => 25],
        ];

        foreach ($highClasses as $cName) {
            $classObj = StudentClass::where('name', $cName)->first();
            foreach ($highSubjects as $subj) {
                Subject::updateOrCreate(
                    [
                        'subject_code' => $subj['code'] . '-' . str_replace('Class ', 'C', $cName),
                        'class_name'   => $cName,
                    ],
                    [
                        'subject_name'     => $subj['name'],
                        'subject_type'     => $subj['type'],
                        'student_class_id' => $classObj?->id,
                        'total_marks'      => $subj['total'],
                        'passing_marks'    => $subj['pass'],
                        'status'           => 'active',
                        'description'      => "Standard {$subj['name']} curriculum for {$cName}",
                    ]
                );
            }
        }
    }
}
