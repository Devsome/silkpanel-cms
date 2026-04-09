import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: ['resources/css/filament/admin/theme.css', 'resources/css/app.css', 'resources/js/app.js'],
            refresh: ['resources/views/**', 'packages/**', 'vendor/silkpanel/**'],
        }),
    ],
    server: {
        host: '0.0.0.0',
        hmr: {
            host: 'silkpanel-cms.ddev.site',
            protocol: 'wss',
        },
        cors: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
