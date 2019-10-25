wplj(document).ready(function () {
    wplj(".wpl-profile-listing-wp .wpl_profile_container ul li.fax").wrapInner("<a></a>");

    /*Gen-tabs*/
    var realtyna = {};
    realtyna.options = {};
    // Tab System

    realtyna.options.tabs = {
        // Class selectors
        tabSystemClass: '.wpl-js-tab-system',
        tabsClass: '.wpl-gen-tab-wp',
        tabContentsClass: '.wpl-gen-tab-contents-wp',
        tabContentClass: '.wpl-gen-tab-content',

        tabActiveClass      : 'wpl-gen-tab-active', // Class Name
        tabParentActiveClass: 'wpl-gen-tab-active-parent', // Class Name

        activeChildIndex: 0 // Active tab index
    };

    realtyna.tabs = function () {
        var _tabOptions = realtyna.options.tabs;

        wplj(_tabOptions.tabSystemClass).each(function(){
            var _tabs = wplj(this).find(_tabOptions.tabsClass).first(),
                _tabContents = wplj(this).find(_tabOptions.tabContentsClass).first();

            // Tab click trigger
            _tabs.find('ul > li > a').on('touchstart click', function (e) {
                e.preventDefault();

                if (wplj(this).hasClass(_tabOptions.tabActiveClass))
                    return false;

                // Hide previous tab and content
                _tabs.find('ul > li > a').removeClass(_tabOptions.tabActiveClass).parent().removeClass(_tabOptions.tabParentActiveClass);
                _tabContents.find('> div').hide();

                // Show corrent tab
                wplj(this).addClass(_tabOptions.tabActiveClass).parent().addClass(_tabOptions.tabParentActiveClass);

                _tabContents.find(wplj(this).attr('href')).fadeIn();

            });

            // Show first
            if(_tabs.find('ul > li > .' + _tabOptions.tabActiveClass).length === 0)
                _tabs.find('ul > li > a').eq(_tabOptions.activeChildIndex).trigger('click');
        });

    };
    realtyna.tabs();



});
