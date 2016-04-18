'use strict';

var
    app,
    Vue = require('vue'),
    Resource = require('vue-resource'),
    linkify = require('linkifyjs'),
    linkifyStr = require('linkifyjs/string'),
    csrfToken = document.querySelector('meta[name=csrf-token]').getAttribute('content')
;

// require('linkifyjs/plugin/hashtag')(linkify); // optional

Vue.config.debug = true;

// Import vue-resource and configure to use the csrf token in all requests,
// in which I put him in a meta tag in home.blade.php
Vue.use(Resource);
Vue.http.headers.common['X-CSRF-TOKEN'] = csrfToken;

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
app = new Vue({
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
            jQuery('#nav-mobile').css({ overflow: 'auto'});
        }

        // Set checkbox on forms.html to indeterminate
        var indeterminateCheckbox = document.getElementById('indeterminate-checkbox');
        if (indeterminateCheckbox !== null) {
            indeterminateCheckbox.indeterminate = true;
        }
    }
});