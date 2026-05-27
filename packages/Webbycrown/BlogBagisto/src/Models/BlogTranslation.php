<?php

namespace Webbycrown\BlogBagisto\Models;

use Illuminate\Database\Eloquent\Model;
use Webbycrown\BlogBagisto\Contracts\BlogTranslation as BlogTranslationContract;

class BlogTranslation extends Model implements BlogTranslationContract
{
    protected $table = 'blog_translations';

    public $timestamps = false;

    protected $fillable = [
        'blog_id',
        'locale',
        'name',
        'slug',
        'short_description',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];
}
