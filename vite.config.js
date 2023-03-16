import {defineConfig} from 'vite';
export default defineConfig({
    build: {
        emptyOutDir: false,
        manifest: true,
        rollupOptions: {
            input: ['resources/js/app.js'],
            output: {
                entryFileNames: `js/moonshine.js`,
                assetFileNames: file => {
                    let ext = file.name.split('.').pop()
                    if (ext === 'css') {
                        return 'css/moonshine.css'
                    }

                    if (ext === 'woff2') {
                        return 'fonts/[name].[ext]'
                    }

                    return 'assets/[name].[ext]'
                }
            }
        },
        outDir: 'public',
    },
});
