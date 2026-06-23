<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Category\Models\Category;

class ReorganizeCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Find or create the "For Rent" category first (before deleting others)
        // Also migrate old "rental" slug to "for-rent" if it exists
        $forRent = Category::whereHas('translations', function ($q) {
            $q->whereIn('slug', ['rental', 'for-rent']);
        })->first();

        if (! $forRent) {
            $root = Category::find(1);

            $forRent = new Category([
                'position'     => 1,
                'status'       => 1,
                'display_mode' => 'products_and_description',
            ]);

            $forRent->appendToNode($root)->save();

            foreach (['en' => 'For Rent', 'zh_CN' => '租車'] as $locale => $name) {
                $forRent->translations()->create([
                    'locale'           => $locale,
                    'name'             => $name,
                    'slug'             => 'for-rent',
                    'url_path'         => 'for-rent',
                    'description'      => '',
                    'meta_title'       => $name,
                    'meta_description' => '',
                    'meta_keywords'    => '',
                ]);
            }

            $this->command->info('  Created "For Rent" category (ID: ' . $forRent->id . ').');
        } else {
            // Update name/slug if still using old "rental" label
            $forRent->translations()->update([
                'slug'       => 'for-rent',
                'url_path'   => 'for-rent',
                'meta_title' => \DB::raw("CASE WHEN locale = 'en' THEN 'For Rent' ELSE meta_title END"),
                'name'       => \DB::raw("CASE WHEN locale = 'en' THEN 'For Rent' ELSE name END"),
            ]);

            $this->command->line('  "For Rent" category already exists (ID: ' . $forRent->id . '), updated labels.');
        }

        // 2. Find or create the "For Sale" category
        $forSale = Category::whereHas('translations', function ($q) {
            $q->where('slug', 'for-sale');
        })->first();

        if (! $forSale) {
            $root = Category::find(1);

            $forSale = new Category([
                'position'     => 2,
                'status'       => 1,
                'display_mode' => 'products_and_description',
            ]);

            $forSale->appendToNode($root)->save();

            foreach (['en' => 'For Sale', 'zh_CN' => '出售'] as $locale => $name) {
                $forSale->translations()->create([
                    'locale'           => $locale,
                    'name'             => $name,
                    'slug'             => 'for-sale',
                    'url_path'         => 'for-sale',
                    'description'      => '',
                    'meta_title'       => $name,
                    'meta_description' => '',
                    'meta_keywords'    => '',
                ]);
            }

            $this->command->info('  Created "For Sale" category (ID: ' . $forSale->id . ').');
        } else {
            $this->command->line('  For Sale category already exists (ID: ' . $forSale->id . ').');
        }

        // 3. Delete all non-root categories except For Rent and For Sale, detaching products first
        $toDelete = Category::where('parent_id', '!=', null)
            ->whereNotIn('id', [$forRent->id, $forSale->id])
            ->pluck('id')
            ->toArray();

        if (! empty($toDelete)) {
            foreach ($toDelete as $categoryId) {
                $category = Category::find($categoryId);
                if ($category) {
                    $category->products()->detach();
                    $category->delete();
                }
            }

            $this->command->info('  Deleted ' . count($toDelete) . ' old categories.');
        } else {
            $this->command->line('  No old categories to remove.');
        }

        // 4. Attach all existing non-wallet products to the For Rent category
        $productIds = \DB::table('products')
            ->where('sku', '!=', 'WALLET-CREDIT')
            ->pluck('id')
            ->toArray();

        if (! empty($productIds)) {
            $forRent->products()->syncWithoutDetaching($productIds);
            $this->command->info('  Attached ' . count($productIds) . ' products to "For Rent" category.');
        }
    }
}
