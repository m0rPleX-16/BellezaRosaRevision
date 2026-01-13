<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin User - Update or create to ensure it exists and is active
        $admin = User::updateOrCreate(
            ['username' => 'nina'],
            [
                'full_name' => 'Nina Angela Malinaw',
                'phone'     => '09171234567',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        // 2. Staff Users + Staff Profile
        $staffNames = ['Anna Cruz', 'Maria Santos', 'Liza Reyes'];
        $specialties = ['hair', 'nail', 'both'];
        $colors = ['#EF4444', '#3B82F6', '#10B981'];

        foreach ($staffNames as $i => $name) {
            $username = strtolower(str_replace(' ', '', $name));
            $user = User::firstOrCreate(
                ['username' => $username],
                [
                    'full_name' => $name,
                    'phone'     => '09' . rand(100000000, 999999999),
                    'password'  => Hash::make('password'),
                    'role'      => 'staff',
                    'is_active' => true,
                ]
            );

            // Only create staff profile if it doesn't exist
            if (!$user->staff) {
                $user->staff()->create([
                    'specialty'   => $specialties[$i],
                    'color_code'  => $colors[$i],
                ]);
            }
        }

        // 3. Customer Users (for login testing)
        $customerUsers = [
            ['full_name' => 'John Smith', 'username' => 'johnsmith', 'phone' => '09171234568'],
            ['full_name' => 'Mary Johnson', 'username' => 'maryjohnson', 'phone' => '09171234569'],
            ['full_name' => 'David Williams', 'username' => 'davidwilliams', 'phone' => '09171234570'],
        ];

        foreach ($customerUsers as $customer) {
            User::firstOrCreate(
                ['username' => $customer['username']],
                [
                    'full_name' => $customer['full_name'],
                    'phone'     => $customer['phone'],
                    'password'  => Hash::make('password'),
                    'role'      => 'customer',
                    'is_active' => true,
                ]
            );
        }

        // 4. Run all seeders in correct order
        $this->call([
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            CustomerSeeder::class,    // Creates Customer records (not Users)
            AppointmentSeeder::class,
            AppointmentAddonSeeder::class,
            PaymentSeeder::class,
            InventorySeeder::class,
            CommissionSeeder::class,  // NEW: Add this
            // ReportDataSeeder::class, // REMOVE or fix factories first
        ]);
    }
}