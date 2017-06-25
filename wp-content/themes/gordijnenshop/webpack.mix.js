let mix = require('laravel-mix');

mix.options({
  extractVueStyles: false,
  processCssUrls: false,
  uglify: {},
  purifyCss: false,
  postCss: [require('autoprefixer')],
  clearConsole: true,
});

mix.copy('node_modules/font-awesome/fonts', 'assets/fonts')
.browserSync({
  proxy: 'gordijnenshop.app',
  files: [
    "./**/*.php",
    "./assets/**/*"
  ]
})
.sass('resources/sass/site.scss', 'assets/css')
.sass('resources/sass/wp-editor-style.scss', 'assets/css')
.sass('resources/sass/includes.scss', 'assets/css')

.js('resources/js/site.js', 'assets/js')
