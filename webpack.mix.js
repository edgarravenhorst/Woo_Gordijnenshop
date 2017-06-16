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

const BrowserSyncPlugin = require('browser-sync-webpack-plugin')
mix.webpackConfig({
  output: {
    path: __dirname
  },
  plugins: [
    new BrowserSyncPlugin({
      host: 'gordijnenshop.app',
      port: 3000,
      files: ['resources/js/*.js', 'resources/images/**']
    })
  ]
});

mix.js('resources/assets/js/site.js', 'assets/js')
.sass('resources/assets/sass/site.scss', 'assets/css')
.sass('resources/assets/sass/wp-editor-style.scss', 'assets/css');

mix.copy('resources/assets/images', 'assets/images');
mix.copy('node_modules/font-awesome/fonts', 'assets/fonts');
