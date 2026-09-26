<?php

namespace Database\Seeders;

use App\Models\BookIssue;
use App\Models\Library;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            [
                'book_code'        => 'BK-ISL-101',
                'title'            => 'The Holy Quran - Tajweed Edition (English Translation)',
                'author'           => 'Abdullah Yusuf Ali',
                'publisher'        => 'Dar-us-Salam Publications',
                'isbn'             => '978-1590080467',
                'category'         => 'Islamic Studies',
                'rack_location'    => 'Shelf A-1 (Main Hall)',
                'total_copies'     => 10,
                'available_copies' => 8,
                'issued_copies'    => 2,
                'price'            => 45.00,
                'cover_image'      => 'https://images.unsplash.com/photo-1609599006353-e629aaabfeae?w=500&auto=format&fit=crop&q=80',
                'status'           => 'Available',
                'description'      => 'Comprehensive English commentary and Tajweed rules color-coded for recitation.',
            ],
            [
                'book_code'        => 'BK-ISL-102',
                'title'            => 'Riyad as-Salihin (The Meadows of the Righteous)',
                'author'           => 'Imam An-Nawawi',
                'publisher'        => 'Dar-us-Salam Publications',
                'isbn'             => '978-9960717006',
                'category'         => 'Islamic Studies',
                'rack_location'    => 'Shelf A-2 (Main Hall)',
                'total_copies'     => 6,
                'available_copies' => 5,
                'issued_copies'    => 1,
                'price'            => 38.00,
                'cover_image'      => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=500&auto=format&fit=crop&q=80',
                'status'           => 'Available',
                'description'      => 'Selection of authentic Hadiths covering ethics, manners, worship, and social responsibilities.',
            ],
            [
                'book_code'        => 'BK-SCI-201',
                'title'            => 'Fundamentals of Physics (Extended 10th Edition)',
                'author'           => 'David Halliday & Robert Resnick',
                'publisher'        => 'Wiley Global Education',
                'isbn'             => '978-1118230725',
                'category'         => 'Science & Tech',
                'rack_location'    => 'Shelf B-3 (Science Section)',
                'total_copies'     => 8,
                'available_copies' => 6,
                'issued_copies'    => 2,
                'price'            => 120.00,
                'cover_image'      => 'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=500&auto=format&fit=crop&q=80',
                'status'           => 'Available',
                'description'      => 'Standard textbook for high school and introductory college physics covering mechanics, thermodynamics, and optics.',
            ],
            [
                'book_code'        => 'BK-MTH-304',
                'title'            => 'Cambridge IGCSE Mathematics Extended Coursebook',
                'author'           => 'Karen Morrison & Nick Hamshaw',
                'publisher'        => 'Cambridge University Press',
                'isbn'             => '978-1108437219',
                'category'         => 'Mathematics',
                'rack_location'    => 'Shelf C-1 (Math Section)',
                'total_copies'     => 15,
                'available_copies' => 12,
                'issued_copies'    => 3,
                'price'            => 65.00,
                'cover_image'      => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=500&auto=format&fit=crop&q=80',
                'status'           => 'Available',
                'description'      => 'Official Cambridge syllabus curriculum textbook covering algebra, trigonometry, statistics, and geometry.',
            ],
            [
                'book_code'        => 'BK-LIT-402',
                'title'            => 'To Kill a Mockingbird (Classic Literature)',
                'author'           => 'Harper Lee',
                'publisher'        => 'Harper Perennial Modern Classics',
                'isbn'             => '978-0060935467',
                'category'         => 'Literature & Fiction',
                'rack_location'    => 'Shelf D-4 (Fiction Cabinet)',
                'total_copies'     => 5,
                'available_copies' => 3,
                'issued_copies'    => 2,
                'price'            => 18.00,
                'cover_image'      => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=500&auto=format&fit=crop&q=80',
                'status'           => 'Available',
                'description'      => 'Pulitzer Prize-winning masterpiece exploring human nature, empathy, justice, and integrity.',
            ],
            [
                'book_code'        => 'BK-HIS-505',
                'title'            => 'A Concise History of Pakistan & South Asia',
                'author'           => 'Muhammad Abdul Aziz',
                'publisher'        => 'Oxford University Press',
                'isbn'             => '978-0195475210',
                'category'         => 'History & Geography',
                'rack_location'    => 'Shelf E-2 (History Archive)',
                'total_copies'     => 4,
                'available_copies' => 0,
                'issued_copies'    => 4,
                'price'            => 32.00,
                'cover_image'      => 'https://images.unsplash.com/photo-1461360370896-922624d12aa1?w=500&auto=format&fit=crop&q=80',
                'status'           => 'Fully Issued',
                'description'      => 'Historical survey of the Indian subcontinent, independence movement, and modern regional dynamics.',
            ],
        ];

        $studentIds = Student::pluck('id')->toArray();

        foreach ($books as $bData) {
            $book = Library::updateOrCreate(['book_code' => $bData['book_code']], $bData);

            // Create sample issue records for issued books
            if ($bData['issued_copies'] > 0 && !empty($studentIds)) {
                for ($i = 0; $i < min($bData['issued_copies'], count($studentIds)); $i++) {
                    $stId = $studentIds[$i % count($studentIds)];
                    $isOverdue = ($i % 2 === 1);
                    $issueDate = Carbon::now()->subDays($isOverdue ? 25 : 7);
                    $dueDate = Carbon::now()->subDays($isOverdue ? 11 : -7);

                    BookIssue::updateOrCreate(
                        [
                            'library_id' => $book->id,
                            'student_id' => $stId,
                        ],
                        [
                            'issue_code'  => 'ISS-' . date('Ymd') . '-' . $book->id . '-' . $stId,
                            'issue_date'  => $issueDate->format('Y-m-d'),
                            'due_date'    => $dueDate->format('Y-m-d'),
                            'status'      => 'Issued',
                            'fine_amount' => $isOverdue ? 15.00 : 0.00,
                            'fine_paid'   => false,
                            'remarks'     => $isOverdue ? 'Overdue by 11 days. Reminder sent.' : 'Standard 14-day loan.',
                        ]
                    );
                }
            }
        }
    }
}
