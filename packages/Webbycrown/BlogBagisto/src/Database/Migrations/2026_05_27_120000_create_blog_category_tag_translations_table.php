<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- blog_category_translations ---
        Schema::create('blog_category_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blog_category_id');
            $table->string('locale');
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->longText('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->unique(['blog_category_id', 'locale']);
            $table->foreign('blog_category_id')->references('id')->on('blog_categories')->onDelete('cascade');
        });

        $defaultLocale = config('app.locale', 'en');

        foreach (DB::table('blog_categories')->get() as $category) {
            $locale = (isset($category->locale) && !empty($category->locale)) ? $category->locale : $defaultLocale;

            DB::table('blog_category_translations')->insert([
                'blog_category_id' => $category->id,
                'locale'           => $locale,
                'name'             => $category->name,
                'slug'             => $category->slug,
                'description'      => $category->description,
                'meta_title'       => $category->meta_title,
                'meta_description' => $category->meta_description,
                'meta_keywords'    => $category->meta_keywords,
            ]);
        }

        Schema::table('blog_categories', function (Blueprint $table) {
            $table->dropColumn(['name', 'slug', 'description', 'meta_title', 'meta_description', 'meta_keywords', 'locale']);
        });

        // --- blog_tag_translations ---
        Schema::create('blog_tag_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blog_tag_id');
            $table->string('locale');
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->longText('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->unique(['blog_tag_id', 'locale']);
            $table->foreign('blog_tag_id')->references('id')->on('blog_tags')->onDelete('cascade');
        });

        foreach (DB::table('blog_tags')->get() as $tag) {
            $locale = (isset($tag->locale) && !empty($tag->locale)) ? $tag->locale : $defaultLocale;

            DB::table('blog_tag_translations')->insert([
                'blog_tag_id'      => $tag->id,
                'locale'           => $locale,
                'name'             => $tag->name,
                'slug'             => $tag->slug,
                'description'      => $tag->description,
                'meta_title'       => $tag->meta_title,
                'meta_description' => $tag->meta_description,
                'meta_keywords'    => $tag->meta_keywords,
            ]);
        }

        Schema::table('blog_tags', function (Blueprint $table) {
            $table->dropColumn(['name', 'slug', 'description', 'meta_title', 'meta_description', 'meta_keywords', 'locale']);
        });
    }

    public function down(): void
    {
        // Restore blog_categories columns
        Schema::table('blog_categories', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->string('slug')->nullable()->after('name');
            $table->longText('description')->nullable()->after('slug');
            $table->string('locale')->nullable()->after('description');
            $table->string('meta_title')->nullable()->after('locale');
            $table->string('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
        });

        DB::table('blog_category_translations')
            ->orderBy('blog_category_id')->orderBy('id')->get()
            ->groupBy('blog_category_id')
            ->each(function ($rows, $categoryId) {
                $first = $rows->first();
                DB::table('blog_categories')->where('id', $categoryId)->update([
                    'name'             => $first->name,
                    'slug'             => $first->slug,
                    'description'      => $first->description,
                    'locale'           => $first->locale,
                    'meta_title'       => $first->meta_title,
                    'meta_description' => $first->meta_description,
                    'meta_keywords'    => $first->meta_keywords,
                ]);
            });

        Schema::dropIfExists('blog_category_translations');

        // Restore blog_tags columns
        Schema::table('blog_tags', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->string('slug')->nullable()->after('name');
            $table->longText('description')->nullable()->after('slug');
            $table->string('locale')->nullable()->after('description');
            $table->string('meta_title')->nullable()->after('locale');
            $table->string('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
        });

        DB::table('blog_tag_translations')
            ->orderBy('blog_tag_id')->orderBy('id')->get()
            ->groupBy('blog_tag_id')
            ->each(function ($rows, $tagId) {
                $first = $rows->first();
                DB::table('blog_tags')->where('id', $tagId)->update([
                    'name'             => $first->name,
                    'slug'             => $first->slug,
                    'description'      => $first->description,
                    'locale'           => $first->locale,
                    'meta_title'       => $first->meta_title,
                    'meta_description' => $first->meta_description,
                    'meta_keywords'    => $first->meta_keywords,
                ]);
            });

        Schema::dropIfExists('blog_tag_translations');
    }
};
