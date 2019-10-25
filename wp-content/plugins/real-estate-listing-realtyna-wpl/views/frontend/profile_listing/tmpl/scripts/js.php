<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
var wpl_ajax = <?php echo (wpl_global::check_addon('aps') ? wpl_global::get_setting('aps_ajax_listing') : 0); ?>;
var wpl_listing_request_str = '<?php echo wpl_global::generate_request_str(); ?>';
var wpl_listing_limit = <?php echo $this->model->limit; ?>;
var wpl_listing_total_pages = <?php echo $this->total_pages; ?>;
var wpl_listing_current_page = <?php echo $this->page_number; ?>;

/** CSS Class **/
var wpl_current_property_css_class;

jQuery(document).ready(function()
{
    <?php if($this->property_css_class == 'row_box'): ?>
    setTimeout(function(){wpl_tooltip_rename('data-original-title', 'data-raw-title')}, 1000);
    <?php endif; ?>

    main_win_size = wplj(window).width();
	if((main_win_size <= 480))
	{
		wplj('#wpl_profile_listing_main_container .wpl_sort_options_container .wpl_sort_options_container_title').click(function()
		{
			wplj(this).next('ul').stop().slideToggle();
		});
	}

    /** jQuery Triggers **/
    wpl_listing_set_js_triggers();
});

wplj(document).ajaxComplete(function()
{
    /** jQuery Triggers **/
    wpl_listing_set_js_triggers();
});

wplj(window).resize(function()
{
	win_size = wplj(window).width();
	if((win_size <= 480))
	{
		wplj('#wpl_profile_listing_main_container .wpl_sort_options_container .wpl_sort_options_container_title').unbind('click').click(function()
		{
			wplj(this).next('ul').slideToggle();
		});
	}
	else if(win_size > 480)
	{
		wplj('#wpl_profile_listing_main_container .wpl_sort_options_container .wpl_sort_options_container_title').unbind('click');
		wplj('#wpl_profile_listing_main_container .wpl_sort_options_container ul').show();
	}
});

function wpl_page_sortchange(order_string)
{
    order_obj = order_string.split('&');

    order_v1 = order_obj[0].split('=');
    order_v2 = order_obj[1].split('=');

    // AJAX
    if(wpl_ajax == '1')
    {
        wpl_listing_request_str = wpl_update_qs(order_v1[0], order_v1[1], wpl_listing_request_str);
        wpl_listing_request_str = wpl_update_qs(order_v2[0], order_v2[1], wpl_listing_request_str);

        wplj(".wpl_profile_listing_list_view_container").fadeTo(300, 0.5);

		var request_str = 'wpl_format=f:profile_listing:list&'+wpl_listing_request_str;
        var full_url = window.location.href;

        try {
            full_url = wpl_qs_apply(full_url, wpl_listing_request_str);
            history.pushState({search: 'WPL'}, "<?php echo addslashes(__('Search Results', 'real-estate-listing-realtyna-wpl')); ?>", full_url);
        }
        catch (err) {
        }

		wplj.ajax(
		{
			url: '<?php echo wpl_global::get_full_url(); ?>',
			data: request_str,
            dataType: 'json',
			type: 'GET',
			async: true,
			cache: false,
			timeout: 30000,
			success: function(data)
			{
                wplj.when( wplj(".wpl_profile_listing_list_view_container").html(data.html) ).then(function() {
					wplj(".wpl-sort-options-selectbox .wpl_plist_sort").chosen({ width: 'initial' });
				});
                wplj(".wpl_profile_listing_list_view_container").fadeTo(300, 1);
			}
		});
    }
    else
    {
        url = window.location.href;

        url = wpl_update_qs(order_v1[0], order_v1[1], url);
        url = wpl_update_qs(order_v2[0], order_v2[1], url);

        /** Move to First Page **/
        url = wpl_update_qs('wplpage', '1', url);

        window.location = url;
    }
}

