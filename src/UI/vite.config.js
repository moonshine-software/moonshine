import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'
import moonShineBuildPlugin from "./resources/js/moonshine-build";
import tailwindcss from "@tailwindcss/vite";
import path from 'path';

export default defineConfig(({mode}) => {
  const env = loadEnv(mode, process.cwd())

  return {
    base: '/vendor/moonshine/',
    resolve: {
      alias: {
        '@moonshine-resources': path.resolve(__dirname, '/resources'),
      }
    },
    plugins: [
      moonShineBuildPlugin(),
      laravel({
        input: ['resources/css/main.css', 'resources/js/app.js'],
        refresh: true,
        publicDirectory: 'dist'
      }),
      tailwindcss(),
    ],
    server: {
      host: env.VITE_SERVER_HOST,
      hmr: {
        host: env.VITE_SERVER_HMR_HOST,
      },
      ...env.VITE_SERVER_CORS_ORIGIN ? {
        cors: {
          origin: env.VITE_SERVER_CORS_ORIGIN,
          credentials: true
        }
      } : {}
    },
    css: {
      devSourcemap: true,
      preprocessorOptions: {
        sass: {
          quietDeps: true,
        },
        scss: {
          quietDeps: true,
        },
      },
    },
    build: {
      emptyOutDir: false,
      outDir: 'dist',
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
    define: {
      'process.env.CHOICES_CAN_USE_DOM': true,
    },
  }
})
