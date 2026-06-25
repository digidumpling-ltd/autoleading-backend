<x-admin::layouts>
    <x-slot:title>
        {{ __('blog::app.setting.title') }}
    </x-slot:title>

    @pushOnce('styles')

        <style type="text/css">

            .w-50 {
                width: calc(50% - 4px);
            }
            @media (max-width: 767px) {
              .w-50 {
                width: 100%;
            }
            .flex-col-box {
                flex-direction: column;
            }
        }
        </style>

    @endPushOnce

    <!-- Blog Setting Form -->
    <x-admin::form
        :action="route('admin.blog.setting.store')"
        method="POST"
        enctype="multipart/form-data"
    >

        {!! view_render_event('admin.blogs.setting.before') !!}

        <div class="flex gap-4 justify-between items-center max-sm:flex-wrap">
            <p class="text-xl text-gray-800 dark:text-white font-bold">
                {{ __('blog::app.setting.title') }}
            </p>

            <div class="flex gap-x-2.5 items-center">

                <!-- Save Button -->
                <button
                    type="submit"
                    class="primary-button"
                >{{ __('blog::app.setting.save-btn') }}</button>
            </div>

        </div>

        <!-- Locale Switcher -->
        <div class="mt-7 flex items-center gap-x-1">
            <x-admin::dropdown
                position="bottom-{{ core()->getCurrentLocale()->direction === 'ltr' ? 'left' : 'right' }}"
                :class="core()->getAllLocales()->count() <= 1 ? 'hidden' : ''"
            >
                <x-slot:toggle>
                    <button
                        type="button"
                        class="transparent-button px-1 py-1.5 hover:bg-gray-200 focus:bg-gray-200 dark:text-white dark:hover:bg-gray-800 dark:focus:bg-gray-800"
                    >
                        <span class="icon-language text-2xl"></span>

                        <span v-pre>{{ $currentLocale->name }}</span>

                        <span class="icon-sort-down text-2xl"></span>
                    </button>
                </x-slot>

                <x-slot:content class="!p-0">
                    @foreach (core()->getAllLocales() as $locale)
                        <a
                            href="?{{ Arr::query(['locale' => $locale->code]) }}"
                            class="flex gap-2.5 px-5 py-2 text-base cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-950 dark:text-white {{ $locale->code == $currentLocale->code ? 'bg-gray-100 dark:bg-gray-950' : '' }}"
                            v-pre
                        >
                            {{ $locale->name }}
                        </a>
                    @endforeach
                </x-slot>
            </x-admin::dropdown>
        </div>

        <!-- Full Pannel -->
        <div class="flex gap-2.5 mt-3.5 max-xl:flex-wrap">

            <div class="flex flex-wrap flex-col-box gap-2 flex-1 max-xl:flex-auto">

                <!-- Post Setting Section -->
                <div class="p-4 w-50 bg-white dark:bg-gray-900 rounded-[4px] box-shadow">
                    <p class="mb-4 text-base text-gray-800 dark:text-white font-semibold">
                        {{ __('blog::app.setting.post-setting') }}
                    </p>

                    <div class="mt-8">

                        <!-- Post Per Page Records -->
                        <x-admin::form.control-group class="mb-2.5">
                            <x-admin::form.control-group.label class="">
                                {{ __('blog::app.setting.per-page') }}
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="number"
                                name="blog_post_per_page"
                                id="blog_post_per_page"
                                :value="old('blog_post_per_page') ?? $settings['blog_post_per_page']"
                                :label="__('blog::app.setting.per-page')"
                                :placeholder="__('blog::app.setting.per-page')"
                                min="1"
                            >
                            </x-admin::form.control-group.control>

                        </x-admin::form.control-group>

                        <!-- Post Maximum Related Posts Allowed -->
                        <x-admin::form.control-group class="mb-2.5">
                            <x-admin::form.control-group.label class="">
                                {{ __('blog::app.setting.max-related') }}
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="number"
                                name="blog_post_maximum_related"
                                id="blog_post_maximum_related"
                                :value="old('blog_post_maximum_related') ?? $settings['blog_post_maximum_related']"
                                :label="__('blog::app.setting.max-related')"
                                :placeholder="__('blog::app.setting.max-related')"
                                min="1"
                            >
                            </x-admin::form.control-group.control>

                        </x-admin::form.control-group>

                        <!-- Show Categories With Posts Count -->
                        <input type="hidden" name="blog_post_show_categories_with_count" id="blog_post_show_categories_with_count" value="@php echo $settings['blog_post_show_categories_with_count'] @endphp">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="text-gray-800 dark:text-white font-medium">
                                {{ __('blog::app.setting.show-categories-count') }}
                            </x-admin::form.control-group.label>

                            @php $blog_post_show_categories_with_count = old('blog_post_show_categories_with_count') ?: $settings['blog_post_show_categories_with_count'] @endphp

                            <x-admin::form.control-group.control
                                type="switch"
                                name="switch_blog_post_show_categories_with_count"
                                id="switch_blog_post_show_categories_with_count"
                                class="cursor-pointer"
                                value="1"
                                :label="__('blog::app.setting.show-categories-count')"
                                :checked="(boolean) $blog_post_show_categories_with_count"
                            >
                            </x-admin::form.control-group.control>
                        </x-admin::form.control-group>

                        <!-- Show Tags With Posts Count -->
                        <input type="hidden" name="blog_post_show_tags_with_count" id="blog_post_show_tags_with_count" value="@php echo $settings['blog_post_show_tags_with_count'] @endphp">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="text-gray-800 dark:text-white font-medium">
                                {{ __('blog::app.setting.show-tags-count') }}
                            </x-admin::form.control-group.label>

                            @php $blog_post_show_tags_with_count = old('blog_post_show_tags_with_count') ?: $settings['blog_post_show_tags_with_count'] @endphp

                            <x-admin::form.control-group.control
                                type="switch"
                                name="switch_blog_post_show_tags_with_count"
                                id="switch_blog_post_show_tags_with_count"
                                class="cursor-pointer"
                                value="1"
                                :label="__('blog::app.setting.show-tags-count')"
                                :checked="(boolean) $blog_post_show_tags_with_count"
                            >
                            </x-admin::form.control-group.control>
                        </x-admin::form.control-group>

                        <!-- Show Author Page -->
                        <input type="hidden" name="blog_post_show_author_page" id="blog_post_show_author_page" value="@php echo $settings['blog_post_show_author_page'] @endphp">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="text-gray-800 dark:text-white font-medium">
                                {{ __('blog::app.setting.show-author-page') }}
                            </x-admin::form.control-group.label>

                            @php $blog_post_show_author_page = old('blog_post_show_author_page') ?: $settings['blog_post_show_author_page'] @endphp

                            <x-admin::form.control-group.control
                                type="switch"
                                name="switch_blog_post_show_author_page"
                                id="switch_blog_post_show_author_page"
                                class="cursor-pointer"
                                value="1"
                                :label="__('blog::app.setting.show-author-page')"
                                :checked="(boolean) $blog_post_show_author_page"
                            >
                            </x-admin::form.control-group.control>
                        </x-admin::form.control-group>

                    </div>

                </div>

                <!-- Comment Setting Section -->
                <div class="p-4 w-50 bg-white dark:bg-gray-900 rounded-[4px] box-shadow">
                    <p class="mb-4 text-base text-gray-800 dark:text-white font-semibold">
                        {{ __('blog::app.setting.comment-setting') }}
                    </p>

                    <div class="mt-8">

                        <!-- Enable Post Comment -->
                        <input type="hidden" name="blog_post_enable_comment" id="blog_post_enable_comment" value="@php echo $settings['blog_post_enable_comment'] @endphp">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="text-gray-800 dark:text-white font-medium">
                                {{ __('blog::app.setting.enable-comment') }}
                            </x-admin::form.control-group.label>

                            @php $blog_post_enable_comment = old('blog_post_enable_comment') ?: $settings['blog_post_enable_comment'] @endphp

                            <x-admin::form.control-group.control
                                type="switch"
                                id="switch_blog_post_enable_comment"
                                name="switch_blog_post_enable_comment"
                                class="cursor-pointer"
                                value="1"
                                :label="__('blog::app.setting.enable-comment')"
                                :checked="(boolean) $blog_post_enable_comment"
                            >
                            </x-admin::form.control-group.control>
                        </x-admin::form.control-group>

                        <!-- Allow Guest Comment -->
                        <input type="hidden" name="blog_post_allow_guest_comment" id="blog_post_allow_guest_comment" value="@php echo $settings['blog_post_allow_guest_comment'] @endphp">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="text-gray-800 dark:text-white font-medium">
                                {{ __('blog::app.setting.allow-guest-comment') }}
                            </x-admin::form.control-group.label>

                            @php $blog_post_allow_guest_comment = old('blog_post_allow_guest_comment') ?: $settings['blog_post_allow_guest_comment'] @endphp

                            <x-admin::form.control-group.control
                                type="switch"
                                id="switch_blog_post_allow_guest_comment"
                                name="switch_blog_post_allow_guest_comment"
                                class="cursor-pointer"
                                value="1"
                                :label="__('blog::app.setting.allow-guest-comment')"
                                :checked="(boolean) $blog_post_allow_guest_comment"
                            >
                            </x-admin::form.control-group.control>
                        </x-admin::form.control-group>

                        <!-- Allowed maximum nested comment level -->
                        <x-admin::form.control-group class="mb-2.5">
                            <x-admin::form.control-group.label class="">
                                {{ __('blog::app.setting.max-nested-comment') }}
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="number"
                                name="blog_post_maximum_nested_comment"
                                id="blog_post_maximum_nested_comment"
                                :value="old('blog_post_maximum_nested_comment') ?? $settings['blog_post_maximum_nested_comment']"
                                :label="__('blog::app.setting.max-nested-comment')"
                                :placeholder="__('blog::app.setting.max-nested-comment')"
                                min="2"
                                max="4"
                            >
                            </x-admin::form.control-group.control>

                        </x-admin::form.control-group>

                    </div>

                </div>

                <!-- Default Blog SEO Setting Section -->
                <div class="p-4 w-50 bg-white dark:bg-gray-900 rounded-[4px] box-shadow">
                    <p class="mb-4 text-base text-gray-800 dark:text-white font-semibold">
                        {{ __('blog::app.setting.seo-setting') }}
                    </p>

                    <div class="mt-8">

                        <!-- SEO Preview -->
                        <x-admin::seo
                            slug="blog"
                            :meta-title-field="'blog_seo_meta_title_' . $currentLocale->code"
                            :url-key-field="''"
                            :meta-description-field="'blog_seo_meta_description_' . $currentLocale->code"
                        />

                        {{-- Meta Title --}}
                        <x-admin::form.control-group class="mb-2.5">
                            <x-admin::form.control-group.label>
                                {{ __('blog::app.setting.meta-title') }}
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                :name="'blog_seo_meta_title_' . $currentLocale->code"
                                :id="'blog_seo_meta_title_' . $currentLocale->code"
                                :value="old('blog_seo_meta_title_' . $currentLocale->code) ?? $settings['blog_seo_meta_title_' . $currentLocale->code]"
                                :label="__('blog::app.setting.meta-title')"
                                :placeholder="__('blog::app.setting.meta-title')"
                            >
                            </x-admin::form.control-group.control>
                        </x-admin::form.control-group>

                        {{-- Meta Keywords --}}
                        <x-admin::form.control-group class="mb-2.5">
                            <x-admin::form.control-group.label>
                                {{ __('blog::app.setting.meta-keywords') }}
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                :name="'blog_seo_meta_keywords_' . $currentLocale->code"
                                :id="'blog_seo_meta_keywords_' . $currentLocale->code"
                                :value="old('blog_seo_meta_keywords_' . $currentLocale->code) ?? $settings['blog_seo_meta_keywords_' . $currentLocale->code]"
                                :label="__('blog::app.setting.meta-keywords')"
                                :placeholder="__('blog::app.setting.meta-keywords')"
                            >
                            </x-admin::form.control-group.control>
                        </x-admin::form.control-group>

                        {{-- Meta Description --}}
                        <x-admin::form.control-group class="mb-2.5">
                            <x-admin::form.control-group.label>
                                {{ __('blog::app.setting.meta-description') }}
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="textarea"
                                :name="'blog_seo_meta_description_' . $currentLocale->code"
                                :id="'blog_seo_meta_description_' . $currentLocale->code"
                                :value="old('blog_seo_meta_description_' . $currentLocale->code) ?? $settings['blog_seo_meta_description_' . $currentLocale->code]"
                                :label="__('blog::app.setting.meta-description')"
                                :placeholder="__('blog::app.setting.meta-description')"
                            >
                            </x-admin::form.control-group.control>
                        </x-admin::form.control-group>

                    </div>

                </div>

                <v-wc-custom-js></v-wc-custom-js>

            </div>

        </div>

        {!! view_render_event('admin.blogs.setting.after') !!}

    </x-admin::form>

