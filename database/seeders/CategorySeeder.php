<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Agriculture & Farming',    'icon' => '🌾', 'description' => 'Subsidies, insurance, and support for farmers and agricultural workers.'],
            ['name' => 'Education & Scholarships', 'icon' => '🎓', 'description' => 'Scholarships, free education schemes, and skill development programs.'],
            ['name' => 'Health & Medical',         'icon' => '🏥', 'description' => 'Health insurance, free treatment, and maternal care schemes.'],
            ['name' => 'Women Empowerment',        'icon' => '👩', 'description' => 'Financial aid, safety, and self-employment programs for women.'],
            ['name' => 'Housing & Urban Development', 'icon' => '🏠', 'description' => 'Affordable housing loans, urban housing projects.'],
            ['name' => 'Finance & Banking',        'icon' => '💰', 'description' => 'Micro-loans, financial inclusion, and banking access schemes.'],
            ['name' => 'Employment & Skill',       'icon' => '💼', 'description' => 'Job creation, skill training, and MGNREGA-type programs.'],
            ['name' => 'Social Welfare',           'icon' => '🤝', 'description' => 'Pension, disability support, and minority welfare schemes.'],
            ['name' => 'Digital India',            'icon' => '💻', 'description' => 'E-governance, digital literacy, and broadband connectivity.'],
            ['name' => 'Environment & Energy',     'icon' => '♻️', 'description' => 'Solar subsidies, clean cooking fuel, and green energy programs.'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                array_merge($cat, [
                    'slug'      => Str::slug($cat['name']),
                    'is_active' => true,
                ])
            );
        }

        $this->command->info('Categories seeded: '.count($categories));
    }
}
