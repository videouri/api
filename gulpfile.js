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
                "bower/jquery/dist/jquery.js",
                "scripts/vendor/jquery-ui-1.10.3.custom.min.js",
                "scripts/vendor/jquery.ui.touch-punch.min.js",

                "scripts/vendor/jquery.placeholder.js",
                "scripts/vendor/jquery.cookie.js",
                "scripts/vendor/jquery.query.js",

                "bower/bootstrap/dist/js/bootstrap.js",
                "bower/jquery.lazyload/jquery.lazyload.js",
                "bower/isotope/dist/isotope.pkgd.js",

                "bower/video.js/dist/video-js/video.js",
                "bower/videojs-youtube/dist/vjs.youtube.js",
                "bower/videojs-vimeo/vjs.vimeo.js",
                "bower/videojs-dailymotion/src/dailymotion.js",
                // "scripts/vendor/video.js-dailymotion/vjs.dailymotion.js",
                "scripts/main.js"
            ], 'public/js/app.js', 'resources/assets/')

            .copy([
                './resources/assets/bower/font-awesome/fonts/**',
                './resources/assets/fonts/**'
            ], 'public/fonts')
        ;

        // TESTING!
        mix.phpUnit();
    }
);
