<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $sampleEvents = [
            [
                'title'           => 'Annual Sports Gala 2026',
                'event_type'      => 'Sports',
                'target_audience' => 'All Students & Parents',
                'start_date'      => Carbon::now()->addDays(2)->format('Y-m-d'),
                'end_date'        => Carbon::now()->addDays(4)->format('Y-m-d'),
                'start_time'      => '08:30:00',
                'end_time'        => '14:00:00',
                'is_all_day'      => true,
                'location'        => 'Main Sports Complex Ground',
                'organizer'       => 'Physical Education Department',
                'status'          => 'Upcoming',
                'budget_pkr'      => 150000.00,
                'banner_image'    => 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?auto=format&fit=crop&w=1200&q=80',
                'description'     => 'Three-day inter-house sports tournament including 100m sprint, cricket final, football trophy, high jump, and relay races.',
            ],
            [
                'title'           => 'Parent-Teacher Consultation (PTM Q2)',
                'event_type'      => 'Parent-Teacher',
                'target_audience' => 'Parents & Guardians',
                'start_date'      => Carbon::now()->addDays(5)->format('Y-m-d'),
                'end_date'        => Carbon::now()->addDays(5)->format('Y-m-d'),
                'start_time'      => '09:00:00',
                'end_time'        => '13:30:00',
                'is_all_day'      => false,
                'location'        => 'Main Auditorium & Classrooms',
                'organizer'       => 'Academic Affairs Office',
                'status'          => 'Upcoming',
                'budget_pkr'      => 35000.00,
                'banner_image'    => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=1200&q=80',
                'description'     => 'Quarterly parent-teacher review session to discuss student academic progress, midterm result cards, and behavioral attendance reports.',
            ],
            [
                'title'           => 'Inter-School Qira\'at & Naat Competition',
                'event_type'      => 'Islamic / Religious',
                'target_audience' => 'All Students',
                'start_date'      => Carbon::now()->addDays(9)->format('Y-m-d'),
                'end_date'        => Carbon::now()->addDays(9)->format('Y-m-d'),
                'start_time'      => '09:30:00',
                'end_time'        => '12:30:00',
                'is_all_day'      => false,
                'location'        => 'School Masjid Hall',
                'organizer'       => 'Quranic Studies Department',
                'status'          => 'Upcoming',
                'budget_pkr'      => 50000.00,
                'banner_image'    => 'https://images.unsplash.com/photo-1542810634-71277d95dcbb?auto=format&fit=crop&w=1200&q=80',
                'description'     => 'Prestigious Tajweed recitation competition featuring guest Qaris and certificates for top position holders.',
            ],
            [
                'title'           => 'Annual STEM & Science Exhibition',
                'event_type'      => 'Academic',
                'target_audience' => 'Secondary Section',
                'start_date'      => Carbon::now()->addDays(14)->format('Y-m-d'),
                'end_date'        => Carbon::now()->addDays(15)->format('Y-m-d'),
                'start_time'      => '10:00:00',
                'end_time'        => '15:00:00',
                'is_all_day'      => false,
                'location'        => 'Science & Computer Labs',
                'organizer'       => 'Faculty of Science',
                'status'          => 'Upcoming',
                'budget_pkr'      => 95000.00,
                'banner_image'    => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=1200&q=80',
                'description'     => 'Student innovation showcase with working models in Robotics, Physics, Chemistry, and Renewable Energy projects.',
            ],
            [
                'title'           => 'Youm-e-Azadi Pakistan Independence Day',
                'event_type'      => 'Cultural',
                'target_audience' => 'All Students & Staff',
                'start_date'      => Carbon::now()->subDays(10)->format('Y-m-d'),
                'end_date'        => Carbon::now()->subDays(10)->format('Y-m-d'),
                'start_time'      => '08:00:00',
                'end_time'        => '11:00:00',
                'is_all_day'      => false,
                'location'        => 'Front Assembly Ground',
                'organizer'       => 'Culture & Events Committee',
                'status'          => 'Completed',
                'budget_pkr'      => 80000.00,
                'banner_image'    => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1200&q=80',
                'description'     => 'Flag hoisting ceremony, national anthem, Milli Naghmay performances, and sweet distribution.',
            ],
        ];

        foreach ($sampleEvents as $eData) {
            Event::create(array_merge($eData, ['created_by' => $user?->id]));
        }
    }
}
