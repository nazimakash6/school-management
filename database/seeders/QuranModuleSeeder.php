<?php

namespace Database\Seeders;

use App\Models\QuranModule;
use App\Models\Student;
use App\Models\StudentClass;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class QuranModuleSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        if ($students->isEmpty()) return;

        $sampleData = [
            [
                'student_id' => $students[0]->id ?? 1,
                'category'   => 'Hifz',
                'status'     => 'In Progress',
                'teacher_name' => 'Qari Abdul Rahman',
                'para_no'    => 5,
                'surah_name' => 'An-Nisa',
                'ayah_from'  => 1,
                'ayah_to'    => 24,
                'sabaq'      => 'Para 5 (An-Nisa v1-24) - Memorized with Tajweed',
                'sabqi'      => 'Para 4 (Ali-Imran v150-200)',
                'manzil'     => 'Para 1 to Para 3 (Recited fluently)',
                'total_parahs_memorized' => 4,
                'score'      => 95.0,
                'mistakes_count' => 2,
                'remarks'    => 'Excellent retention on Sabaq. Needs slight attention on Ghunna in Sabqi.',
                'entry_date' => Carbon::now()->subDays(1)->format('Y-m-d'),
            ],
            [
                'student_id' => $students[1]->id ?? 2,
                'category'   => 'Nazra',
                'status'     => 'Completed',
                'teacher_name' => 'Qari Mohammad Usman',
                'para_no'    => 15,
                'surah_name' => 'Al-Isra',
                'ayah_from'  => 1,
                'ayah_to'    => 50,
                'sabaq'      => 'Para 15 Recitation (Al-Isra)',
                'total_parahs_memorized' => 0,
                'score'      => 88.5,
                'mistakes_count' => 3,
                'remarks'    => 'Recitation speed is consistent. Good application of Makharij.',
                'entry_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
            ],
            [
                'student_id' => $students[2]->id ?? 3,
                'category'   => 'Hifz',
                'status'     => 'Excellent',
                'teacher_name' => 'Qari Abdul Rahman',
                'para_no'    => 12,
                'surah_name' => 'Yusuf',
                'ayah_from'  => 1,
                'ayah_to'    => 40,
                'sabaq'      => 'Para 12 (Surah Yusuf v1-40)',
                'sabqi'      => 'Para 11 (Surah Yunus)',
                'manzil'     => 'Para 1 to Para 10 (Full revision)',
                'total_parahs_memorized' => 11,
                'score'      => 98.0,
                'mistakes_count' => 1,
                'remarks'    => 'Outstanding memorization! Strong grip on Manzil.',
                'entry_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
            ],
            [
                'student_id' => $students[3]->id ?? 4,
                'category'   => 'Qaida',
                'status'     => 'In Progress',
                'teacher_name' => 'Ustadh Abdullah',
                'lesson_name' => 'Lesson 8: Leen Letters (Waw & Ya Leen)',
                'score'      => 82.0,
                'mistakes_count' => 4,
                'remarks'    => 'Recognizes letters well. Needs practice connecting silent letters.',
                'entry_date' => Carbon::now()->subDays(4)->format('Y-m-d'),
            ],
            [
                'student_id' => $students[4]->id ?? 5,
                'category'   => 'Tajweed',
                'status'     => 'Completed',
                'teacher_name' => 'Qari Mohammad Usman',
                'lesson_name' => 'Rules of Noon Sakinah & Tanween (Idgham & Ikhfa)',
                'score'      => 92.0,
                'mistakes_count' => 2,
                'remarks'    => 'Passed practical Tajweed evaluation with distinction.',
                'entry_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
            ],
            [
                'student_id' => $students[5]->id ?? 6,
                'category'   => 'Hadith',
                'status'     => 'Completed',
                'teacher_name' => 'Mawlana Farooq',
                'lesson_name' => 'Hadith #12: "Leave that which makes you doubtful"',
                'score'      => 90.0,
                'mistakes_count' => 0,
                'remarks'    => 'Memorized Arabic text and Urdu translation perfectly.',
                'entry_date' => Carbon::now()->subDays(6)->format('Y-m-d'),
            ],
            [
                'student_id' => $students[6]->id ?? 7,
                'category'   => 'Dua',
                'status'     => 'Completed',
                'teacher_name' => 'Mawlana Farooq',
                'lesson_name' => 'Masnoon Duas: Before & After Meals & Sleeping',
                'score'      => 96.0,
                'mistakes_count' => 1,
                'remarks'    => 'Fluent pronunciation with proper etiquette.',
                'entry_date' => Carbon::now()->subDays(7)->format('Y-m-d'),
            ],
            [
                'student_id' => $students[7]->id ?? 8,
                'category'   => 'Nazra',
                'status'     => 'Needs Improvement',
                'teacher_name' => 'Ustadh Abdullah',
                'para_no'    => 2,
                'surah_name' => 'Al-Baqarah',
                'ayah_from'  => 142,
                'ayah_to'    => 180,
                'score'      => 65.0,
                'mistakes_count' => 8,
                'remarks'    => 'Hesitant on heavy letters (Mufakhkhamah). Recommended extra practice.',
                'entry_date' => Carbon::now()->subDays(8)->format('Y-m-d'),
            ],
        ];

        foreach ($sampleData as $data) {
            $student = Student::find($data['student_id']);
            if ($student) {
                $classObj = StudentClass::where('name', $student->class_name)->first();
                $data['student_class_id'] = $classObj?->id;
                QuranModule::create($data);
            }
        }
    }
}
