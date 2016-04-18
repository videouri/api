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

    ready: function() {
        $('.video-action').click(function(event) {
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
        var title = encodeURIComponent(document.title),
            url = encodeURI(window.location.href);

        var facebookUrl = 'http://www.facebook.com/sharer.php?u=' + url + '&t=' + title,
            tuentiUrl = 'http://www.tuenti.com/?m=Share&func=index&url=' + url + '&suggested-text=',
            twitterUrl = 'https://twitter.com/intent/tweet?url=' + url + '&text=' + title + '&via=videouri';

        $('#facebook-share').attr('href', facebookUrl);
        $('#tuenti-share').attr('href', tuentiUrl);
        $('#twitter-share').attr('href', twitterUrl);

        $('.popup').click(function() {
            var width = 575,
                height = 400,
                left = ($(window).width() - width) / 2,
                top = ($(window).height() - height) / 2,
                url = this.href,
                title = $(this).attr('id'),
                opts = 'status=1' +
                ',width=' + width +
                ',height=' + height +
                ',top=' + top +
                ',left=' + left;

            window.open(url, title, opts);

            return false;
        });

        /////////////
        // VideoJS //
        /////////////
        var
            videoContainer = $('#video-player'),
            videoSource = videoContainer.data('src').toLowerCase(),
            videoUrl = videoContainer.data('url')
        ;

        videojs.options.flash.swf = "/misc/video-js.swf";

        videojs('video-player', {
            'techOrder': [videoSource],
            'src': videoUrl
        }).ready(function() {
            // Store the video object
            var myPlayer = this;

            // Make up an aspect ratio
            var aspectRatio = 264 / 640;

            function resizeVideoJS() {
                var
                    width = document.getElementById('video-player').parentElement.offsetWidth,
                    height = width * aspectRatio
                ;

                if (height > 530) {
                    height = 530;
                }

                if (! height < 530) {
                    myPlayer
                        .width(width)
                        .height(height)
                    ;
                }
            }

            // Initialize resizeVideoJS()
            resizeVideoJS();

            // Then on resize call resizeVideoJS()
            window.onresize = resizeVideoJS;

            // You can use the video.js events even though we use the vimeo controls
            // As you can see here, we change the background to red when the video is paused and set it back when unpaused
            // this.on('pause', function() {
            //     document.body.style.backgroundColor = 'red';
            // });

            // this.on('play', function() {
            //     document.body.style.backgroundColor = '';
            // });

            // You can also change the video when you want
            // Here we cue a second video once the first is done
            // this.one('ended', function() {
            //     this.src('http://vimeo.com/79380715');
            //     this.play();
            // });
        });

        ////////////
        // Disqus //
        ////////////
        // function initializeDisqus() {
        //     /*
        //     var disqus_config = function () {
        //     this.page.url = PAGE_URL; // Replace PAGE_URL with your page's canonical URL variable
        //     this.page.identifier = PAGE_IDENTIFIER; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
        //     };
        //     */
        //     (function() { // DON'T EDIT BELOW THIS LINE
        //         var d = document,
        //             s = d.createElement('script');
        //         s.src = '//videouri.disqus.com/embed.js';
        //
        //         s.setAttribute('data-timestamp', +new Date());
        //         (d.head || d.body).appendChild(s);
        //     })();
        // }

        // if (window.location.hostname !== 'local.videouri.com') {
        //     initializeDisqus();
        // }
    },

    methods: {
        toggleAction: function(action, originalId) {
            jQuery('#loading-bar').removeClass('hide');

            var
                endpoint = '/api/user',
                parameters = {
                    'original_id': originalId
                }
            ;

            switch (action) {
                case 'favorite':
                    endpoint = endpoint + '/favorite';
                    break;

                case 'watch_later':
                    endpoint = endpoint + '/watch-later';
                    break;
            }

            this.$http.post(endpoint, parameters, function(response) {
                if (response.errors !== false) {
                    Materialize.toast(response.errors.message, 3000, 'error');
                } else {
                    this.$set('video', response.data);
                }

                jQuery('#loading-bar').addClass('hide');
            });

            // Materialize.toast(message.success, 3000, 'success');
        },
        checkIfUserIsLogged: function() {
            if (typeof(this.user) === undefined) {
                Materialize.toast('Please log in with your account if you want to use this feature!', 3000, 'error');
                return false;
            }

            return true;
        }
    }
};
