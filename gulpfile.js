var
    elixir       = require('laravel-elixir'),
    autoprefixer = require('gulp-autoprefixer')
;

require('laravel-elixir-livereload');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(
    function(mix) {
        mix.less('app.less');

        mix
            .scripts([
                // '../../bower_components/vue/dist/vue.js',
                // '../../bower_components/vue-resource/dist/vue-resource.js',
                '../../bower_components/jquery/dist/jquery.js',
                '../../bower_components/Materialize/dist/js/materialize.js',

                'js/vendor/jquery.placeholder.js',
                'js/vendor/jquery.cookie.js',
                'js/vendor/jquery.query.js',

                '../../bower_components/imagesloaded/imagesloaded.pkgd.js',
                '../../bower_components/isotope/dist/isotope.pkgd.js',

                '../../bower_components/video.js/dist/video-js/video.js',
                '../../bower_components/videojs-youtube/src/youtube.js',
                '../../bower_components/videojs-vimeo/src/media.vimeo.js',
                '../../bower_components/videojs-dailymotion/src/dailymotion.js',

                '../../bower_components/Readmore.js/readmore.js',
            ], 'public/js/vendor.js', 'resources/assets/')

            .browserify('app.js')
        ;

        mix.livereload();

        mix
            .copy([
                './bower_components/font-awesome/fonts/**',
                './bower_components/Materialize/dist/font/**',
                './bower_components/video.js/dist/video-js/font/**',
                './resources/assets/font/**'
            ], 'public/font')
        ;

        // TESTING!
        // mix.phpUnit();
    }
);
