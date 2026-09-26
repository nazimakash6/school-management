<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExamType;
use App\Models\Examination;
use App\Models\ExamSchedule;
use App\Models\ExamMark;
use App\Models\Admission;
use App\Models\AcademicSession;
use Illuminate\Support\Facades\DB;

class ExaminationDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch sessions
        $session2026 = AcademicSession::firstOrCreate(
            ['session_name' => '2025-2026'],
            ['start_date' => '2025-04-01', 'end_date' => '2026-03-31', 'status' => 'Active']
        );
        $session2025 = $session2026;

        // Fetch Exam Types
        $dailyType = ExamType::firstOrCreate(['code' => 'DAILY'], ['name' => 'Daily Test']);
        $weeklyType = ExamType::firstOrCreate(['code' => 'WEEKLY'], ['name' => 'Weekly Test']);
        $monthlyType = ExamType::firstOrCreate(['code' => 'MONTHLY'], ['name' => 'Monthly Assessment']);
        $midType = ExamType::firstOrCreate(['code' => 'MID1'], ['name' => 'First Mid-Term']);
        $termType = ExamType::firstOrCreate(['code' => 'TERM2'], ['name' => 'Second Term']);
        $annualType = ExamType::firstOrCreate(['code' => 'ANNUAL'], ['name' => 'Annual Exam']);
        $reboardType = ExamType::firstOrCreate(['code' => 'REBOARD'], ['name' => 'Re-Board Practice']);

        // Define subjects
        $standardSubjects = [
            ['name' => 'English', 'max' => 100, 'pass' => 40],
            ['name' => 'Mathematics', 'max' => 100, 'pass' => 40],
            ['name' => 'Science', 'max' => 100, 'pass' => 40],
            ['name' => 'Urdu', 'max' => 100, 'pass' => 40],
            ['name' => 'Islamiat', 'max' => 100, 'pass' => 40],
            ['name' => 'Computer', 'max' => 100, 'pass' => 40],
        ];

        $shortTestSubjects = [
            ['name' => 'English', 'max' => 25, 'pass' => 10],
            ['name' => 'Mathematics', 'max' => 25, 'pass' => 10],
            ['name' => 'Science', 'max' => 25, 'pass' => 10],
            ['name' => 'Urdu', 'max' => 25, 'pass' => 10],
        ];

        // Examinations to create
        $examsData = [
            [
                'title' => 'Daily Evaluation Test - August',
                'exam_type_id' => $dailyType->id,
                'academic_session_id' => $session2026->id,
                'class_name' => 'Class 1',
                'section_name' => 'A',
                'start_date' => '2026-08-01',
                'end_date' => '2026-08-02',
                'total_marks' => 25,
                'pass_marks' => 10,
                'status' => 'completed',
                'description' => 'Daily quick assessment for Class 1 foundational concepts.',
                'subjects' => $shortTestSubjects,
            ],
            [
                'title' => 'Weekly Progress Test 1',
                'exam_type_id' => $weeklyType->id,
                'academic_session_id' => $session2026->id,
                'class_name' => 'Class 1',
                'section_name' => 'A',
                'start_date' => '2026-08-08',
                'end_date' => '2026-08-09',
                'total_marks' => 25,
                'pass_marks' => 10,
                'status' => 'completed',
                'description' => 'Weekly recap test covering chapters 1 & 2.',
                'subjects' => $shortTestSubjects,
            ],
            [
                'title' => 'Monthly Assessment Test August 2026',
                'exam_type_id' => $monthlyType->id,
                'academic_session_id' => $session2026->id,
                'class_name' => 'Class 1',
                'section_name' => 'A',
                'start_date' => '2026-08-15',
                'end_date' => '2026-08-20',
                'total_marks' => 100,
                'pass_marks' => 40,
                'status' => 'completed',
                'description' => 'Comprehensive monthly examination for Class 1.',
                'subjects' => $standardSubjects,
            ],
            [
                'title' => 'First Mid-Term Examination 2026',
                'exam_type_id' => $midType->id,
                'academic_session_id' => $session2026->id,
                'class_name' => 'Class 1',
                'section_name' => 'A',
                'start_date' => '2026-09-10',
                'end_date' => '2026-09-15',
                'total_marks' => 100,
                'pass_marks' => 40,
                'status' => 'scheduled',
                'description' => 'First mid-term official term evaluation.',
                'subjects' => $standardSubjects,
            ],
            [
                'title' => 'Annual Final Examination 2025-2026',
                'exam_type_id' => $annualType->id,
                'academic_session_id' => $session2025->id,
                'class_name' => 'Class 1',
                'section_name' => 'A',
                'start_date' => '2026-03-01',
                'end_date' => '2026-03-10',
                'total_marks' => 100,
                'pass_marks' => 40,
                'status' => 'published',
                'description' => 'Final annual exam for academic session 2025-2026.',
                'subjects' => $standardSubjects,
            ],
            [
                'title' => 'Monthly Assessment Test Class 5',
                'exam_type_id' => $monthlyType->id,
                'academic_session_id' => $session2026->id,
                'class_name' => 'Class 5',
                'section_name' => 'All',
                'start_date' => '2026-08-12',
                'end_date' => '2026-08-16',
                'total_marks' => 100,
                'pass_marks' => 40,
                'status' => 'completed',
                'description' => 'Monthly evaluation for Class 5 students.',
                'subjects' => $standardSubjects,
            ],
            [
                'title' => 'Re Board Practice Examination 2026',
                'exam_type_id' => $reboardType->id,
                'academic_session_id' => $session2026->id,
                'class_name' => 'Class 10',
                'section_name' => 'A',
                'start_date' => '2026-08-05',
                'end_date' => '2026-08-12',
                'total_marks' => 100,
                'pass_marks' => 40,
                'status' => 'completed',
                'description' => 'Practice re-board preparatory examination for Class 10.',
                'subjects' => $standardSubjects,
            ],
        ];

        DB::transaction(function () use ($examsData) {
            foreach ($examsData as $examInfo) {
                $subjects = $examInfo['subjects'];
                unset($examInfo['subjects']);

                $exam = Examination::create($examInfo);

                foreach ($subjects as $sub) {
                    ExamSchedule::create([
                        'examination_id' => $exam->id,
                        'subject_name' => $sub['name'],
                        'max_marks' => $sub['max'],
                        'pass_marks' => $sub['pass'],
                    ]);
                }

                // Populate student marks if status is completed or published
                if (in_array($exam->status, ['completed', 'published'])) {
                    $students = Admission::where('class_name', $exam->class_name)
                        ->where('admission_status', 'active')
                        ->get();

                    // If no active students found in that specific class name, pick any students to demonstrate data
                    if ($students->isEmpty()) {
                        $students = Admission::take(10)->get();
                    }

                    foreach ($students as $stIndex => $st) {
                        foreach ($subjects as $subIndex => $sub) {
                            $maxM = $sub['max'];
                            $passM = $sub['pass'];

                            // Generate realistic score ranges
                            $isAbsent = ($stIndex === 3 && $subIndex === 2); // Simulate one absent case
                            
                            if ($isAbsent) {
                                $obt = null;
                            } else {
                                // High performer / average performer simulation based on student ID
                                $seedMultiplier = (($st->id * 7 + $subIndex * 13) % 40); // 0 to 39
                                $minScore = $maxM * 0.45; // at least 45%
                                $obt = round(min($maxM, $minScore + $seedMultiplier * ($maxM / 50)), 1);
                            }

                            ExamMark::create([
                                'examination_id' => $exam->id,
                                'admission_id' => $st->id,
                                'subject_name' => $sub['name'],
                                'marks_obtained' => $obt,
                                'total_marks' => $maxM,
                                'pass_marks' => $passM,
                                'is_absent' => $isAbsent,
                                'remarks' => $isAbsent ? 'Absent due to illness' : ($obt >= ($maxM * 0.8) ? 'Excellent performance' : 'Good effort'),
                            ]);
                        }
                    }
                }
            }
        });
    }
}
