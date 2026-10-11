import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // CSS Global & JS
                'resources/css/app.css',
                'resources/js/app.js',

                // Auth
                'resources/css/auth.css',

                // CSS Spesifik Admin
                'resources/css/admin/dashboard.css',
                'resources/css/admin/users.css',
                'resources/css/admin/units.css',
                'resources/css/admin/categories.css',
                'resources/css/admin/profile.css',
                'resources/css/admin/items.css',
                'resources/css/admin/transactions.css',
                'resources/css/admin/restock-requests.css',
                'resources/css/admin/activity-log.css',

                // CSS Spesifik Barista
                'resources/css/barista/transactions.css',
                'resources/css/barista/restock-requests.css',
                'resources/css/barista/dashboard.css',
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
