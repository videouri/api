module.exports = {
    template: require('./VideoCard.template.html'),

    props: ['video'],

    replace: true,

    // data: function() {
    //     return {
    //         id: '',
    //         url: '',
    //         thumbnail: '',
    //         title: '',
    //         source: ''
    //     };
    // },

    methods: {
        saveForLater: function(stuff) {
            var parameters = {
                original_id: this.video.original_id
            };

            this.$http.post('/api/user/watch-later', parameters, function(homeVideos) {
                alert('success bitches')
            });
        }
    }
};
