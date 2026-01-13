<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class FixAdminUser extends Command
{
    protected $signature = 'admin:fix';
    protected $description = 'Ensure admin user exists with correct credentials';

    public function handle()
    {
        $username = 'nina';
        $user = User::where('username', $username)->first();

        if (!$user) {
            $this->info('Admin user not found. Creating...');
            $user = User::create([
                'full_name' => 'Nina Angela Malinaw',
                'username'  => $username,
                'phone'     => '09171234567',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'is_active' => true,
            ]);
            $this->info('Admin user created successfully!');
        } else {
            $this->info('Admin user found. Updating...');
            $user->update([
                'role' => 'admin',
                'is_active' => true,
                'password' => Hash::make('password'),
            ]);
            $this->info('Admin user updated successfully!');
        }

        $this->info('Username: ' . $user->username);
        $this->info('Role: ' . $user->role);
        $this->info('Is Active: ' . ($user->is_active ? 'Yes' : 'No'));
        $this->info('Password: password');

        return 0;
    }
}
