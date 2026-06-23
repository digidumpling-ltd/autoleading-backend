<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Attribute\Models\Attribute;
use Webkul\Attribute\Models\AttributeFamily;
use Webkul\Attribute\Models\AttributeGroup;

class UsedCarFamilySeeder extends Seeder
{
    private array $carAttributes = [
        'listing_ref' => [
            'admin_name'          => 'Listing Reference',
            'type'                => 'text',
            'is_required'         => 0,
            'is_filterable'       => 0,
            'is_visible_on_front' => 1,
            'position'            => 42,
        ],
        'make' => [
            'admin_name'          => 'Make',
            'type'                => 'select',
            'is_required'         => 1,
            'is_filterable'       => 1,
            'is_visible_on_front' => 1,
            'position'            => 43,
        ],
        'car_model' => [
            'admin_name'          => 'Model',
            'type'                => 'text',
            'is_required'         => 1,
            'is_filterable'       => 0,
            'is_visible_on_front' => 1,
            'position'            => 44,
        ],
        'manufacture_year' => [
            'admin_name'          => 'Year',
            'type'                => 'text',
            'is_required'         => 0,
            'is_filterable'       => 1,
            'is_visible_on_front' => 1,
            'position'            => 45,
        ],
        'fuel_type' => [
            'admin_name'          => 'Fuel Type',
            'type'                => 'select',
            'is_required'         => 0,
            'is_filterable'       => 1,
            'is_visible_on_front' => 1,
            'position'            => 46,
        ],
        'engine_cc' => [
            'admin_name'          => 'Displacement (cc)',
            'type'                => 'text',
            'is_required'         => 0,
            'is_filterable'       => 0,
            'is_visible_on_front' => 1,
            'position'            => 47,
        ],
        'transmission' => [
            'admin_name'          => 'Transmission',
            'type'                => 'select',
            'is_required'         => 0,
            'is_filterable'       => 1,
            'is_visible_on_front' => 1,
            'position'            => 48,
        ],
        'mileage' => [
            'admin_name'          => 'Mileage',
            'type'                => 'text',
            'is_required'         => 0,
            'is_filterable'       => 0,
            'is_visible_on_front' => 1,
            'position'            => 49,
        ],
        'seats' => [
            'admin_name'          => 'Seats',
            'type'                => 'select',
            'is_required'         => 0,
            'is_filterable'       => 1,
            'is_visible_on_front' => 1,
            'position'            => 50,
        ],
        'car_features' => [
            'admin_name'          => 'Features',
            'type'                => 'textarea',
            'is_required'         => 0,
            'is_filterable'       => 0,
            'is_visible_on_front' => 1,
            'position'            => 51,
        ],
    ];

    private array $selectOptions = [
        'make' => [
            ['en' => 'Mercedes-Benz', 'zh_CN' => '平治'],
            ['en' => 'BMW',           'zh_CN' => '寶馬'],
            ['en' => 'Audi',          'zh_CN' => '奧迪'],
            ['en' => 'Toyota',        'zh_CN' => '豐田'],
            ['en' => 'Honda',         'zh_CN' => '本田'],
            ['en' => 'Lexus',         'zh_CN' => '凌志'],
            ['en' => 'Porsche',       'zh_CN' => '保時捷'],
            ['en' => 'Volkswagen',    'zh_CN' => '福士'],
            ['en' => 'Nissan',        'zh_CN' => '日產'],
            ['en' => 'Mazda',         'zh_CN' => '萬事得'],
            ['en' => 'Land Rover',    'zh_CN' => '陸虎'],
            ['en' => 'Volvo',         'zh_CN' => '富豪'],
        ],
        'fuel_type' => [
            ['en' => 'Petrol',   'zh_CN' => '汽油'],
            ['en' => 'Diesel',   'zh_CN' => '柴油'],
            ['en' => 'Hybrid',   'zh_CN' => '混能'],
            ['en' => 'Electric', 'zh_CN' => '電動'],
        ],
        'transmission' => [
            ['en' => 'Automatic (AT)', 'zh_CN' => '自動波 AT'],
            ['en' => 'Manual (MT)',    'zh_CN' => '手動波 MT'],
        ],
        'seats' => [
            ['en' => '2', 'zh_CN' => '2'],
            ['en' => '4', 'zh_CN' => '4'],
            ['en' => '5', 'zh_CN' => '5'],
            ['en' => '7', 'zh_CN' => '7'],
            ['en' => '8', 'zh_CN' => '8'],
        ],
    ];

