<?php
defined('_WPLEXEC') or die('Restricted access');

if(wpl_global::check_addon('membership')) $this->membership = new wpl_addon_membership();
?>
<form action="<?php echo wpl_property::get_property_listing_link(); ?>" id="wpl_search_form_<?php echo $widget_id; ?>" method="GET" onsubmit="return wpl_do_search_<?php echo $widget_id; ?>();" class="wpl_search_from_box simple clearfix wpl_search_kind<?php echo $this->kind; ?> <?php echo $this->css_class; ?>">
    <!-- Do not change the ID -->
    <div id="wpl_searchwidget_<?php echo $widget_id; ?>" class="clearfix">
	    <div class="wpl_search_from">
	    	<?php
                foreach($this->rendered as $data)
                {
                    echo '<div class="wpl_search_fields '.$data['field_data']['type'].'">'.$data['html'].'</div>';
                }
            ?>
			<?php if($this->show_reset_button): ?>
				<div class="wpl_search_reset" onclick="wpl_do_reset<?php echo $this->widget_id; ?>([], <?php echo ($this->ajax == 2 ? 'true' : 'false'); ?>);" id="wpl_search_reset<?php echo $widget_id; ?>"><?php echo __('Reset', 'real-estate-listing-realtyna-wpl'); ?></div>
	    	<?php endif; ?>
	    	<div class="search_submit_box">
		    	<input id="wpl_search_widget_submit<?php echo $widget_id; ?>" class="wpl_search_widget_submit" type="submit" value="<?php echo __('Search', 'real-estate-listing-realtyna-wpl'); ?>" />
                <?php if($this->show_total_results == 1): ?><span id="wpl_total_results<?php echo $widget_id; ?>" class="wpl-total-results">(<span></span>)</span><?php endif; ?>
		    </div>
            <?php if($this->show_total_results == 2): ?><span id="wpl_total_results<?php echo $widget_id; ?>" class="wpl-total-results-after"><?php echo sprintf('%s listings', '<span></span>'); ?></span><?php endif; ?>
            <?php if(wpl_global::check_addon('membership') and ($this->kind == 0 or $this->kind == 1)): ?>
				<div class="wpl_dashboard_links_container">
					<?php if(wpl_global::check_addon('save_searches') and ($this->show_saved_searches)): ?>
                    <a class="wpl-addon-save-searches-link" href="<?php echo $this->membership->URL('searches'); ?>"><?php echo __('Saved Searches', 'real-estate-listing-realtyna-wpl'); ?>
                        <span id="wpl-addon-save-searches-count<?php echo $widget_id; ?>"><?php echo $this->saved_searches_count; ?></span>
                    </a>
					<?php endif; ?>
					<?php if($this->show_favorites): ?>
						<a class="wpl-widget-favorites-link" href="<?php echo $this->membership->URL('favorites'); ?>"><?php echo __('Favorites', 'real-estate-listing-realtyna-wpl'); ?>
							<span id="wpl-widget-favorites-count<?php echo $widget_id; ?>"><?php echo $this->favorites_count; ?></span>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
	    </div>
	</div>
</form>
<?php
/** import js codes **/
$this->_wpl_import('widgets.search.scripts.js', true, true);