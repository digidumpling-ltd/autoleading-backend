<?php

namespace Webkul\AutoLeadingTheme\Http\Controllers;

use Illuminate\View\View;

class BlogController
{
    /**
     * Blog list page
     */
    public function index(): View
    {
        return view('auto-leading-theme::blog.index');
    }

    /**
     * Blog detail page
     */
    public function show(string $slug): View
    {
        return view('auto-leading-theme::blog.show', [
            'slug' => $slug,
        ]);
    }
}
