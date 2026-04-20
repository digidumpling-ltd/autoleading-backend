import { defineConfig, loadEnv } from 'vite';
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

        plugins: [
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

        experimental: {
            renderBuiltUrl(filename, { hostType }) {
                if (hostType === 'css') {
                    return path.basename(filename);
                }
            },
        },
    };
});