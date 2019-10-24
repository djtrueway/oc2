<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="wpl_googlemap_widget_backend_form wpl-widget-form-wp" id="<?php echo $this->get_field_id('wpl_google_map_widget_container'); ?>">
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('layout'); ?>"><?php echo __('Layout', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('layout'); ?>" name="<?php echo $this->get_field_name('layout'); ?>" class="widefat">
            <?php echo $this->generate_layouts_selectbox('googlemap', $instance); ?>
        </select>
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_css_class'); ?>"><?php echo __('CSS Class', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('data_css_class'); ?>" name="<?php echo $this->get_field_name('data'); ?>[css_class]" value="<?php echo isset($instance['data']['css_class']) ? $instance['data']['css_class'] : ''; ?>" />
    </div>
    
    <!-- Create a button to show Short-code of this widget -->
    <?php if(wpl_global::check_addon('pro') and !wpl_global::is_page_builder()): ?>
        <button id="<?php echo $this->get_field_id('btn-shortcode'); ?>"
                data-item-id="<?php echo $this->number; ?>"
                data-realtyna-lightbox-opts="clearContent:false"
                data-fancy-id="<?php echo $this->get_field_id('wpl_view_shortcode'); ?>" class="wpl-open-lightbox-btn wpl-button button-1"
                href="#<?php echo $this->get_field_id('wpl_view_shortcode'); ?>" data-realtyna-lightbox type="button"><?php echo __('View Shortcode', 'real-estate-listing-realtyna-wpl'); ?></button>

        <div id="<?php echo $this->get_field_id('wpl_view_shortcode'); ?>" class="hidden">
            <div class="fanc-content size-width-1">
                <h2><?php echo __('View Shortcode', 'real-estate-listing-realtyna-wpl'); ?></h2>
                <div class="fanc-body fancy-search-body">
                    <p class="wpl_widget_shortcode_preview"><?php echo '[wpl_widget_instance id="' . $this->id . '"]'; ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>