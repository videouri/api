'use strict';

var Vue = require('vue');
var VueResource = require('vue-resource');
var linkifyStr = require('linkifyjs/string');

Vue.config.debug = true;

Vue.use(VueResource);
Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name=csrf-token]').getAttribute('content');

Vue.filter('linkify', function(text) {
    if (text) {
        return linkifyStr(text);
    }

    return 'Empty';
});

/**
 * Main APP
 * @return {Vue}
 */
var app = new Vue({
    el: '#app',

    components: {
        'videos-list': require('./components/VideosList'),
        'video-page': require('./components/VideoPage')
    },

    ready: function() {
        $('.button-collapse').sideNav();

        $('.custom-dropdown-button').dropdown({
            // constrain_width: true, // Constrains width of dropdown to the activator
            // hover: false,
            // gutter: 0, // Spacing from edge
            belowOrigin: true
        });

        // Detect touch screen and enable scrollbar if necessary
        function is_touch_device() {
            try {
                document.createEvent("TouchEvent");
                return true;
            } catch (e) {
                return false;
            }
        }

        if (is_touch_device()) {
            $('#nav-mobile').css({ overflow: 'auto'});
        }

        // Set checkbox on forms.html to indeterminate
        var indeterminateCheckbox = document.getElementById('indeterminate-checkbox');
        if (indeterminateCheckbox !== null) {
            indeterminateCheckbox.indeterminate = true;
        }
    }
});
