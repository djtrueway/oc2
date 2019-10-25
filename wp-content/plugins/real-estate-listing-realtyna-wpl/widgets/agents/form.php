<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

include _wpl_import('widgets.agents.scripts.css_backend', true, true);
include _wpl_import('widgets.agents.scripts.js_backend', true, true);
?>
<div class="wpl_agents_widget_backend_form wpl-widget-form-wp" id="<?php echo $this->get_field_id('wpl_agents_widget_container'); ?>">
    
    <h4><?php echo __('Widget Configurations', 'real-estate-listing-realtyna-wpl'); ?></h4>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('titlew'); ?>"><?php echo __('Title', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo isset($instance['title']) ? $instance['title'] : ''; ?>" />
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('layout'); ?>"><?php echo __('Layout', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('layout'); ?>" name="<?php echo $this->get_field_name('layout'); ?>">
	        <?php echo $this->generate_layouts_selectbox('agents', $instance); ?>
        </select>
    </div>

    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('style'); ?>"><?php echo __('Style', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('data'); ?>[style]">
            <option <?php if(isset($instance['data']['style']) and  $instance['data']['style']== '1') echo 'selected="selected"'; ?> value="1"><?php echo __('Horizontal', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option <?php if(isset($instance['data']['style']) and  $instance['data']['style']== '2') echo 'selected="selected"'; ?> value="2"><?php echo __('Vertical', 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('wpltarget'); ?>"><?php echo __('Target page', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('wpltarget'); ?>" name="<?php echo $this->get_field_name('wpltarget'); ?>">
            <option value="">-----</option>
	        <?php echo $this->generate_pages_selectbox($instance); ?>
        </select>
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_css_class'); ?>"><?php echo __('CSS Class', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('data_css_class'); ?>" name="<?php echo $this->get_field_name('data'); ?>[css_class]" value="<?php echo isset($instance['data']['css_class']) ? $instance['data']['css_class'] : ''; ?>" />
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_image_width'); ?>"><?php echo __('Image Width', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('data_image_width'); ?>" name="<?php echo $this->get_field_name('data'); ?>[image_width]" value="<?php echo isset($instance['data']['image_width']) ? $instance['data']['image_width'] : '230'; ?>" />
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_image_height'); ?>"><?php echo __('Image Height', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('data_image_height'); ?>" name="<?php echo $this->get_field_name('data'); ?>[image_height]" value="<?php echo isset($instance['data']['image_height']) ? $instance['data']['image_height'] : '230'; ?>" />
    </div>

    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_lazyload'); ?>"><?php echo __('Lazyload', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select class="text_box" name="<?php echo $this->get_field_name('data'); ?>[lazyload]" id="<?php echo $this->get_field_id('data_lazyload'); ?>">
            <option value="0" <?php if(isset($instance['data']['lazyload']) and $instance['data']['lazyload'] == '0') echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="1" <?php if(isset($instance['data']['lazyload']) and $instance['data']['lazyload'] == '1') echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
    </div>

    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_mailto_status'); ?>"><?php echo __('Mailto Status', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="checkbox" <?php if(isset($instance['data']['mailto_status']) and $instance['data']['mailto_status']) echo 'checked="checked"'; ?> value="1" id="<?php echo $this->get_field_id('data_mailto_status'); ?>" name="<?php echo $this->get_field_name('data'); ?>[mailto_status]" />
    </div>

    <h4><?php echo __('Filter Profiles'); ?></h4>
    <?php if(wpl_global::check_addon('pro')): ?>
    <?php $membership_types = wpl_users::get_membership_types(); ?>
    <div class="wpl-widget-row">
    	<label for="<?php echo $this->get_field_id('data_user_type'); ?>"><?php echo __('User Type', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('data_user_type'); ?>" name="<?php echo $this->get_field_name('data'); ?>[user_type]">
        	<option value="-1"><?php echo __('All', 'real-estate-listing-realtyna-wpl'); ?></option>
            <?php foreach($membership_types as $membership_type): ?>
            <option <?php if(isset($instance['data']['user_type']) and $instance['data']['user_type'] == $membership_type->id) echo 'selected="selected"'; ?> value="<?php echo $membership_type->id; ?>"><?php echo __($membership_type->name, 'real-estate-listing-realtyna-wpl'); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <?php $memberships = wpl_users::get_wpl_memberships(); ?>
    <div class="wpl-widget-row">
    	<label for="<?php echo $this->get_field_id('data_membership'); ?>"><?php echo __('Membership', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('data_membership'); ?>" name="<?php echo $this->get_field_name('data'); ?>[membership]">
        	<option value=""><?php echo __('All', 'real-estate-listing-realtyna-wpl'); ?></option>
            <?php foreach($memberships as $membership): ?>
            <option <?php if(isset($instance['data']['membership']) and $instance['data']['membership'] == $membership->id) echo 'selected="selected"'; ?> value="<?php echo $membership->id; ?>"><?php echo __($membership->membership_name, 'real-estate-listing-realtyna-wpl'); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>
    
    <div class="wpl-widget-row">
    	<label for="<?php echo $this->get_field_id('data_user_ids'); ?>"><?php echo __('User IDs', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('data_user_ids'); ?>" name="<?php echo $this->get_field_name('data'); ?>[user_ids]" value="<?php echo isset($instance['data']['user_ids']) ? $instance['data']['user_ids'] : ''; ?>" />
    </div>
    
    <div class="wpl-widget-row">
    	<input <?php if(isset($instance['data']['random']) and $instance['data']['random']) echo 'checked="checked"'; ?> value="1" type="checkbox" id="<?php echo $this->get_field_id('data_random'); ?>" name="<?php echo $this->get_field_name('data'); ?>[random]" onclick="random_clicked_agents<?php echo $this->widget_id; ?>();" />
    	<label for="<?php echo $this->get_field_id('data_random'); ?>"><?php echo __('Random', 'real-estate-listing-realtyna-wpl'); ?></label>
    </div>
    
    <h4><?php echo __('Sort and Limit'); ?></h4>
    <?php $sort_options = wpl_sort_options::render(wpl_sort_options::get_sort_options(2)); ?>
    <div class="wpl-widget-row">
    	<label for="<?php echo $this->get_field_id('data_orderby'); ?>"><?php echo __('Order by', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('data_orderby'); ?>" name="<?php echo $this->get_field_name('data'); ?>[orderby]">
        	<?php foreach($sort_options as $sort_option): ?>
            <option <?php if(isset($instance['data']['orderby']) and urlencode($sort_option['field_name']) == $instance['data']['orderby']) echo 'selected="selected"'; ?> value="<?php echo urlencode($sort_option['field_name']); ?>"><?php echo $sort_option['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="wpl-widget-row">
    	<label for="<?php echo $this->get_field_id('data_order'); ?>"><?php echo __('Order', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('data_order'); ?>" name="<?php echo $this->get_field_name('data'); ?>[order]">
            <option <?php if(isset($instance['data']['order']) and $instance['data']['order'] == 'ASC') echo 'selected="selected"'; ?> value="ASC"><?php echo __('ASC', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option <?php if(isset($instance['data']['order']) and $instance['data']['order'] == 'DESC') echo 'selected="selected"'; ?> value="DESC"><?php echo __('DESC', 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
    </div>
    
    <div class="wpl-widget-row">
    	<label for="<?php echo $this->get_field_id('data_limit'); ?>"><?php echo __('Number of users', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('data_limit'); ?>" name="<?php echo $this->get_field_name('data'); ?>[limit]" value="<?php echo isset($instance['data']['limit']) ? $instance['data']['limit'] : ''; ?>" />
    </div>
    
    <!-- Create a button to show Short-code of this widget -->
    <?php if(wpl_global::check_addon('pro') and !wpl_global::is_page_builder()): ?>
        <button id="<?php echo $this->get_field_id('btn-shortcode'); ?>"
                data-item-id="<?php echo $this->number; ?>"
                data-fancy-id="<?php echo $this->get_field_id('wpl_view_shortcode'); ?>" class="wpl-open-lightbox-btn wpl-button button-1"
                href="#<?php echo $this->get_field_id('wpl_view_shortcode'); ?>" type="button"><?php echo __('View Shortcode', 'real-estate-listing-realtyna-wpl'); ?></button>
    
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