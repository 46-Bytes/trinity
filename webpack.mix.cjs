const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');

// Copy TinyMCE to public directory
mix.copyDirectory('vendor/tinymce/tinymce', 'public/js/tinymce');