function wpl_pagesize_changed(page_size)
{
    // AJAX
    if(wpl_ajax == '1')
    {
        wpl_listing_request_str = wpl_update_qs('limit', page_size, wpl_listing_request_str);

        /** Move to First Page **/
        wpl_listing_request_str = wpl_update_qs('wplpage', '1', wpl_listing_request_str);

        wplj(".wpl_profile_listing_list_view_container").fadeTo(300, 0.5);

		var request_str = 'wpl_format=f:profile_listing:list&'+wpl_listing_request_str;
        var full_url = window.location.href;

        try {
            full_url = wpl_qs_apply(full_url, wpl_listing_request_str);
            history.pushState({search: 'WPL'}, "<?php echo addslashes(__('Search Results', 'real-estate-listing-realtyna-wpl')); ?>", full_url);
        }
        catch (err) {
        }

		wplj.ajax(
		{
			url: '<?php echo wpl_global::get_full_url(); ?>',
			data: request_str,
            dataType: 'json',
			type: 'GET',
			async: true,
			cache: false,
			timeout: 30000,
			success: function(data)
			{
                wplj.when( wplj(".wpl_profile_listing_list_view_container").html(data.html) ).then(function() {
					wplj(".wpl-sort-options-selectbox .wpl_plist_sort").chosen({ width: 'initial' });
				});
                wplj(".wpl_profile_listing_list_view_container").fadeTo(300, 1);
			}
		});
    }
    else
    {
        url = window.location.href;
        url = wpl_update_qs('limit', page_size, url);

        /** Move to First Page **/
        url = wpl_update_qs('wplpage', '1', url);

        window.location = url;
    }
}

function wpl_paginate(page)
{
    // AJAX
    if(wpl_ajax == '1')
    {
        wpl_listing_request_str = wpl_update_qs('wplpage', page, wpl_listing_request_str);

        wplj(".wpl_profile_listing_list_view_container").fadeTo(300, 0.5);

		var request_str = 'wpl_format=f:profile_listing:list&'+wpl_listing_request_str;
        var full_url = window.location.href;

        try {
            full_url = wpl_qs_apply(full_url, wpl_listing_request_str);
            history.pushState({search: 'WPL'}, "<?php echo addslashes(__('Search Results', 'real-estate-listing-realtyna-wpl')); ?>", full_url);
        }
        catch (err) {
        }

		wplj.ajax(
		{
			url: '<?php echo wpl_global::get_full_url(); ?>',
			data: request_str,
            dataType: 'json',
			type: 'GET',
			async: true,
			cache: false,
			timeout: 30000,
			success: function(data)
			{
                wplj.when( wplj(".wpl_profile_listing_list_view_container").html(data.html) ).then(function() {
					wplj(".wpl-sort-options-selectbox .wpl_plist_sort").chosen({ width: 'initial' });
				});
                wplj(".wpl_profile_listing_list_view_container").fadeTo(300, 1);
			}
		});
    }
    else
    {
        url = window.location.href;
        url = wpl_update_qs('wplpage', page, url);

        window.location = url;
    }
}

function wpl_set_property_css_class(pcc)
{
    wpl_current_property_css_class = pcc;

    wplj.ajax(
    {
        url: '<?php echo wpl_global::get_full_url(); ?>',
        data: 'wpl_format=f:property_listing:ajax&wpl_function=set_pcc&pcc='+pcc,
        type: 'GET',
        dataType: 'json',
        cache: false,
        success: function(data)
        {
        }
    });
}

function wpl_listing_set_js_triggers()
{
    wplj('#list_view').on('click', function()
    {
        wplj('#grid_view').removeClass('active');
        wplj('#list_view').addClass('active');

        wpl_set_property_css_class('row_box');

        wplj('.wpl_profile_container').animate({opacity:0},function()
        {
            wplj(this).removeClass('grid_box').addClass('row_box');
            wplj('.wpl_profile_listing_profiles_container').removeClass('wpl-large-up-<?php echo $this->profile_columns; ?>').removeClass('wpl-medium-up-2').removeClass('wpl-small-up-1');
            wplj(this).stop().animate({opacity:1});
        });

        wpl_tooltip_rename('data-original-title', 'data-raw-title');
    });

    wplj('#grid_view').on('click', function()
    {
        wplj('#list_view').removeClass('active');
        wplj('#grid_view').addClass('active');

        wpl_set_property_css_class('grid_box');

        wplj('.wpl_profile_container').animate({opacity:0},function()
        {
            wplj(this).removeClass('row_box').addClass('grid_box');
            wplj('.wpl_profile_listing_profiles_container').addClass('wpl-large-up-<?php echo $this->profile_columns; ?>').addClass('wpl-medium-up-2').addClass('wpl-small-up-1');
            wplj(this).stop().animate({opacity:1});
        });

        wpl_tooltip_rename('data-raw-title', 'data-original-title');
    });

    if(wplj.isFunction(wplj.fn.tooltip)) wplj('.wpl_profile_container li').tooltip();
}

function wpl_tooltip_rename(name, new_name)
{
    wplj('.wpl_profile_container ul li').each(function()
    {
        var val = wplj.attr(this, name);
        wplj.attr(this, new_name, val);
        wplj.removeAttr(this, name);
    });
}
</script>
