<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentClass;
use App\Models\StudentSkill;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SkillsInstituteSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::orderBy('id')->get();
        if ($students->isEmpty()) {
            return;
        }

        $user = User::first();

        $evaluations = [
            [
                'skill_category'     => 'IT & Programming',
                'skill_name'         => 'Web Development (HTML/CSS & JS)',
                'assessment_type'    => 'Project Submission',
                'performance_period' => 'monthly',
                'total_score'        => 100,
                'obtained_score'     => 95,
                'instructor_notes'   => 'Outstanding execution of responsive website layout. Mastered flexbox and grid styling.',
                'certificate_code'   => 'SKL-IT-2026-001',
            ],
            [
                'skill_category'     => 'Robotics & Electronics',
                'skill_name'         => 'Arduino Microcontroller & Sensor Wiring',
                'assessment_type'    => 'Practical Evaluation',
                'performance_period' => 'monthly',
                'total_score'        => 100,
                'obtained_score'     => 88,
                'instructor_notes'   => 'Successfully built an obstacle-avoiding rover circuit with ultrasonic distance sensors.',
                'certificate_code'   => 'SKL-ROB-2026-002',
            ],
            [
                'skill_category'     => 'Quranic & Tajweed',
                'skill_name'         => 'Tajweed Rules & Makharij Precision',
                'assessment_type'    => 'Oral Assessment',
                'performance_period' => 'weekly',
                'total_score'        => 50,
                'obtained_score'     => 49,
                'instructor_notes'   => 'Flawless pronunciation of Letters & Ghunna rules during Juz Amma recitation test.',
                'certificate_code'   => 'SKL-QRN-2026-003',
            ],
            [
                'skill_category'     => 'Communication & Soft Skills',
                'skill_name'         => 'English Declamation & Public Speaking',
                'assessment_type'    => 'Live Demonstration',
                'performance_period' => 'monthly',
                'total_score'        => 100,
                'obtained_score'     => 92,
                'instructor_notes'   => 'Excellent voice modulation, posture, and convincing rhetoric during inter-class debate.',
                'certificate_code'   => 'SKL-SPK-2026-004',
            ],
            [
                'skill_category'     => 'Arts & Calligraphy',
                'skill_name'         => 'Arabic Khat-e-Nastalik Calligraphy',
                'assessment_type'    => 'Workshop Performance',
                'performance_period' => 'monthly',
                'total_score'        => 100,
                'obtained_score'     => 84,
                'instructor_notes'   => 'Demonstrated great pen stroke control and ink distribution in canvas composition.',
                'certificate_code'   => 'SKL-ART-2026-005',
            ],
            [
                'skill_category'     => 'Technical & Crafts',
                'skill_name'         => 'Home Electrical Circuiting & Safety',
                'assessment_type'    => 'Practical Evaluation',
                'performance_period' => 'quarterly',
                'total_score'        => 100,
                'obtained_score'     => 90,
                'instructor_notes'   => 'Correctly wired two-way switch boards with breaker fuse safety testing.',
                'certificate_code'   => 'SKL-TEC-2026-006',
            ],
            [
                'skill_category'     => 'IT & Programming',
                'skill_name'         => 'Touch Typing & Keyboard Speed (WPM)',
                'assessment_type'    => 'Skill Exam',
                'performance_period' => 'weekly',
                'total_score'        => 60,
                'obtained_score'     => 55,
                'instructor_notes'   => 'Achieved 55 Words Per Minute (WPM) with 98% accuracy on standard QWERTY test.',
                'certificate_code'   => 'SKL-IT-2026-007',
            ],
            [
                'skill_category'     => 'Physical & Sports',
                'skill_name'         => 'Cricket Bowling Line & Length Control',
                'assessment_type'    => 'Practical Evaluation',
                'performance_period' => 'weekly',
                'total_score'        => 50,
                'obtained_score'     => 44,
                'instructor_notes'   => 'Demonstrated consistent yorker delivery and seam position in target bowling drills.',
                'certificate_code'   => 'SKL-SPT-2026-008',
            ],
        ];

        $idx = 0;
        foreach ($students as $student) {
            $classObj = StudentClass::where('name', $student->class_name)->first();
            $eval = $evaluations[$idx % count($evaluations)];

            $total = floatval($eval['total_score']);
            $obtained = floatval($eval['obtained_score']);
            $starRating = StudentSkill::calculateStarRating($obtained, $total);
            $badgeLevel = StudentSkill::determineBadgeLevel($starRating);

            StudentSkill::updateOrCreate(
                [
                    'student_id'      => $student->id,
                    'skill_name'      => $eval['skill_name'],
                    'evaluation_date' => Carbon::now()->subDays(($idx * 2) % 30)->format('Y-m-d'),
                ],
                [
                    'student_class_id'   => $classObj?->id,
                    'skill_category'     => $eval['skill_category'],
                    'assessment_type'    => $eval['assessment_type'],
                    'performance_period' => $eval['performance_period'],
                    'total_score'        => $total,
                    'obtained_score'     => $obtained,
                    'star_rating'        => $starRating,
                    'badge_level'        => $badgeLevel,
                    'certificate_code'   => $eval['certificate_code'] . '-' . str_pad($student->id, 3, '0', STR_PAD_LEFT),
                    'instructor_notes'   => $eval['instructor_notes'],
                    'created_by'         => $user?->id,
                ]
            );

            $idx++;
        }
    }
}
