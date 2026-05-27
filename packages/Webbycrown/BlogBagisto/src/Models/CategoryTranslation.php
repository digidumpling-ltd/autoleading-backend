<?php

namespace Webbycrown\BlogBagisto\Models;

use Illuminate\Database\Eloquent\Model;
use Webbycrown\BlogBagisto\Contracts\CategoryTranslation as CategoryTranslationContract;

class CategoryTranslation extends Model implements CategoryTranslationContract
{
    protected $table = 'blog_category_translations';

    public $timestamps = false;

    protected $fillable = [
        'blog_category_id',
        'locale',
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];
}
