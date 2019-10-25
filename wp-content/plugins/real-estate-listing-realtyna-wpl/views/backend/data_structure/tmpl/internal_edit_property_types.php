<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="fanc-content size-width-1">
    <h2><?php echo (isset($this->property_type_data->name) ? $this->property_type_data->name : __('Add new property type', 'real-estate-listing-realtyna-wpl')); ?></h2>
    <div class="fanc-body">
        <div class="fanc-row">
            <label for="wpl_title<?php echo $this->property_type_id; ?>"><?php echo __('Name', 'real-estate-listing-realtyna-wpl'); ?></label>
            <input class="text_box" type="text" id="wpl_name<?php echo $this->property_type_id; ?>" value="<?php echo (isset($this->property_type_data->name) ? $this->property_type_data->name : ''); ?>" onchange="wpl_ajax_save_property_type('name', this, '<?php echo $this->property_type_id; ?>');" autocomplete="off" />
            <span class="ajax-inline-save" id="wpl_name<?php echo $this->property_type_id; ?>_ajax_loader"></span>
        </div>
        <div class="fanc-row">
            <label for="wpl_parent<?php echo $this->property_type_id; ?>"><?php echo __('Category', 'real-estate-listing-realtyna-wpl'); ?></label>
            <select class="text_box" id="wpl_parent<?php echo $this->property_type_id; ?>" onchange="wpl_ajax_save_property_type('parent', this, '<?php echo $this->property_type_id; ?>');" autocomplete="off">
                <?php foreach($this->property_types_category as $property_types_category): ?>
				<option <?php if(isset($this->property_type_data->parent) and $property_types_category["id"] == $this->property_type_data->parent): ?> selected="selected" <?php endif; ?> value="<?php echo $property_types_category["id"] ?>"><?php echo $property_types_category["name"] ?></option>
                <?php endforeach; ?>
            </select>
            <span class="ajax-inline-save" id="wpl_parent<?php echo $this->property_type_id; ?>_ajax_loader"></span>
        </div>
        <?php if(wpl_global::check_addon('demographic')): ?>
        <div class="fanc-row">
            <label for="wpl_show_marker<?php echo $this->property_type_id; ?>"><?php echo __('Show Marker', 'real-estate-listing-realtyna-wpl'); ?></label>
            <select class="text_box" id="wpl_show_marker<?php echo $this->property_type_id; ?>" onchange="wpl_ajax_save_property_type('show_marker', this, '<?php echo $this->property_type_id; ?>');" autocomplete="off">
                <option value="1" <?php echo ((isset($this->property_type_data->show_marker) and $this->property_type_data->show_marker == 1) ? 'selected="selected"' : ''); ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
                <option value="0" <?php echo ((isset($this->property_type_data->show_marker) and $this->property_type_data->show_marker == 0) ? 'selected="selected"' : ''); ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
            </select>
            <span class="ajax-inline-save" id="wpl_show_marker<?php echo $this->property_type_id; ?>_ajax_loader"></span>
        </div>
        <?php endif; ?>
        <?php if($this->property_type_id === 10000): ?>
        <div class="fanc-row">
            <label></label>
            <input type="button" class="wpl-button button-1" onclick="wpl_ajax_insert_property_type(<?php echo $this->property_type_id; ?>);" value="<?php echo __('Save', 'real-estate-listing-realtyna-wpl'); ?>"/>
        </div>
		<?php endif; ?>
        <div class="wpl_show_message<?php echo $this->property_type_id; ?>" style="margin: 0 10px;"></div>
    </div>
</div>