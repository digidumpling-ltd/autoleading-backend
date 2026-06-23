<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Attribute\Models\Attribute;
use Webkul\Attribute\Models\AttributeFamily;
use Webkul\Category\Models\Category;
use Webkul\Product\Models\Product;

class PorscheCaymanSeeder extends Seeder
{
    private const SKU = 's2695414';

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

        $classicSeriesId = $this->ensureCarTypeOption(
            'Classic Series',
            ['en' => 'Classic Series', 'zh_CN' => '經典系列']
        );

        $product = Product::firstOrCreate(
            ['sku' => self::SKU],
            ['type' => 'simple', 'attribute_family_id' => $family->id]
        );

        $this->command->info("  Product: {$product->sku} (id:{$product->id})");

        $this->seedAttributeValues($product, $classicSeriesId);
        $this->seedCategory($product);
        $this->seedInventory($product);
        $this->seedChannel($product);
    }

    private function ensureCarTypeOption(string $adminName, array $labels): int
    {
        $attr = Attribute::where('code', 'car_type')->firstOrFail();
        $option = $attr->options()->where('admin_name', $adminName)->first();

        if ($option) {
            $this->command->line("  car_type option already exists: {$adminName} (id:{$option->id})");

            return $option->id;
        }

        $nextSort = $attr->options()->max('sort_order') + 1;

        $option = $attr->options()->create([
            'admin_name' => $adminName,
            'sort_order' => $nextSort,
        ]);

        foreach ($labels as $locale => $label) {
            $option->translations()->create(['locale' => $locale, 'label' => $label]);
        }

        $this->command->info("  Created car_type option: {$adminName} (id:{$option->id})");

        return $option->id;
    }

    private function seedAttributeValues(Product $product, int $classicSeriesId): void
    {
        $makeId         = $this->optionId('make', 'Porsche');
        $fuelTypeId     = $this->optionId('fuel_type', 'Petrol');
        $transmissionId = $this->optionId('transmission', 'Automatic (AT)');
        $seatsId        = $this->optionId('seats', '5');

        $localeValues = [
            'name' => [
                'en'    => '2008 Porsche Cayman',
                'zh_CN' => '2008 保時捷 CAYMAN',
            ],
            'url_key' => [
                'en'    => '2008-porsche-cayman',
                'zh_CN' => '2008-porsche-cayman',
            ],
            'short_description' => [
                'en'    => '2008 Porsche Cayman, 2687cc petrol, Automatic (AT), 5 seats, 50,000 km. Genuine import, year-end registration, top spec. 4x Michelin tyres, new Pioneer head unit with rear camera.',
                'zh_CN' => '2008 保時捷 CAYMAN，2687cc 汽油，自動波AT，5座位，50,000km。行貨年底登記，頂版。四條米芝蓮胎，新Pioneer機後鏡頭。',
            ],
            'description' => [
                'en'    => '<p>2008 Porsche Cayman — genuine Hong Kong import (行貨), year-end registration, top specification, 50,000 km.</p><p><strong>Highlights:</strong> 4x Michelin tyres, new Pioneer head unit with rear camera.</p><p>Listing reference: s2695414 &nbsp;|&nbsp; Price: HKD $63,000 (was $80,000)</p>',
                'zh_CN' => '<p>2008 保時捷 CAYMAN — 行貨，年底登記，頂版，50,000km。</p><p><strong>配備亮點：</strong>四條米芝蓮胎，新Pioneer機後鏡頭。</p><p>編號：s2695414 &nbsp;|&nbsp; 售價：HKD $63,000（原價 $80,000）</p>',
            ],
            'meta_title' => [
                'en'    => '2008 Porsche Cayman | HKD $63,000',
                'zh_CN' => '2008 保時捷 CAYMAN | HKD $63,000',
            ],
            'meta_keywords' => [
                'en'    => 'Porsche Cayman, 2008, used car, Hong Kong, sports car',
                'zh_CN' => '保時捷 Cayman, 2008, 二手車, 香港, 跑車',
            ],
            'meta_description' => [
                'en'    => '2008 Porsche Cayman, 2687cc petrol, automatic, 50,000 km, top spec. HKD $63,000.',
                'zh_CN' => '2008 保時捷 CAYMAN，汽油2687cc，自動波，50,000km，頂版。售價HKD $63,000。',
            ],
        ];

        $channelOnlyScalars = ['status', 'manage_stock'];

        $scalarValues = [
            'price'               => 80000.00,   // original price
            'special_price'       => 63000.00,   // reduced price
            'status'              => 1,
            'new'                 => 0,
            'featured'            => 0,
            'visible_individually'=> 1,
            'guest_checkout'      => 1,
            'manage_stock'        => 0,
            'weight'              => '1300',
            'product_number'      => self::SKU,
            'listing_ref'         => self::SKU,
            'car_model'           => 'CAYMAN',
            'manufacture_year'    => '2008',
            'engine_cc'           => '2687cc',
            'mileage'             => '50,000 km',
            'car_features'        => '行貨年底登記、頂版、四條米芝蓮胎、新Pioneer機後鏡頭 (Genuine import, year-end reg, top spec, 4x Michelin, new Pioneer + rear cam)',
            'car_type'            => $classicSeriesId,
            'make'                => $makeId,
            'fuel_type'           => $fuelTypeId,
            'transmission'        => $transmissionId,
            'seats'               => $seatsId,
        ];

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
        $resolvedLocale  = $attr->value_per_locale ? ($locale ?? 'en') : null;
        $resolvedChannel = $attr->value_per_channel ? ($channel ?? 'default') : null;

        $uniqueId = collect(array_filter(
            [$resolvedChannel, $resolvedLocale, $product->id, $attr->id],
            fn ($v) => $v !== null
        ))->implode('|');

        $typeField = $this->typeFields[$attr->type] ?? 'text_value';

        DB::table('product_attribute_values')->updateOrInsert(
            ['unique_id' => $uniqueId],
            [
                'product_id'     => $product->id,
                'attribute_id'   => $attr->id,
                'locale'         => $resolvedLocale,
                'channel'        => $resolvedChannel,
                'unique_id'      => $uniqueId,
                'text_value'     => null,
                'boolean_value'  => null,
                'integer_value'  => null,
                'float_value'    => null,
                'datetime_value' => null,
                'date_value'     => null,
                'json_value'     => null,
                $typeField       => $value,
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
