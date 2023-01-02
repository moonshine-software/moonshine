import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve, dirname } from 'node:path'
import { fileURLToPath } from 'url'
import { vueI18n } from '@intlify/vite-plugin-vue-i18n'

// https://vitejs.dev/config/
export default defineConfig({
    base: './',

    build: {
        emptyOutDir: false,
        manifest: true,
        rollupOptions: {
            input: ['resources/js/main.ts'],
        },
        outDir: 'public',
        chunkSizeWarningLimit: 1400,
    },
    plugins: [
        vue(),
        vueI18n({
            // if you want to use Vue I18n Legacy API, you need to set `compositionOnly: false`
            // compositionOnly: false,

            // you need to set i18n resource including paths !
            include: resolve(
                dirname(fileURLToPath(import.meta.url)),
                './resources/js/locales/**'
            ),
        }),
    ],
    resolve: {
        alias: {
            //'~': fileURLToPath('node_modules'),
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
        },
    },
})
