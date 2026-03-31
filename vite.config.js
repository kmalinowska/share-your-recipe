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
        host: '0.0.0.0', // Wymusza nasłuchiwanie na wszystkich IP
        port: 5173,      // Jawne określenie portu
        origin: 'http://192.168.1.143:5173',
        strictPort: true,

        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
