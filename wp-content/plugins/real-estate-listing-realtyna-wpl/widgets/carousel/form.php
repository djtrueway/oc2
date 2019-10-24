<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

include _wpl_import('widgets.carousel.scripts.css_backend', true, true);
include _wpl_import('widgets.carousel.scripts.js_backend', true, true);

$location_settings = wpl_settings::get_settings(3);
?>
<script type="text/javascript">
function wpl_carousel_toggle<?php echo $this->widget_id; ?>(element_id)
{
    wplj('#'+element_id).toggle();
}
</script>
<div class="wpl_carousel_widget_backend_form wpl-carousel-widget-<?php echo $this->widget_id; ?>" id="<?php echo $this->get_field_id('wpl_carousel_widget_container'); ?>">
    
    <h4><?php echo __('Widget Configurations', 'real-estate-listing-realtyna-wpl'); ?></h4>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" placeholder="<?php echo __('Main Carousel', 'real-estate-listing-realtyna-wpl'); ?>" value="<?php echo isset($instance['title']) ? $instance['title'] : ''; ?>" />
    </div>

    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('wpltarget'); ?>"><?php echo __('Target page', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('wpltarget'); ?>" name="<?php echo $this->get_field_name('wpltarget'); ?>">
            <option value="">-----</option>
            <?php echo $this->generate_pages_selectbox($instance); ?>
        </select>
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('layout'); ?>"><?php echo __('Layout', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('layout'); ?>" name="<?php echo $this->get_field_name('layout'); ?>" class="wpl-carousel-widget-layout" data-wpl-carousel-id="<?php echo $this->widget_id; ?>">
            <?php echo $this->generate_layouts_selectbox('carousel', $instance); ?>
        </select>
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_css_class'); ?>"><?php echo __('CSS Class', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('data_css_class'); ?>" name="<?php echo $this->get_field_name('data'); ?>[css_class]" value="<?php echo isset($instance['data']['css_class']) ? $instance['data']['css_class'] : ''; ?>" />
    </div>
    
    <div class="wpl-widget-row wpl-carousel-opt" data-wpl-carousel-type="general" data-wpl-pl-init="default:310|details:1920|multi_images:310|modern:1920|list:90|modern_full_responsive:1920">
        <label for="<?php echo $this->get_field_id('image_width'); ?>"><?php echo __('Image Width', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('image_width'); ?>" name="<?php echo $this->get_field_name('data'); ?>[image_width]" placeholder="310" value="<?php echo isset($instance['data']['image_width']) ? $instance['data']['image_width'] : '1920'; ?>" />
    </div>
    
    <div class="wpl-widget-row wpl-carousel-opt" data-wpl-carousel-type="general" data-wpl-pl-init="default:220|modern:558|list:82|modern_full_responsive:750">
        <label for="<?php echo $this->get_field_id('image_height'); ?>"><?php echo __('Image Height', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('image_height'); ?>" name="<?php echo $this->get_field_name('data'); ?>[image_height]" placeholder="220" value="<?php echo isset($instance['data']['image_height']) ? $instance['data']['image_height'] : '558'; ?>" />
    </div>
    <div class="wpl-widget-row wpl-carousel-opt" data-wpl-carousel-type="modern">
        <label for="<?php echo $this->get_field_id('tablet_image_height'); ?>"><?php echo __('Tablet Image Height', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('tablet_image_height'); ?>" name="<?php echo $this->get_field_name('data'); ?>[tablet_image_height]" placeholder="400" value="<?php echo isset($instance['data']['tablet_image_height']) ? $instance['data']['tablet_image_height'] : '400'; ?>" />
    </div>
    <div class="wpl-widget-row wpl-carousel-opt" data-wpl-carousel-type="modern">
        <label for="<?php echo $this->get_field_id('phone_image_height'); ?>"><?php echo __('Phone Image Height', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('phone_image_height'); ?>" name="<?php echo $this->get_field_name('data'); ?>[phone_image_height]" placeholder="310" value="<?php echo isset($instance['data']['phone_image_height']) ? $instance['data']['phone_image_height'] : '310'; ?>" />
    </div>
    
    <div class="wpl-widget-row wpl-carousel-opt" data-wpl-carousel-type="modern" data-wpl-pl-init="modern:150">
        <label for="<?php echo $this->get_field_id('thumbnail_width'); ?>"><?php echo __('Thumbnail Width', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('thumbnail_width'); ?>" name="<?php echo $this->get_field_name('data'); ?>[thumbnail_width]" value="<?php echo isset($instance['data']['thumbnail_width']) ? $instance['data']['thumbnail_width'] : '150'; ?>" />
    </div>
    
    <div class="wpl-widget-row wpl-carousel-opt" data-wpl-carousel-type="">
        <label for="<?php echo $this->get_field_id('thumbnail_height'); ?>"><?php echo __('Thumbnail Height', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('thumbnail_height'); ?>" name="<?php echo $this->get_field_name('data'); ?>[thumbnail_height]" value="<?php echo isset($instance['data']['thumbnail_height']) ? $instance['data']['thumbnail_height'] : '60'; ?>" />
    </div>

    <div class="wpl-widget-row wpl-carousel-opt" data-wpl-carousel-type="default details multi_images modern modern_full_responsive">
        <label for="<?php echo $this->get_field_id('slide_interval'); ?>"><?php echo __('Slide Interval (ms)', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('slide_interval'); ?>" name="<?php echo $this->get_field_name('data'); ?>[slide_interval]" placeholder="3000" value="<?php echo isset($instance['data']['slide_interval']) ? $instance['data']['slide_interval'] : '3000'; ?>" />
    </div>

    <div class="wpl-widget-row wpl-carousel-opt" data-wpl-carousel-type="default modern_full_responsive">
        <input <?php if(isset($instance['data']['show_nav']) and $instance['data']['show_nav']) echo 'checked="checked"'; ?> value="1" type="checkbox" id="<?php echo $this->get_field_id('data_show_nav'); ?>" name="<?php echo $this->get_field_name('data'); ?>[show_nav]" />
        <label for="<?php echo $this->get_field_id('show_nav'); ?>"><?php echo __('Show Navigation', 'real-estate-listing-realtyna-wpl'); ?></label>
    </div>

    <div class="wpl-widget-row wpl-carousel-opt" data-wpl-carousel-type="modern_full_responsive">
        <input <?php if(isset($instance['data']['hide_pagination']) and $instance['data']['hide_pagination']) echo 'checked="checked"'; ?> value="1" type="checkbox" id="<?php echo $this->get_field_id('data_hide_pagination'); ?>" name="<?php echo $this->get_field_name('data'); ?>[hide_pagination]" />
        <label for="<?php echo $this->get_field_id('hide_pagination'); ?>"><?php echo __('Hide Pagination', 'real-estate-listing-realtyna-wpl'); ?></label>
    </div>

    <div class="wpl-widget-row wpl-carousel-opt" data-wpl-carousel-type="modern_full_responsive">
        <input <?php if(isset($instance['data']['hide_caption']) and $instance['data']['hide_caption']) echo 'checked="checked"'; ?> value="1" type="checkbox" id="<?php echo $this->get_field_id('data_hide_caption'); ?>" name="<?php echo $this->get_field_name('data'); ?>[hide_caption]" />
        <label for="<?php echo $this->get_field_id('hide_caption'); ?>"><?php echo __('Hide Caption', 'real-estate-listing-realtyna-wpl'); ?></label>
    </div>

    <div class="wpl-widget-row wpl-carousel-opt" data-wpl-carousel-type="default details multi_images modern modern_full_responsive">
        <input <?php if(isset($instance['data']['auto_play']) and $instance['data']['auto_play']) echo 'checked="checked"'; ?> value="1" type="checkbox" id="<?php echo $this->get_field_id('data_auto_play'); ?>" name="<?php echo $this->get_field_name('data'); ?>[auto_play]" />
        <label for="<?php echo $this->get_field_id('auto_play'); ?>"><?php echo __('Auto Play', 'real-estate-listing-realtyna-wpl'); ?></label>
    </div>

    <div class="wpl-widget-row wpl-carousel-opt" data-wpl-carousel-type="modern">
        <input <?php if(isset($instance['data']['smart_resize']) and $instance['data']['smart_resize']) echo 'checked="checked"'; ?> value="1" type="checkbox" id="<?php echo $this->get_field_id('data_smart_resize'); ?>" name="<?php echo $this->get_field_name('data'); ?>[smart_resize]" />
        <label for="<?php echo $this->get_field_id('smart_resize'); ?>"><?php echo __('Smart Resize', 'real-estate-listing-realtyna-wpl'); ?></label>
    </div>

	<div class="wpl-widget-row">
        <input <?php if(isset($instance['data']['show_tags']) and $instance['data']['show_tags']) echo 'checked="checked"'; ?> value="1" type="checkbox" id="<?php echo $this->get_field_id('data_show_tags'); ?>" name="<?php echo $this->get_field_name('data'); ?>[show_tags]" />
        <label><?php echo __('Show Tags', 'real-estate-listing-realtyna-wpl'); ?></label>
    </div>
    <div class="wpl-widget-row">
        <input <?php if(isset($instance['data']['lazy_load']) and $instance['data']['lazy_load']) echo 'checked="checked"'; ?> value="1" type="checkbox" id="<?php echo $this->get_field_id('data_lazy_load'); ?>" name="<?php echo $this->get_field_name('data'); ?>[lazy_load]" />
        <label><?php echo __('lazy Load', 'real-estate-listing-realtyna-wpl'); ?></label>
    </div>
	
    <div class="wpl-widget-row wpl-carousel-opt" data-wpl-carousel-type="multi_images">
        <label for="<?php echo $this->get_field_id('images_per_page'); ?>"><?php echo __('Images per Page', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('images_per_page'); ?>" name="<?php echo $this->get_field_name('data'); ?>[images_per_page]" placeholder="3" value="<?php echo isset($instance['data']['images_per_page']) ? $instance['data']['images_per_page'] : '3'; ?>" />
    </div>

    <div class="wpl-widget-row wpl-carousel-opt">
        <label for="<?php echo $this->get_field_id('slide_fillmode'); ?>"><?php echo __('Fill Mode', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('slide_fillmode'); ?>" name="<?php echo $this->get_field_name('data'); ?>[slide_fillmode]">
            <option value="0" <?php echo (isset($instance['data']['slide_fillmode']) and $instance['data']['slide_fillmode'] == 0) ? 'selected="selected"' : ''; ?>><?php echo __('Stretch', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="1" <?php echo (isset($instance['data']['slide_fillmode']) and $instance['data']['slide_fillmode'] == 1) ? 'selected="selected"' : ''; ?>><?php echo __('Contain', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="2" <?php echo (isset($instance['data']['slide_fillmode']) and $instance['data']['slide_fillmode'] == 2) ? 'selected="selected"' : ''; ?>><?php echo __('Cover', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="4" <?php echo (isset($instance['data']['slide_fillmode']) and $instance['data']['slide_fillmode'] == 4) ? 'selected="selected"' : ''; ?>><?php echo __('Actual Size', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="5" <?php echo (isset($instance['data']['slide_fillmode']) and $instance['data']['slide_fillmode'] == 5) ? 'selected="selected"' : ''; ?>><?php echo __('Contain/Actual', 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
    </div>

    <h4><?php echo __('Filter Properties', 'real-estate-listing-realtyna-wpl'); ?></h4>
    <div class="wpl-widget-row">
        <?php $kinds = wpl_flex::get_kinds('wpl_properties'); ?>
        <label for="<?php echo $this->get_field_id('data_kind'); ?>"><?php echo __('Kind', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('data_kind'); ?>" name="<?php echo $this->get_field_name('data'); ?>[kind]">
            <?php foreach($kinds as $kind): ?>
            <option <?php if(isset($instance['data']['kind']) and $instance['data']['kind'] == $kind['id']) echo 'selected="selected"'; ?> value="<?php echo $kind['id']; ?>"><?php echo __($kind['name'], 'real-estate-listing-realtyna-wpl'); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <?php $listings = wpl_global::get_listings(); ?>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_listing'); ?>"><?php echo __('Listing', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('data_listing'); ?>" name="<?php echo $this->get_field_name('data'); ?>[listing]">
            <option value="-1"><?php echo __('All', 'real-estate-listing-realtyna-wpl'); ?></option>
            <?php foreach($listings as $listing): ?>
            <option <?php if(isset($instance['data']['listing']) and $instance['data']['listing'] == $listing['id']) echo 'selected="selected"'; ?> value="<?php echo $listing['id']; ?>"><?php echo __($listing['name'], 'real-estate-listing-realtyna-wpl'); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <?php $property_types = wpl_global::get_property_types(); ?>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_property_type'); ?>"><?php echo __('Property type', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('data_property_type'); ?>" name="<?php echo $this->get_field_name('data'); ?>[property_type]">
            <option value="-1"><?php echo __('All', 'real-estate-listing-realtyna-wpl'); ?></option>
            <?php foreach($property_types as $property_type): ?>
            <option <?php if(isset($instance['data']['property_type']) and $instance['data']['property_type'] == $property_type['id']) echo 'selected="selected"'; ?> value="<?php echo $property_type['id']; ?>"><?php echo __($property_type['name'], 'real-estate-listing-realtyna-wpl'); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php for ($i = 2; $i < 8; $i++): if(!trim($location_settings['location'.$i.'_keyword'])) continue; ?>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_location'.$i.'_name'); ?>"><?php echo __($location_settings['location'.$i.'_keyword'], 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('data_location'.$i.'_name'); ?>" name="<?php echo $this->get_field_name('data'); ?>[location<?php echo $i; ?>_name]" value="<?php echo isset($instance['data']['location'.$i.'_name']) ? $instance['data']['location'.$i.'_name'] : ''; ?>" />
    </div>
    <?php endfor; ?>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_zip_name'); ?>"><?php echo __('Zip-Code', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('data_zip_name'); ?>" name="<?php echo $this->get_field_name('data'); ?>[zip_name]" value="<?php echo isset($instance['data']['zip_name']) ? $instance['data']['zip_name'] : ''; ?>" />
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_build_year'); ?>"><?php echo __('Year Built Range', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('data_build_year'); ?>" name="<?php echo $this->get_field_name('data'); ?>[build_year]" value="<?php echo isset($instance['data']['build_year']) ? $instance['data']['build_year'] : ''; ?>" />
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_living_area'); ?>"><?php echo __('SqFt Range', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('data_living_area'); ?>" name="<?php echo $this->get_field_name('data'); ?>[living_area]" value="<?php echo isset($instance['data']['living_area']) ? $instance['data']['living_area'] : ''; ?>" />
    </div>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_price'); ?>"><?php echo __('Price Range', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('data_price'); ?>" name="<?php echo $this->get_field_name('data'); ?>[price]" value="<?php echo isset($instance['data']['price']) ? $instance['data']['price'] : ''; ?>" />
    </div>
    
    <?php if(wpl_global::check_addon('complex')): ?>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_parent'); ?>"><?php echo __('Parent', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('data_parent'); ?>" title="<?php echo __('Listing ID of parent property', 'real-estate-listing-realtyna-wpl'); ?>" name="<?php echo $this->get_field_name('data'); ?>[parent]" value="<?php echo isset($instance['data']['parent']) ? $instance['data']['parent'] : ''; ?>" />
    </div>
    <div class="wpl-widget-row">
        <input <?php if(isset($instance['data']['auto_parent']) and $instance['data']['auto_parent']) echo 'checked="checked"'; ?> value="1" type="checkbox" id="<?php echo $this->get_field_id('data_auto_parent'); ?>" name="<?php echo $this->get_field_name('data'); ?>[auto_parent]" />
        <label for="<?php echo $this->get_field_id('data_auto_parent'); ?>" title="<?php echo __('For single property pages.', 'real-estate-listing-realtyna-wpl'); ?>"><?php echo __('Detect parent automatically.', 'real-estate-listing-realtyna-wpl'); ?></label>
    </div>
    <?php endif; ?>
    
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_listing_ids'); ?>"><?php echo __('Listing IDs (Comma Separated)', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('data_listing_ids'); ?>" name="<?php echo $this->get_field_name('data'); ?>[listing_ids]" value="<?php echo isset($instance['data']['listing_ids']) ? $instance['data']['listing_ids'] : ''; ?>" />
    </div>

    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_agent_ids'); ?>"><?php echo __('Agent IDs (Comma Separated)', 'real-estate-listing-realtyna-wpl'); ?></label>
        <input type="text" id="<?php echo $this->get_field_id('data_agent_ids'); ?>" name="<?php echo $this->get_field_name('data'); ?>[agent_ids]" value="<?php echo isset($instance['data']['agent_ids']) ? $instance['data']['agent_ids'] : ''; ?>" />
    </div>
    
    <div class="wpl-widget-row">
        <input <?php if(isset($instance['data']['random']) and $instance['data']['random']) echo 'checked="checked"'; ?> value="1" type="checkbox" id="<?php echo $this->get_field_id('data_random'); ?>" name="<?php echo $this->get_field_name('data'); ?>[random]" onclick="random_clicked<?php echo $this->widget_id; ?>();" />
        <label for="<?php echo $this->get_field_id('data_random'); ?>"><?php echo __('Random', 'real-estate-listing-realtyna-wpl'); ?></label>
    </div>
    
    <?php
    $tags = wpl_flex::get_fields(NULL, NULL, NULL, NULL, NULL, "AND `type`='tag' AND `enabled`>='1' GROUP BY `table_column`");
    foreach($tags as $tag)
    {
        $tagkey = 'only_'.ltrim($tag->table_column, 'sp_');
    ?>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_'.$tagkey); ?>" class="<?php echo $this->get_field_id('data_tags_label'); ?>"><?php echo sprintf(__('Only %s', 'real-estate-listing-realtyna-wpl'), $tag->name); ?></label>
        <select id="<?php echo $this->get_field_id('data_'.$tagkey); ?>" name="<?php echo $this->get_field_name('data'); ?>[<?php echo $tagkey; ?>]">
            <option value="0" <?php if(isset($instance['data'][$tagkey]) and $instance['data'][$tagkey] == 0) echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="1" <?php if(isset($instance['data'][$tagkey]) and $instance['data'][$tagkey] == 1) echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
    </div>
    <?php } ?>

    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('tag_group_join_type_or'); ?>">
            <input <?php if(isset($instance['data']['tag_group_join_type']) and
                            $instance['data']['tag_group_join_type'] == 'or') echo
        'checked="checked"'; ?> value="or" type="radio" id="<?php echo $this->get_field_id('tag_group_join_type_or'); ?>" name="<?php echo $this->get_field_name('data'); ?>[tag_group_join_type]" onclick="" />
        <?php echo __('Or', 'real-estate-listing-realtyna-wpl'); ?>
        </label>

        <label for="<?php echo $this->get_field_id('tag_group_join_type_and'); ?>">
            <input <?php if(!isset($instance['data']['tag_group_join_type']) or (isset($instance['data']['tag_group_join_type']) and
                            $instance['data']['tag_group_join_type'] == 'and')) echo
        'checked="checked"'; ?> value="and" type="radio" id="<?php echo $this->get_field_id('tag_group_join_type_and'); ?>" name="<?php echo $this->get_field_name('data'); ?>[tag_group_join_type]" onclick="" />
        <?php echo __('And', 'real-estate-listing-realtyna-wpl'); ?>
        </label>
    </div>

    <h4><?php echo __('Similar Properties', 'real-estate-listing-realtyna-wpl'); ?></h4>
    <div class="wpl-widget-row">
        <label for="<?php echo $this->get_field_id('data_sml_only_similars'); ?>"><?php echo __('Only Similars', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $this->get_field_id('data_sml_only_similars'); ?>" name="<?php echo $this->get_field_name('data'); ?>[sml_only_similars]" onchange="wpl_carousel_toggle<?php echo $this->widget_id; ?>('<?php echo $this->get_field_id('data_sml_fields_container'); ?>');">
            <option value="0" <?php if(isset($instance['data']['sml_only_similars']) and $instance['data']['sml_only_similars'] == 0) echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="1" <?php if(isset($instance['data']['sml_only_similars']) and $instance['data']['sml_only_similars'] == 1) echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
    </div>
    
    <div id="<?php echo $this->get_field_id('data_sml_fields_container'); ?>" style="<?php echo ((!isset($instance['data']['sml_only_similars']) or (isset($instance['data']['sml_only_similars']) and !$instance['data']['sml_only_similars'])) ? 'display: none;' : ''); ?>">
        <div class="wpl-widget-row">
            <label for="<?php echo $this->get_field_id('data_sml_inc_listing'); ?>"><?php echo __('Include Listings', 'real-estate-listing-realtyna-wpl'); ?></label>
            <select id="<?php echo $this->get_field_id('data_sml_inc_listing'); ?>" name="<?php echo $this->get_field_name('data'); ?>[sml_inc_listing]">
                <option value="1" <?php if(isset($instance['data']['sml_inc_listing']) and $instance['data']['sml_inc_listing'] == 1) echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
                <option value="0" <?php if(isset($instance['data']['sml_inc_listing']) and $instance['data']['sml_inc_listing'] == 0) echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
            </select>
        </div>

        <div class="wpl-widget-row">
            <input onchange="wpl_carousel_toggle<?php echo $this->widget_id; ?>('<?php echo $this->get_field_id('data_sml_override_listing_container'); ?>')" <?php if(isset($instance['data']['sml_override_listing']) and $instance['data']['sml_override_listing']) echo 'checked="checked"'; ?> value="1" type="checkbox" id="<?php echo $this->get_field_id('sml_override_listing'); ?>" name="<?php echo $this->get_field_name('data'); ?>[sml_override_listing]" />
            <label for="<?php echo $this->get_field_id('sml_override_listing'); ?>"><?php echo __('Override Listing Type', 'real-estate-listing-realtyna-wpl'); ?></label>
        </div>
        <div id="<?php echo $this->get_field_id('data_sml_override_listing_container'); ?>" style="<?php echo ((!isset($instance['data']['sml_override_listing']) or (isset($instance['data']['sml_override_listing']) and !$instance['data']['sml_override_listing'])) ? 'display: none;' : ''); ?>">
            <div class="wpl-widget-row">
                <label for="<?php echo $this->get_field_id('sml_override_listing_new'); ?>"><?php echo __('Show', 'real-estate-listing-realtyna-wpl'); ?></label>
                <select id="<?php echo $this->get_field_id('sml_override_listing_new'); ?>" name="<?php echo $this->get_field_name('data'); ?>[sml_override_listing_new]">
                    <?php foreach($listings as $listing): ?>
                        <option <?php if(isset($instance['data']['sml_override_listing_new']) and $instance['data']['sml_override_listing_new'] == $listing['id']) echo 'selected="selected"'; ?> value="<?php echo $listing['id']; ?>"><?php echo __($listing['name'], 'real-estate-listing-realtyna-wpl'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="wpl-widget-row">
                <label for="<?php echo $this->get_field_id('sml_override_listing_old'); ?>"><?php echo __('when current listing type is', 'real-estate-listing-realtyna-wpl'); ?></label>
                <select id="<?php echo $this->get_field_id('sml_override_listing_old'); ?>" name="<?php echo $this->get_field_name('data'); ?>[sml_override_listing_old]">
                    <?php foreach($listings as $listing): ?>
                        <option <?php if(isset($instance['data']['sml_override_listing_old']) and $instance['data']['sml_override_listing_old'] == $listing['id']) echo 'selected="selected"'; ?> value="<?php echo $listing['id']; ?>"><?php echo __($listing['name'], 'real-estate-listing-realtyna-wpl'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="wpl-widget-row">
            <label for="<?php echo $this->get_field_id('data_sml_inc_property_type'); ?>"><?php echo __('Include Property Type', 'real-estate-listing-realtyna-wpl'); ?></label>
            <select id="<?php echo $this->get_field_id('data_sml_inc_property_type'); ?>" name="<?php echo $this->get_field_name('data'); ?>[sml_inc_property_type]">
                <option value="1" <?php if(isset($instance['data']['sml_inc_property_type']) and $instance['data']['sml_inc_property_type'] == 1) echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
                <option value="0" <?php if(isset($instance['data']['sml_inc_property_type']) and $instance['data']['sml_inc_property_type'] == 0) echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
            </select>
        </div>

        <div class="wpl-widget-row">
            <label for="<?php echo $this->get_field_id('data_sml_inc_agent'); ?>"><?php echo __('Include Agent', 'real-estate-listing-realtyna-wpl'); ?></label>
            <select id="<?php echo $this->get_field_id('data_sml_inc_agent'); ?>" name="<?php echo $this->get_field_name('data'); ?>[sml_inc_agent]">
                <option value="0" <?php if(isset($instance['data']['sml_inc_agent']) and $instance['data']['sml_inc_agent'] == 0) echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
                <option value="1" <?php if(isset($instance['data']['sml_inc_agent']) and $instance['data']['sml_inc_agent'] == 1) echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
            </select>
        </div>

        <div class="wpl-widget-row">
            <label for="<?php echo $this->get_field_id('data_sml_inc_price'); ?>"><?php echo __('Include Price', 'real-estate-listing-realtyna-wpl'); ?></label>
            <select id="<?php echo $this->get_field_id('data_sml_inc_price'); ?>" name="<?php echo $this->get_field_name('data'); ?>[sml_inc_price]" onchange="wpl_carousel_toggle<?php echo $this->widget_id; ?>('<?php echo $this->get_field_id('data_sml_price_container'); ?>');">
                <option value="1" <?php if(isset($instance['data']['sml_inc_price']) and $instance['data']['sml_inc_price'] == 1) echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
                <option value="0" <?php if(isset($instance['data']['sml_inc_price']) and $instance['data']['sml_inc_price'] == 0) echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
            </select>
        </div>
        
        <div id="<?php echo $this->get_field_id('data_sml_price_container'); ?>" style="<?php echo ((!isset($instance['data']['sml_inc_price']) or (isset($instance['data']['sml_inc_price']) and !$instance['data']['sml_inc_price'])) ? 'display: none;' : ''); ?>">
            <div class="wpl-widget-row">
                <label for="<?php echo $this->get_field_id('data_sml_price_down_rate'); ?>"><?php echo __('Price Down Rate', 'real-estate-listing-realtyna-wpl'); ?></label>
                <input type="text" id="<?php echo $this->get_field_id('data_sml_price_down_rate'); ?>" name="<?php echo $this->get_field_name('data'); ?>[sml_price_down_rate]" value="<?php echo isset($instance['data']['sml_price_down_rate']) ? $instance['data']['sml_price_down_rate'] : '0.8'; ?>" />
            </div>

            <div class="wpl-widget-row">
                <label for="<?php echo $this->get_field_id('data_sml_price_up_rate'); ?>"><?php echo __('Price Up Rate', 'real-estate-listing-realtyna-wpl'); ?></label>
                <input type="text" id="<?php echo $this->get_field_id('data_sml_price_up_rate'); ?>" name="<?php echo $this->get_field_name('data'); ?>[sml_price_up_rate]" value="<?php echo isset($instance['data']['sml_price_up_rate']) ? $instance['data']['sml_price_up_rate'] : '1.2'; ?>" />
            </div>
        </div>
        
        <div class="wpl-widget-row">
            <label for="<?php echo $this->get_field_id('data_sml_inc_radius'); ?>"><?php echo __('Include Radius', 'real-estate-listing-realtyna-wpl'); ?></label>
            <select id="<?php echo $this->get_field_id('data_sml_inc_radius'); ?>" name="<?php echo $this->get_field_name('data'); ?>[sml_inc_radius]" onchange="wpl_carousel_toggle<?php echo $this->widget_id; ?>('<?php echo $this->get_field_id('data_sml_radius_container'); ?>');">
                <option value="0" <?php if(isset($instance['data']['sml_inc_radius']) and $instance['data']['sml_inc_radius'] == 0) echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
                <option value="1" <?php if(isset($instance['data']['sml_inc_radius']) and $instance['data']['sml_inc_radius'] == 1) echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
            </select>
        </div>
        
        <div id="<?php echo $this->get_field_id('data_sml_radius_container'); ?>" style="<?php echo ((!isset($instance['data']['sml_inc_radius']) or (isset($instance['data']['sml_inc_radius']) and !$instance['data']['sml_inc_radius'])) ? 'display: none;' : ''); ?>">
            <div class="wpl-widget-row">
                <label for="<?php echo $this->get_field_id('data_sml_radius'); ?>"><?php echo __('Radius', 'real-estate-listing-realtyna-wpl'); ?></label>
                <input type="text" id="<?php echo $this->get_field_id('data_sml_radius'); ?>" name="<?php echo $this->get_field_name('data'); ?>[sml_radius]" value="<?php echo isset($instance['data']['sml_radius']) ? $instance['data']['sml_radius'] : '2000'; ?>" />
            </div>

            <?php $units = wpl_units::get_units(1); ?>
            <div class="wpl-widget-row">
                <label for="<?php echo $this->get_field_id('data_sml_radius_unit'); ?>"><?php echo __('Radius Unit', 'real-estate-listing-realtyna-wpl'); ?></label>
                <select id="<?php echo $this->get_field_id('data_sml_radius_unit'); ?>" name="<?php echo $this->get_field_name('data'); ?>[sml_radius_unit]">
                    <?php foreach($units as $unit): ?>
                    <option value="<?php echo $unit['id']; ?>" <?php if(isset($instance['data']['sml_radius_unit']) and $instance['data']['sml_radius_unit'] == $unit['id']) echo 'selected="selected"'; ?>><?php echo __($unit['name'], 'real-estate-listing-realtyna-wpl'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="wpl-widget-row">
            <label for="<?php echo $this->get_field_id('data_sml_zip_code'); ?>"><?php echo __('Include Zip-Code', 'real-estate-listing-realtyna-wpl'); ?></label>
            <select id="<?php echo $this->get_field_id('data_sml_zip_code'); ?>" name="<?php echo $this->get_field_name('data'); ?>[data_sml_zip_code]">
                <option value="0" <?php if(isset($instance['data']['data_sml_zip_code']) and $instance['data']['data_sml_zip_code'] == 0) echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
                <option value="1" <?php if(isset($instance['data']['data_sml_zip_code']) and $instance['data']['data_sml_zip_code'] == 1) echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
            </select>
        </div>
    </div>
    
    <h4><?php echo __('Sort and Limit', 'real-estate-listing-realtyna-wpl'); ?></h4>
    <?php $sort_options = wpl_sort_options::render(wpl_sort_options::get_sort_options(0)); ?>
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
        <label for="<?php echo $this->get_field_id('data_limit'); ?>"><?php echo __('Number of properties', 'real-estate-listing-realtyna-wpl'); ?></label>
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