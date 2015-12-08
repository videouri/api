module.exports = {
    template: require('./VideoPage.template.html'),

    props: ['data'],

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
    }
};
