import { defineConfig, loadEnv } from "vite";
import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";
import path from "path";

export default defineConfig(({ mode }) => {
    const envDir = "../../../";

    Object.assign(process.env, loadEnv(mode, envDir));

    return {
         build: {
            emptyOutDir: true,
            outDir: "src/public/build", 
            manifest: true,
            rollupOptions: {
                input: {
                    app: "src/Resources/assets/js/app.js",
                    style: "src/Resources/assets/css/app.css",
                },
            },
        },

        envDir,

        server: {
            host: process.env.VITE_HOST || "localhost",
            port: process.env.VITE_PORT || 5173,
        },

        plugins: [
            vue(),

            laravel({
                hotFile: "../../../public/shiprocket-default-vite.hot",
                publicDirectory: "../../../public",
                buildDirectory: "themes/shiprocket/default/build",
                input: [
                    "src/Resources/assets/css/app.css",
                    "src/Resources/assets/js/app.js",
                ],
                refresh: true,
            }),
        ],

        experimental: {
            renderBuiltUrl(filename, { hostId, hostType, type }) {
                if (hostType === "css") {
                    return path.basename(filename);
                }
            },
        },
    };
});
