/**
 * Track theme images and fonts for publishing.
 */
import.meta.glob([
    '../images/**',
    '../fonts/**',
]);

/**
 * Reuse the core Shop bootstrap so all expected plugins/directives/components
 * are available to blade-injected Vue code.
 */
import app from '../../../../../Shop/src/Resources/assets/js/app';

export default app;