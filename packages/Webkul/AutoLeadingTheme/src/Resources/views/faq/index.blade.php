<x-shop::layouts :has-feature="false">
    <x-slot:title>
        {{ __('auto-leading-theme::app.faq.title') }}
    </x-slot>

    <div class="al-container">
        <div class="al-page-header">
            <h1 class="al-page-title">{{ __('auto-leading-theme::app.faq.heading') }}</h1>
        </div>

        <div class="al-faq-section">
            <!-- Search Box -->
            <div class="al-faq-search">
                <input
                    type="text"
                    id="faq-search"
                    class="al-faq-search-input"
                    placeholder="{{ __('auto-leading-theme::app.faq.search_placeholder') }}"
                    aria-label="{{ __('auto-leading-theme::app.faq.search_label') }}"
                >
            </div>

            <!-- FAQ Accordion -->
            <div class="al-faq-accordion" id="faq-accordion">
                <!-- FAQ Item 1 -->
                <div class="al-faq-item">
                    <button class="al-faq-header" aria-expanded="false">
                        <span class="al-faq-question">{{ __('auto-leading-theme::app.faq.q1_question') }}</span>
                        <span class="al-faq-icon">+</span>
                    </button>
                    <div class="al-faq-content">
                        <p class="al-faq-answer">{{ __('auto-leading-theme::app.faq.q1_answer') }}</p>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="al-faq-item">
                    <button class="al-faq-header" aria-expanded="false">
                        <span class="al-faq-question">{{ __('auto-leading-theme::app.faq.q2_question') }}</span>
                        <span class="al-faq-icon">+</span>
                    </button>
                    <div class="al-faq-content">
                        <p class="al-faq-answer">{{ __('auto-leading-theme::app.faq.q2_answer') }}</p>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="al-faq-item">
                    <button class="al-faq-header" aria-expanded="false">
                        <span class="al-faq-question">{{ __('auto-leading-theme::app.faq.q3_question') }}</span>
                        <span class="al-faq-icon">+</span>
                    </button>
                    <div class="al-faq-content">
                        <p class="al-faq-answer">{{ __('auto-leading-theme::app.faq.q3_answer') }}</p>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="al-faq-item">
                    <button class="al-faq-header" aria-expanded="false">
                        <span class="al-faq-question">{{ __('auto-leading-theme::app.faq.q4_question') }}</span>
                        <span class="al-faq-icon">+</span>
                    </button>
                    <div class="al-faq-content">
                        <p class="al-faq-answer">{{ __('auto-leading-theme::app.faq.q4_answer') }}</p>
                    </div>
                </div>

                <!-- FAQ Item 5 -->
                <div class="al-faq-item">
                    <button class="al-faq-header" aria-expanded="false">
                        <span class="al-faq-question">{{ __('auto-leading-theme::app.faq.q5_question') }}</span>
                        <span class="al-faq-icon">+</span>
                    </button>
                    <div class="al-faq-content">
                        <p class="al-faq-answer">{{ __('auto-leading-theme::app.faq.q5_answer') }}</p>
                    </div>
                </div>
            </div>

            <!-- No Results Message -->
            <div id="faq-no-results" class="al-faq-no-results" style="display: none;">
                <p>{{ __('auto-leading-theme::app.faq.no_results') }}</p>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .al-faq-search {
            margin-bottom: 2rem;
            text-align: center;
        }

        .al-faq-search-input {
            width: 100%;
            max-width: 500px;
            padding: 0.75rem 1rem;
            border: 2px solid var(--al-orange, #d18a1b);
            border-radius: 0.375rem;
            font-size: 1rem;
        }

        .al-faq-item {
            border-bottom: 1px solid #ddd;
            margin-bottom: 0;
        }

        .al-faq-header {
            width: 100%;
            padding: 1.5rem 1rem;
            background: none;
            border: none;
            text-align: left;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.125rem;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .al-faq-header:hover {
            background-color: #f5f5f5;
        }

        .al-faq-icon {
            font-size: 1.5rem;
            transition: transform 0.3s ease;
        }

        .al-faq-header[aria-expanded="true"] .al-faq-icon {
            transform: rotate(45deg);
        }

        .al-faq-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding: 0 1rem;
        }

        .al-faq-header[aria-expanded="true"] ~ .al-faq-content {
            max-height: 500px;
            padding: 0 1rem 1.5rem 1rem;
        }

        .al-faq-answer {
            line-height: 1.6;
            color: #333;
        }

        .al-faq-no-results {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('faq-search');
            const faqItems = document.querySelectorAll('.al-faq-item');
            const noResults = document.getElementById('faq-no-results');

            // Accordion toggle
            document.querySelectorAll('.al-faq-header').forEach(header => {
                header.addEventListener('click', function () {
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';
                    this.setAttribute('aria-expanded', !isExpanded);
                });
            });

            // Search functionality
            searchInput.addEventListener('keyup', function () {
                const searchTerm = this.value.toLowerCase();
                let visibleCount = 0;

                faqItems.forEach(item => {
                    const question = item.querySelector('.al-faq-question').textContent.toLowerCase();
                    const answer = item.querySelector('.al-faq-answer').textContent.toLowerCase();

                    if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                        item.style.display = 'block';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                noResults.style.display = visibleCount === 0 ? 'block' : 'none';
            });
        });
    </script>
    @endpush
</x-shop::layouts>
