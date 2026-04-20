<x-shop::layouts :has-feature="false">
    <x-slot:title>
        {{ __('auto-leading-theme::app.blog.title') }}
    </x-slot>

    <div class="al-container">
        <div class="al-page-header">
            <h1 class="al-page-title">{{ __('auto-leading-theme::app.blog.heading') }}</h1>
        </div>

        <div class="al-blog-section">
            <div class="al-blog-grid">
                <!-- Blog Post 1 -->
                <article class="al-blog-card">
                    <div class="al-blog-thumbnail">
                        <div class="al-blog-placeholder" aria-hidden="true"></div>
                    </div>
                    <div class="al-blog-content">
                        <h2 class="al-blog-title">{{ __('auto-leading-theme::app.blog.post1_title') }}</h2>
                        <p class="al-blog-meta">{{ now()->format('F d, Y') }}</p>
                        <p class="al-blog-excerpt">{{ __('auto-leading-theme::app.blog.post1_excerpt') }}</p>
                        <a href="#" class="al-blog-link">{{ __('auto-leading-theme::app.blog.read_more') }}</a>
                    </div>
                </article>

                <!-- Blog Post 2 -->
                <article class="al-blog-card">
                    <div class="al-blog-thumbnail">
                        <div class="al-blog-placeholder" aria-hidden="true"></div>
                    </div>
                    <div class="al-blog-content">
                        <h2 class="al-blog-title">{{ __('auto-leading-theme::app.blog.post2_title') }}</h2>
                        <p class="al-blog-meta">{{ now()->subDay()->format('F d, Y') }}</p>
                        <p class="al-blog-excerpt">{{ __('auto-leading-theme::app.blog.post2_excerpt') }}</p>
                        <a href="#" class="al-blog-link">{{ __('auto-leading-theme::app.blog.read_more') }}</a>
                    </div>
                </article>

                <!-- Blog Post 3 -->
                <article class="al-blog-card">
                    <div class="al-blog-thumbnail">
                        <div class="al-blog-placeholder" aria-hidden="true"></div>
                    </div>
                    <div class="al-blog-content">
                        <h2 class="al-blog-title">{{ __('auto-leading-theme::app.blog.post3_title') }}</h2>
                        <p class="al-blog-meta">{{ now()->subDays(2)->format('F d, Y') }}</p>
                        <p class="al-blog-excerpt">{{ __('auto-leading-theme::app.blog.post3_excerpt') }}</p>
                        <a href="#" class="al-blog-link">{{ __('auto-leading-theme::app.blog.read_more') }}</a>
                    </div>
                </article>
            </div>

            <!-- Pagination -->
            <div class="al-pagination">
                <a href="#" class="al-pagination-link">{{ __('auto-leading-theme::app.common.previous') }}</a>
                <a href="#" class="al-pagination-link al-active">1</a>
                <a href="#" class="al-pagination-link">2</a>
                <a href="#" class="al-pagination-link">{{ __('auto-leading-theme::app.common.next') }}</a>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .al-blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .al-blog-card {
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .al-blog-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .al-blog-thumbnail {
            width: 100%;
            aspect-ratio: 4 / 3;
            overflow: hidden;
        }

        .al-blog-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #e8e0d0 0%, #c8b99a 100%);
        }

        .al-blog-content {
            padding: 1.5rem;
        }

        .al-blog-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        .al-blog-meta {
            font-size: 0.875rem;
            color: #666;
            margin-bottom: 1rem;
        }

        .al-blog-excerpt {
            color: #555;
            line-height: 1.6;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .al-blog-link {
            color: var(--al-orange, #d18a1b);
            font-weight: 600;
            text-decoration: none;
        }

        .al-blog-link:hover {
            text-decoration: underline;
        }
    </style>
    @endpush
</x-shop::layouts>
