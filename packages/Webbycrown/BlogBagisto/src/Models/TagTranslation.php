<?php

namespace Webbycrown\BlogBagisto\Models;

use Illuminate\Database\Eloquent\Model;
use Webbycrown\BlogBagisto\Contracts\TagTranslation as TagTranslationContract;

class TagTranslation extends Model implements TagTranslationContract
{
    protected $table = 'blog_tag_translations';

    public $timestamps = false;

    protected $fillable = [
        'blog_tag_id',
        'locale',
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];
}
