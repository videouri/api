'use strict';

module.exports = {
    template: require('./VideoPage.template.html'),

    props: [
        'video',
        'user'
    ],

    components: {
        'videos-list': require('./VideosList')
    },

    ready: function () {
        $('.video-action').click(function (event) {
            event.preventDefault();
            event.stopPropagation();
        });

        $('#video-details').readmore({
            collapsedHeight: 150,
            moreLink: '<a href="#" class="btn" style="margin: 10px 0 20px;">Read More</a>',
            lessLink: '<a href="#" class="btn" style="margin: 10px 0 20px;">Close</a>'
        });

        ///////////////
        // Socialize //
        ///////////////
        var title = encodeURIComponent(document.title);
        var currentPage = encodeURI(window.location.href);

        var facebookUrl = 'http://www.facebook.com/sharer.php?u=' + currentPage + '&t=' + title;
        var tuentiUrl = 'http://www.tuenti.com/?m=Share&func=index&url=' + currentPage + '&suggested-text=';
        var twitterUrl = 'https://twitter.com/intent/tweet?url=' + currentPage + '&text=' + title + '&via=videouri';

        $('#facebook-share').attr('href', facebookUrl);
        $('#tuenti-share').attr('href', tuentiUrl);
        $('#twitter-share').attr('href', twitterUrl);

        $('.popup').click(function () {
            var width = 575;
            var height = 400;
            var left = ($(window).width() - width) / 2;
            var top = ($(window).height() - height) / 2;
            var socialUrl = this.href;
            var title = $(this).attr('id');
            var opts = 'status=1,width=' + width + ',height=' + height + ',top=' + top + ',left=' + left;

            window.open(socialUrl, title, opts);

            return false;
        });

        /////////////
        // VideoJS //
        /////////////
        var container = $('#video-player');
        var source = container.data('src').toLowerCase();
        var url = container.data('url');

        videojs.options.flash.swf = "/misc/video-js.swf";

        videojs('video-player', {
            'techOrder': [source],
            'sources': [{
                'type': 'video/' + source,
                'src': url
            }]
        }).ready(function () {
            var player = this;
            var aspectRatio = 264 / 640;

            function resizeVideoJS() {
                var width = document.getElementById('video-player').parentElement.offsetWidth;
                var height = width * aspectRatio;

                if (height > 530) {
                    height = 530;
                }

                if (!height < 530) {
                    player
                        .width(width)
                        .height(height)
                    ;
                }
            }

            resizeVideoJS();

            // Re-adjust aspect ration on window resize
            window.onresize = resizeVideoJS;
        });
    },

    methods: {
        toggleAction: function (action, originalId) {
            jQuery('#loading-bar').removeClass('hide');

            var endpoint = '/api/user';
            var parameters = {
                'original_id': originalId
            };

            switch (action) {
                case 'favorite':
                    endpoint = endpoint + '/favorite';
                    break;

                case 'watch_later':
                    endpoint = endpoint + '/watch-later';
                    break;
            }

            this.$http.post(endpoint, parameters, function (response) {
                if (response.errors !== false) {
                    Materialize.toast(response.errors.message, 3000, 'error');
                } else {
                    this.$set('video', response.data);
                }

                jQuery('#loading-bar').addClass('hide');
            });
        },
        checkIfUserIsLogged: function () {
            if (typeof(this.user) === undefined) {
                Materialize.toast('Please log in with your account if you want to use this feature!', 3000, 'error');
                return false;
            }

            return true;
        }
    }
};
