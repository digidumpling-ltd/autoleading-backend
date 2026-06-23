<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Attribute\Models\Attribute;
use Webkul\Attribute\Models\AttributeFamily;
use Webkul\Category\Models\Category;
use Webkul\Product\Models\Product;

class MercedesE300Seeder extends Seeder
{
    private const SKU = 's2748264';

    private const LOCALES = ['en', 'zh_CN'];

    private array $typeFields = [
        'text'     => 'text_value',
        'textarea' => 'text_value',
        'price'    => 'float_value',
        'boolean'  => 'boolean_value',
        'select'   => 'integer_value',
        'date'     => 'date_value',
        'datetime' => 'datetime_value',
    ];

    public function run(): void
    {
        $family = AttributeFamily::where('code', 'used_car')->firstOrFail();

        $product = Product::firstOrCreate(
            ['sku' => self::SKU],
            ['type' => 'simple', 'attribute_family_id' => $family->id]
        );

        $this->command->info("  Product: {$product->sku} (id:{$product->id})");

        $this->seedAttributeValues($product);
        $this->seedCategory($product);
        $this->seedInventory($product);
        $this->seedChannel($product);
    }

    private function seedAttributeValues(Product $product): void
    {
        // Resolve select option IDs
        $makeId         = $this->optionId('make', 'Mercedes-Benz');
        $carTypeId      = $this->optionId('car_type', 'Sedan');
        $fuelTypeId     = $this->optionId('fuel_type', 'Petrol');
        $transmissionId = $this->optionId('transmission', 'Automatic (AT)');
        $seatsId        = $this->optionId('seats', '5');

        // Per-locale text values: [code => [locale => value]]
        $localeValues = [
            'name' => [
                'en'    => '2017 Mercedes-Benz E300 Avantgarde',
                'zh_CN' => '2017 平治 E300 AVANTGARDE',
            ],
            'url_key' => [
                'en'    => '2017-mercedes-benz-e300-avantgarde',
                'zh_CN' => '2017-mercedes-benz-e300-avantgarde',
            ],
            'short_description' => [
                'en'    => '2017 Mercedes-Benz E300 Avantgarde. Petrol, 1991cc, Automatic (AT), 5 seats, ~90,000 km. Genuine import. Keyless entry, multifunction steering wheel, Burmester audio, new AGM battery, new rear brake discs and pads.',
                'zh_CN' => '2017 平治 E300 AVANTGARDE。汽油，1991cc，自動波AT，5座位，9萬多公里。行貨三字。keyless，多功能呔環，柏林之聲音響，全新AGM車電池，新尾迫力碟，煞車皮。',
            ],
            'description' => [
                'en'    => '<p>2017 Mercedes-Benz E300 Avantgarde — genuine Hong Kong import (行貨), approximately 90,000 km, primarily highway driven.</p><p><strong>Highlights:</strong> Keyless entry and start, multifunction steering wheel, Burmester surround-sound audio system, brand-new AGM battery, new rear brake discs and pads.</p><p>Listing reference: s2748264 &nbsp;|&nbsp; Price: HKD $128,000</p>',
                'zh_CN' => '<p>2017 平治 E300 AVANTGARDE — 行貨，三字，9萬多公里，行高速多。</p><p><strong>配備亮點：</strong>Keyless免匙啟動，多功能呔環，柏林之聲音響系統，全新AGM車電池，新尾迫力碟及煞車皮。</p><p>編號：s2748264 &nbsp;|&nbsp; 售價：HKD $128,000</p>',
            ],
            'meta_title' => [
                'en'    => '2017 Mercedes-Benz E300 Avantgarde | HKD $128,000',
                'zh_CN' => '2017 平治 E300 AVANTGARDE | HKD $128,000',
            ],
            'meta_keywords' => [
                'en'    => 'Mercedes-Benz E300, Avantgarde, 2017, used car, Hong Kong',
                'zh_CN' => '平治 E300, AVANTGARDE, 2017, 二手車, 香港',
            ],
            'meta_description' => [
                'en'    => '2017 Mercedes-Benz E300 Avantgarde, 1991cc petrol, automatic, 5-seat, ~90,000km. Genuine import. HKD $128,000.',
                'zh_CN' => '2017 平治 E300 AVANTGARDE，汽油1991cc，自動波，5座，9萬多公里，行貨。售價HKD $128,000。',
            ],
        ];

        // Non-locale attribute values: [code => value]
        $scalarValues = [
            'price'            => 128000.00,
            'status'           => 1,
            'new'              => 0,
            'featured'         => 0,
            'visible_individually' => 1,
            'guest_checkout'   => 1,
            'manage_stock'     => 0,
            'weight'           => '1650',  // approximate kerb weight in kg
            'product_number'   => self::SKU,
            'listing_ref'      => self::SKU,
            'car_model'        => 'E300 AVANTGARDE',
            'manufacture_year' => '2017',
            'engine_cc'        => '1991cc',
            'mileage'          => '~90,000 km',
            'car_features'     => 'Keyless entry & start, Multifunction steering wheel, Burmester surround-sound audio, New AGM battery, New rear brake discs & pads (行貨 三字 行高速多)',
            // Select attributes (resolved to option IDs)
            'make'             => $makeId,
            'car_type'         => $carTypeId,
            'fuel_type'        => $fuelTypeId,
            'transmission'     => $transmissionId,
            'seats'            => $seatsId,
        ];

        // Per-channel scalars (status needs channel=default)
        $channelOnlyScalars = ['status', 'manage_stock'];

        foreach ($localeValues as $code => $translations) {
            $attr = Attribute::where('code', $code)->first();
            if (! $attr) {
                $this->command->warn("  Attribute not found: {$code}");
                continue;
            }

            foreach (self::LOCALES as $locale) {
                $value = $translations[$locale] ?? $translations['en'];
                $this->upsertValue($product, $attr, $value, $locale, null);
            }
        }

        foreach ($scalarValues as $code => $value) {
            if ($value === null) {
                continue;
            }

            $attr = Attribute::where('code', $code)->first();
            if (! $attr) {
                $this->command->warn("  Attribute not found: {$code}");
                continue;
            }

            $channel = in_array($code, $channelOnlyScalars) ? 'default' : null;
            $this->upsertValue($product, $attr, $value, null, $channel);
        }
    }

