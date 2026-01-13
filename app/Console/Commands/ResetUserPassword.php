<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetUserPassword extends Command
{
    protected $signature = 'user:reset-password {username} {--password=}';
    protected $description = 'Reset a user\'s password';

    public function handle()
    {
        $username = $this->argument('username');
        $newPassword = $this->option('password') ?: 'password';

        $user = User::where('username', $username)
            ->orWhere('full_name', 'like', '%' . $username . '%')
            ->first();

        if (!$user) {
            $this->error("User not found with username or name containing: {$username}");
            return 1;
        }

        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        $this->info("Password reset successfully!");
        $this->info("Username: {$user->username}");
        $this->info("Full Name: {$user->full_name}");
        $this->info("New Password: {$newPassword}");

        return 0;
    }
}
