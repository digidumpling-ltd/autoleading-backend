<?php

namespace Themes\CustomTheme\Providers;

use Illuminate\Support\ServiceProvider;

class CustomThemeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'custom-theme');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'custom-theme');

        $this->mergeConfigFrom(__DIR__.'/../Config/system.php', 'core');
    }
}
