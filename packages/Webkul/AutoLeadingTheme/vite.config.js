import { defineConfig, loadEnv } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig(({ mode }) => {
    const envDir = '../../../';

    Object.assign(process.env, loadEnv(mode, envDir));

    return {
        build: {
            emptyOutDir: true,
            minify: 'esbuild',
            cssCodeSplit: true,
        },

        envDir,

        server: {
            host: process.env.VITE_HOST || 'localhost',
            port: process.env.VITE_PORT || 5173,
            cors: true,
        },

        define: {
            __VUE_OPTIONS_API__: true,
            __VUE_PROD_DEVTOOLS__: false,
            __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: false,
        },

        plugins: [
            vue(),

            laravel({
                hotFile: '../../../public/auto-leading-theme-vite.hot',
                publicDirectory: '../../../public',
                buildDirectory: 'themes/shop/auto-leading-theme/build',
                input: [
                    'src/Resources/assets/css/app.css',
                    'src/Resources/assets/js/app.js',
                ],
                refresh: true,
                preload: false,
            }),
        ],

        resolve: {
            alias: {
                'vue/dist/vue.esm-bundler': path.resolve(__dirname, 'node_modules/vue/dist/vue.esm-bundler.js'),
                'axios': path.resolve(__dirname, 'node_modules/axios'),
                'mitt': path.resolve(__dirname, 'node_modules/mitt'),
                'flatpickr': path.resolve(__dirname, 'node_modules/flatpickr'),
                'vee-validate': path.resolve(__dirname, 'node_modules/vee-validate'),
                '@vee-validate/i18n': path.resolve(__dirname, 'node_modules/@vee-validate/i18n'),
                '@vee-validate/rules': path.resolve(__dirname, 'node_modules/@vee-validate/rules'),
            },
        },

        experimental: {
            renderBuiltUrl(filename, { hostType }) {
                if (hostType === 'css') {
                    return path.basename(filename);
                }
            },
        },
    };
});