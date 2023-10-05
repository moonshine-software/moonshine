import {defineConfig} from 'vite'
import laravel from 'laravel-vite-plugin'

/**
 * @todo pull css into a separate entry point
 */
export default defineConfig({
  plugins: [
    laravel({
      input: 'resources/js/app.js',
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
        entryFileNames: `js/moonshine.js`,
        assetFileNames: file => {
          let ext = file.name.split('.').pop()
          if (ext === 'css') {
            return 'css/moonshine.css'
          }

          return 'assets/[name].[ext]'
        },
      },
    },
  },
})
