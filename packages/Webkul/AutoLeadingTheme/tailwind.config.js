/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './src/Resources/**/*.blade.php',
        './src/Resources/**/*.js',
        '../../../resources/themes/auto-leading-theme/**/*.blade.php',
    ],

    theme: {
        container: {
            center: true,
            screens: {
                '2xl': '1240px',
            },
            padding: {
                DEFAULT: '1rem',
            },
        },

        extend: {},
    },

    plugins: [],
};