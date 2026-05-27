<?php

namespace Webbycrown\BlogBagisto\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webbycrown\BlogBagisto\Models\Blog::class,
        \Webbycrown\BlogBagisto\Models\BlogTranslation::class,
        \Webbycrown\BlogBagisto\Models\Category::class,
        \Webbycrown\BlogBagisto\Models\CategoryTranslation::class,
        \Webbycrown\BlogBagisto\Models\Tag::class,
        \Webbycrown\BlogBagisto\Models\TagTranslation::class,
        \Webbycrown\BlogBagisto\Models\Comment::class,
    ];
}