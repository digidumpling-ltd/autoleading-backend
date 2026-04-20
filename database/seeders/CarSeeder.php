<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Webkul\Product\Models\Product;
use Webkul\Attribute\Models\Attribute;
use Webkul\Category\Models\Category;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cars = [
            [
                'name' => 'BMW 3 Series',
                'sku' => 'BMW-3-2024',
                'price' => 89.99,
                'weight' => 1500,
                'status' => 1,
                'visible_individually' => 1,
                'brand' => 'BMW',
                'type' => 'Sedan',
                'description' => 'Luxury sedan with premium features and excellent performance.',
                'short_description' => 'Premium sedan for the discerning driver.',
            ],
            [
                'name' => 'Mercedes-Benz C-Class',
                'sku' => 'MB-C-2024',
                'price' => 95.99,
                'weight' => 1600,
                'status' => 1,
                'visible_individually' => 1,
                'brand' => 'Mercedes Benz',
                'type' => 'Sedan',
                'description' => 'Elegant and sophisticated sedan with advanced technology.',
                'short_description' => 'Sophisticated luxury sedan.',
            ],
            [
                'name' => 'Audi A4',
                'sku' => 'AUDI-A4-2024',
                'price' => 85.99,
                'weight' => 1450,
                'status' => 1,
                'visible_individually' => 1,
                'brand' => 'Audi',
                'type' => 'Sedan',
                'description' => 'Sporty and refined sedan with quattro all-wheel drive.',
                'short_description' => 'Sporty luxury sedan.',
            ],
            [
                'name' => 'BMW X5',
                'sku' => 'BMW-X5-2024',
                'price' => 129.99,
                'weight' => 2200,
                'status' => 1,
                'visible_individually' => 1,
                'brand' => 'BMW',
                'type' => 'SUV',
                'description' => 'Powerful SUV with exceptional handling and luxury interior.',
                'short_description' => 'Luxury SUV for all terrains.',
            ],
            [
                'name' => 'Mercedes-Benz GLC',
                'sku' => 'MB-GLC-2024',
                'price' => 119.99,
                'weight' => 2100,
                'status' => 1,
                'visible_individually' => 1,
                'brand' => 'Mercedes Benz',
                'type' => 'SUV',
                'description' => 'Versatile SUV combining luxury with practicality.',
                'short_description' => 'Luxury compact SUV.',
            ],
            [
                'name' => 'Audi Q5',
                'sku' => 'AUDI-Q5-2024',
                'price' => 115.99,
                'weight' => 2000,
                'status' => 1,
                'visible_individually' => 1,
                'brand' => 'Audi',
                'type' => 'SUV',
                'description' => 'Refined SUV with advanced technology and comfort.',
                'short_description' => 'Premium compact SUV.',
            ],
            [
                'name' => 'BMW M3',
                'sku' => 'BMW-M3-2024',
                'price' => 149.99,
                'weight' => 1700,
                'status' => 1,
                'visible_individually' => 1,
                'brand' => 'BMW',
                'type' => 'Sports',
                'description' => 'High-performance sports sedan with racing heritage.',
                'short_description' => 'Ultimate driving machine.',
            ],
            [
                'name' => 'Mercedes-Benz SL',
                'sku' => 'MB-SL-2024',
                'price' => 199.99,
                'weight' => 1800,
                'status' => 1,
                'visible_individually' => 1,
                'brand' => 'Mercedes Benz',
                'type' => 'Convertible',
                'description' => 'Luxurious roadster with retractable hardtop.',
                'short_description' => 'Elegant convertible for open-top driving.',
            ],
        ];

        foreach ($cars as $carData) {
            $product = Product::create([
                'sku' => $carData['sku'],
                'type' => 'simple',
                'name' => $carData['name'],
                'url_key' => Str::slug($carData['name']),
                'price' => $carData['price'],
                'weight' => $carData['weight'],
                'status' => $carData['status'],
                'visible_individually' => $carData['visible_individually'],
                'description' => $carData['description'],
                'short_description' => $carData['short_description'],
                'meta_title' => $carData['name'],
                'meta_description' => $carData['short_description'],
                'meta_keywords' => $carData['brand'] . ', ' . $carData['type'],
                'attribute_family_id' => 1, // Default attribute family
                'brand' => $carData['brand'],
            ]);

            // Add to default category
            $category = Category::first();
            if ($category) {
                $product->categories()->attach($category->id);
            }

            // Add attribute values
            $this->addAttributeValues($product, $carData);
        }
    }

    private function addAttributeValues($product, $carData)
    {
        // Add brand attribute if it exists
        $brandAttribute = Attribute::where('code', 'brand')->first();
        if ($brandAttribute) {
            $product->attribute_values()->create([
                'attribute_id' => $brandAttribute->id,
                'value' => $carData['brand'],
                'channel' => 'default',
                'locale' => 'en',
            ]);
        }

        // Add type attribute if it exists
        $typeAttribute = Attribute::where('code', 'type')->first();
        if ($typeAttribute) {
            $product->attribute_values()->create([
                'attribute_id' => $typeAttribute->id,
                'value' => $carData['type'],
                'channel' => 'default',
                'locale' => 'en',
            ]);
        }
    }
}
