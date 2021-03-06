require('dotenv').config()
const mix = require('laravel-mix')
require('laravel-mix-alias')

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

mix.webpackConfig({
    devtool: 'eval-source-map'
    // devtool: "inline-source-map",
    // devtool: 'source-map'
})

mix.alias({
    '@': '/resources/js',
    '~': '/node_modules/'
    // '@components': '/resources/assets/js/components',
});

mix.js('resources/js/app.js', 'public/js').version()

mix.sass('resources/sass/app.scss', 'public/css')
mix.sass('resources/sass/font.scss', 'public/css')
