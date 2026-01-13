<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Console\Command;

class FixServiceCategories extends Command
{
    protected $signature = 'services:fix-categories';
    protected $description = 'Fix service category assignments based on service names';

    public function handle()
    {
        $this->info('Fixing service category assignments...');

        // Get categories
        $hairCategory = ServiceCategory::where('name', 'Hair Services')->first();
        $nailCategory = ServiceCategory::where('name', 'Nail Services')->first();
        $spaCategory = ServiceCategory::where('name', 'Spa Services')->first();
        $fullServiceCategory = ServiceCategory::where('name', 'Full Service')->first();

        if (!$hairCategory || !$nailCategory) {
            $this->error('Required categories not found. Please run ServiceCategorySeeder first.');
            return 1;
        }

        // Define hair service keywords
        $hairKeywords = [
            'hair', 'cut', 'blow dry', 'wash', 'shampoo', 'setting', 'color', 'highlight', 
            'balayage', 'ombre', 'rebond', 'keratin', 'treatment', 'spa', 'styling', 
            'bridal', 'formal', 'updo', 'iron', 'root touch', 'protein', 'smoothing',
            'reconstructive', 'touch up', 'highlights', 'partial'
        ];

        // Define nail service keywords
        $nailKeywords = [
            'manicure', 'pedicure', 'gel', 'nail', 'extension', 'acrylic', 'fill', 
            'french', 'art', 'stone', 'charms', 'repair', 'polish'
        ];

        // Define spa service keywords
        $spaKeywords = [
            'foot spa', 'hand spa', 'spa'
        ];

        $fixed = 0;
        $services = Service::with('category')->get();

        foreach ($services as $service) {
            $serviceName = strtolower($service->name);
            $currentCategory = $service->category ? $service->category->name : null;
            $shouldBeCategory = null;

            // Check if it's a hair service
            foreach ($hairKeywords as $keyword) {
                if (strpos($serviceName, $keyword) !== false) {
                    // Make sure it's not a nail service with "nail" in the name
                    if (strpos($serviceName, 'nail') === false || strpos($serviceName, 'nail art') !== false) {
                        $shouldBeCategory = 'Hair Services';
                        break;
                    }
                }
            }

            // Check if it's a nail service
            if (!$shouldBeCategory) {
                foreach ($nailKeywords as $keyword) {
                    if (strpos($serviceName, $keyword) !== false) {
                        $shouldBeCategory = 'Nail Services';
                        break;
                    }
                }
            }

            // Check if it's a spa service
            if (!$shouldBeCategory) {
                foreach ($spaKeywords as $keyword) {
                    if (strpos($serviceName, $keyword) !== false && 
                        strpos($serviceName, 'pedicure with foot spa') === false) {
                        $shouldBeCategory = 'Spa Services';
                        break;
                    }
                }
            }

            // Fix the category if needed
            if ($shouldBeCategory && $currentCategory !== $shouldBeCategory) {
                $targetCategory = null;
                switch ($shouldBeCategory) {
                    case 'Hair Services':
                        $targetCategory = $hairCategory;
                        break;
                    case 'Nail Services':
                        $targetCategory = $nailCategory;
                        break;
                    case 'Spa Services':
                        $targetCategory = $spaCategory;
                        break;
                }

                if ($targetCategory) {
                    $service->update(['category_id' => $targetCategory->id]);
                    $this->line("Fixed: '{$service->name}' from '{$currentCategory}' to '{$shouldBeCategory}'");
                    $fixed++;
                }
            }
        }

        $this->info("Fixed {$fixed} service category assignments.");
        return 0;
    }
}
