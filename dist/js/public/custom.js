(function ($) {



    // Navigation scrolls
    $('.navbar-nav li a').bind('click', function (event) {
        $('.navbar-nav li').removeClass('active');
        $(this).closest('li').addClass('active');
        var $anchor = $(this);
        var nav = $($anchor.attr('href'));
        if (nav.length) {
            $('html, body').stop().animate({
                scrollTop: $($anchor.attr('href')).offset().top
            }, 1500, 'easeInOutExpo');

            event.preventDefault();
        }
    });

    // About section scroll
    $(".overlay-detail a").on('click', function (event) {
        event.preventDefault();
        var hash = this.hash;
        $('html, body').animate({
            scrollTop: $(hash).offset().top
        }, 900, function () {
            window.location.hash = hash;
        });
    });

    
    $('.navbar-collapse').on('show.bs.collapse', function () {
        $(".navbar-fixed-top").addClass("top-nav-collapse-shadow");
    })
    $('.navbar-collapse').on('hide.bs.collapse', function () {
        $(".navbar-fixed-top").removeClass("top-nav-collapse-shadow");
    })

})(jQuery);