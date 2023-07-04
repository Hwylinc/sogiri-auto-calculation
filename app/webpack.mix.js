const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');


/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
.sass('resources/sass/app.scss', 'public/css')
.options({
    processCssUrls: false,
    postCss: [ tailwindcss('./tailwind.config.js') ],
})
    // jqueryを使うための記述 - urano
.copy('node_modules/jquery/dist/jquery.min.js', 'public/js')
