@if (
    request()->routeIs('admin.*')
    || request()->is(config('app.admin_url'))
    || request()->is(config('app.admin_url') . '/*')
    || Request::segment(1) == config('app.admin_url')
)
    @bagistoVite(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'], 'reward')
@else
    @bagistoVite(['src/Resources/assets/css/shop.css', 'src/Resources/assets/js/app.js'], 'reward')
@endif