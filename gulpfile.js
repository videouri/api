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
                '../../bower_components/jquery/dist/jquery.js',
                'js/vendor/jquery-ui-1.10.3.custom.min.js',
                'js/vendor/jquery.ui.touch-punch.min.js',

                'js/vendor/jquery.placeholder.js',
                'js/vendor/jquery.cookie.js',
                'js/vendor/jquery.query.js',

                '../../bower_components/bootstrap/dist/js/bootstrap.js',
                '../../bower_components/imagesloaded/imagesloaded.pkgd.js',
                '../../bower_components/isotope/dist/isotope.pkgd.js',

                '../../bower_components/video.js/dist/video-js/video.js',
                '../../bower_components/videojs-youtube/dist/vjs.youtube.js',
                '../../bower_components/videojs-vimeo/vjs.vimeo.js',
                '../../bower_components/videojs-dailymotion/src/dailymotion.js',
                // 'js/vendor/video.js-dailymotion/vjs.dailymotion.js',
                'js/main.js'
            ], 'public/js/app.js', 'resources/assets/')

            .copy([
                './resources/assets/js/modules/**'
            ], 'public/js/modules/')
        ;

        mix.livereload();

        mix
            .copy([
                './bower_components/font-awesome/fonts/**',
                './bower_components/video.js/dist/video-js/font/**',
                './resources/assets/fonts/**'
            ], 'public/fonts')
        ;

        // TESTING!
        // mix.phpUnit();
    }
);
