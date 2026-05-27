<?php

namespace Webbycrown\BlogBagisto\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Webkul\Core\Eloquent\TranslatableModel;
use Webbycrown\BlogBagisto\Contracts\Tag as TagContract;

class Tag extends TranslatableModel implements TagContract
{
    use HasFactory;

    protected $table = 'blog_tags';

    protected $translationForeignKey = 'blog_tag_id';

    public $translatedAttributes = [
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $fillable = [
        'image',
        'status',
    ];

    protected $with = ['translations'];

    public function getImageUrlAttribute()
    {
        if (! $this->image) {
            return null;
        }

        return Storage::url($this->image);
    }
}