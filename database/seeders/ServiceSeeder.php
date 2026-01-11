<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [];
        $categories = ServiceCategory::pluck('id')->toArray();

        $serviceNames = [
            // Hair Services
            'Regular Haircut', 'Stylist Haircut', 'Kids Haircut', 'Senior Haircut',
            'Blow Dry', 'Hair Wash & Blow Dry', 'Iron Styling', 'Updo Hairstyle',
            'Bridal Hairstyle', 'Formal Hairstyle', 'Single Process Color', 'Full Highlight',
            'Partial Highlight', 'Balayage', 'Ombre', 'Color Correction', 'Root Touch Up',
            'Toner', 'Deep Conditioning', 'Protein Treatment', 'Scalp Treatment',
            'Hair Spa', 'Anti-Dandruff Treatment', 'Hair Fall Treatment', 'Keratin Smoothing',
            
            // Nail Services
            'Regular Manicure', 'Spa Manicure', 'Gel Manicure', 'French Manicure',
            'Acrylic Full Set', 'Acrylic Fill', 'Dip Powder Manicure', 'Nail Art Design',
            'Regular Pedicure', 'Spa Pedicure', 'Gel Pedicure', 'French Pedicure',
            'Medical Pedicure', 'Callus Treatment', 'Paraffin Pedicure', 'Nail Repair',
            
            // Other Services
            'Full Body Wax', 'Brazilian Wax', 'Bikini Wax', 'Underarm Wax',
            'Basic Facial', 'Acne Treatment Facial', 'Anti-Aging Facial', 'Hydrating Facial',
            'Swedish Massage', 'Deep Tissue Massage', 'Aromatherapy Massage', 'Hot Stone Massage',
            'Full Makeup', 'Bridal Makeup', 'Evening Makeup', 'Airbrush Makeup',
        ];

        // Realistic price ranges for salon services (in PHP)
        $priceRanges = [
            'Regular Haircut' => [150, 300],
            'Stylist Haircut' => [300, 500],
            'Kids Haircut' => [100, 200],
            'Senior Haircut' => [120, 250],
            'Blow Dry' => [200, 400],
            'Hair Wash & Blow Dry' => [250, 450],
            'Single Process Color' => [800, 1500],
            'Full Highlight' => [1500, 3000],
            'Balayage' => [2000, 4000],
            'Regular Manicure' => [150, 300],
            'Spa Manicure' => [300, 500],
            'Gel Manicure' => [400, 700],
            'Regular Pedicure' => [200, 400],
            'Spa Pedicure' => [400, 700],
            'Gel Pedicure' => [500, 800],
        ];

        for ($i = 0; $i < 50; $i++) {
            $serviceName = $serviceNames[$i] ?? "Service " . ($i + 1);
            $isPremium = rand(1, 100) <= 30;
            
            // Use realistic prices if available, otherwise generate reasonable range
            if (isset($priceRanges[$serviceName])) {
                $priceRegular = rand($priceRanges[$serviceName][0] * 100, $priceRanges[$serviceName][1] * 100) / 100;
            } else {
                // Default realistic range: 150-2000 PHP
                $priceRegular = rand(15000, 200000) / 100;
            }
            
            $pricePremium = $isPremium ? $priceRegular * 1.3 : null;
            
            $services[] = [
                'category_id' => $categories[array_rand($categories)],
                'name' => $serviceNames[$i] ?? "Service " . ($i + 1),
                'duration_minutes' => [30, 45, 60, 75, 90, 120, 150, 180][array_rand([30, 45, 60, 75, 90, 120, 150, 180])],
                'price_regular' => $priceRegular,
                'price_premium' => $pricePremium,
                'is_premium' => $isPremium,
                'description' => rand(1, 5) !== 1 ? 'Professional ' . ($serviceNames[$i] ?? "Service " . ($i + 1)) . ' service' : null,
                'consumables' => rand(1, 2) === 1 ? json_encode([
                    'shampoo' => rand(1, 5),
                    'conditioner' => rand(1, 3)
                ]) : null,
                'is_active' => rand(1, 10) !== 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Service::insert($services);
    }
}