<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

if((isset($values->specificable) and $values->specificable) or !$dbst_id)
{
?>
<div class="fanc-row">
    <label for="<?php echo $__prefix; ?>specificable"><?php echo __('Specificable', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select id="<?php echo $__prefix; ?>specificable" name="<?php echo $__prefix; ?>specificable" onchange="wpl_flex_change_specificable(this.value, '<?php echo $__prefix; ?>');">
        <option value="0"><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="1" <?php if(isset($values->listing_specific) and trim($values->listing_specific) != '') echo 'selected="selected"'; ?>><?php echo __('Listing specific', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="2" <?php if(isset($values->property_type_specific) and trim($values->property_type_specific) != '') echo 'selected="selected"'; ?>><?php echo __('Property type specific', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="4" <?php if(isset($values->field_specific) and trim($values->field_specific) != '') echo 'selected="selected"'; ?>><?php echo __('Field specific', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
    <div class="wpl_flex_specificable_cnt" id="<?php echo $__prefix; ?>specificable1" style="<?php if(!isset($values->listing_specific) or (isset($values->listing_specific) and trim($values->listing_specific) == '')) echo 'display: none;'; ?>">
        <?php if(!$dbst_id or (isset($values->specificable) and ($values->specificable == 1 or $values->specificable == 2))): ?>
        <ul id="<?php echo $__prefix ?>_listing_specific" class="wpl_listing_specific_ul">
            <li><input id="wpl_flex_listing_checkbox_all" type="checkbox" onclick="wpl_listing_specific_all(this.checked)" <?php if(!isset($values->listing_specific) or (isset($values->listing_specific) and trim($values->listing_specific) == '')) echo 'checked="checked"'; ?> /><label class="wpl_specific_label" for="wpl_flex_listing_checkbox_all">&nbsp;<?php echo __('All', 'real-estate-listing-realtyna-wpl'); ?></label></li>
            <?php
            $listing_specific = isset($values->listing_specific) ? explode(',', $values->listing_specific) : array();
            foreach($listings as $listing)
            {
                ?>
                <li><input id="wpl_flex_listing_checkbox<?php echo $listing['id']; ?>" type="checkbox" value="<?php echo $listing['id']; ?>" <?php if(!isset($values->listing_specific) or (isset($values->listing_specific) and trim($values->listing_specific) == '') or in_array($listing['id'], $listing_specific)) echo 'checked="checked"'; if(!isset($values->listing_specific) or (isset($values->listing_specific) and trim($values->listing_specific) == '')) echo 'disabled="disabled"'; ?> /><label class="wpl_specific_label" for="wpl_flex_listing_checkbox<?php echo $listing['id']; ?>">&nbsp;<?php echo __($listing['name'], 'real-estate-listing-realtyna-wpl'); ?></label></li>
                <?php
            }
            ?>
        </ul>
        <?php endif; ?>
    </div>
    <div class="wpl_flex_specificable_cnt" id="<?php echo $__prefix; ?>specificable2" style="<?php if(!isset($values->property_type_specific) or (isset($values->property_type_specific) and trim($values->property_type_specific) == '')) echo 'display: none;'; ?>">
        <?php if(!$dbst_id or (isset($values->specificable) and ($values->specificable == 1 or $values->specificable == 3))): ?>
        <ul id="<?php echo $__prefix ?>_property_type_specific" class="wpl_property_type_specific_ul">
            <li><input id="wpl_flex_property_type_checkbox_all" type="checkbox" onclick="wpl_property_type_specific_all(this.checked)" <?php if(!isset($values->property_type_specific) or (isset($values->property_type_specific) and trim($values->property_type_specific) == '')) echo 'checked="checked"'; ?> /><label class="wpl_specific_label" for="wpl_flex_property_type_checkbox_all">&nbsp;<?php echo __('All', 'real-estate-listing-realtyna-wpl'); ?></label></li>
            <?php
            $property_type_specific = isset($values->property_type_specific) ? explode(',', $values->property_type_specific) : array();
            foreach($property_types as $property_type)
            {
                ?>
                <li><input id="wpl_flex_property_type_checkbox<?php echo $property_type['id']; ?>" type="checkbox" value="<?php echo $property_type['id']; ?>" <?php if(!isset($values->property_type_specific) or (isset($values->property_type_specific) and trim($values->property_type_specific) == '') or in_array($property_type['id'], $property_type_specific)) echo 'checked="checked"'; if(!isset($values->property_type_specific) or (isset($values->property_type_specific) and trim($values->property_type_specific) == '')) echo 'disabled="disabled"'; ?> /><label class="wpl_specific_label" for="wpl_flex_property_type_checkbox<?php echo $property_type['id']; ?>">&nbsp;<?php echo __($property_type['name'], 'real-estate-listing-realtyna-wpl'); ?></label></li>
                <?php
            }
            ?>
        </ul>
        <?php endif; ?>
    </div>

    <div class="wpl_flex_specificable_cnt" id="<?php echo $__prefix; ?>specificable4" style="<?php if(!isset($values->field_specific) or (isset($values->field_specific) and trim($values->field_specific) == '')) echo 'display: none;'; ?>">
        <?php if(!$dbst_id or (isset($values->specificable) and ($values->specificable == 1 or $values->specificable == 4))): ?>
        
        <?php 
        $field_name = '';
        $field_value = '';
        $fields = wpl_flex::get_fields('', 0, 0, '', '', "AND `type` IN ('feature','neighborhood','boolean','checkbox','') AND `kind` = '$kind' AND `enabled` > 0");
        
        if(isset($values->field_specific) and trim($values->field_specific) != '')
        {
            $value = explode(':', $values->field_specific);
            $field_name = $value[0];
            $field_value = $value[1];
        }
        ?>

        <label for="<?php echo $__prefix; ?>field_specific_name"><?php echo __('Field', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $__prefix; ?>field_specific_name" name="<?php echo $__prefix; ?>field_specific_name" onchange="wpl_flex_change_field_specific_fields(this.value, '<?php echo $__prefix; ?>');">
            <?php foreach ($fields as $field): ?>
                <option value="<?php echo $field->id; ?>" <?php if($field_name == $field->id) echo 'selected="selected"'; ?>><?php echo __($field->name, 'real-estate-listing-realtyna-wpl'); ?></option>
            <?php endforeach; ?>            
        </select>

        <label for="<?php echo $__prefix; ?>field_specific_value"><?php echo __('Value', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="<?php echo $__prefix; ?>field_specific_value" name="<?php echo $__prefix; ?>field_specific_value">
            <option value="0" <?php if($field_value == 0) echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="1" <?php if($field_value == 1) echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>

        <?php endif; ?>
    </div>
</div>
<?php
}
?>