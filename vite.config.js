import {defineConfig} from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/main.css', 'resources/js/app.js'],
      refresh: true,
    }),
  ],
  server: {
    host: 'localhost',
  },
  build: {
    emptyOutDir: false,
    outDir: 'public',
    rollupOptions: {
      // Currently, fonts and images – external resources
      external: [/\.woff2/, /\.svg/],
      output: {
        entryFileNames: `assets/[name].js`,
        assetFileNames: 'assets/[name].css',
      },
    },
  },
})
