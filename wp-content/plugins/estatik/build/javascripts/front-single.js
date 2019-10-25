(function($) {

    function initGoogleSingleMap() {
        var map = document.getElementById('es-google-map');

        var data = $( map ).data();

        if (map && data.lat && data.lon && typeof(EsGoogleMap) != 'undefined' ) {
            var instance = new EsGoogleMap(map, data.lon, data.lat).init();
            instance.setMarker();
        }
    }

    $(function () {
        var $singleSlickGallery = $('.es-gallery-image');
        var $singleSlickGalleryPager = $('.es-gallery-image-pager');
        var hash = document.location.hash.substring(1);

        var $nav = $('.es-single-tabs');

        if ( $nav.length ) {
            var navPos = parseInt($nav.offset().top);
            var navPosLeft = parseInt($nav.offset().left);
            var navWidth = parseInt($nav.width());

            $(window).scroll(function (e) {
                if($(this).scrollTop() >= navPos){
                    $nav.addClass('es-fixed');
                    $nav.css({'left':navPosLeft+'px','width':navWidth+'px'});
                } else {
                    $nav.removeClass('es-fixed');
                    $nav.css({'left':'0px','width':'auto'});
                }

            });
        }

        jQuery('.es-gallery-image').magnificPopup({
            delegate: 'a:not(.slick-cloned a)',
            type: 'image',
            tLoading: 'Loading image #%curr%...',
            mainClass: 'mfp-img-mobile',
            gallery: {
                enabled: true,
                navigateByImgClick: true,
                preload: [0,5] // Will preload 0 - before current, and 1 after the current image
            }
        });

        jQuery('.es-property-single-fields').magnificPopup({
            delegate: 'a.js-magnific-gallery',
            type: 'image',
            tLoading: 'Loading image #%curr%...',
            mainClass: 'mfp-img-mobile',
            gallery: {
                enabled: true,
                navigateByImgClick: true,
                preload: [0,5]
            }
        });

        // if ($singleBxGallery.length) {

        if ($singleSlickGallery.length) {
            $singleSlickGallery.slick({
                arrows: false,
                asNavFor: $singleSlickGalleryPager,
                rtl: Estatik.settings.isRTL,
                adaptiveHeight: true,
            });

            var show = 5;

            if ($singleSlickGallery.width() < 430) {
                show = 4;
            }

            $singleSlickGalleryPager.slick({
                asNavFor: $singleSlickGallery,
                slidesToScroll: 1,
                slidesToShow: show,
                focusOnSelect: true,
                arrows: false,
                rtl: Estatik.settings.isRTL,
                // centerMode: true,
                // nextArrow: $('.es-single-gallery-slick-next'),
                // prevArrow: $('.es-single-gallery-slick-prev'),
                responsive: [
                    {
                        breakpoint: 1130,
                        settings: {
                            slidesToShow: 4
                        }
                    },
                    {
                        breakpoint: 780,
                        settings: {
                            slidesToShow: 3
                        }
                    },
                    {
                        breakpoint: 320,
                        settings: {
                            slidesToShow: 2
                        }
                    }
                ]
            });

            $('.es-single-gallery-slick-next').click(function() {
                $singleSlickGallery.slick('slickNext');
                $singleSlickGalleryPager.slick('slickNext');
                return false;
            });

            $('.es-single-gallery-slick-prev').click(function() {
                $singleSlickGallery.slick('slickPrev');
                $singleSlickGalleryPager.slick('slickPrev');
                return false;
            });

        }

        initGoogleSingleMap();

        if (hash) {
            var $activeTab = $('.es-tab-' + hash).addClass('active');
        } else {
            $('.es-single-tabs').find('a').eq(0).addClass('active');
        }

        $('.es-single-tabs a').each(function() {
             if (!$($(this).attr('href')).length) {
                 $(this).hide();
             }
        });

        $('.es-single-tabs a').click(function() {
            $('.es-single-tabs a').removeClass('active');
            $(this).addClass('active');

            var target = $(this).attr('href') == '#es-info' ? 'body' : $(this).attr('href');

            $('html, body').animate({
                scrollTop: $(target).offset().top - 50
            }, 600);

            return false;
        });

        $( '.es-top-link' ).click( function() {

            $('html, body').animate({
                scrollTop: $( 'body' ).offset().top - 50
            }, 600);

            return false;
        } );
    });
})(jQuery);
