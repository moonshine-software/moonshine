const mix = require('laravel-mix');

mix.js('src/assets/js/app.js', 'src/assets/js/compiled')
.postCss('src/assets/css/app.css', 'src/assets/css/compiled', [
    require('tailwindcss'),
])
    //.version()
    //.sourceMaps()
;
