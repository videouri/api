var
    elixir       = require('laravel-elixir'),
    autoprefixer = require('gulp-autoprefixer')
;

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
        // Front-end assets
        mix
            .less('app.less')

            .scripts([
                "../../bower_components/jquery/dist/jquery.js",
                "scripts/vendor/jquery-ui-1.10.3.custom.min.js",
                "scripts/vendor/jquery.ui.touch-punch.min.js",

                "scripts/vendor/jquery.placeholder.js",
                "scripts/vendor/jquery.cookie.js",
                "scripts/vendor/jquery.query.js",

                "../../bower_components/bootstrap/dist/js/bootstrap.js",
                "../../bower_components/jquery.lazyload/jquery.lazyload.js",
                "../../bower_components/isotope/dist/isotope.pkgd.js",

                "../../bower_components/video.js/dist/video-js/video.js",
                "../../bower_components/videojs-youtube/dist/vjs.youtube.js",
                "../../bower_components/videojs-vimeo/vjs.vimeo.js",
                "../../bower_components/videojs-dailymotion/src/dailymotion.js",
                // "scripts/vendor/video.js-dailymotion/vjs.dailymotion.js",
                "scripts/main.js"
            ], 'public/js/app.js', 'resources/assets/')

            .copy([
                './resources/assets/scripts/modules/**'
            ], 'public/js/modules/')

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
