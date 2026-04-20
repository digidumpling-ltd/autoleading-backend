<x-shop::layouts :has-feature="false">
    <x-slot:title>
        {{ __('auto-leading-theme::app.blog.post_detail_title') }}
    </x-slot>

    <div class="al-container">
        <!-- Back Link -->
        <div class="al-back-link">
            <a href="{{ route('shop.cms.page', ['slug' => 'blog']) }}">← {{ __('auto-leading-theme::app.blog.back_to_blog') }}</a>
        </div>

        <!-- Blog Post Detail -->
        <article class="al-blog-detail">
            <header class="al-post-header">
                <h1 class="al-post-title">{{ __('auto-leading-theme::app.blog.post_detail') }}</h1>
                <div class="al-post-meta">
                    <span class="al-post-date">{{ now()->format('F d, Y') }}</span>
                    <span class="al-post-author">{{ __('auto-leading-theme::app.blog.by_author', ['author' => 'AutoLeading Team']) }}</span>
                </div>
            </header>

            <!-- Featured Image -->
            <figure class="al-post-featured">
                <div class="al-blog-placeholder al-featured-placeholder" aria-hidden="true"></div>
            </figure>

            <!-- Post Content -->
            <div class="al-post-body">
                <p>{{ __('auto-leading-theme::app.blog.post_content_intro') }}</p>
                <p>{{ __('auto-leading-theme::app.blog.post_content_body') }}</p>
                <h2>{{ __('auto-leading-theme::app.blog.section_heading') }}</h2>
                <p>{{ __('auto-leading-theme::app.blog.section_content') }}</p>
                <p>{{ __('auto-leading-theme::app.blog.post_conclusion') }}</p>
            </div>

            <!-- Related Posts -->
            <aside class="al-related-posts">
                <h2 class="al-related-title">{{ __('auto-leading-theme::app.blog.related_posts') }}</h2>
                <div class="al-related-cards">
                    <!-- Related Post 1 -->
                    <div class="al-related-card">
                        <div class="al-blog-placeholder" aria-hidden="true" style="height:150px;"></div>
                        <h3 class="al-related-heading">{{ __('auto-leading-theme::app.blog.related_post1') }}</h3>
                        <a href="#" class="al-blog-link">{{ __('auto-leading-theme::app.blog.read_more') }}</a>
                    </div>

                    <!-- Related Post 2 -->
                    <div class="al-related-card">
                        <div class="al-blog-placeholder" aria-hidden="true" style="height:150px;"></div>
                        <h3 class="al-related-heading">{{ __('auto-leading-theme::app.blog.related_post2') }}</h3>
                        <a href="#" class="al-blog-link">{{ __('auto-leading-theme::app.blog.read_more') }}</a>
                    </div>

                    <!-- Related Post 3 -->
                    <div class="al-related-card">
                        <div class="al-blog-placeholder" aria-hidden="true" style="height:150px;"></div>
                        <h3 class="al-related-heading">{{ __('auto-leading-theme::app.blog.related_post3') }}</h3>
                        <a href="#" class="al-blog-link">{{ __('auto-leading-theme::app.blog.read_more') }}</a>
                    </div>
                </div>
            </aside>
        </article>
    </div>

    @push('styles')
    <style>
        .al-back-link {
            margin-bottom: 2rem;
        }

        .al-back-link a {
            color: var(--al-orange, #d18a1b);
            text-decoration: none;
            font-weight: 600;
        }

        .al-back-link a:hover {
            text-decoration: underline;
        }

        .al-blog-detail {
            max-width: 800px;
            margin: 0 auto;
        }

        .al-post-header {
            margin-bottom: 2rem;
        }

        .al-post-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .al-post-meta {
            display: flex;
            gap: 1rem;
            color: #666;
            font-size: 0.875rem;
        }

        .al-post-featured {
            width: 100%;
            margin-bottom: 2rem;
        }

        .al-blog-placeholder {
            width: 100%;
            background: linear-gradient(135deg, #e8e0d0 0%, #c8b99a 100%);
        }

        .al-featured-placeholder {
            height: 400px;
            border-radius: 0.5rem;
        }

        .al-post-body {
            line-height: 1.8;
            color: #333;
            margin-bottom: 3rem;
        }

        .al-post-body p {
            margin-bottom: 1rem;
        }

        .al-post-body h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 2rem 0 1rem 0;
            color: var(--al-orange, #d18a1b);
        }

        .al-related-posts {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #ddd;
        }

        .al-related-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .al-related-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .al-related-card {
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .al-related-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .al-related-card > * {
            padding: 0 1rem;
        }

        .al-related-heading {
            font-size: 1rem;
            font-weight: 600;
            margin: 1rem 0 0.5rem 0;
        }

        .al-related-card .al-blog-link {
            display: inline-block;
            margin-bottom: 1rem;
        }
    </style>
    @endpush
</x-shop::layouts>
