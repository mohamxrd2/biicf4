import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
            preload: false,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                assetFileNames: "assets/[name].[hash][extname]", // Versionnement des fichiers
            },
        },
    },
});
