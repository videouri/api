module.exports = {
    template: require('./VideoCard.template.html'),

    props: ['video'],

    replace: true,

    data: function() {
        return {
            id: '',
            url: '',
            thumbnail: '',
            title: '',
            source: ''
        };
    }
};
