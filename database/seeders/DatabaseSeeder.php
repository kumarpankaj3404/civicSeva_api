<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles & permissions (must run first)
        $this->call(RoleSeeder::class);

        // 2. Categories
        $this->call(CategorySeeder::class);

        // 3. Schemes (depends on categories)
        $this->call(SchemeSeeder::class);

        // 4. Demo users
        $this->createDemoUsers();
    }

    private function createDemoUsers(): void
    {
        // Admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@civicconnect.in'],
            [
                'name'              => 'CivicConnect Admin',
                'password'          => Hash::make('Admin@1234'),
                'phone'             => '9000000001',
                'profile_completed' => true,
            ]
        );
        $admin->syncRoles(['admin']);

        // Demo citizen
        $citizen = User::updateOrCreate(
            ['email' => 'demo@civicconnect.in'],
            [
                'name'              => 'Demo Citizen',
                'password'          => Hash::make('Demo@1234'),
                'phone'             => '9000000002',
                'state'             => 'Uttar Pradesh',
                'district'          => 'Lucknow',
                'gender'            => 'male',
                'annual_income'     => 80000,
                'caste_category'    => 'general',
                'profile_completed' => true,
            ]
        );
        $citizen->syncRoles(['citizen']);

        $this->command->info('Demo users created:');
        $this->command->line('  Admin   → admin@civicconnect.in  / Admin@1234');
        $this->command->line('  Citizen → demo@civicconnect.in   / Demo@1234');
    }
}
