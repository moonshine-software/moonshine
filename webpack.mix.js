const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'js/moonshine.js')
    .postCss('resources/css/app.css', 'css/moonshine.css', [
        require('tailwindcss'),
    ])
    .setPublicPath('public')
    //.version()
    //.sourceMaps()
;
