module.exports = {
    template: require('./VideoPage.template.html'),

    props: ['video'],

    replace: true,

    data: function() {
        return {
            id: '',
            url: '',
            title: '',
            duration: '',
            views: '',
            description: '',
            tags: [],
            related: [],
        };
    },

    ready: function() {
        // var videoDetailsHeight = $('#video-details').height();

        // function videoDetailsReadMore() {

        // }

        $('#video-details').readmore({
            collapsedHeight: 150,
        });

        videojs.options.flash.swf = "/dist/misc/video-js.swf";

        var title = encodeURIComponent(document.title),
            url   = encodeURI(window.location.href);

        var facebookUrl = 'http://www.facebook.com/sharer.php?u='+url+'&t='+title,
            tuentiUrl   = 'http://www.tuenti.com/?m=Share&func=index&url='+url+'&suggested-text=',
            twitterUrl  = 'https://twitter.com/intent/tweet?url='+url+'&text='+title+'&via=videouri';

        $('#facebook-share').attr('href', facebookUrl);
        $('#tuenti-share').attr('href', tuentiUrl);
        $('#twitter-share').attr('href', twitterUrl);

        $('.popup').click(function(event) {
            var width  = 575,
                height = 400,
                left   = ($(window).width()  - width)  / 2,
                top    = ($(window).height() - height) / 2,
                url    = this.href,
                title  = $(this).attr('id'),
                opts   = 'status=1' +
                       ',width='  + width  +
                       ',height=' + height +
                       ',top='    + top    +
                       ',left='   + left;

            window.open(url, title, opts);

            return false;
        });

        var
            videoContainer = $('#videoPlayer'),
            videoSource = videoContainer.data('src'),
            videoUrl = videoContainer.data('url')
        ;

        videojs('videoPlayer', {"techOrder": [videoSource], "src": videoUrl}).ready(function() {

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
        function initializeDisqus() {
            /*
            var disqus_config = function () {
            this.page.url = PAGE_URL; // Replace PAGE_URL with your page's canonical URL variable
            this.page.identifier = PAGE_IDENTIFIER; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
            };
            */
            (function() { // DON'T EDIT BELOW THIS LINE
                var d = document, s = d.createElement('script');
                s.src = '//videouri.disqus.com/embed.js';

                s.setAttribute('data-timestamp', +new Date());
                (d.head || d.body).appendChild(s);
            })();
        }

        if (window.location.hostname !== 'local.videouri.com') {
            initializeDisqus();
        }
    }
};
