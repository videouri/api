'use strict';

var
    app,
    Vue = require('vue'),
    Resource = require('vue-resource'),
    linkify = require('linkifyjs'),
    linkifyStr = require('linkifyjs/string')
;

// require('linkifyjs/plugin/hashtag')(linkify); // optional

Vue.config.debug = false;

// Import vue-resource and configure to use the csrf token in all requests,
// in which I put him in a meta tag in home.blade.php
Vue.use(Resource);
Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#_token').getAttribute('value');


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
        // Floating-Fixed table of contents
        // if (jQuery('nav').length) {
        //     jQuery('.toc-wrapper').pushpin({
        //         top: jQuery('nav').height()
        //     });
        // } else if (jQuery('#index-banner').length) {
        //     jQuery('.toc-wrapper').pushpin({
        //         top: jQuery('#index-banner').height()
        //     });
        // } else {
        //     jQuery('.toc-wrapper').pushpin({
        //         top: 0
        //     });
        // }

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

// /**
//  * ROUTER
//  * @type {Router}
//  */
// var router = new VueRouter();

// // Define some routes.
// // Each route should map to a component. The "component" can
// // either be an actual component constructor created via
// // Vue.extend(), or just a component options object.
// // We'll talk about nested routes later.
// router.map({
//     '/foo': {
//         component: Foo
//     },
//     '/bar': {
//         component: Bar
//     }
// })

// // Now we can start the app!
// // The router will create an instance of App and mount to
// // the element matching the selector #app.
// router.start(App, '#app')

// // ['all', 'active', 'completed'].forEach(function (visibility) {
// //     router.on(visibility, function () {
// //         app.visibility = visibility;
// //     });
// // });

// // router.configure({
// //     notfound: function () {
// //         window.location.hash = '';
// //         app.visibility = 'all';
// //     }
// // });

// // router.init();
