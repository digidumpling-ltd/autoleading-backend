<?php

namespace Webbycrown\BlogBagisto\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webbycrown\BlogBagisto\Models\Category;
use Webkul\Core\Eloquent\Repository;

class BlogRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webbycrown\BlogBagisto\Models\Blog';
    }

    /**
     * Save blog.
     *
     * @param  array  $data
     * @return bool|\Webbycrown\BlogBagisto\Contracts\Blog
     */
    public function save(array $data)
    {
        Event::dispatch('admin.blogs.create.before', $data);

        $create_data = $data;

        if (array_key_exists('src', $create_data)) {
            unset($create_data['src']);
        }

        $model = $this->getModel();

        foreach (core()->getAllLocales() as $locale) {
            foreach ($model->translatedAttributes as $attribute) {
                if (isset($create_data[$attribute])) {
                    $create_data[$locale->code][$attribute] = $create_data[$attribute];
                }
            }
        }

        foreach ($model->translatedAttributes as $attribute) {
            unset($create_data[$attribute]);
        }

        $blog = $this->create($create_data);

        $this->uploadImages($data, $blog);

        Event::dispatch('admin.blogs.create.after', $blog);

        return true;
    }

    /**
     * Update item.
     *
     * @param  array  $data
     * @param  int  $id
     * @return bool
     */
    public function updateItem(array $data, $id)
    {
        Event::dispatch('admin.blogs.update.before', $id);

        $update_data = $data;

        if (array_key_exists('src', $update_data)) {
            unset($update_data['src']);
        }

        $blog = $this->update($update_data, $id);

        $this->uploadImages($data, $blog);

        Event::dispatch('admin.blogs.update.after', $blog);

        return true;
    }

    /**
     * Upload blog images.
     *
     * @param  array  $data
     * @param  \Webbycrown\BlogBagisto\Contracts\Blog  $blog
     * @param  string  $type
     * @return void
     */
    public function uploadImages($data, $blog, $type = 'src')
    {
        if (isset($data[$type])) {
            foreach ($data[$type] as $imageId => $image) {
                $file = $type . '.' . $imageId;

                if (request()->hasFile($file)) {
                    if ($blog->{$type}) {
                        Storage::delete($blog->{$type});
                    }

                    $encoded = app('image_manager')
                        ->read(request()->file($file))
                        ->toWebp();

                    $blog->{$type} = 'blog-images/' . $blog->id . '/' . Str::random(40) . '.webp';

                    Storage::put($blog->{$type}, $encoded->toString());

                    $blog->save();
                }
            }
        } else {
            if ($blog->{$type}) {
                Storage::delete($blog->{$type});
            }

            $blog->{$type} = null;

            $blog->save();
        }
    }

    /**
     * Delete a blog item and its image.
     *
     * @param  int  $id
     * @return bool
     */
    public function destroy($id)
    {
        $blogItem = $this->find($id);

        $blogItemImage = $blogItem->src;

        Storage::delete($blogItemImage);

        return $this->model->destroy($id);
    }

    /**
     * Get only active blogs.
     *
     * @return array
     */
    public function getActiveBlogs()
    {
        $locale = config('app.locale');

        $blogs = DB::table('blogs')
            ->join('blog_translations', function ($join) use ($locale) {
                $join->on('blogs.id', '=', 'blog_translations.blog_id')
                    ->where('blog_translations.locale', '=', $locale);
            })
            ->where('published_at', '<=', Carbon::now()->format('Y-m-d'))
            ->where('status', 1)
            ->orderBy('blogs.id', 'DESC')
            ->paginate(12);

        return $blogs;
    }

    /**
     * Get only single blogs.
     *
     * @return array
     */
    public function getSingleBlogs($id)
    {
        $blog = DB::table('blogs')
            ->join('blog_translations', 'blogs.id', '=', 'blog_translations.blog_id')
            ->where('blog_translations.slug', $id)
            ->where('published_at', '<=', Carbon::now()->format('Y-m-d'))
            ->where('status', 1)
            ->first();

        return $blog;
    }

    /**
     * Get blogs by category.
     *
     * @return array
     */
    public function getBlogCategories($id)
    {
        $locale = config('app.locale');

        $categoryId = DB::table('blog_categories')
            ->join('blog_category_translations', 'blog_categories.id', '=', 'blog_category_translations.blog_category_id')
            ->where('blog_category_translations.slug', $id)
            ->select('blog_categories.id')
            ->first();

        $blogs = DB::table('blogs')
            ->join('blog_translations', function ($join) use ($locale) {
                $join->on('blogs.id', '=', 'blog_translations.blog_id')
                    ->where('blog_translations.locale', '=', $locale);
            })
            ->where('published_at', '<=', Carbon::now()->format('Y-m-d'))
            ->where('default_category', $categoryId['id'])
            ->where('status', 1)
            ->orderBy('blogs.id', 'DESC')
            ->paginate(12);

        return $blogs;
    }
}
