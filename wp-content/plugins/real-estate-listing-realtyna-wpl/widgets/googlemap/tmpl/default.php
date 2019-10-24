<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

// WPL Main listing page
$listings_page = wpl_property::get_property_listing_link();
?>
<div id="wpl_googlemap_widget_cnt<?php echo $this->widget_id; ?>" class="wpl-googlemap-widget <?php echo $this->css_class; ?>">
    <?php echo $args['before_title'].__($this->title, 'real-estate-listing-realtyna-wpl').$args['after_title']; ?>
    
    <div class="wpl-googlemap-widget-link">
        
        <?php if(wpl_global::check_addon('aps')): ?>
        <a href="<?php echo wpl_global::add_qs_var('wplpcc', 'map_box', wpl_global::add_qs_var('wplfmap', '1', $listings_page)); ?>"><?php echo __('Map View', 'real-estate-listing-realtyna-wpl'); ?></a>
        <?php else: ?>
        <a href="<?php echo wpl_global::add_qs_var('wplfmap', '1', $listings_page); ?>"><?php echo __('Map View', 'real-estate-listing-realtyna-wpl'); ?></a>
        <?php endif; ?>
        
    </div>
    
</div>