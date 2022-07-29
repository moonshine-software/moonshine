import {defineConfig} from 'vite';

export default defineConfig({
    build: {
        emptyOutDir: false,
        manifest: true,
        rollupOptions: {
            input: ['resources/js/app.js', 'resources/css/app.css'],
            output: {
                entryFileNames: `js/moonshine.js`,
                assetFileNames: file => {
                    let ext = file.name.split('.').pop()
                    if (ext === 'css') {
                        return 'css/moonshine.css'
                    }
                    return 'assets/[name].[ext]'
                }
            }
        },
        outDir: 'public',
    },
});
