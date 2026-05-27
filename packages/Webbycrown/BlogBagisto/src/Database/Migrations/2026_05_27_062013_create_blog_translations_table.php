<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blog_id');
            $table->string('locale');
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->unique(['blog_id', 'locale']);
            $table->foreign('blog_id')->references('id')->on('blogs')->onDelete('cascade');
        });

        $defaultLocale = config('app.locale', 'en');

        $blogs = DB::table('blogs')->get();

        foreach ($blogs as $blog) {
            $locale = $blog->locale ?? $defaultLocale;

            if (empty($locale)) {
                $locale = $defaultLocale;
                echo "Blog ID {$blog->id}: no locale set, falling back to {$defaultLocale}\n";
            }

            DB::table('blog_translations')->insert([
                'blog_id'           => $blog->id,
                'locale'            => $locale,
                'name'              => $blog->name,
                'slug'              => $blog->slug,
                'short_description' => $blog->short_description,
                'description'       => $blog->description,
                'meta_title'        => $blog->meta_title,
                'meta_description'  => $blog->meta_description,
                'meta_keywords'     => $blog->meta_keywords,
            ]);
        }

        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'slug',
                'short_description',
                'description',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'locale',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('slug')->after('name');
            $table->string('short_description')->after('slug');
            $table->longText('description')->after('short_description');
            $table->string('locale')->after('description');
            $table->string('meta_title')->after('locale');
            $table->string('meta_description')->after('meta_title');
            $table->string('meta_keywords')->after('meta_description');
        });

        $translations = DB::table('blog_translations')
            ->orderBy('blog_id')
            ->orderBy('id')
            ->get()
            ->groupBy('blog_id');

        foreach ($translations as $blogId => $rows) {
            $first = $rows->first();
            DB::table('blogs')->where('id', $blogId)->update([
                'name'              => $first->name,
                'slug'              => $first->slug,
                'short_description' => $first->short_description,
                'description'       => $first->description,
                'locale'            => $first->locale,
                'meta_title'        => $first->meta_title,
                'meta_description'  => $first->meta_description,
                'meta_keywords'     => $first->meta_keywords,
            ]);
        }

        Schema::dropIfExists('blog_translations');
    }
};
