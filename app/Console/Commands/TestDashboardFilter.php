<?php

namespace App\Console\Commands;

use App\Http\Controllers\DashboardController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class TestDashboardFilter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:dashboard-filter {--date_range=today : Date range to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test dashboard filter functionality directly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Dashboard Filter...');
        
        // Create a mock request
        $request = new Request();
        $request->merge([
            'date_range' => $this->option('date_range')
        ]);
        
        $this->info("Testing with date_range: " . $request->get('date_range'));
        
        try {
            // Create controller instance
            $controller = new DashboardController();
            
            // Call the filter method
            $response = $controller->filter($request);
            
            $this->info('Filter method executed successfully!');
            $this->info('Response Status: ' . $response->getStatusCode());
            
            $data = $response->getData(true);
            
            $this->info('=== FILTER RESULTS ===');
            $this->info('Success: ' . ($data['success'] ? 'YES' : 'NO'));
            $this->info('Label: ' . $data['label']);
            
            if (isset($data['stats'])) {
                $this->info('--- Statistics ---');
                $this->info('Appointments Count: ' . $data['stats']['appointments_count']);
                $this->info('Customers Count: ' . $data['stats']['customers_count']);
                $this->info('Revenue: ₱' . number_format($data['stats']['revenue'], 2));
                $this->info('Total Staff: ' . $data['stats']['total_staff']);
            }
            
            if (isset($data['appointments'])) {
                $this->info('--- Appointments (' . count($data['appointments']) . ') ---');
                foreach (array_slice($data['appointments'], 0, 3) as $appointment) {
                    $this->info('- ' . $appointment['customer']['full_name'] . ' - ' . $appointment['service']['name']);
                }
                if (count($data['appointments']) > 3) {
                    $this->info('... and ' . (count($data['appointments']) - 3) . ' more');
                }
            }
            
            if (isset($data['customer_services'])) {
                $this->info('--- Customer Services ---');
                $this->info('Total Services: ' . $data['customer_services']['total_services']);
                $this->info('Popular Service: ' . $data['customer_services']['popular_service']);
                $this->info('Customers with Services: ' . count($data['customer_services']['customers']));
            }
            
            $this->info('=== TEST COMPLETED SUCCESSFULLY ===');
            
        } catch (\Exception $e) {
            $this->error('ERROR: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile());
            $this->error('Line: ' . $e->getLine());
            return 1;
        }
        
        return 0;
    }
}
