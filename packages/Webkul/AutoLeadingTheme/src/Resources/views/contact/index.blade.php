<x-shop::layouts :has-feature="false">
    <x-slot:title>
        {{ __('auto-leading-theme::app.contact.title') }}
    </x-slot>

    <div class="al-container">
        <div class="al-page-header">
            <h1 class="al-page-title">{{ __('auto-leading-theme::app.contact.heading') }}</h1>
        </div>

        <div class="al-contact-layout">
            <!-- Left Column: Business Information -->
            <div class="al-business-info">
                <h2 class="al-info-title">{{ __('auto-leading-theme::app.contact.business_info') }}</h2>

                <!-- Business Hours -->
                <div class="al-info-section">
                    <h3 class="al-info-heading">{{ __('auto-leading-theme::app.contact.business_hours') }}</h3>
                    <table class="al-hours-table">
                        <tr>
                            <td>{{ __('auto-leading-theme::app.contact.monday_friday') }}</td>
                            <td>9:00 AM - 6:00 PM</td>
                        </tr>
                        <tr>
                            <td>{{ __('auto-leading-theme::app.contact.saturday') }}</td>
                            <td>10:00 AM - 4:00 PM</td>
                        </tr>
                        <tr>
                            <td>{{ __('auto-leading-theme::app.contact.sunday') }}</td>
                            <td>{{ __('auto-leading-theme::app.contact.closed') }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Contact Methods -->
                <div class="al-info-section">
                    <h3 class="al-info-heading">{{ __('auto-leading-theme::app.contact.contact_methods') }}</h3>
                    <div class="al-contact-methods">
                        <p>
                            <strong>{{ __('auto-leading-theme::app.contact.phone') }}:</strong>
                            <a href="tel:+1234567890">+1 (234) 567-890</a>
                        </p>
                        <p>
                            <strong>{{ __('auto-leading-theme::app.contact.email') }}:</strong>
                            <a href="mailto:info@autoleading.com">info@autoleading.com</a>
                        </p>
                    </div>
                </div>

                <!-- Office Address -->
                <div class="al-info-section">
                    <h3 class="al-info-heading">{{ __('auto-leading-theme::app.contact.address') }}</h3>
                    <p class="al-address">
                        123 Automotive Drive<br>
                        Car City, CC 12345<br>
                        United States
                    </p>
                </div>

                <!-- Social Media -->
                <div class="al-info-section">
                    <h3 class="al-info-heading">{{ __('auto-leading-theme::app.contact.follow_us') }}</h3>
                    <div class="al-social-icons">
                        <a href="#" class="al-social-link" aria-label="Facebook">f</a>
                        <a href="#" class="al-social-link" aria-label="Instagram">📷</a>
                        <a href="#" class="al-social-link" aria-label="Twitter">𝕏</a>
                        <a href="#" class="al-social-link" aria-label="LinkedIn">in</a>
                    </div>
                </div>
            </div>

            <!-- Right Column: Contact Form -->
            <div class="al-contact-form-section">
                <form action="{{ route('shop.home.contact_us.send_mail') }}" method="POST" class="al-contact-form" id="contact-form">
                    @csrf

                    <h2 class="al-form-title">{{ __('auto-leading-theme::app.contact.form_title') }}</h2>

                    <!-- Name Field -->
                    <div class="al-form-group">
                        <label for="name" class="al-form-label">{{ __('auto-leading-theme::app.contact.name') }} <span class="al-required">*</span></label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="al-form-input"
                            value="{{ old('name') }}"
                            required
                            minlength="2"
                            placeholder="{{ __('auto-leading-theme::app.contact.name_placeholder') }}"
                        >
                        @error('name')
                            <span class="al-error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="al-form-group">
                        <label for="email" class="al-form-label">{{ __('auto-leading-theme::app.contact.email') }} <span class="al-required">*</span></label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="al-form-input"
                            value="{{ old('email') }}"
                            required
                            placeholder="{{ __('auto-leading-theme::app.contact.email_placeholder') }}"
                        >
                        @error('email')
                            <span class="al-error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Phone Field -->
                    <div class="al-form-group">
                        <label for="phone" class="al-form-label">{{ __('auto-leading-theme::app.contact.phone') }}</label>
                        <input
                            type="tel"
                            id="phone"
                            name="contact"
                            class="al-form-input"
                            value="{{ old('contact') }}"
                            placeholder="{{ __('auto-leading-theme::app.contact.phone_placeholder') }}"
                        >
                        @error('contact')
                            <span class="al-error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Message Field -->
                    <div class="al-form-group">
                        <label for="message" class="al-form-label">{{ __('auto-leading-theme::app.contact.message') }} <span class="al-required">*</span></label>
                        <textarea
                            id="message"
                            name="message"
                            class="al-form-textarea"
                            required
                            minlength="10"
                            rows="6"
                            placeholder="{{ __('auto-leading-theme::app.contact.message_placeholder') }}"
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <span class="al-error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="al-submit-btn">
                        {{ __('auto-leading-theme::app.contact.submit') }}
                    </button>

                    @if(session('success'))
                        <div class="al-success-message">
                            {{ session('success') }}
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .al-contact-form-section {
            display: flex;
            flex-direction: column;
        }

        .al-form-group {
            margin-bottom: 1.5rem;
        }

        .al-form-input,
        .al-form-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: 0.375rem;
            font-size: 1rem;
            font-family: inherit;
        }

        .al-submit-btn {
            background-color: var(--al-orange, #d18a1b);
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 0.375rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .al-submit-btn:hover {
            background-color: var(--al-orange-dark, #a96f14);
        }

        .al-error-text {
            color: #dc2626;
            font-size: 0.875rem;
            display: block;
            margin-top: 0.25rem;
        }

        .al-success-message {
            background-color: #dcfce7;
            color: #166534;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-top: 1rem;
        }
    </style>
    @endpush
</x-shop::layouts>