    // Attribute labels per locale (code => [locale => label])
    private array $attributeLabels = [
        'car_type'        => ['en' => 'Car Type',           'zh_CN' => '車類'],
        'listing_ref'     => ['en' => 'Listing Reference',  'zh_CN' => '編號'],
        'make'            => ['en' => 'Make',               'zh_CN' => '車廠'],
        'car_model'       => ['en' => 'Model',              'zh_CN' => '型號'],
        'manufacture_year'=> ['en' => 'Year',               'zh_CN' => '年份'],
        'fuel_type'       => ['en' => 'Fuel Type',          'zh_CN' => '燃料'],
        'engine_cc'       => ['en' => 'Displacement (cc)',  'zh_CN' => '容積'],
        'transmission'    => ['en' => 'Transmission',       'zh_CN' => '傳動'],
        'mileage'         => ['en' => 'Mileage',            'zh_CN' => '里數'],
        'seats'           => ['en' => 'Seats',              'zh_CN' => '座位'],
        'car_features'    => ['en' => 'Features',           'zh_CN' => '配備'],
    ];

    // Groups for the Used Car family: code => [name, column, position]
    private array $groupDefs = [
        'general'            => ['General',             1, 1],
        'description'        => ['Description',         1, 2],
        'meta_description'   => ['Meta Description',    1, 3],
        'vehicle_identity'   => ['Vehicle Identity',    1, 4],
        'engine_performance' => ['Engine & Performance',1, 5],
        'price'              => ['Price',               2, 1],
        'shipping'           => ['Shipping',            2, 2],
        'settings'           => ['Settings',            2, 3],
        'inventories'        => ['Inventories',         2, 4],
        'comfort_specs'      => ['Comfort & Specs',     2, 5],
    ];

    // Core attribute IDs (from Bagisto install seeder) mapped to group code
    private array $coreGroupMappings = [
        'general' => [1, 27, 2, 3],       // sku, product_number, name, url_key
        'description' => [9, 10],          // short_description, description
        'meta_description' => [16, 17, 18],// meta_title, meta_keywords, meta_description
        'price' => [11, 13, 14, 15],       // price, special_price, from, to
        'shipping' => [22],                // weight
        'settings' => [5, 6, 7, 8, 26, 4],// new, featured, visible_individually, status, guest_checkout, tax
        'inventories' => [28],             // manage_stock
    ];

    // Car-specific attribute codes mapped to group code
    private array $carGroupMappings = [
        'vehicle_identity'   => ['listing_ref', 'car_type', 'make', 'car_model', 'manufacture_year'],
        'engine_performance' => ['fuel_type', 'engine_cc', 'transmission', 'mileage'],
        'comfort_specs'      => ['seats', 'car_features'],
    ];

    public function run(): void
    {
        $family = $this->seedFamily();
        $groups = $this->seedGroups($family);
        $attrs  = $this->seedAttributes();
        $this->seedAttributeTranslations($attrs);
        $this->seedGroupMappings($groups, $attrs);
    }

    private function seedFamily(): AttributeFamily
    {
        $family = AttributeFamily::firstOrCreate(
            ['code' => 'used_car'],
            ['name' => 'Used Car', 'status' => 0, 'is_user_defined' => 1]
        );

        $this->command->info("  Family: {$family->name} (id:{$family->id})");

        return $family;
    }

