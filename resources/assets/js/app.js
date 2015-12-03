'use strict';

var Vue = require('vue');
Vue.config.debug = true;

Vue.use(require('vue-resource'));

new Vue({
    el: '#app',

    components: {
        'videos-list': require('./components/VideosList')
    },

    ready: function() {
        // Floating-Fixed table of contents
        if (jQuery('nav').length) {
            jQuery('.toc-wrapper').pushpin({
                top: jQuery('nav').height()
            });
        } else if (jQuery('#index-banner').length) {
            jQuery('.toc-wrapper').pushpin({
                top: jQuery('#index-banner').height()
            });
        } else {
            jQuery('.toc-wrapper').pushpin({
                top: 0
            });
        }

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

// (function($) {

//     'use strict';

//     $('.dropdown-button').dropdown();

//     $('.family-filter').click(function () {
//         if ($.cookie('ff') === null) {
//             $.cookie('ff', 'off', { expires: 30, path: '/' });
//         }
//         else {
//             $.cookie('ff', null,{ path: '/' });
//         }
//     });

//     if ($.cookie('source-list') === null) {
//         $('a.button[data-source=all]').addClass('selected-title');
//     }

//     if ($.cookie('period-list') === null) {
//         $('a.button[data-period=ever]').addClass('selected-title');
//     }

//     // $('#sources-list li').click(function () {
//     //     var method = $('.tabNavigation').find('.selected2').data('method');
//     //     var source = $(this).find('a').data('source');
//     //     var period = $('#periods-list .selected-title').data('period');
//     //     console.log(method + ' ' + period + ' ' + source);
//     //     //$.post('')
//     // });

//     // $('#periods-list li').click(function () {
//     //     var method = $('.tabNavigation').find('.selected2').data('method');
//     //     var source = $('#sources-list .selected-title').data('source');
//     //     var period = $(this).find('a').data('period');
//     //     console.log(method + ' ' + period + ' ' + source);
//     //     //$.post('')
//     // });

//     // $('.categories').filter(function () {
//     //     if ($(this).children("li").length > 1) {
//     //         $(this).each(function (){
//     //             $('li:eq(0)', this).addClass('first-category');
//     //             $('li:gt(0)', this).wrapAll('<ul class="submenu" />');
//     //         });

//     //         $('.submenu').hide();

//     //         $(this).hover(function (){
//     //             $(this).find('.submenu').toggle();
//     //         });
//     //     }
//     // });

// }(jQuery));
