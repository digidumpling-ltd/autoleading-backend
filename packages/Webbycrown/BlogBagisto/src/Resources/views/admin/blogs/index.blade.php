<x-admin::layouts>
    <x-slot:title>
        {{ __('Blogs') }}
    </x-slot:title>

    <div class="flex items-center justify-between">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            {{ __('Blogs') }}
        </p>

        <div class="flex items-center gap-x-2.5">
            @if (bouncer()->hasPermission('blog.blogs.create'))
                <a
                    href="{{ route('admin.blog.create') }}"
                    class="primary-button"
                >
                    {{ __('Blogs') }}
                </a>
            @endif
        </div>
    </div>

    {!! view_render_event('bagisto.admin.blog.list.before') !!}

    <x-admin::datagrid src="{{ route('admin.blog.index') }}" />

    {!! view_render_event('bagisto.admin.blog.list.after') !!}

</x-admin::layouts>
