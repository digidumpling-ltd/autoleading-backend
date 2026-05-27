<?php

namespace Webbycrown\BlogBagisto\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Webkul\Core\Eloquent\TranslatableModel;
use Webkul\Core\Models\ChannelProxy;
use Webbycrown\BlogBagisto\Contracts\Blog as BlogContract;
use Webbycrown\BlogBagisto\Models\Category;

class Blog extends TranslatableModel implements BlogContract
{
    use HasFactory;

    protected $table = 'blogs';

    protected $translationForeignKey = 'blog_id';

    public $translatedAttributes = [
        'name',
        'slug',
        'short_description',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $fillable = [
        'channels',
        'default_category',
        'author',
        'author_id',
        'categorys',
        'tags',
        'src',
        'status',
        'allow_comments',
        'published_at',
    ];

    protected $with = ['translations'];

    /**
     * Appends.
     *
     * @var array
     */
    protected $appends = ['src_url', 'assign_categorys'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'default_category');
    }

    /**
     * Get the channels.
     */
    public function channels()
    {
        return $this->belongsToMany(ChannelProxy::modelClass(), 'channels');
    }

    /**
     * Get image url for the blog image.
     *
     * @return string|null
     */
    public function getSrcUrlAttribute()
    {
        if (! $this->src) {
            return null;
        }

        return Storage::url($this->src);
    }

    public function getAssignCategorysAttribute()
    {
        $categorys = [];
        $categories_ids = array_values(array_unique(array_merge(explode(',', $this->default_category), explode(',', $this->categorys))));
        if (is_array($categories_ids) && ! empty($categories_ids) && count($categories_ids) > 0) {
            $categories = Category::whereIn('id', $categories_ids)->get();
            $categorys = (! empty($categories) && count($categories) > 0) ? $categories : [];
        }

        return $categorys;
    }
}
