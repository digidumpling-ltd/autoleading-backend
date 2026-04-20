<?php

namespace Webkul\AutoLeadingTheme\Http\Controllers;

use Illuminate\View\View;

class FaqController
{
    /**
     * FAQ page
     */
    public function index(): View
    {
        return view('auto-leading-theme::faq.index');
    }
}
