require('dotenv').config()
const mix = require('laravel-mix')
const path = require('path')
// require('laravel-mix-alias')

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

mix.webpackConfig({
    devtool: 'eval-source-map',
    // devtool: "inline-source-map",
    // devtool: 'source-map'
    // resolve: {
    //     // extensions: ['.js', '.json', '.vue'],
    //     alias: {
    //         '@': path.resolve(__dirname, '/resources/js'),
    //         '~': path.resolve(__dirname, '/node_modules')
    //     }
    // },
    resolve: {
        extensions: [".ts", ".tsx", ".js", ".jsx", ".vue", "*"],
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            '~': path.resolve(__dirname, 'node_modules')
            // 'vue$': 'vue/dist/vue.esm.js',
            // '@': path.resolve(__dirname, 'resources/js/'),
            // '~': path.resolve(__dirname, 'resources/sass/')
        },
    },
})
    // .sourceMaps();

// mix.alias({
//     '@': '/resources/js',
//     '~': '/node_modules'
//     // '@components': '/resources/assets/js/components',
// });

mix.js('resources/js/app.js', 'public/js').vue().version()

mix.sass('resources/sass/app.scss', 'public/css')
mix.sass('resources/sass/font.scss', 'public/css')
