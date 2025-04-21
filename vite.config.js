import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/slider.js",
                "resources/css/slider.css",
                "resources/js/registro.js",
                "resources/css/registro.css",
                "resources/js/login.js",
                "resources/css/compraEntradas.css",
                "resources/js/entradas.js",
                "resources/js/compraEntradas.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    base: "/",
    build: {
        outDir: "public/build",
        manifest: true,
    },
});
