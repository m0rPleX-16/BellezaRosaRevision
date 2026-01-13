<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Hair Services',
                'description' => 'All hair-related services',
                'specialty' => 'hair',
                'is_active' => true,
                'display_order' => 1
            ],
            [
                'name' => 'Nail Services',
                'description' => 'All nail-related services',
                'specialty' => 'nail',
                'is_active' => true,
                'display_order' => 2
            ],
            [
                'name' => 'Spa Services',
                'description' => 'Foot and hand spa treatments',
                'specialty' => 'spa',
                'is_active' => true,
                'display_order' => 3
            ],
            [
                'name' => 'Full Service',
                'description' => 'Combined hair and nail services',
                'specialty' => 'all',
                'is_active' => true,
                'display_order' => 4
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}