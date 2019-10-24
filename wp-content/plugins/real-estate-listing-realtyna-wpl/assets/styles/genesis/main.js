/* ========================================================================
 * Genesis compatibility add-on code.
 * Author: Realtyna UI Department/
 * ======================================================================== */

wplj(function () {

    const TIMER_INTERVAL = 2000;
    var gC = {},
        priceContainer = wplj('.price_box'),
        priceContainerCheck = priceContainer.children("span").length,
        propertyTop = wplj('.wpl_prp_top'),
        profilePicture = wplj('.wpl_profile_picture'),
        timer;

    gC.createCookie = function (name, value, days) {
        var expires;
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toGMTString();
        }
        else expires = "";
        document.cookie = name + "=" + value + expires + "; path=/";
    };
    gC.readCookie = function (name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    };
    gC.isMobile = {
        Android: function () {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function () {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function () {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function () {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function () {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function () {
            return (gC.isMobile.Android() || gC.isMobile.BlackBerry() || gC.isMobile.iOS() || gC.isMobile.Opera() || gC.isMobile.Windows());
        }
    };
    gC.setHeight = function () {
        var listingContainer = wplj('.wpl_prp_cont');
        listingContainer.each(function () {
            var containerHeight = wplj(this).find('.wpl_prp_top').height();
            var priceContainer = wplj(this).find('.price_box');
            priceContainer.css('top', containerHeight - 20);
        })
    };
    gC.removeHeight = function () {
        priceContainer.css('top', '');
    };
    gC.setHeightTimer = function () {
        gC.setHeight();
    };
    gC.textTruncate = function (element) {
        var showChar = 100;
        var ellipsestext = "...";

        element.each(function () {
            var content = wplj(this).html();
            if (content.length > showChar) {

                var c = content.substr(0, showChar);

                return wplj(this).html(c + ellipsestext);
            }
        })
    };

    gC.textTruncate(wplj('.about'));

    //If mobile browser detected, add prevent-animation class to the wpl_prp_top
    if (gC.isMobile.any()) {
        wplj('.wpl_prp_top').attr('data-is-mobile', 'true');
        wplj('.wpl_profile_picture').attr('data-is-mobile', 'true');
    }

    //Check if required cookie is set
    if (gC.readCookie('wplpcc') === null || undefined) {
        //If cookie 'wplccc' is not available set it to default value, grid_box
        gC.createCookie('wplpcc', 'grid_box', 365);
    }

    if (gC.readCookie('wplpcc') === 'grid_box' && priceContainerCheck > 0) {
        timer = setInterval(gC.setHeightTimer, TIMER_INTERVAL);
    } else {
        clearInterval(timer);
        gC.removeHeight();
    }

    wplj('body').ajaxSuccess(function () {
        gC.textTruncate(wplj('.about'));
        if (gC.isMobile.any()) {
            propertyTop.attr('data-is-mobile', 'true');
            profilePicture.attr('data-is-mobile', 'true');
        }
    });

    wplj('body').on('click', '#grid_view', function () {
        if (priceContainerCheck > 0) {
            timer = setInterval(gC.setHeightTimer, TIMER_INTERVAL);
        }
        setTimeout(function () {
            gC.setHeight();
        }, 500);

    });
    wplj('body').on('click', '#list_view', function () {
        clearInterval(timer);
        setTimeout(function () {
            gC.removeHeight();
        }, 500);
    });
    wplj('body').on('click', '#map_view', function () {
        clearInterval(timer);
        setTimeout(function () {
            gC.removeHeight();
        }, 500);
    });
});

