const { mix } = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css');

mix.combine([
    'resources/assets/css/auth/css.css',
    'resources/assets/css/auth/icons.css',
    'resources/assets/css/auth/main.css',
    'resources/assets/css/auth/voyager.css',
], 'public/css/auth.css');

mix.combine([
    'resource/assets/css/hrms/theme.css',
], 'public/css/hrms.css');
