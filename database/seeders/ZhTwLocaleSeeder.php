<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZhTwLocaleSeeder extends Seeder
{
    /**
     * Add zh_TW locale and copy all zh_CN content rows to zh_TW.
     *
     * Run with: php artisan db:seed --class=Database\\Seeders\\ZhTwLocaleSeeder
     */
    public function run(): void
    {
        $prefix = DB::getTablePrefix();

        // 1.1 Add zh_TW locale record (skip if already exists)
        if (! DB::table('locales')->where('code', 'zh_TW')->exists()) {
            DB::table('locales')->insert([
                'code'       => 'zh_TW',
                'name'       => '繁體中文',
                'direction'  => 'ltr',
                'logo_path'  => '',
            ]);
            $this->command->info('✓ Locale zh_TW created.');
        } else {
            $this->command->info('  Locale zh_TW already exists — skipping insert.');
        }

        // 1.2 product_flat
        $inserted = DB::statement("
            INSERT INTO {$prefix}product_flat
                (sku, type, product_number, name, short_description, description, url_key,
                 new, featured, status, meta_title, meta_keywords, meta_description,
                 price, special_price, special_price_from, special_price_to, weight,
                 created_at, locale, channel, attribute_family_id, product_id,
                 updated_at, parent_id, visible_individually)
            SELECT
                sku, type, product_number, name, short_description, description, url_key,
                new, featured, status, meta_title, meta_keywords, meta_description,
                price, special_price, special_price_from, special_price_to, weight,
                created_at, 'zh_TW', channel, attribute_family_id, product_id,
                updated_at, parent_id, visible_individually
            FROM {$prefix}product_flat
            WHERE locale = 'zh_CN'
              AND NOT EXISTS (
                  SELECT 1 FROM {$prefix}product_flat pf2
                  WHERE pf2.product_id = {$prefix}product_flat.product_id
                    AND pf2.channel   = {$prefix}product_flat.channel
                    AND pf2.locale    = 'zh_TW'
              )
        ");
        $this->reportCount('product_flat');

        // 1.3 category_translations
        DB::statement("
            INSERT INTO {$prefix}category_translations
                (category_id, name, slug, url_path, description, meta_title,
                 meta_description, meta_keywords, locale_id, locale)
            SELECT
                category_id, name, slug, url_path, description, meta_title,
                meta_description, meta_keywords, locale_id, 'zh_TW'
            FROM {$prefix}category_translations
            WHERE locale = 'zh_CN'
              AND NOT EXISTS (
                  SELECT 1 FROM {$prefix}category_translations ct2
                  WHERE ct2.category_id = {$prefix}category_translations.category_id
                    AND ct2.locale = 'zh_TW'
              )
        ");
        $this->reportCount('category_translations');

        // 1.4 cms_page_translations
        DB::statement("
            INSERT INTO {$prefix}cms_page_translations
                (page_title, url_key, html_content, meta_title, meta_description,
                 meta_keywords, locale, cms_page_id)
            SELECT
                page_title, url_key, html_content, meta_title, meta_description,
                meta_keywords, 'zh_TW', cms_page_id
            FROM {$prefix}cms_page_translations
            WHERE locale = 'zh_CN'
              AND NOT EXISTS (
                  SELECT 1 FROM {$prefix}cms_page_translations cpt2
                  WHERE cpt2.cms_page_id = {$prefix}cms_page_translations.cms_page_id
                    AND cpt2.locale = 'zh_TW'
              )
        ");
        $this->reportCount('cms_page_translations');

        // 1.5 attribute_translations
        DB::statement("
            INSERT INTO {$prefix}attribute_translations (attribute_id, locale, name)
            SELECT attribute_id, 'zh_TW', name
            FROM {$prefix}attribute_translations
            WHERE locale = 'zh_CN'
              AND NOT EXISTS (
                  SELECT 1 FROM {$prefix}attribute_translations at2
                  WHERE at2.attribute_id = {$prefix}attribute_translations.attribute_id
                    AND at2.locale = 'zh_TW'
              )
        ");
        $this->reportCount('attribute_translations');

        // 1.6 attribute_option_translations
        DB::statement("
            INSERT INTO {$prefix}attribute_option_translations (attribute_option_id, locale, label)
            SELECT attribute_option_id, 'zh_TW', label
            FROM {$prefix}attribute_option_translations
            WHERE locale = 'zh_CN'
              AND NOT EXISTS (
                  SELECT 1 FROM {$prefix}attribute_option_translations aot2
                  WHERE aot2.attribute_option_id = {$prefix}attribute_option_translations.attribute_option_id
                    AND aot2.locale = 'zh_TW'
              )
        ");
        $this->reportCount('attribute_option_translations');

        // 1.7 product_attribute_values (unique_id = '{locale}|{product_id}|{attribute_id}')
        DB::statement("
            INSERT INTO {$prefix}product_attribute_values
                (locale, channel, text_value, boolean_value, integer_value, float_value,
                 datetime_value, date_value, json_value, product_id, attribute_id, unique_id)
            SELECT
                'zh_TW', channel, text_value, boolean_value, integer_value, float_value,
                datetime_value, date_value, json_value, product_id, attribute_id,
                CONCAT('zh_TW|', product_id, '|', attribute_id)
            FROM {$prefix}product_attribute_values
            WHERE locale = 'zh_CN'
              AND NOT EXISTS (
                  SELECT 1 FROM {$prefix}product_attribute_values pav2
                  WHERE pav2.unique_id = CONCAT('zh_TW|', {$prefix}product_attribute_values.product_id, '|', {$prefix}product_attribute_values.attribute_id)
              )
        ");
        $this->reportCount('product_attribute_values');

        // 1.8 theme_customization_translations
        DB::statement("
            INSERT INTO {$prefix}theme_customization_translations (theme_customization_id, locale, options)
            SELECT theme_customization_id, 'zh_TW', options
            FROM {$prefix}theme_customization_translations
            WHERE locale = 'zh_CN'
              AND NOT EXISTS (
                  SELECT 1 FROM {$prefix}theme_customization_translations tct2
                  WHERE tct2.theme_customization_id = {$prefix}theme_customization_translations.theme_customization_id
                    AND tct2.locale = 'zh_TW'
              )
        ");
        $this->reportCount('theme_customization_translations');

        // 1.9 blog_translations
        DB::statement("
            INSERT INTO {$prefix}blog_translations
                (blog_id, locale, name, slug, short_description, description,
                 meta_title, meta_description, meta_keywords)
            SELECT
                blog_id, 'zh_TW', name, slug, short_description, description,
                meta_title, meta_description, meta_keywords
            FROM {$prefix}blog_translations
            WHERE locale = 'zh_CN'
              AND NOT EXISTS (
                  SELECT 1 FROM {$prefix}blog_translations bt2
                  WHERE bt2.blog_id = {$prefix}blog_translations.blog_id
                    AND bt2.locale = 'zh_TW'
              )
        ");
        $this->reportCount('blog_translations');

        // 1.10 channel_translations
        DB::statement("
            INSERT INTO {$prefix}channel_translations
                (channel_id, locale, name, description, maintenance_mode_text, home_seo, created_at, updated_at)
            SELECT
                channel_id, 'zh_TW', name, description, maintenance_mode_text, home_seo, created_at, updated_at
            FROM {$prefix}channel_translations
            WHERE locale = 'zh_CN'
              AND NOT EXISTS (
                  SELECT 1 FROM {$prefix}channel_translations chtr2
                  WHERE chtr2.channel_id = {$prefix}channel_translations.channel_id
                    AND chtr2.locale = 'zh_TW'
              )
        ");
        $this->reportCount('channel_translations');

        // core_config (locale-specific configuration values, e.g. footer/navbar settings)
        DB::statement("
            INSERT INTO {$prefix}core_config (code, value, channel_code, locale_code, created_at, updated_at)
            SELECT code, value, channel_code, 'zh_TW', NOW(), NOW()
            FROM {$prefix}core_config
            WHERE locale_code = 'zh_CN'
              AND NOT EXISTS (
                  SELECT 1 FROM {$prefix}core_config cc2
                  WHERE cc2.code = {$prefix}core_config.code
                    AND IFNULL(cc2.channel_code, '') = IFNULL({$prefix}core_config.channel_code, '')
                    AND cc2.locale_code = 'zh_TW'
              )
        ");
        $zhTWCoreConfig = DB::table('core_config')->where('locale_code', 'zh_TW')->count();
        $this->command->info("  → core_config: {$zhTWCoreConfig} zh_TW rows now present");

        // Assign zh_TW to all channels that have zh_CN
        $this->command->line('');
        $this->command->info('Assigning zh_TW to channels...');
        $zhTWLocaleId = DB::table('locales')->where('code', 'zh_TW')->value('id');
        $channelsWithZhCN = DB::table('channel_locales')
            ->join('locales', 'channel_locales.locale_id', '=', 'locales.id')
            ->where('locales.code', 'zh_CN')
            ->pluck('channel_locales.channel_id');

        foreach ($channelsWithZhCN as $channelId) {
            $exists = DB::table('channel_locales')
                ->where('channel_id', $channelId)
                ->where('locale_id', $zhTWLocaleId)
                ->exists();

            if (! $exists) {
                DB::table('channel_locales')->insert([
                    'channel_id' => $channelId,
                    'locale_id'  => $zhTWLocaleId,
                ]);
                $this->command->info("  ✓ zh_TW assigned to channel #{$channelId}");
            } else {
                $this->command->info("  Channel #{$channelId} already has zh_TW — skipping.");
            }
        }

        // 1.11 Verify row counts match zh_CN
        $this->command->line('');
        $this->command->info('Row count verification (zh_TW should equal zh_CN):');
        foreach ($this->tables() as $table) {
            $zhCN = DB::table($table)->where('locale', 'zh_CN')->count();
            $zhTW = DB::table($table)->where('locale', 'zh_TW')->count();
            $status = ($zhTW >= $zhCN) ? '✓' : '✗';
            $this->command->line("  {$status} {$table}: zh_CN={$zhCN}, zh_TW={$zhTW}");
        }
    }

    private function reportCount(string $table): void
    {
        $count = DB::table($table)->where('locale', 'zh_TW')->count();
        $this->command->info("  → {$table}: {$count} zh_TW rows now present");
    }

    private function tables(): array
    {
        return [
            'product_flat',
            'category_translations',
            'cms_page_translations',
            'attribute_translations',
            'attribute_option_translations',
            'product_attribute_values',
            'theme_customization_translations',
            'blog_translations',
            'channel_translations',
        ];
    }
}
