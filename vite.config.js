import {defineConfig, loadEnv} from 'vite'
import laravel from 'laravel-vite-plugin'
import moonShineBuildPlugin from "./resources/js/moonshine-build";

export default defineConfig(({mode}) => {
  const env = loadEnv(mode, process.cwd())

  return {
    base: '/vendor/moonshine/',
    plugins: [
      moonShineBuildPlugin(),
      laravel({
        input: ['resources/css/main.css', 'resources/css/minimalistic.css', 'resources/js/app.js'],
        refresh: true,
      }),
    ],
    server: {
      host: env.VITE_SERVER_HOST,
      hmr: {
        host: env.VITE_SERVER_HMR_HOST,
      },
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
  }
})
