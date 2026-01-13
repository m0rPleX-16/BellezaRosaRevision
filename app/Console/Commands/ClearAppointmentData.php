<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearAppointmentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:clear-appointments {--inventory : Also clear inventory data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all appointments, payments, commissions, appointment addons, and optionally inventory data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing appointment-related data...');

        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear tables in correct order (respecting foreign keys)
        DB::table('appointment_addons')->truncate();
        DB::table('commissions')->truncate();
        DB::table('payments')->truncate();
        DB::table('appointments')->truncate();

        $this->info('✓ Cleared appointment_addons');
        $this->info('✓ Cleared commissions');
        $this->info('✓ Cleared payments');
        $this->info('✓ Cleared appointments');

        // Clear inventory if requested
        if ($this->option('inventory')) {
            DB::table('inventory_updates')->truncate();
            DB::table('inventory_items')->truncate();
            $this->info('✓ Cleared inventory_updates');
            $this->info('✓ Cleared inventory_items');
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('');
        $this->info('All data has been cleared successfully!');
    }
}