    private function seedGroups(AttributeFamily $family): array
    {
        $groups = [];

        foreach ($this->groupDefs as $code => [$name, $column, $position]) {
            $group = AttributeGroup::firstOrCreate(
                ['code' => $code, 'attribute_family_id' => $family->id],
                ['name' => $name, 'column' => $column, 'position' => $position, 'is_user_defined' => 1]
            );

            $groups[$code] = $group;
            $this->command->line("  Group: {$group->name} (id:{$group->id})");
        }

        return $groups;
    }

    private function seedAttributes(): array
    {
        $created = [];

        // Include existing car_type so it can be mapped to vehicle_identity group
        $carType = Attribute::where('code', 'car_type')->first();
        if ($carType) {
            $created['car_type'] = $carType;
        }

        foreach ($this->carAttributes as $code => $config) {
            $attr = Attribute::firstOrCreate(
                ['code' => $code],
                array_merge($config, [
                    'is_unique'         => 0,
                    'validation'        => null,
                    'is_configurable'   => 0,
                    'enable_wysiwyg'    => 0,
                    'is_comparable'     => 0,
                    'value_per_locale'  => 0,
                    'value_per_channel' => 0,
                    'is_user_defined'   => 1,
                ])
            );

            $created[$code] = $attr;
            $this->command->line("  Attribute: {$attr->code} (id:{$attr->id})");

            if ($attr->type === 'select' && isset($this->selectOptions[$code])) {
                $this->seedOptions($attr, $this->selectOptions[$code]);
            }
        }

        return $created;
    }

    private function seedAttributeTranslations(array $attrs): void
    {
        foreach ($this->attributeLabels as $code => $labels) {
            $attr = $attrs[$code] ?? Attribute::where('code', $code)->first();
            if (! $attr) {
                continue;
            }

            foreach ($labels as $locale => $name) {
                DB::table('attribute_translations')->updateOrInsert(
                    ['attribute_id' => $attr->id, 'locale' => $locale],
                    ['name' => $name]
                );

                $this->command->line("  Translation: {$code} [{$locale}] = {$name}");
            }
        }
    }

    private function seedOptions(Attribute $attr, array $options): void
    {
        if ($attr->options()->exists()) {
            $this->command->line("    Options already exist for: {$attr->code}");

            return;
        }

        foreach ($options as $sort => $labels) {
            $option = $attr->options()->create([
                'admin_name' => $labels['en'],
                'sort_order' => $sort + 1,
            ]);

            foreach (['en', 'zh_CN'] as $locale) {
                $option->translations()->create([
                    'locale' => $locale,
                    'label'  => $labels[$locale],
                ]);
            }

            $this->command->line("    Option: {$labels['en']}");
        }
    }

    private function seedGroupMappings(array $groups, array $carAttrs): void
    {
        // Map core attributes (by ID) to their groups
        foreach ($this->coreGroupMappings as $groupCode => $attrIds) {
            $group = $groups[$groupCode];

            foreach ($attrIds as $pos => $attrId) {
                $this->insertMappingIfMissing($attrId, $group->id, $pos + 1);
            }
        }

        // Map car-specific attributes (by code) to car groups
        foreach ($this->carGroupMappings as $groupCode => $codes) {
            $group = $groups[$groupCode];

            foreach ($codes as $pos => $code) {
                if (! isset($carAttrs[$code])) {
                    continue;
                }

                $this->insertMappingIfMissing($carAttrs[$code]->id, $group->id, $pos + 1);
            }
        }
    }

    private function insertMappingIfMissing(int $attrId, int $groupId, int $position): void
    {
        $exists = DB::table('attribute_group_mappings')
            ->where('attribute_id', $attrId)
            ->where('attribute_group_id', $groupId)
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('attribute_group_mappings')->insert([
            'attribute_id'       => $attrId,
            'attribute_group_id' => $groupId,
            'position'           => $position,
        ]);

        $this->command->line("    Mapped attr:{$attrId} → group:{$groupId}");
    }
}
