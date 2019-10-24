<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$sort_options = wpl_sort_options::get_sort_options($this->kind, 1);
$listings_page = wpl_property::get_property_listing_link();
?>
<?php if(wpl_global::check_addon('aps')): ?>
<i id="map_view_handler" class="map_view_handler cl" style="display: none;" onclick="map_view_toggle_listing()">&nbsp;</i>
<?php endif; ?>

<div class="wpl_sort_options_container">

    <?php if(count($sort_options)): ?>
    <div class="wpl_sort_options_container_title"><?php echo __('Sort Option', 'real-estate-listing-realtyna-wpl'); ?></div>
        <div class="wpl-sort-options-list <?php if ($this->wpl_listing_sort_type == 'list') echo 'active'; ?> <?php if ($this->wpl_listing_sort_type == 'dropdown') echo 'wpl-util-hidden'; ?>"><?php echo $this->model->generate_sorts(array('type'=>1, 'kind'=>$this->kind, 'sort_options'=>$sort_options)); ?></div>
        <span class="wpl-sort-options-selectbox <?php if ($this->wpl_listing_sort_type == 'dropdown') echo 'active'; ?> <?php if ($this->wpl_listing_sort_type == 'list') echo 'wpl-util-hidden'; ?>"><?php echo $this->model->generate_sorts(array('type'=>0, 'kind'=>$this->kind, 'sort_options'=>$sort_options)); ?></span>
    <?php endif; ?>
    
    <?php if($this->property_css_class_switcher): ?>
    <div class="wpl_list_grid_switcher <?php if($this->switcher_type == "icon+text") echo 'wpl-list-grid-switcher-icon-text'; ?>">
        <div id="grid_view" class="<?php echo ($this->switcher_type == "icon") ? 'wpl-tooltip-top ' : ''; ?>grid_view <?php if($this->property_css_class == 'grid_box') echo 'active'; ?>">
            <?php if($this->switcher_type == "icon+text") echo '<span>'.__('Grid', 'real-estate-listing-realtyna-wpl').'</span>'; ?>
        </div>
        <?php if ($this->switcher_type == "icon"): ?>
            <div class="wpl-util-hidden"><?php _e('Grid', 'real-estate-listing-realtyna-wpl') ?></div>
        <?php endif; ?>

        <div id="list_view" class="<?php echo ($this->switcher_type == "icon") ? 'wpl-tooltip-top ' : ''; ?>list_view <?php if($this->property_css_class == 'row_box') echo 'active'; ?>">
            <?php if($this->switcher_type == "icon+text") echo '<span>'.__('List', 'real-estate-listing-realtyna-wpl').'</span>'; ?>
        </div>
        <?php if ($this->switcher_type == "icon"): ?>
            <div class="wpl-util-hidden"><?php _e('List', 'real-estate-listing-realtyna-wpl') ?></div>
        <?php endif; ?>

        <?php if(wpl_global::check_addon('aps') and $this->map_activity and (!isset($this->settings['googlemap_display_status']) or (isset($this->settings['googlemap_display_status']) and $this->settings['googlemap_display_status']) or (isset($this->settings['googlemap_display_status']) and !$this->settings['googlemap_display_status'] and wpl_request::getVar('wplfmap', 0)))): ?>
            <div id="map_view" class="<?php echo ($this->switcher_type == "icon") ? 'wpl-tooltip-top ' : ''; ?>map_view <?php if($this->property_css_class == 'map_box') echo 'active'; ?>">
                <?php if($this->switcher_type == "icon+text") echo '<span>'.__('Map', 'real-estate-listing-realtyna-wpl').'</span>'; ?>
            </div>
            <?php if ($this->switcher_type == "icon"): ?>
                <div class="wpl-util-hidden"><?php _e('Map', 'real-estate-listing-realtyna-wpl') ?></div>
            <?php endif; ?>

        <?php elseif(wpl_global::check_addon('aps') and $this->map_activity): ?>
			<a class="<?php echo ($this->switcher_type == "icon") ? 'wpl-tooltip-top ' : ''; ?>map_view" href="<?php echo wpl_global::add_qs_var('wplpcc', 'map_box', wpl_global::add_qs_var('wplfmap', 1, wpl_global::get_full_url())); ?>">
				<?php if($this->switcher_type == "icon+text") echo '<span>'.__('Map', 'real-estate-listing-realtyna-wpl').'</span>'; ?>
			</a>
            <?php if ($this->switcher_type == "icon"): ?>
                <div class="wpl-util-hidden"><?php _e('Map', 'real-estate-listing-realtyna-wpl') ?></div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if(wpl_global::check_addon('pro') and $this->listings_rss_enabled): ?>
    <div class="wpl-rss-wp">
        <a class="wpl-rss-link" href="#" onclick="wpl_generate_rss();"><span><?php echo __('RSS', 'real-estate-listing-realtyna-wpl'); ?></span></a>
    </div>
    <?php endif; ?>
    
    <?php if(wpl_global::check_addon('pro') and $this->print_results_page): ?>
    <div class="wpl-print-rp-wp">
        <a class="wpl-print-rp-link" href="#" onclick="wpl_generate_print_rp();"><span><i class="fa fa-print"></i></span></a>
    </div>
    <?php endif; ?>
    
    <?php if(wpl_global::check_addon('save_searches') and $this->save_search_button): ?>
    <div class="wpl-save-search-wp wpl-plisting-link-btn">
        <a id="wpl_save_search_link_lightbox" class="wpl-save-search-link" data-realtyna-href="#wpl_plisting_lightbox_content_container" onclick="return wpl_generate_save_search();" data-realtyna-lightbox-opts="title:<?php echo __('Save this Search', 'real-estate-listing-realtyna-wpl'); ?>"><span><?php echo __('Save Search', 'real-estate-listing-realtyna-wpl'); ?></span></a>
    </div>
    <?php endif; ?>
    
    <?php if(wpl_global::check_addon('aps') and wpl_global::get_setting('aps_landing_page_generator') and wpl_users::check_access('landing_page')): ?>
    <div class="wpl-landing-page-generator-wp wpl-plisting-link-btn">
        <a id="wpl_landing_page_generator_link_lightbox" class="wpl-landing-page-generator-link" data-realtyna-href="#wpl_plisting_lightbox_content_container" onclick="return wpl_generate_landing_page_generator();" data-realtyna-lightbox-opts="title:<?php echo __('Landing Page Generator', 'real-estate-listing-realtyna-wpl'); ?>"><span><?php echo __('Create Landing Page', 'real-estate-listing-realtyna-wpl'); ?></span></a>
    </div>
    <?php endif; ?>
</div>

<div class="wpl-row wpl-expanded <?php if($this->property_css_class == "grid_box") echo "wpl-small-up-1 wpl-medium-up-2 wpl-large-up-".$this->listing_columns; ?>  wpl_property_listing_listings_container clearfix">
    <?php echo $this->properties_str; ?>
</div>

<?php if($this->wplpagination != 'scroll'): ?>
<div class="wpl_pagination_container">
    <?php echo $this->pagination->show(); ?>
</div>
<?php endif;