    private function upsertValue(Product $product, Attribute $attr, mixed $value, ?string $locale, ?string $channel): void
    {
        $resolvedLocale   = $attr->value_per_locale ? ($locale ?? 'en') : null;
        $resolvedChannel  = $attr->value_per_channel ? ($channel ?? 'default') : null;

        $uniqueId = collect(array_filter(
            [$resolvedChannel, $resolvedLocale, $product->id, $attr->id],
            fn ($v) => $v !== null
        ))->implode('|');

        $typeField = $this->typeFields[$attr->type] ?? 'text_value';

        DB::table('product_attribute_values')->updateOrInsert(
            ['unique_id' => $uniqueId],
            [
                'product_id'      => $product->id,
                'attribute_id'    => $attr->id,
                'locale'          => $resolvedLocale,
                'channel'         => $resolvedChannel,
                'unique_id'       => $uniqueId,
                'text_value'      => null,
                'boolean_value'   => null,
                'integer_value'   => null,
                'float_value'     => null,
                'datetime_value'  => null,
                'date_value'      => null,
                'json_value'      => null,
                $typeField        => $value,
            ]
        );
    }

    private function optionId(string $attrCode, string $adminName): ?int
    {
        $attr = Attribute::where('code', $attrCode)->first();
        if (! $attr) {
            return null;
        }

        $option = $attr->options()->where('admin_name', $adminName)->first();
        if (! $option) {
            $this->command->warn("  Option '{$adminName}' not found for attribute '{$attrCode}'");

            return null;
        }

        return $option->id;
    }

    private function seedCategory(Product $product): void
    {
        $category = Category::whereHas(
            'translations',
            fn ($q) => $q->where('slug', 'for-sale')
        )->first();

        if (! $category) {
            $this->command->warn('  "For Sale" category not found — skipping category assignment');

            return;
        }

        if (! $product->categories()->where('category_id', $category->id)->exists()) {
            $product->categories()->attach($category->id);
            $this->command->line("  Assigned to category: {$category->id}");
        }
    }

    private function seedInventory(Product $product): void
    {
        DB::table('product_inventories')->updateOrInsert(
            ['product_id' => $product->id, 'inventory_source_id' => 1],
            ['qty' => 1]
        );

        $this->command->line('  Inventory set: qty=1');
    }

    private function seedChannel(Product $product): void
    {
        $exists = DB::table('product_channels')
            ->where('product_id', $product->id)
            ->where('channel_id', 1)
            ->exists();

        if (! $exists) {
            DB::table('product_channels')->insert([
                'product_id' => $product->id,
                'channel_id' => 1,
            ]);

            $this->command->line('  Assigned to channel: 1');
        }
    }
}
