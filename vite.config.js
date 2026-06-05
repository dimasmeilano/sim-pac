
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    // 🟢 TAMBAHKAN BLOK CSS INI:
    css: {
        preprocessorOptions: {
            scss: {
                silenceDeprecations: [
                    'import', 
                    'global-builtin', 
                    'color-functions', 
                    'if-function',
                    'legacy-js-api'
                ],
            },
        },
    },
});