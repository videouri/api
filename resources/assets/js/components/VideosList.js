'use strict';

module.exports = {
    template: require('./VideosList.template.html'),

    props: ['content', 'query'],

    data: function() {
        return {
            videos: {}
        };
    },

    components: {
        'video-card': require('./VideoCard')
    },

    created: function(e) {
        // switch (this.content) {
        //     case 'homeVideos':
        //         jQuery.getJSON('/api/videos/home', function(homeVideos) {
        //             // console.log(this.videos);
        //             this.videos.$set('videos', homeVideos);
        //             // console.log(this.setVideos);
        //             // this.setVideos(homeVideos);
        //             // console.log(this.videos);
        //         });
        //         break;
        // }
        // console.log('created,' this)
        // this.$set('videos', 'plm');
    },

    beforeCompile: function(e) {
        switch (this.content) {
            case 'homeVideos':
                this.$http.get('/api/videos/home', function(homeVideos) {
                    this.$set('videos', homeVideos.data);
                });

                break;
            case 'search':
                var parameters = {
                    'query': this.query
                };

                this.$http.get('/api/search/videos', parameters, function(searchResults) {
                    this.$set('videos', searchResults.data);
                });

                break;
        }
    },

    // watch: {
    //     videos: function (e) {
    //         // console.log('new: %s, old: %s', val, oldVal)
    //         console.log(e)
    //     }
    // },

    ready: function(e) {
        // this.$nextTick(function () {
        //     // DOM is now updated
        //     // `this` is bound to the current instance
        //     // this.doSomethingElse()
        //     console.log('nextTick');
        // });

        var $grid = jQuery('#videos').isotope({
            // columnWidth: '.video',
            itemSelector: '.video',
            layoutMode: 'fitRows',
            // layoutMode: 'masonry',
            // disable initial layout
            isInitLayout: false,
            gutter: 200
        });

        // bind event
        $grid.isotope( 'on', 'arrangeComplete', function() {
            console.log('arrange is complete');
        });

        $grid.isotope();

        $grid.on('layoutComplete', function(event, laidOutItems) {
            console.log( 'Isotope layout completed on ' +
                        laidOutItems.length + ' items' );
        });

        console.log($grid);

        // jQuery(window).on('resize', function(){
        //     jQuery('#videos').isotope('layout');
        // });
    },

    // methods: {
    //     setVideos: function(videos) {
    //         console.log(videos);
    //     },
    //     homeVideos: function(videos) {
    //         console.log(videos);
    //     }
    // }
};
