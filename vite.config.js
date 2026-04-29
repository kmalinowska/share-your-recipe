import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: true,
        port: 5173,      // Jawne określenie portu
        origin: 'http://192.168.0.3:5173',
        strictPort: true,
        cors:true,
        hmr: {
            host: '192.168.0.3',
        },

        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
