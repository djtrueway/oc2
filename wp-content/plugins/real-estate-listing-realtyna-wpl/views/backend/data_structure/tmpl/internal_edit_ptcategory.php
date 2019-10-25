<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="fanc-content size-width-1">
    <h2><?php echo (isset($this->ptcategory_data->name) ? $this->ptcategory_data->name : __('Add new category', 'real-estate-listing-realtyna-wpl')); ?></h2>
    <div class="fanc-body">
        <div class="fanc-row">
            <label for="wpl_title<?php echo $this->ptcategory_id; ?>"><?php echo __('Name', 'real-estate-listing-realtyna-wpl'); ?></label>
            <input class="text_box" type="text" id="wpl_name<?php echo $this->ptcategory_id; ?>" value="<?php echo (isset($this->ptcategory_data->name) ? $this->ptcategory_data->name : ''); ?>" onchange="wpl_ajax_save_property_type('name', this, '<?php echo $this->ptcategory_id; ?>');" autocomplete="off" />
            <span class="ajax-inline-save" id="wpl_name<?php echo $this->ptcategory_id; ?>_ajax_loader"></span>
        </div>
        <?php if($this->ptcategory_id === 10000): ?>
        <div class="fanc-row">
            <label></label>
            <input type="button" class="wpl-button button-1" onclick="wpl_ajax_insert_ptcategory(<?php echo $this->ptcategory_id; ?>);" value="<?php echo __('Save', 'real-estate-listing-realtyna-wpl'); ?>"/>
        </div>
		<?php endif; ?>
        <div class="wpl_show_message<?php echo $this->ptcategory_id; ?>" style="margin: 0 10px;"></div>
    </div>
</div>