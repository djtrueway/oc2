<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
jQuery(document).ready(function()
{
	wplj('.wpl_prp_show_tabs .tabs_container div.tabs_contents').hide();
	wplj('.wpl_prp_show_tabs .tabs_container').each(function()
    {
		wplj(this).children('div.tabs_contents').first().show();
	});
    
	wplj('.wpl_prp_show_tabs ul.tabs').each(function()
    {
		wplj(this).children('li').first().addClass('active');
	})

	wplj('.wpl_prp_show_tabs ul.tabs li a').off('touchstart click').on('touchstart click',function()
	{
		if(wplj(this).parent().hasClass('active')) return false;
		wplj(this).parent('li').siblings().removeClass('active');
		wplj(this).parent().addClass('active');
        
		var currentTab = wplj(this).data('for');
		wplj(this).parents('.wpl_prp_show_tabs').find('div.tabs_contents').hide();
		wplj(wplj(this).parents('.wpl_prp_show_tabs').find('#'+currentTab)).show();
		
        <?php if(isset($this->pshow_googlemap_activity_id)): ?>
        var init_google_map = wplj(this).attr('data-init-googlemap');
		if(init_google_map && typeof wpl_pshow_map_init<?php echo $this->pshow_googlemap_activity_id; ?> == 'function')
		{
			wpl_pshow_map_init<?php echo $this->pshow_googlemap_activity_id; ?>();
		}
		<?php endif; ?>
        
        <?php if(isset($this->pshow_bingmap_activity_id)): ?>
        var init_bing_map = wplj(this).attr('data-init-bingmap');
		if(init_bing_map && typeof wpl_pshow_map_init<?php echo $this->pshow_bingmap_activity_id; ?> == 'function')
		{
			wpl_pshow_map_init<?php echo $this->pshow_bingmap_activity_id; ?>();
		}
		<?php endif; ?>

		return false;
	});

	wpl_listing_set_js_triggers();
	wpl_idx_check_existence();
    
    <?php if(wpl_global::check_addon('membership') and wpl_session::get('wpl_dpr_popup')): ?>
    	wpl_dpr_popup();
    <?php endif; ?>    
    // check if listhub is enbaled run api tracker of api
    <?php if(wpl_global::check_addon('listhub') and $this->settings['listhub_tracking_status'] == '1'): ?>
    	wpl_listhub_tracker();
    <?php endif; ?>
});

/** Complex unit List/Grid View **/
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
	wplj('.list_view').on('click', function()
	{
		wplj('.grid_view').removeClass('active');
		wplj('.list_view').addClass('active');

		wpl_set_property_css_class('row_box');

		wplj('.wpl-complex-unit-cnt').animate({opacity:0},function()
		{
			wplj(this).removeClass('grid_box').addClass('row_box');
			wplj(this).stop().animate({opacity:1});
		});
	});

	wplj('.grid_view').on('click', function()
	{
		wplj('.list_view').removeClass('active');
		wplj('.grid_view').addClass('active');

		wpl_set_property_css_class('grid_box');

		wplj('.wpl-complex-unit-cnt').animate({opacity:0},function()
		{
			wplj(this).removeClass('row_box').addClass('grid_box');
			wplj(this).stop().animate({opacity:1});
		});
	});
}

<?php if(wpl_global::check_addon('membership') and wpl_session::get('wpl_dpr_popup')): ?>
// dpr = Details Page registration
function wpl_dpr_popup()
{
    var request_str = 'wpl_format=f:profile_show:raw&wplmethod=login';
    var ajax = wpl_run_ajax_query('<?php echo wpl_global::get_wp_url(); ?>', request_str, false, 'HTML', 'GET');
	
	ajax.success(function(html)
	{
        wplj("#wpl_pshow_lightbox_content_container").html(html);
        
        /** Open lightbox **/
        wplj._realtyna.lightbox.open("#wpl_dpr_lightbox",
        {
            reloadPage: false,
			cssClasses: {wrap: 'wpl-frontend-lightbox-wp', overlay: 'realtyna-lightbox-overlay realtyna-lightbox-overlay-drp'},
			closeOnOverlay: <?php echo (wpl_session::get('wpl_dpr_popup') == 1 ? 'true' : 'false'); ?>,
            callbacks:
            {
                afterClose: function()
                {
                    var request_str = 'wpl_format=f:property_show:ajax&wpl_function=dpr_closed';
                    var ajax = wpl_run_ajax_query('<?php echo wpl_global::get_wp_url(); ?>', request_str, false, 'JSON', 'GET');
                }
            }
        });
	});
}
<?php endif; ?>

<?php if(wpl_global::check_addon('listhub') and $this->settings['listhub_tracking_status'] == '1'): ?>
function wpl_listhub_tracker()
{
	// Provider id of google metrics
	<?php echo wpl_addon_listhub::lishub_metrics_js(); ?>
	
	lh('init', {provider:'<?php echo $this->settings['listhub_tracking_metrics_id']; ?>', test:false});
	lh('submit', 'DETAIL_PAGE_VIEWED', {lkey:'<?php echo $this->wpl_properties['current']['raw']['listing_key']; ?>'});
}
<?php endif; ?>

function wpl_idx_check_existence()
{
	var request_str = 'wpl_format=b:addon_idx:ajax&wpl_function=check_payment';

	wplj.ajax(
	{
		type: "POST",
		url: '<?php echo wpl_global::get_full_url(); ?>',
		data: request_str,
		success: function(data)
		{
			if(data.status == '200' || data.status == '201')
			{
				wplj('.wpl-powered-by-realtyna').show();
			}
		}
	});
}
</script>