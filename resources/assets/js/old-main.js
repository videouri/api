

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
