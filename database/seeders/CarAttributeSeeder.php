<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Attribute\Models\Attribute;
use Webkul\Attribute\Models\AttributeOption;

class CarAttributeSeeder extends Seeder
{
    private array $options = [
        ['en' => 'SUV',            'zh_CN' => 'SUV'],
        ['en' => 'Sedan',          'zh_CN' => '轎車'],
        ['en' => 'Business Sedan', 'zh_CN' => '商務轎車'],
        ['en' => 'Hatchback',      'zh_CN' => '揭背車'],
        ['en' => 'Sports Car',     'zh_CN' => '跑車'],
        ['en' => 'Convertible',    'zh_CN' => '開蓬車'],
        ['en' => 'MPV',            'zh_CN' => '多用途MPV'],
        ['en' => 'Hybrid',         'zh_CN' => '混能轎車'],
        ['en' => 'Sports Saloon',  'zh_CN' => '運動房車'],
        ['en' => 'EV',             'zh_CN' => '電動車'],
    ];

    public function run(): void
    {
        $attribute = Attribute::firstOrCreate(
            ['code' => 'car_type'],
            [
                'admin_name'             => 'Car Type',
                'type'                   => 'select',
                'is_required'            => 0,
                'is_unique'              => 0,
                'validation'             => null,
                'is_filterable'          => 1,
                'is_configurable'        => 0,
                'is_user_defined'        => 1,
                'is_visible_on_front'    => 1,
                'position'               => 27,
                'enable_wysiwyg'         => 0,
                'is_comparable'          => 0,
            ]
        );

        if ($attribute->options()->exists()) {
            $this->command->line('  Skipping options (already exist): car_type');

            return;
        }

        foreach ($this->options as $sort => $labels) {
            $option = new AttributeOption([
                'admin_name' => $labels['en'],
                'sort_order' => $sort + 1,
            ]);

            $attribute->options()->save($option);

            foreach (['en', 'zh_CN'] as $locale) {
                $option->translations()->create([
                    'locale' => $locale,
                    'label'  => $labels[$locale],
                ]);
            }

            $this->command->info("  Created option: {$labels['en']} ({$labels['zh_CN']})");
        }

        $this->command->info('  car_type attribute ready.');
    }
}
