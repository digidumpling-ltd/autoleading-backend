import.meta.glob(["../images/**", "../fonts/**"]);

import "../../../../../../Webkul/Shop/src/Resources/assets/js/app.js";

// Parallax: activate on any <img data-parallax> or <video data-parallax>
// The parent element must have overflow:hidden and a fixed height.
// Optional: data-parallax-speed="0.3" (0 = no movement, 1 = full scroll speed)
document.addEventListener('DOMContentLoaded', () => {
    const els = Array.from(document.querySelectorAll('[data-parallax]'));
    if (!els.length) return;

    let ticking = false;

    const update = () => {
        const viewH = window.innerHeight;

        els.forEach(el => {
            const rect = el.parentElement.getBoundingClientRect();
            if (rect.bottom < -viewH || rect.top > viewH * 2) return;

            const speed = parseFloat(el.dataset.parallaxSpeed ?? 0.3);
            const offset = (rect.top + rect.height / 2 - viewH / 2) * speed;
            el.style.transform = `translateY(${offset}px)`;
        });

        ticking = false;
    };

    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(update);
            ticking = true;
        }
    }, { passive: true });

    update();
});

// --- Intercept v-rental-slots registration to add max-days limit -------------
// The shop registers the component in an inline <script type="module"> block that
// runs after this Vite bundle. We wrap app.component() so the definition is
// patched before it is committed to the Vue app registry.

const _origAppComponent = app.component.bind(app);

app.component = function (name, definition) {
    if (name === 'v-rental-slots' && definition && typeof definition === 'object') {
        definition = _patchRentalSlots(definition);
    }
    return _origAppComponent(name, definition);
};

function _patchRentalSlots(def) {
    const computed = def.computed || {};
    const watch    = def.watch    || {};

    // Number of days allowed (0 = unlimited) — set globally by the blade view
    computed.maxRentalDays = function () {
        return parseInt(window.__rentalMaxDays ?? 0);
    };

    // Dynamic max date for the "To" picker
    computed.maxDateTo = function () {
        const limit = this.maxRentalDays;

        if (!limit || !this.booking?.date_from) {
            return this.maxDate;
        }

        const from = new Date(this.booking.date_from + 'T00:00:00');
        const cap  = new Date(from);
        cap.setDate(cap.getDate() + limit - 1); // inclusive: 7 days = day 1 … day 7

        const formatted = this.formatDate(cap);

        return (this.maxDate && formatted > this.maxDate) ? this.maxDate : formatted;
    };

    // Auto-clear date_to when date_from shifts it outside the allowed window
    watch['booking.date_from'] = function (newVal) {
        if (!newVal || !this.booking.date_to) return;

        const limit = this.maxRentalDays;
        if (!limit) return;

        const from = new Date(newVal + 'T00:00:00');
        const to   = new Date(this.booking.date_to + 'T00:00:00');
        const diff = Math.round((to - from) / (1000 * 60 * 60 * 24));

        if (diff >= limit) {
            this.booking.date_to = '';
        }
    };

    def.computed = computed;
    def.watch    = watch;

    return def;
}

// --- VeeValidate rule: guards manual keyboard input on the "To" date field ---
defineRule('within_max_days', (value, [target, maxDays]) => {
    if (!value || !target || !parseInt(maxDays)) return true;

    const diff = Math.round(
        (new Date(value + 'T00:00:00') - new Date(target + 'T00:00:00'))
        / (1000 * 60 * 60 * 24)
    );

    return diff < parseInt(maxDays)
        || 'The selected dates exceed the maximum allowed rental period.';
});
