import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js','resources/js/slider.js',
                'resources/css/slider.css','resources/js/registro.js', 'resources/css/registro.css'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        outDir: 'public/build', // Asegúrate de que los archivos se generen en la carpeta correcta
    }
});
