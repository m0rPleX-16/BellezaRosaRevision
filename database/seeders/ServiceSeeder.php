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
                'specialty' => 'both',
                'is_active' => true,
                'display_order' => 3
            ]);
        }
        if (!$fullServiceCategory) {
            $fullServiceCategory = ServiceCategory::create([
                'name' => 'Full Service',
                'description' => 'Combined hair and nail services',
                'specialty' => 'both',
                'is_active' => true,
                'display_order' => 4
            ]);
        }

        $services = [
            // ========== NAIL SERVICES ==========
            [
                'category_id' => $nailCategory->id,
                'name' => 'Manicure',
                'duration_minutes' => 45,
                'price_regular' => 150.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Professional nail care including shaping, cuticle care, and polish application.',
                'is_active' => true,
            ],
            [
                'category_id' => $nailCategory->id,
                'name' => 'Pedicure',
                'duration_minutes' => 60,
                'price_regular' => 200.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Complete foot care including nail shaping, cuticle care, exfoliation, and polish.',
                'is_active' => true,
            ],
            [
                'category_id' => $nailCategory->id,
                'name' => 'Gel Manicure',
                'duration_minutes' => 60,
                'price_regular' => 350.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Long-lasting gel polish manicure that stays chip-free for up to 2-3 weeks.',
                'is_active' => true,
            ],
            [
                'category_id' => $nailCategory->id,
                'name' => 'Gel Pedicure',
                'duration_minutes' => 75,
                'price_regular' => 450.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Long-lasting gel polish pedicure with complete foot care treatment.',
                'is_active' => true,
            ],
            [
                'category_id' => $nailCategory->id,
                'name' => 'Pedicure with Foot Spa',
                'duration_minutes' => 90,
                'price_regular' => 450.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Complete pedicure treatment with relaxing foot spa soak and massage.',
                'is_active' => true,
            ],
            [
                'category_id' => $nailCategory->id,
                'name' => 'Soft Gel Extension with 2-color Gel Polish',
                'duration_minutes' => 120,
                'price_regular' => 599.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Soft gel nail extensions with two-color gel polish design for added length and style.',
                'is_active' => true,
            ],
            [
                'category_id' => $nailCategory->id,
                'name' => 'Stone/Charms',
                'duration_minutes' => 15,
                'price_regular' => 50.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Add decorative stones or charms to your nail design.',
                'is_active' => true,
            ],
            [
                'category_id' => $nailCategory->id,
                'name' => 'Nail Art',
                'duration_minutes' => 30,
                'price_regular' => 99.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Custom nail art design to express your unique style.',
                'is_active' => true,
            ],

            // ========== SPA SERVICES ==========
            [
                'category_id' => $spaCategory->id,
                'name' => 'Foot Spa',
                'duration_minutes' => 45,
                'price_regular' => 300.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Relaxing foot spa treatment with warm water soak, exfoliation, and massage.',
                'is_active' => true,
            ],
            [
                'category_id' => $spaCategory->id,
                'name' => 'Hand Spa',
                'duration_minutes' => 30,
                'price_regular' => 200.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Nourishing hand treatment with warm soak, exfoliation, and moisturizing massage.',
                'is_active' => true,
            ],

            // ========== HAIR SERVICES ==========
            [
                'category_id' => $hairCategory->id,
                'name' => 'Hair Cut',
                'duration_minutes' => 45,
                'price_regular' => 300.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Professional haircut tailored to your style and face shape.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Wash & Blow Dry',
                'duration_minutes' => 45,
                'price_regular' => 250.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Hair wash with professional blow dry styling.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Shampoo Setting',
                'duration_minutes' => 90,
                'price_regular' => 450.00,
                'price_premium' => 650.00,
                'is_premium' => true,
                'description' => 'Professional shampoo and setting service for elegant styling.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Single-Color Process',
                'duration_minutes' => 120,
                'price_regular' => 999.00,
                'price_premium' => 1999.00,
                'is_premium' => true,
                'description' => 'Full head single-color hair coloring using regular/plant-based products.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Single-Color Process (Premium)',
                'duration_minutes' => 120,
                'price_regular' => 1999.00,
                'price_premium' => null,
                'is_premium' => true,
                'description' => 'Full head single-color hair coloring using premium professional products (Davines, Schwarzkopf).',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Balayage',
                'duration_minutes' => 180,
                'price_regular' => 1999.00,
                'price_premium' => 2799.00,
                'is_premium' => true,
                'description' => 'Hand-painted balayage highlighting technique for natural-looking color dimension.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Rebond',
                'duration_minutes' => 240,
                'price_regular' => 1500.00,
                'price_premium' => 3500.00,
                'is_premium' => true,
                'description' => 'Hair straightening treatment using regular products for smooth, straight hair.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Rebond (Premium)',
                'duration_minutes' => 240,
                'price_regular' => 3500.00,
                'price_premium' => null,
                'is_premium' => true,
                'description' => 'Premium hair straightening treatment using professional-grade products for long-lasting results.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Reconstructive Keratin',
                'duration_minutes' => 180,
                'price_regular' => 2500.00,
                'price_premium' => 3500.00,
                'is_premium' => true,
                'description' => 'Deep conditioning keratin treatment to repair and restore damaged hair.',
                'is_active' => true,
            ],

            // ========== ADDITIONAL HAIR SERVICES ==========
            [
                'category_id' => $hairCategory->id,
                'name' => 'Hair Color Touch Up',
                'duration_minutes' => 90,
                'price_regular' => 699.00,
                'price_premium' => 1299.00,
                'is_premium' => true,
                'description' => 'Root touch-up service to maintain your hair color.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Hair Highlights',
                'duration_minutes' => 150,
                'price_regular' => 1299.00,
                'price_premium' => 1999.00,
                'is_premium' => true,
                'description' => 'Full head highlights for added dimension and brightness.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Partial Highlights',
                'duration_minutes' => 120,
                'price_regular' => 899.00,
                'price_premium' => 1499.00,
                'is_premium' => true,
                'description' => 'Highlighting focused on specific sections of your hair.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Hair Treatment',
                'duration_minutes' => 60,
                'price_regular' => 500.00,
                'price_premium' => 800.00,
                'is_premium' => true,
                'description' => 'Deep conditioning treatment to nourish and repair damaged hair.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Hair Spa',
                'duration_minutes' => 90,
                'price_regular' => 600.00,
                'price_premium' => 1000.00,
                'is_premium' => true,
                'description' => 'Complete hair spa treatment with massage, deep conditioning, and styling.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Hair Styling',
                'duration_minutes' => 60,
                'price_regular' => 400.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Professional hair styling for special occasions.',
                'is_active' => true,
            ],
            [
                'category_id' => $hairCategory->id,
                'name' => 'Bridal Hair Styling',
                'duration_minutes' => 120,
                'price_regular' => 1500.00,
                'price_premium' => 2500.00,
                'is_premium' => true,
                'description' => 'Elegant bridal hairstyle with trial consultation included.',
                'is_active' => true,
            ],

            // ========== ADDITIONAL NAIL SERVICES ==========
            [
                'category_id' => $nailCategory->id,
                'name' => 'French Manicure',
                'duration_minutes' => 60,
                'price_regular' => 250.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Classic French manicure with white tips and natural base.',
                'is_active' => true,
            ],
            [
                'category_id' => $nailCategory->id,
                'name' => 'French Pedicure',
                'duration_minutes' => 75,
                'price_regular' => 300.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Classic French pedicure with white tips and natural base.',
                'is_active' => true,
            ],
            [
                'category_id' => $nailCategory->id,
                'name' => 'Nail Repair',
                'duration_minutes' => 30,
                'price_regular' => 150.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Professional repair service for broken or damaged nails.',
                'is_active' => true,
            ],
            [
                'category_id' => $nailCategory->id,
                'name' => 'Nail Extension (Acrylic)',
                'duration_minutes' => 120,
                'price_regular' => 800.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Full set of acrylic nail extensions.',
                'is_active' => true,
            ],
            [
                'category_id' => $nailCategory->id,
                'name' => 'Nail Fill (Acrylic)',
                'duration_minutes' => 90,
                'price_regular' => 500.00,
                'price_premium' => null,
                'is_premium' => false,
                'description' => 'Refill service for existing acrylic nail extensions.',
                'is_active' => true,
            ],
        ];

        // Insert all services
        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