@pushOnce('scripts')
    <script type="text/x-template" id="v-wc-custom-js-template">

    </script>

    <script type="module">
        app.component('v-wc-custom-js', {
            template: '#v-wc-custom-js-template',

            data() {
                return {

                }
            },

            mounted() {
                let self = this;

                document.getElementById('switch_blog_post_show_categories_with_count').addEventListener('change', function(e) {
                    document.getElementById('blog_post_show_categories_with_count').value = ( e.target.checked == true || e.target.checked == 'true' ) ? 1 : 0;
                });

                document.getElementById('switch_blog_post_show_tags_with_count').addEventListener('change', function(e) {
                    document.getElementById('blog_post_show_tags_with_count').value = ( e.target.checked == true || e.target.checked == 'true' ) ? 1 : 0;
                });

                document.getElementById('switch_blog_post_show_author_page').addEventListener('change', function(e) {
                    document.getElementById('blog_post_show_author_page').value = ( e.target.checked == true || e.target.checked == 'true' ) ? 1 : 0;
                });

                document.getElementById('switch_blog_post_enable_comment').addEventListener('change', function(e) {
                    document.getElementById('blog_post_enable_comment').value = ( e.target.checked == true || e.target.checked == 'true' ) ? 1 : 0;
                });

                document.getElementById('switch_blog_post_allow_guest_comment').addEventListener('change', function(e) {
                    document.getElementById('blog_post_allow_guest_comment').value = ( e.target.checked == true || e.target.checked == 'true' ) ? 1 : 0;
                });

            },
        });
    </script>
@endPushOnce

</x-admin::layouts>
