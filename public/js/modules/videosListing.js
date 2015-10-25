var $isotopeContainer,
    page, curPage, nextPage;

$(document).ready(function() {
    /**
     * Isotope plugin
     */
    // $isotopeContainer = $('#video-list');
    $isotopeContainer = $('#videos').isotope({
        columnWidth: '.col-md-4',
        itemSelector: '.col-md-4',
        layoutMode: 'masonry',
        gutter: 20
    });

    $(window).on('load', function(){
        $('#videos').isotope('layout');
    });

    // filter items on button click
    $('.video-source').on('click', function() {
        var filterValue = $(this).data('filter');
        $('.choosen-source').html('Source: '  + $(this).text());
        $isotopeContainer.isotope({ filter: filterValue });
    });

    $('.pagination .previous').click(function () {
        curPage = $.query.get('page');
        console.log(curPage + 'previous');

        if (curPage.length === 0) {
            return false;
        } else {
            nextPage = curPage - 1;
        }

        page = $.query.set('page', nextPage).toString();
        window.location.replace(page);
    });

    $('.pagination .next').click(function () {
        curPage = $.query.get('page');
        console.log(curPage + 'next');

        if (curPage.length === 0) {
            nextPage = curPage + 2;
        }
        else {
            nextPage = curPage + 1;
        }

        page = $.query.set('page', nextPage).toString();
        window.location.replace(page);
    });

});
