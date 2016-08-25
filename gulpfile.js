var elixir = require('laravel-elixir');

elixir.config.js.browserify.watchify = {
    enabled: true,
    options: {
        poll: true
    }
};

elixir(
    function (mix) {
        mix
            .sass('app.scss', 'public/dist/css/app.css')

            .scripts([
                // 'node_modules/vue/dist/vue.js',
                // 'node_modules/vue-resource/dist/vue-resource.js',

                'node_modules/jquery/dist/jquery.js',
                'node_modules/materialize-css/dist/js/materialize.js',

                'node_modules/imagesloaded/imagesloaded.pkgd.js',
                'node_modules/isotope-layout/dist/isotope.pkgd.js',

                'node_modules/video.js/dist/video.js',
                'node_modules/videojs-youtube/dist/Youtube.js',
                'node_modules/videojs-vimeo/src/Vimeo.js',
                // 'node_modules/videojs-dailymotion/es5/dailymotion.js',
                // 'resources/assets/js/vendor/videojs-dailymotion.js',

                'node_modules/readmore-js/readmore.js',

                'resources/assets/js/vendor/jquery.cookie.js',
                'resources/assets/js/vendor/jquery.query.js'
            ], 'public/dist/js/vendors.js', './')

            .browserify('app.js', 'public/dist/js/app.js')

            .browserSync({
                proxy: 'local.videouri.com'
            })

            .version([
                'public/dist/css/app.css',
                'public/dist/js/vendors.js',
                'public/dist/js/app.js'
            ])

            .copy([
                'node_modules/font-awesome/fonts',
                'node_modules/materialize-css/dist/fonts/roboto',
                'node_modules/video.js/dist/font',
                'resources/assets/font'
            ], 'public/dist/font')
        ;

        // TESTING!
        // mix.phpUnit();
    }
);
