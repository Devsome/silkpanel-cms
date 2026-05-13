import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { readdirSync, existsSync } from 'node:fs';
import { join } from 'node:path';

// Dynamically discover per-template CSS entry points.
// This runs at build/dev time and reads the actual filesystem, so it works
// even for templates that are gitignored (as long as they exist locally).
const templateRoot = 'resources/views/templates';
function findTemplateCssEntries() {
    try {
        return readdirSync(templateRoot, { withFileTypes: true })
            .filter((d) => d.isDirectory())
            .map((d) => join(templateRoot, d.name, 'assets', 'app.css'))
            .filter(existsSync);
    } catch {
        return [];
    }
}

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: [
                'resources/css/filament/admin/theme.css',
                'resources/css/app.css',
                'resources/js/app.js',
                ...findTemplateCssEntries(),
            ],
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
