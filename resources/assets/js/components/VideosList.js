'use strict';

module.exports = {
    template: require('./VideosList.template.html'),

    props: ['content'],

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
        // console.log('created', this)
        // this.$set('videos', 'plm');
    },

    beforeCompile: function(e) {
        switch (this.content) {
            case 'homeVideos':
                this.$http.get('/api/videos/home', function(homeVideos) {
                    this.$set('videos', homeVideos);
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
        var $isotopeContainer;

        $isotopeContainer = jQuery('#videos').isotope({
            columnWidth: '.video',
            itemSelector: '.video',
            layoutMode: 'masonry',
            gutter: 20
        });


        jQuery(window).on('resize', function(){
            jQuery('#videos').isotope('layout');
        });
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
