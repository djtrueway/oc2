<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
$kind = trim(wpl_request::getVar('kind')) != '' ? wpl_request::getVar('kind') : 0;
?>
<div class="fanc-content size-width-1">
    <h2><?php echo isset($this->category) ? __('Edit Category', 'real-estate-listing-realtyna-wpl') : __('Add Category', 'real-estate-listing-realtyna-wpl'); ?></h2>
    <div class="wpl_show_message" id="wpl_category_form_message"></div>
    <div class="fanc-body">
        <div class="fanc-row">
            <label for="category_name"><?php echo __('Name','real-estate-listing-realtyna-wpl'); ?></label>
            <input class="text_box" type="text" id="category_name" value="<?php echo isset($this->category) ? $this->category->name : ''; ?>" />
            <input type="hidden" id="category_kind" value="<?php echo $kind; ?>" />
        </div>
        <div class="fanc-row fanc-button-row">
            <div id="wpl_category_form_loader"></div>
            <input class="wpl-button button-1" onclick="wpl_save_category('<?php echo isset($this->category) ? $this->category->id : ''; ?>')" value="<?php echo __('Save','real-estate-listing-realtyna-wpl'); ?>" type="button">
        </div>
    </div>
</div>