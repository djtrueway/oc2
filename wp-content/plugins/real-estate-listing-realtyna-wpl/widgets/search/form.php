<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');

include _wpl_import('widgets.search.scripts.css_backend', true, true);
include _wpl_import('widgets.search.scripts.js_backend', true, true);

wpl_extensions::import_javascript((object) array('param1'=>'wpl-sly-scrollbar', 'param2'=>'js/libraries/wpl.slyscrollbar.min.js'));
?>
<div class="wpl-widget-search-wp wpl-widget-form-wp" id="<?php echo $this->get_field_id('wpl_search_widget_container'); ?>">
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>"
               onblur="wplSearchWidget<?php echo $this->number ?>.saveChange(this);" />
    </div>
    
    <div class="wpl-widget-row">
        <?php $kinds = wpl_flex::get_kinds(''); ?>
        <label for="<?php echo $this->get_field_id('kind'); ?>"><?php echo __('Kind', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('kind'); ?>" name="<?php echo $this->get_field_name('kind'); ?>"
               onchange="wplSearchWidget<?php echo $this->number ?>.saveAndReload(this);">
            <?php foreach($kinds as $kind): if(trim($kind['addon_name']) and !wpl_global::check_addon($kind['addon_name'])) continue; ?>
            <option <?php if(isset($instance['kind']) and $instance['kind'] == $kind['id']) echo 'selected="selected"'; ?> value="<?php echo $kind['id']; ?>"><?php echo __($kind['name'], 'real-estate-listing-realtyna-wpl'); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('layout'); ?>"><?php echo __('Layout', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('layout'); ?>" name="<?php echo $this->get_field_name('layout'); ?>"
                onchange="wplSearchWidget<?php echo $this->number ?>.saveChange(this);">
            <?php echo $this->generate_layouts_selectbox('search', $instance); ?>
        </select>
    </div>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('style'); ?>"><?php echo __('Style', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>">
            <option value="wpl-search-default" <?php if(isset($instance['style']) and $instance['style'] == "wpl-search-default") echo 'selected="selected"'; ?>><?php echo __('Default', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="wpl-search-sidebar" <?php if(isset($instance['style']) and $instance['style'] == "wpl-search-sidebar") echo 'selected="selected"'; ?>><?php echo __('Sidebar', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="wpl-search-float" <?php if(isset($instance['style']) and $instance['style'] == "wpl-search-float") echo 'selected="selected"'; ?>><?php echo __('Float', 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
    </div>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('wpltarget'); ?>"><?php echo __('Target page', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('wpltarget'); ?>" name="<?php echo $this->get_field_name('wpltarget'); ?>">
            <option value="">-----</option>
            <?php echo $this->generate_pages_selectbox($instance); ?>
            <option value="-1" <?php echo ((isset($instance['wpltarget']) and $instance['wpltarget'] == '-1') ? 'selected="selected"' : ''); ?>><?php echo __('Self Page', 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
    </div>
    
    <?php if(wpl_global::check_addon('aps')): ?>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('ajax'); ?>"><?php echo __('AJAX Search', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('ajax'); ?>"
               name="<?php echo $this->get_field_name('ajax'); ?>">
            <option value="0" <?php if(isset($instance['ajax']) and $instance['ajax'] == 0) echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="1" <?php if(isset($instance['ajax']) and $instance['ajax'] == 1) echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="2" <?php if(isset($instance['ajax']) and $instance['ajax'] == 2) echo 'selected="selected"'; ?>><?php echo __('Yes (On the fly)', 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
    </div>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('total_results'); ?>"><?php echo __('Show Total Results', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('total_results'); ?>" name="<?php echo $this->get_field_name('total_results'); ?>">
            <option value="0" <?php if(isset($instance['total_results']) and $instance['total_results'] == "0") echo 'selected="selected"'; ?>><?php echo __('Hide', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="1" <?php if(isset($instance['total_results']) and $instance['total_results'] == "1") echo 'selected="selected"'; ?>><?php echo __('Show (Inside of Search Button)', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="2" <?php if(isset($instance['total_results']) and $instance['total_results'] == "2") echo 'selected="selected"'; ?>><?php echo __('Show (Next to Search Button)', 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
    </div>
    <?php endif; ?>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('css_class'); ?>"><?php echo __('CSS Class', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('css_class'); ?>" name="<?php echo $this->get_field_name('css_class'); ?>" value="<?php echo isset($instance['css_class']) ? $instance['css_class'] : ''; ?>" />
    </div>

    <?php if(wpl_global::check_addon('aps')): ?>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('more_options_type'); ?>"><?php echo __('More Options Type', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('more_options_type'); ?>" name="<?php echo $this->get_field_name('more_options_type'); ?>">
            <option value="0" <?php if(isset($instance['more_options_type']) and $instance['more_options_type'] == 0) echo 'selected="selected"'; ?>><?php echo __('Slide', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="1" <?php if(isset($instance['more_options_type']) and $instance['more_options_type'] == 1) echo 'selected="selected"'; ?>><?php echo __('Pop-up', 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
    </div>
    <?php endif; ?>
	
	<div class="wpl-widget-row">
        <input <?php if(isset($instance['show_reset_button']) and $instance['show_reset_button']) echo 'checked="checked"'; ?> value="1" type="checkbox" id="<?php echo $this->get_field_id('show_reset_button'); ?>" name="<?php echo $this->get_field_name('show_reset_button'); ?>" />
        <label for="<?php echo $this->get_field_id('show_reset_button'); ?>"><?php echo __('Show Reset Button', 'real-estate-listing-realtyna-wpl'); ?></label>
    </div>

    <?php if(wpl_global::check_addon('membership') and ($this->kind == 0 or $this->kind == 1)): ?>
        <?php if(wpl_global::check_addon('save_searches')): ?>
            <div class="wpl-widget-row">
                <input <?php if(isset($instance['show_saved_searches']) and $instance['show_saved_searches']) echo 'checked="checked"'; ?> value="1" type="checkbox" id="<?php echo $this->get_field_id('show_saved_searches'); ?>" name="<?php echo $this->get_field_name('show_saved_searches'); ?>" />
                <label for="<?php echo $this->get_field_id('show_saved_searches'); ?>"><?php echo __('Show Saved Searches', 'real-estate-listing-realtyna-wpl'); ?></label>
            </div>
        <?php endif; ?>
        <div class="wpl-widget-row">
            <input <?php if(isset($instance['show_favorites']) and $instance['show_favorites']) echo 'checked="checked"'; ?> value="1" type="checkbox" id="<?php echo $this->get_field_id('show_favorites'); ?>" name="<?php echo $this->get_field_name('show_favorites'); ?>" />
            <label for="<?php echo $this->get_field_id('show_favorites'); ?>"><?php echo __('Show Favorites', 'real-estate-listing-realtyna-wpl'); ?></label>
        </div>
    <?php endif; ?>

    <button id="btn-search-<?php echo $this->number ?>"
            data-is-init="false"
            data-item-id="<?php echo $this->number ?>"
            data-fancy-id="wpl_view_fields_<?php echo $this->number; ?>" class="wpl-btn-search-view-fields wpl-button button-1"
            href="#wpl_view_fields_<?php echo $this->number ?>" type="button"><?php echo __('View Fields', 'real-estate-listing-realtyna-wpl'); ?></button>

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

    <span id="wpl-js-page-must-reload-<?php echo $this->number ?>" class="wpl-widget-search-must-reload"><?php echo __('The page needs to be reloaded before opening the field editor.', 'real-estate-listing-realtyna-wpl'); ?></span>
</div>

<div id="wpl_view_fields_<?php echo $this->number ?>" class="hidden">
    <div class="fanc-content" id="wpl_flex_modify_container_<?php echo $this->number ?>">
        <h2><?php echo __('Search Fields Configurations', 'real-estate-listing-realtyna-wpl'); ?></h2>
        <div class="fanc-body fancy-search-body wpl-widget-search-fields-wp">
            <div class="search-fields-wp">
                <div class="search-tabs-wp">
                    <?php $this->generate_backend_categories_tabs($instance['data']); ?>
                </div>
                <div class="search-tab-content">
                    <?php $this->generate_backend_categories($instance['data']); ?>
                </div>
            </div>
            <div id="fields-order" class="order-list-wp">
                <h4>
                    <span>
                        <?php echo __('Fields Order', 'real-estate-listing-realtyna-wpl'); ?>
                    </span>
                </h4>

                <div class="order-list-body">
                    <ul>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>