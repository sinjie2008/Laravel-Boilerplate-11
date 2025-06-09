import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'Modules/Post/resources/css/app.css',
                'Modules/Post/resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
