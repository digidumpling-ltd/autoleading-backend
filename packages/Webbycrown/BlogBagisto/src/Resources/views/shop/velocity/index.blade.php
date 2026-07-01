@php
    $channel = core()->getCurrentChannel();
    $show_sidebar = (int)$show_categories_count === 1 || (int)$show_tags_count === 1;
@endphp


{{-- SEO Meta Content --}}
@push ('meta')
    <meta name="title" content="{{ $blog_seo_meta_title ?? ( $channel->home_seo['meta_title'] ?? __('blog::app.shop.blog-title') ) }}" />

    <meta name="description" content="{{ $blog_seo_meta_description ?? ( $channel->home_seo['meta_description'] ?? '' ) }}" />

    <meta name="keywords" content="{{ $blog_seo_meta_keywords ?? ( $channel->home_seo['meta_keywords'] ?? '' ) }}" />
@endPush

<x-shop::layouts>
    {{-- Page Title --}}
    <x-slot:title>
        {{ $blog_seo_meta_title ?? ( $channel->home_seo['meta_title'] ?? __('blog::app.shop.blog-title') ) }}
    </x-slot>

    @push ('styles')

        @include ('blog::custom-css.custom-css')

    @endpush

    <div class="main">
        <div>
            <div class="row col-12 remove-padding-margin">
                <div id="home-right-bar-container" class="col-12 no-padding content">
                    <div class="container-right row no-margin col-12 no-padding">
                        <section class="blog-category-hero {{ ! $blog_index_banner_image ? 'blog-category-hero--no-image' : '' }}">
                            @if ($blog_index_banner_image)
                                <img
                                    src="{{ \Illuminate\Support\Facades\Storage::url($blog_index_banner_image) }}"
                                    alt="{{ $blog_index_banner_title ?? __('blog::app.shop.blog-title') }}"
                                    class="blog-category-hero__img">
                            @endif
                            <div class="blog-category-hero__overlay"></div>
                            <div class="blog-category-hero__content">
                                <h1 class="blog-category-hero__title">{{ !empty($blog_index_banner_title) ? $blog_index_banner_title : __('blog::app.shop.blog-title') }}</h1>
                                @if (!empty($blog_index_banner_description))
                                    <p class="blog-category-hero__desc">{{ $blog_index_banner_description }}</p>
                                @endif
                            </div>
                        </section>
                        <div id="blog" class="container mt-5">
                            <div class="full-content-wrapper">
                                <div class="flex flex-wrap grid-wrap">

                                    <div class="{{ $show_sidebar ? 'column-9' : 'column-12' }}">

                                        @if( !empty($blogs) &&  count($blogs) > 0 )

                                            <div class="flex flex-wrap blog-grid-list">

                                                @foreach($blogs as $blog)
                                                    <div class="blog-post-item">
                                                        <div class="blog-post-box">
                                                            <div class="card mb-5">
                                                                <div class="blog-grid-img"><img
                                                                    src="{{ $blog->src ? \Illuminate\Support\Facades\Storage::url($blog->src) : bagisto_asset('images/medium-product-placeholder.webp') }}"
                                                                    alt="{{ $blog->name }}"
                                                                    class="card-img-top">
                                                                </div>
                                                                <div class="card-body">
                                                                    <h2 class="card-title">
                                                                        <a href="{{ $blog->category?->slug ? route('shop.article.view', [$blog->category->slug . '/' . $blog->slug]) : route('shop.article.view.uncategorized', [$blog->slug]) }}">{{ $blog->name }}</a>
                                                                    </h2>
                                                                    <div class="post-meta">
                                                                        <p>
                                                                            {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $blog->created_at)->format('M j, Y') }} @lang('blog::app.shop.by')
                                                                            @if( (int)$show_author_page == 1 )
                                                                                <a href="{{route('shop.blog.author.index',[$blog->author_id])}}">{{ $blog->author }}</a>
                                                                            @else
                                                                                <a>{{ $blog->author }}</a>
                                                                            @endif
                                                                        </p>
                                                                    </div>

                                                                    @if( !empty($blog->assign_categorys) && count($blog->assign_categorys) > 0 )
                                                                        <div class="post-categories">
                                                                            <p>
                                                                                @foreach($blog->assign_categorys as $assign_category)
                                                                                    @if($assign_category->slug)
                                                                                        <a href="{{route('shop.blog.category.index',[$assign_category->slug])}}" class="cat-link">{{$assign_category->name}}</a>
                                                                                    @endif
                                                                                @endforeach
                                                                            </p>
                                                                        </div>
                                                                    @endif

                                                                    <div class="card-text text-justify">
                                                                        {!! $blog->short_description !!}
                                                                    </div>
                                                                </div>
                                                                <div class="card-footer">
                                                                    <a href="{{ $blog->category?->slug ? route('shop.article.view', [$blog->category->slug . '/' . $blog->slug]) : route('shop.article.view.uncategorized', [$blog->slug]) }}" class="primary-button">@lang('blog::app.shop.read-more')</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                <div class="w-full col-lg-12 mt-5 mb-5">
                                                    {!! $blogs->links() !!}
                                                </div>

                                            </div>

                                        @else

                                            <div class="post-not-available">@lang('blog::app.shop.no-posts')</div>

                                        @endif

                                    </div>

                                    @if ($show_sidebar)
                                    <div class="column-3 blog-sidebar">
                                        <div class="row">
                                            <div class="col-lg-12 mb-4 categories">
                                                @if($categories->isNotEmpty())
                                                    <h3>@lang('blog::app.shop.categories')</h3>
                                                    <ul class="list-group">
                                                        @foreach($categories as $category)
                                                            @if(!$category->slug) @continue @endif
                                                            <li><a href="{{route('shop.blog.category.index',[$category->slug])}}" class="list-group-item list-group-item-action">
                                                                    <span>{{ $category->name }}</span>
                                                                    @if( (int)$show_categories_count == 1 )
                                                                        <span class="badge badge-pill badge-primary">{{ $category->assign_blogs }}</span>
                                                                    @endif
                                                            </a></li>
                                                        @endforeach
                                                    </ul>
                                                @endif

                                                @if($tags->isNotEmpty())
                                                    <div class="tags-part">
                                                        <h3>@lang('blog::app.shop.tags')</h3>
                                                        <div class="tag-list">
                                                            @foreach($tags as $tag)
                                                                @if(!$tag->slug) @continue @endif
                                                                <a href="{{route('shop.blog.tag.index',[$tag->slug])}}" role="button" class="btn btn-primary btn-lg">{{ $tag->name }}
                                                                    @if( (int)$show_tags_count == 1 )
                                                                        <span class="badge badge-light">{{ $tag->count }}</span>
                                                                    @endif
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-shop::layouts>
