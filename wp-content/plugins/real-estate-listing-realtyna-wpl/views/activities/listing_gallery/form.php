<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');

$general_watermark_status = wpl_settings::get('watermark_status');
$categories = wpl_items::get_item_categories('gallery');
?>
<div class="fanc-row">
    <label for="wpl_o_image_width"><?php echo __('Image width', 'real-estate-listing-realtyna-wpl'); ?></label>
    <input class="text_box" name="option[image_width]" type="text" id="wpl_o_image_width" value="<?php echo isset($this->options->image_width) ? $this->options->image_width : '285'; ?>" />
</div>
<div class="fanc-row">
    <label for="wpl_o_image_height"><?php echo __('Image height', 'real-estate-listing-realtyna-wpl'); ?></label>
    <input class="text_box" name="option[image_height]" type="text" id="wpl_o_image_height" value="<?php echo isset($this->options->image_height) ? $this->options->image_height : '140'; ?>" />
</div>
<div class="fanc-row">
    <label for="wpl_o_image_class"><?php echo __('Image class', 'real-estate-listing-realtyna-wpl'); ?></label>
    <input class="text_box" name="option[image_class]" type="text" id="wpl_o_image_class" value="<?php echo isset($this->options->image_class) ? $this->options->image_class : ''; ?>" />
</div>
<div class="fanc-row">
    <label for="wpl_o_category"><?php echo __('Category', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select class="text_box" name="option[category]" type="text" id="wpl_o_category">
        <option value="" <?php if(isset($this->options->category) and $this->options->category == '') echo 'selected="selected"'; ?>><?php echo __('All', 'real-estate-listing-realtyna-wpl'); ?></option>
        <?php foreach($categories as $category): ?>
        <option value="<?php echo $category->category_name; ?>" <?php if(isset($this->options->category) and $this->options->category == $category->category_name) echo 'selected="selected"'; ?>><?php echo __($category->category_name, 'real-estate-listing-realtyna-wpl'); ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="fanc-row">
    <label for="wpl_o_autoplay"><?php echo __('Autoplay', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select class="text_box" name="option[autoplay]" type="text" id="wpl_o_autoplay">
        <option value="1" <?php if(isset($this->options->autoplay) and $this->options->autoplay == '1') echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="0" <?php if(isset($this->options->autoplay) and $this->options->autoplay == '0') echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>

<div class="fanc-row">
    <label for="wpl_o_lazyload"><?php echo __('Lazyload', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select class="text_box" name="option[lazyload]" type="text" id="wpl_o_lazyload">
        <option value="0" <?php if(isset($this->options->lazyload) and $this->options->lazyload == '0') echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="1" <?php if(isset($this->options->lazyload) and $this->options->lazyload == '1') echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>

<div class="fanc-row">
    <label for="wpl_o_resize"><?php echo __('Resize', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select class="text_box" name="option[resize]" type="text" id="wpl_o_resize">
        <option value="1" <?php if(isset($this->options->resize) and $this->options->resize == '1') echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
	    <option value="0" <?php if(isset($this->options->resize) and $this->options->resize == '0') echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>
<div class="fanc-row">
    <label for="wpl_o_rewrite"><?php echo __('Rewrite', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select class="text_box" name="option[rewrite]" type="text" id="wpl_o_rewrite">
	    <option value="0" <?php if(isset($this->options->rewrite) and $this->options->rewrite == '0') echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="1" <?php if(isset($this->options->rewrite) and $this->options->rewrite == '1') echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>
<div class="fanc-row">
    <label for="wpl_o_rewrite"><?php echo __('Show Image Description', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select class="text_box" name="option[imgdesc]" type="text" id="wpl_o_imgdesc">
        <option value="0" <?php if(isset($this->options->imgdesc) and $this->options->imgdesc == '0') echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="1" <?php if(isset($this->options->imgdesc) and $this->options->imgdesc == '1') echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>
<div class="fanc-row">
    <label for="wpl_o_watermark"><?php echo __('Watermark', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select class="text_box" name="option[watermark]" type="text" id="wpl_o_watermark" <?php echo !$general_watermark_status ? 'disabled' : '' ?>>
	    <option value="0" <?php if(isset($this->options->watermark) and $this->options->watermark == '0') echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="1" <?php if(isset($this->options->watermark) and $this->options->watermark == '1') echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
    <?php if(!$general_watermark_status): ?>
    <div class="gray_tip">
        <?php 
            $setting_link = '<a href="'.wpl_global::get_wpl_admin_menu('wpl_admin_settings').'#Gallery" target="_blank">'.__('general watermarking', 'real-estate-listing-realtyna-wpl').'</a>';
            echo sprintf(__('To enable this option, you should enable %s first.', 'real-estate-listing-realtyna-wpl'), $setting_link);
        ?>
    </div>
    <?php endif; ?>
</div>
<div class="fanc-row">
    <label for="wpl_o_thumbnail"><?php echo __('Thumbnail', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select class="text_box" name="option[thumbnail]" type="text" id="wpl_o_thumbnail">
        <option value="0" <?php if(isset($this->options->thumbnail) and $this->options->thumbnail == '0') echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="1" <?php if(isset($this->options->thumbnail) and $this->options->thumbnail == '1') echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>
<div class="fanc-row">
    <label for="wpl_o_thumbnail_width"><?php echo __('Thumbnail width', 'real-estate-listing-realtyna-wpl'); ?></label>
    <input class="text_box" name="option[thumbnail_width]" type="text" id="wpl_o_thumbnail_width" value="<?php echo isset($this->options->thumbnail_width) ? $this->options->thumbnail_width : '100'; ?>" />
</div>
<div class="fanc-row">
    <label for="wpl_o_thumbnail_height"><?php echo __('Thumbnail height', 'real-estate-listing-realtyna-wpl'); ?></label>
    <input class="text_box" name="option[thumbnail_height]" type="text" id="wpl_o_thumbnail_height" value="<?php echo isset($this->options->thumbnail_height) ? $this->options->thumbnail_height : '80'; ?>" />
</div>
<div class="fanc-row">
    <label for="wpl_o_thumbnail_numbers"><?php echo __('Thumbnail numbers', 'real-estate-listing-realtyna-wpl'); ?></label>
    <input class="text_box" name="option[thumbnail_numbers]" type="text" id="wpl_o_thumbnail_numbers" value="<?php echo isset($this->options->thumbnail_numbers) ? $this->options->thumbnail_numbers : '20'; ?>" />
</div>
<div class="fanc-row">
    <label><?php echo __('Show Tags', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select class="text_box" name="option[show_tags]" type="text" id="wpl_o_show_tags">
        <option value="0" <?php if(isset($this->options->show_tags) and $this->options->show_tags == '0') echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="1" <?php if(isset($this->options->show_tags) and $this->options->show_tags == '1') echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>