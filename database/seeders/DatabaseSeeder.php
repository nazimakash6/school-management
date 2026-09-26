<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            ['name' => 'Admin User', 'password' => bcrypt('password')]
        );

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        \App\Models\House::firstOrCreate(
            ['name' => 'Red House'],
            ['code' => 'RED', 'color' => '#ef4444', 'description' => 'Red House represents bravery and strength.', 'status' => 'active']
        );
        \App\Models\House::firstOrCreate(
            ['name' => 'Blue House'],
            ['code' => 'BLU', 'color' => '#3b82f6', 'description' => 'Blue House represents wisdom and loyalty.', 'status' => 'active']
        );
        \App\Models\House::firstOrCreate(
            ['name' => 'Green House'],
            ['code' => 'GRN', 'color' => '#22c55e', 'description' => 'Green House represents growth and harmony.', 'status' => 'active']
        );
        \App\Models\House::firstOrCreate(
            ['name' => 'Yellow House'],
            ['code' => 'YLW', 'color' => '#eab308', 'description' => 'Yellow House represents positivity and innovation.', 'status' => 'active']
        );

        \App\Models\AttendanceSetting::getSettings();

        $this->call([
            AdmissionSeeder::class,
            SubjectSeeder::class,
            EventSeeder::class,
            ExaminationDemoSeeder::class,
            GroupSeeder::class,
            InventorySeeder::class,
            LibrarySeeder::class,
            MeetingSeeder::class,
            QuranModuleSeeder::class,
            SkillsInstituteSeeder::class,
            TransportSeeder::class,
            VisitorSeeder::class,
            WhatsappSeeder::class,
            AuditLogDemoSeeder::class,
            StaffSeeder::class,
            StaffAdvanceSeeder::class,
        ]);
    }
}
