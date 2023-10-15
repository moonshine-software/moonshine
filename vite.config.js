import {defineConfig} from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  base: '/vendor/moonshine/',
  plugins: [
    laravel({
      input: ['resources/css/main.css', 'resources/js/app.js'],
      refresh: true,
    }),
  ],
  server: {
    host: 'localhost',
  },
  css: {
    devSourcemap: true,
  },
  build: {
    emptyOutDir: false,
    outDir: 'public',
    rollupOptions: {
      output: {
        entryFileNames: `assets/[name].js`,
        assetFileNames: chunk => {
          if (chunk.name.endsWith('.woff2')) {
            return 'fonts/[name].[ext]'
          }

          return 'assets/[name].css'
        },
      },
    },
  },
})
