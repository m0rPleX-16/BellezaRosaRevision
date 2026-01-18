<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Temporarily disable foreign key checks to allow deletion
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear all existing services
        Service::query()->delete();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get categories
        $hairCategory = ServiceCategory::where('name', 'Hair Services')->first();
        $nailCategory = ServiceCategory::where('name', 'Nail Services')->first();
        $spaCategory = ServiceCategory::where('name', 'Spa Services')->first();
        $fullServiceCategory = ServiceCategory::where('name', 'Full Service')->first();

        // If categories don't exist, create them
        if (!$hairCategory) {
            $hairCategory = ServiceCategory::create([
                'name' => 'Hair Services',
                'description' => 'All hair-related services',
                'specialty' => 'hair',
                'is_active' => true,
                'display_order' => 1
            ]);
        }
        if (!$nailCategory) {
            $nailCategory = ServiceCategory::create([
                'name' => 'Nail Services',
                'description' => 'All nail-related services',
                'specialty' => 'nail',
                'is_active' => true,
                'display_order' => 2
            ]);
        }
        if (!$spaCategory) {
            $spaCategory = ServiceCategory::create([
                'name' => 'Spa Services',
                'description' => 'Foot and hand spa treatments',
                'specialty' => 'spa',
                'is_active' => true,
                'display_order' => 3
            ]);
        }
        if (!$fullServiceCategory) {
            $fullServiceCategory = ServiceCategory::create([
                'name' => 'Full Service',
                'description' => 'Combined hair and nail services',
                'specialty' => 'all',
                'is_active' => true,
                'display_order' => 4
            ]);
        }

        $services = [
            // ========== HAIR COLOR & TREATMENTS ==========
            [
                'category_id' => $hairCategory->id,
                'name' => 'Single Color Process',
                'duration_minutes' => 180, // 1.5 to 3 hours (average 2 hours, includes consultation, application, processing, rinse, styling)
                'price_regular' => 999.00,
                'price_premium' => 1999.00,
                'is_premium' => true,
                'description' => 'Complete single color application including consultation, application, processing time (30-45 min), rinse, and styling.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Balayage',
                'duration_minutes' => 240, // 3 to 5 hours (average 4 hours, hand-painting technique takes longer)
                'price_regular' => 1999.00,
                'price_premium' => 2799.00,
                'is_premium' => true,
                'description' => 'Hand-painted balayage highlighting technique for natural-looking color dimension. Premium/full head can take 4+ hours.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Highlights',
                'duration_minutes' => 180, // 2 to 4 hours (average 3 hours, foil placement and processing)
                'price_regular' => 1299.00,
                'price_premium' => 1999.00,
                'is_premium' => true,
                'description' => 'Full head highlights with foil placement and processing. Complete service can reach 3-4 hours.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Rebond (Rebonding)',
                'duration_minutes' => 300, // 4 to 6+ hours (average 5 hours, longest service)
                'price_regular' => 1500.00,
                'price_premium' => 3500.00,
                'is_premium' => true,
                'description' => 'Chemical hair rebonding with processing and flat ironing multiple passes. Often the longest service, 5-6 hours for longer hair.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Reconstructive Kera / Protein Straight Bond',
                'duration_minutes' => 180, // 2 to 4 hours (average 3 hours, keratin-based treatment)
                'price_regular' => 2500.00,
                'price_premium' => 3500.00,
                'is_premium' => true,
                'description' => 'Keratin-based reconstructive treatment with application, heat sealing, and processing. Similar to Brazilian keratin treatment.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Hair Spa Treatment',
                'duration_minutes' => 90, // 1 to 2 hours (average 1.5 hours)
                'price_regular' => 600.00,
                'price_premium' => 1000.00,
                'is_premium' => true,
                'description' => 'Complete hair spa treatment including massage, mask application, wash, and styling.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Hair & Scalp Treatment',
                'duration_minutes' => 90, // 1 to 2 hours (average 1.5 hours)
                'price_regular' => 500.00,
                'price_premium' => 800.00,
                'is_premium' => true,
                'description' => 'Focused hair and scalp treatment with massage, deep conditioning, and specialized care.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Hot Oil w/ Hair Cut',
                'duration_minutes' => 105, // 1 to 2 hours + 30-45 min for cut (average 1.75 hours)
                'price_regular' => 450.00,
                'price_premium' => 650.00,
                'is_premium' => true,
                'description' => 'Hot oil treatment combined with professional hair cut service.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Intense Hair Treatment / Hair Botox',
                'duration_minutes' => 90, // 1 to 2 hours (average 1.5 hours, processing 20-90 min + styling)
                'price_regular' => 800.00,
                'price_premium' => 1200.00,
                'is_premium' => true,
                'description' => 'Deep conditioning hair botox treatment with processing time and styling. No harsh chemicals.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Hair Cut',
                'duration_minutes' => 45, // 30 to 60 minutes (average 45 minutes)
                'price_regular' => 300.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Professional haircut tailored to your style and face shape. Longer for complex styling.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Wash & Blow Dry',
                'duration_minutes' => 60, // 45 minutes to 1.5 hours (average 1 hour)
                'price_regular' => 250.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Professional hair wash with blow dry styling service.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Shampoo Setting',
                'duration_minutes' => 90, // 45 minutes to 1.5 hours (average 1.5 hours)
                'price_regular' => 450.00,
                'price_premium' => 650.00,
                'is_premium' => true,
                'description' => 'Professional shampoo and setting service for elegant styling results.',
                'is_active' => true,
            ],

            // ========== NAIL SERVICES ==========
            [
                'category_id' => $nailCategory->id,
                'name' => 'Manicure',
                'duration_minutes' => 38, // 30 to 45 minutes (average 38 minutes)
                'price_regular' => 150.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Professional nail care including cleaning, shaping, cuticle care, and polish application.',
                'is_active' => true,
            ],
            [
                'category_id' => $nailCategory->id,
                'name' => 'Pedicure',
                'duration_minutes' => 53, // 45 minutes to 1 hour (average 53 minutes)
                'price_regular' => 200.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Complete foot care including foot soak, exfoliation, nail shaping, cuticle care, and polish.',
                'is_active' => true,
            ],
            [
                'category_id' => $nailCategory->id,
                'name' => 'Gel Manicure',
                'duration_minutes' => 68, // 45 minutes to 1.5 hours (average 1 hour 8 minutes)
                'price_regular' => 350.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Long-lasting gel polish manicure with LED curing. Longer with nail art designs.',
                'is_active' => true,
            ],
            [
                'category_id' => $nailCategory->id,
                'name' => 'Gel Pedicure',
                'duration_minutes' => 75, // 1 to 1.5 hours (average 1 hour 15 minutes)
                'price_regular' => 450.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Long-lasting gel polish pedicure with complete foot care and LED curing.',
                'is_active' => true,
            ],
            [
                'category_id' => $nailCategory->id,
                'name' => 'Softgel Extension (with 2 colors gel polish)',
                'duration_minutes' => 135, // 1.5 to 3 hours (average 2 hours 15 minutes)
                'price_regular' => 599.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Soft gel nail extensions with two-color gel polish design. Longer for complex designs or add-ons.',
                'is_active' => true,
            ],
            [
                'category_id' => $nailCategory->id,
                'name' => 'Foot Spa',
                'duration_minutes' => 45, // 30 to 60 minutes (average 45 minutes)
                'price_regular' => 300.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Relaxing foot spa treatment with warm water soak, exfoliation, and massage.',
                'is_active' => true,
            ],
            [
                'category_id' => $nailCategory->id,
                'name' => 'Hand Spa',
                'duration_minutes' => 45, // 30 to 60 minutes (average 45 minutes)
                'price_regular' => 200.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Nourishing hand spa treatment with warm soak, exfoliation, and moisturizing massage.',
                'is_active' => true,
            ],

            // ========== NAIL ADD-ON SERVICES ==========
            [
                'category_id' => $nailCategory->id,
                'name' => 'Nail Art Add-on',
                'duration_minutes' => 30, // Add 15-45 minutes (average 30 minutes)
                'price_regular' => 99.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Custom nail art design add-on to express your unique style.',
                'is_active' => true,
            ],
            [
                'category_id' => $nailCategory->id,
                'name' => 'Charms/Stones Add-on',
                'duration_minutes' => 30, // Add 15-45 minutes (average 30 minutes)
                'price_regular' => 50.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Add decorative charms or stones to your nail design.',
                'is_active' => true,
            ],
        ];

        // Insert all services
        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
