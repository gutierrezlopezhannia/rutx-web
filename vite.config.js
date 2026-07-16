import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite'; // <-- 1. Importa esto

export default defineConfig({
    plugins: [
        tailwindcss(), // <-- 2. Agrégalo aquí arriba de laravel
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
