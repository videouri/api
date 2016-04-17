'use strict';

module.exports = {
    template: require('./VideosList.template.html'),

    props: [
        'filter_apis',
        'content',
        'query',
        'videos',

        ////////////////
        // Video page //
        ////////////////
        'api',
        'original_id',
    ],

    // data: function() {
    //     return {
    //         videos: {}
    //     };
    // },

    components: {
        'video-card': require('./VideoCard')
    },

    beforeCompile: function(e) {
        switch (this.content) {
            ////////////
            // Videos //
            ////////////
            case 'homeVideos':
                this.$http.get('/api/videos/home', function(videos) {
                    this.$set('videos', videos.data);
                    this.initIsotope();
                });
                break;

            case 'favorites':
                this.$http.get('/api/videos/favorites', function(videos) {
                    this.$set('videos', videos.data);
                    this.initIsotope();
                });
                break;

            case 'watchLater':
                this.$http.get('/api/videos/watch-later', function(videos) {
                    this.$set('videos', videos.data);
                    this.initIsotope();
                });
                break;

            /////////////
            // History //
            /////////////
            case 'videosWatched':
                this.$http.get('/api/history/videos', function(videos) {
                    this.$set('videos', videos.data);
                    this.initIsotope();
                });
                break;

            ////////////
            // Search //
            ////////////
            case 'search':
                var parameters = {
                    'query': this.query
                };

                this.$http.get('/api/videos/search', parameters, function(searchResults) {
                    this.$set('videos', searchResults.data);
                    this.initIsotope();
                });
                break;

            ////////////////
            // Video page //
            ////////////////
            case 'recommended':
                var parameters = {
                    'api': this.api,
                    'original_id': this.original_id
                };

                this.$http.get('/api/videos/recommended', parameters, function(results) {
                    this.$set('videos', results);
                    this.initIsotope();
                });
                break;
        }
    },

    watch: {
        "videos": function(oldVal, newVal) {
            jQuery('#preloader').fadeOut();
            jQuery('#filter-apis').removeClass('hide');
        }
    },

    methods: {
        initIsotope: function() {
            this.$nextTick(function () {
                var $grid = jQuery('#videos').isotope({
                    // columnWidth: '.video',
                    itemSelector: '.video',
                    layoutMode: 'masonry',
                    // isInitLayout: true,
                    // gutter: 200
                });

                $grid.imagesLoaded().progress( function() {
                    $grid.isotope('layout');
                });


                if (this.filter_apis == 'enabled') {
                    $('.video-source').on('click', function() {
                        var filterValue = $(this).data('filter');
                        $('.choosen-source').html('Source: '  + $(this).text());

                        $grid.isotope({
                            filter: filterValue
                        });
                    });
                }

                // // bind event
                // $grid.isotope( 'on', 'arrangeComplete', function(ev, ve) {
                //     console.log(ev);
                //     console.log(ve);
                //     console.log('arrange is complete');
                // });

                // $grid.isotope();

                // $grid.on('layoutComplete', function(event, laidOutItems) {
                //     console.log(laidOutItems);
                //     console.log( 'Isotope layout completed on ' +
                //                 laidOutItems.length + ' items' );
                // });
            });
        }
    }
};
