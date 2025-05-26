import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import vue from '@vitejs/plugin-vue2';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/css/slider.css",
                "resources/js/registro.js",
                "resources/css/registro.css",
                "resources/js/login.js",
                "resources/css/compraEntradas.css",
                "resources/js/entradas.js",
                "resources/css/user_modal.css",
                "resources/css/adminLogin.css",
                "resources/js/cartaCosmos.js",
                "resources/css/cartaCosmos.css",
                "resources/css/adminLogin.css",
                "resources/js/user.js",
                "resources/js/loginAdmin.js",
                "resources/css/dashboard.css",
                "resources/js/adminDashboardGestionarPelicula.js",
                "resources/js/adminDashboardGestionarMenu.js",
                "resources/js/adminDashboardSesiones.js",
                "resources/js/adminDashboardAñadirEmpleado.js",
                "resources/css/cartelera.css",
                'resources/css/pago.css',
                'resources/css/detalle_pelicula.css',
                "resources/js/detalle_y_asientos.js",
                'resources/css/swiper-custom.css',
                'resources/js/slider-init.js',
            ],
            refresh: true,
        }),
        vue(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            'vue': 'vue/dist/vue.esm.js',
        },
    },
    base: "/",
    build: {
        outDir: "public/build",
        manifest: true,
    },
});