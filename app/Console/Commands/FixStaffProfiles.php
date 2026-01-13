<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Staff;
use Illuminate\Console\Command;

class FixStaffProfiles extends Command
{
    protected $signature = 'staff:fix-profiles {username?}';
    protected $description = 'Create missing staff profiles for staff users';

    public function handle()
    {
        $username = $this->argument('username');
        
        if ($username) {
            $users = User::where('username', $username)->where('role', 'staff')->get();
        } else {
            $users = User::where('role', 'staff')->get();
        }

        if ($users->isEmpty()) {
            $this->info('No staff users found.');
            return 0;
        }

        $created = 0;
        foreach ($users as $user) {
            if (!$user->staff) {
                Staff::create([
                    'user_id' => $user->id,
                    'specialty' => 'all', // Updated to use new default
                    'color_code' => '#' . substr(md5(rand()), 0, 6),
                ]);
                $this->info("Created staff profile for: {$user->username} ({$user->full_name})");
                $created++;
            } else {
                $this->line("Staff profile already exists for: {$user->username}");
            }
        }

        if ($created > 0) {
            $this->info("Created {$created} staff profile(s).");
        } else {
            $this->info("All staff users already have profiles.");
        }

        return 0;
    }
}
