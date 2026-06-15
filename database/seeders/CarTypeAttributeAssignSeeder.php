<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Attribute\Models\Attribute;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductAttributeValue;

class CarTypeAttributeAssignSeeder extends Seeder
{
    public function run(): void
    {
        $attribute = Attribute::where('code', 'car_type')->firstOrFail();

        // Build map: english category name => attribute option id
        $optionMap = $attribute->options()
            ->with('translations')
            ->get()
            ->mapWithKeys(function ($option) {
                $en = $option->translations->firstWhere('locale', 'en')?->label ?? $option->admin_name;

                return [strtolower($en) => $option->id];
            });

        $assigned = 0;
        $skipped  = 0;

        Product::with(['categories.translations'])->chunk(100, function ($products) use ($attribute, $optionMap, &$assigned, &$skipped) {
            foreach ($products as $product) {
                // Find first category whose English name matches an option
                $optionId = null;

                foreach ($product->categories as $category) {
                    $categoryName = $category->translations->firstWhere('locale', 'en')?->name;

                    if ($categoryName && isset($optionMap[strtolower($categoryName)])) {
                        $optionId = $optionMap[strtolower($categoryName)];
                        break;
                    }
                }

                if (! $optionId) {
                    $skipped++;
                    continue;
                }

                $uniqueId = implode('|', array_filter([
                    null, // channel (not value_per_channel)
                    null, // locale  (not value_per_locale)
                    $product->id,
                    $attribute->id,
                ]));

                ProductAttributeValue::updateOrCreate(
                    [
                        'product_id'   => $product->id,
                        'attribute_id' => $attribute->id,
                        'locale'       => null,
                        'channel'      => null,
                    ],
                    [
                        'integer_value' => $optionId,
                        'unique_id'     => $uniqueId,
                    ]
                );

                $assigned++;
            }
        });

        $this->command->info("  Assigned: {$assigned} products");
        $this->command->line("  Skipped (no matching category): {$skipped} products");
    }
}
