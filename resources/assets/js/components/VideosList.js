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
        'custom_id'
    ],

    components: {
        'video-card': require('./VideoCard')
    },

    beforeCompile: function () {
        var baseUri = '/api';
        var uri = {
            recommendations: {
                video: baseUri + '/recommendations/video'
            },
            content: {
                home:  baseUri +'/content/related'
            },
            user: {
                favorites: baseUri + '/user/favorites',
                watchLater: baseUri + '/user/watch-later',
                history: {
                    videos: baseUri + '/user/history/videos',
                    searches: baseUri + '/user/history/searches'
                }
            },
            search: baseUri + '/search'
        };

        switch (this.content) {
            /////////////
            // Content //
            /////////////
            case 'homeVideos':
                this.$http.get(uri.content.home, function (videos) {
                    this.$set('videos', videos.data);
                    this.initIsotope();
                });
                break;

            ////////////
            //  User  //
            ////////////

            case 'favorites':
                this.$http.get(uri.user.favorites, function (videos) {
                    this.$set('videos', videos.data);
                    this.initIsotope();
                });
                break;

            case 'watchLater':
                this.$http.get(uri.user.watchLater, function (videos) {
                    this.$set('videos', videos.data);
                    this.initIsotope();
                });
                break;

            case 'videosWatched':
                this.$http.get(uri.user.history.videos, function (videos) {
                    this.$set('videos', videos.data);
                    this.initIsotope();
                });
                break;

            ////////////
            // Search //
            ////////////
            case 'search':
                this.$http.get(uri.search, {
                    params: {
                        query: this.query
                    }
                }).then(function (searchResults) {
                    this.$set('videos', searchResults.data);
                    this.initIsotope();
                });
                break;

            ////////////////
            // Video page //
            ////////////////
            case 'recommended':
                this.$http.get(uri.recommendations.video, {
                    params: {
                        'custom_id': this.custom_id
                    }
                }).then(function (results) {
                    this.$set('videos', results);
                    this.initIsotope();
                });
                break;
        }
    },

    watch: {
        "videos": function () {
            jQuery('#preloader').fadeOut();
            jQuery('#filter-apis').removeClass('hide');
        }
    },

    methods: {
        initIsotope: function () {
            this.$nextTick(function () {
                var $grid = $('#videos').isotope({
                    itemSelector: '.video',
                    layoutMode: 'masonry'
                });

                $grid.imagesLoaded().progress(function () {
                    $grid.isotope('layout');
                });


                if (this.filter_apis == 'enabled') {
                    $('.video-source').on('click', function () {
                        var filterValue = $(this).data('filter');
                        $('.choosen-source').html('Source: ' + $(this).text());

                        $grid.isotope({
                            filter: filterValue
                        });
                    });
                };
            });
        }
    }
};
