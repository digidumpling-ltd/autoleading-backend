<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Category\Models\Category;

class CarCategorySeeder extends Seeder
{
    /**
     * Vehicle categories derived from product_list.csv (col 2).
     * "SUV， 越野車" is normalised to SUV.
     */
    private array $categories = [
        ['en' => 'SUV',            'zh_CN' => 'SUV',      'slug' => 'suv'],
        ['en' => 'Sedan',          'zh_CN' => '轎車',     'slug' => 'sedan'],
        ['en' => 'Business Sedan', 'zh_CN' => '商務轎車', 'slug' => 'business-sedan'],
        ['en' => 'Hatchback',      'zh_CN' => '揭背車',   'slug' => 'hatchback'],
        ['en' => 'Sports Car',     'zh_CN' => '跑車',     'slug' => 'sports-car'],
        ['en' => 'Convertible',    'zh_CN' => '開蓬車',   'slug' => 'convertible'],
        ['en' => 'MPV',            'zh_CN' => '多用途MPV','slug' => 'mpv'],
        ['en' => 'Hybrid',         'zh_CN' => '混能轎車', 'slug' => 'hybrid'],
        ['en' => 'Sports Saloon',  'zh_CN' => '運動房車', 'slug' => 'sports-saloon'],
    ];

    public function run(): void
    {
        $root = Category::find(1);

        foreach ($this->categories as $position => $cat) {
            $existing = Category::whereHas('translations', function ($q) use ($cat) {
                $q->where('slug', $cat['slug']);
            })->first();

            if ($existing) {
                $this->command->line("  Skipping (exists): {$cat['en']}");
                continue;
            }

            $category = new Category([
                'position'     => $position + 1,
                'status'       => 0,
                'display_mode' => 'products_and_description',
            ]);

            $category->appendToNode($root)->save();

            foreach (['en', 'zh_CN'] as $locale) {
                $category->translations()->create([
                    'locale'           => $locale,
                    'name'             => $cat[$locale],
                    'slug'             => $cat['slug'],
                    'url_path'         => $cat['slug'],
                    'description'      => '',
                    'meta_title'       => $cat[$locale],
                    'meta_description' => '',
                    'meta_keywords'    => '',
                ]);
            }

            $this->command->info("  Created: {$cat['en']} ({$cat['zh_CN']})");
        }
    }
}
