<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

if($type == 'boolean' and !$done_this)
{
    $true_label = isset($options['true_label']) ? $options['true_label'] : 'Yes';
    $false_label = isset($options['false_label']) ? $options['false_label'] : 'No';
?>
<label for="wpl_c_<?php echo $field->id; ?>"><?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?><?php if(in_array($mandatory, array(1, 2))): ?><span class="required-star">*</span><?php endif; ?></label>
<select class="wpl_c_<?php echo $field->table_column; ?>" id="wpl_c_<?php echo $field->id; ?>" name="<?php echo $field->table_column; ?>" onchange="ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>', this.value, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>'); wpl_field_specific_changed('<?php echo $field->id; ?>')" data-specific="<?php echo $specified_children; ?>">
    <option value="1" <?php if(1 == $value) echo 'selected="selected"'; ?>><?php echo __($true_label, 'real-estate-listing-realtyna-wpl'); ?></option>
    <option value="0" <?php if(0 == $value) echo 'selected="selected"'; ?>><?php echo __($false_label, 'real-estate-listing-realtyna-wpl'); ?></option>
</select>
<span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="wpl_listing_saved_span"></span>
<?php
	$done_this = true;
}
elseif($type == 'date' and !$done_this)
{
	wp_enqueue_script('jquery-ui-datepicker');

    $date_format_arr = explode(':', wpl_global::get_setting('main_date_format'));
    $jqdate_format = $date_format_arr[1];

    if($options['minimum_date'] == 'now' or $options['minimum_date'] == 'minimum_date') $options['minimum_date'] = date("Y-m-d");
    if($options['maximum_date'] == 'now') $options['maximum_date'] = date("Y-m-d");

    if(!$value) $value = '0000-00-00';

    $mindate = explode('-', $options['minimum_date']);
    $maxdate = explode('-', $options['maximum_date']);

    $mindate[0] = (array_key_exists(0, $mindate) and $mindate[0]) ? $mindate[0] : '1970';
    $mindate[1] = array_key_exists(1, $mindate) ? intval($mindate[1]) : '01';
    $mindate[2] = array_key_exists(2, $mindate) ? intval($mindate[2]) : '01';

    $maxdate[0] = (array_key_exists(0, $maxdate) and $maxdate[0]) ? $maxdate[0] : date('Y');
    $maxdate[1] = array_key_exists(1, $maxdate) ? intval($maxdate[1]) : date('m');
    $maxdate[2] = array_key_exists(2, $maxdate) ? intval($maxdate[2]) : date('d');
?>
<div class="date-wp">
    <label for="wpl_c_<?php echo $field->id; ?>"><?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?><?php if (in_array($mandatory, array(1, 2))): ?><span class="required-star">*</span><?php endif; ?></label>
    <input type="text" id="wpl_c_<?php echo $field->id; ?>" name="<?php echo $field->table_column; ?>" value="<?php echo wpl_render::render_date($value); ?>" onchange="ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>', this.value, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');" <?php echo ((isset($options['readonly']) and $options['readonly'] == 1) ? 'disabled="disabled"' : ''); ?> />
    <span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="ajax-inline-save"></span>
</div>
<?php
    echo '<script type="text/javascript">
		jQuery(document).ready( function ()
		{
			wplj("#wpl_c_' . $field->id . '").datepicker(
			{ 
				dayNamesMin: ["' . __('SU', 'real-estate-listing-realtyna-wpl') . '", "' . __('MO', 'real-estate-listing-realtyna-wpl') . '", "' . __('TU', 'real-estate-listing-realtyna-wpl') . '", "' . __('WE', 'real-estate-listing-realtyna-wpl') . '", "' . __('TH', 'real-estate-listing-realtyna-wpl') . '", "' . __('FR', 'real-estate-listing-realtyna-wpl') . '", "' . __('SA', 'real-estate-listing-realtyna-wpl') . '"],
				dayNames: 	 ["' . __('Sunday', 'real-estate-listing-realtyna-wpl') . '", "' . __('Monday', 'real-estate-listing-realtyna-wpl') . '", "' . __('Tuesday', 'real-estate-listing-realtyna-wpl') . '", "' . __('Wednesday', 'real-estate-listing-realtyna-wpl') . '", "' . __('Thursday', 'real-estate-listing-realtyna-wpl') . '", "' . __('Friday', 'real-estate-listing-realtyna-wpl') . '", "' . __('Saturday', 'real-estate-listing-realtyna-wpl') . '"],
				monthNames:  ["' . __('January', 'real-estate-listing-realtyna-wpl') . '", "' . __('February', 'real-estate-listing-realtyna-wpl') . '", "' . __('March', 'real-estate-listing-realtyna-wpl') . '", "' . __('April', 'real-estate-listing-realtyna-wpl') . '", "' . __('May', 'real-estate-listing-realtyna-wpl') . '", "' . __('June', 'real-estate-listing-realtyna-wpl') . '", "' . __('July', 'real-estate-listing-realtyna-wpl') . '", "' . __('August', 'real-estate-listing-realtyna-wpl') . '", "' . __('September', 'real-estate-listing-realtyna-wpl') . '", "' . __('October', 'real-estate-listing-realtyna-wpl') . '", "' . __('November', 'real-estate-listing-realtyna-wpl') . '", "' . __('December', 'real-estate-listing-realtyna-wpl') . '"],
				dateFormat: "' . $jqdate_format . '",
				gotoCurrent: true,
				minDate: new Date(' . $mindate[0] . ', ' . $mindate[1] . '-1, ' . $mindate[2] . '),
				maxDate: new Date(' . $maxdate[0] . ', ' . $maxdate[1] . '-1, ' . $maxdate[2] . '),
				changeYear: true,
				yearRange: "' . $mindate[0] . ':' . $maxdate[0] . '",
				showOn: "both",
				buttonImage: "' . wpl_global::get_wpl_asset_url('img/system/calendar3.png') . '",
				buttonImageOnly: false,
				buttonImageOnly: true,
				firstDay: 1,
				onSelect: function(dateText, inst) 
				{
					ajax_save("' . $field->table_name . '","' . $field->table_column . '",dateText,' . $item_id . ',' . $field->id . ');
				}
			});
		});
	</script>';

    $done_this = true;
}
elseif($type == 'datetime' and !$done_this)
{
    // Add DateTime Picker assets
    wpl_extensions::import_javascript((object) array('param1'=>'jquery.datetimepicker', 'param2'=>'packages/datetimepicker/jquery.datetimepicker.full.min.js'));
    wpl_extensions::import_style((object) array('param1'=>'jquery.datetimepicker.style', 'param2'=>'packages/datetimepicker/jquery.datetimepicker.min.css'));

    $date_format_arr = explode(':', wpl_global::get_setting('main_date_format'));
    $jqdate_format = $date_format_arr[0];

    if($options['minimum_date'] == 'now' or $options['minimum_date'] == 'minimum_date') $options['minimum_date'] = date("Y-m-d");
    if($options['maximum_date'] == 'now') $options['maximum_date'] = date("Y-m-d");

    $mindate = explode('-', $options['minimum_date']);
    $maxdate = explode('-', $options['maximum_date']);

    $mindate[0] = (array_key_exists(0, $mindate) and $mindate[0]) ? $mindate[0] : '1970';
    $mindate[1] = array_key_exists(1, $mindate) ? intval($mindate[1]) : '01';
    $mindate[2] = array_key_exists(2, $mindate) ? intval($mindate[2]) : '01';

    $maxdate[0] = (array_key_exists(0, $maxdate) and $maxdate[0]) ? $maxdate[0] : date('Y');
    $maxdate[1] = array_key_exists(1, $maxdate) ? intval($maxdate[1]) : date('m');
    $maxdate[2] = array_key_exists(2, $maxdate) ? intval($maxdate[2]) : date('d');
?>
<div class="date-wp">
    <label for="wpl_c_<?php echo $field->id; ?>"><?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?><?php if (in_array($mandatory, array(1, 2))): ?><span class="required-star">*</span><?php endif; ?></label>
    <input type="text" id="wpl_c_<?php echo $field->id; ?>" name="<?php echo $field->table_column; ?>" value="<?php echo wpl_render::render_datetime($value); ?>" onchange="ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>', this.value, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');" <?php echo ((isset($options['readonly']) and $options['readonly'] == 1) ? 'disabled="disabled"' : ''); ?> />
    <span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="ajax-inline-save"></span>
</div>
<?php
    echo '<script type="text/javascript">
		jQuery(document).ready( function ()
		{
			wplj("#wpl_c_' . $field->id . '").datetimepicker(
			{
                i18n:
                {
                    en:
                    {
                        months: ["' . __('January', 'real-estate-listing-realtyna-wpl') . '", "' . __('February', 'real-estate-listing-realtyna-wpl') . '", "' . __('March', 'real-estate-listing-realtyna-wpl') . '", "' . __('April', 'real-estate-listing-realtyna-wpl') . '", "' . __('May', 'real-estate-listing-realtyna-wpl') . '", "' . __('June', 'real-estate-listing-realtyna-wpl') . '", "' . __('July', 'real-estate-listing-realtyna-wpl') . '", "' . __('August', 'real-estate-listing-realtyna-wpl') . '", "' . __('September', 'real-estate-listing-realtyna-wpl') . '", "' . __('October', 'real-estate-listing-realtyna-wpl') . '", "' . __('November', 'real-estate-listing-realtyna-wpl') . '", "' . __('December', 'real-estate-listing-realtyna-wpl') . '"],
                        dayOfWeek: ["' . __('SU', 'real-estate-listing-realtyna-wpl') . '", "' . __('MO', 'real-estate-listing-realtyna-wpl') . '", "' . __('TU', 'real-estate-listing-realtyna-wpl') . '", "' . __('WE', 'real-estate-listing-realtyna-wpl') . '", "' . __('TH', 'real-estate-listing-realtyna-wpl') . '", "' . __('FR', 'real-estate-listing-realtyna-wpl') . '", "' . __('SA', 'real-estate-listing-realtyna-wpl') . '"],
                    }
                },
                lang: "en",
				format: "'.$jqdate_format.' H:i:s",
				minDate: "-' . $mindate[0] . '/' . ($mindate[1]-1) . '/' . $mindate[2] . '",
				maxDate: "+' . $maxdate[0] . '/' . ($maxdate[1]-1) . '/' . $maxdate[2] . '",
				onChangeDateTime: function(dp,input)
				{
					ajax_save("' . $field->table_name . '","' . $field->table_column . '",input.val(),' . $item_id . ',' . $field->id . ');
				}
			});
		});
	</script>';

    $done_this = true;
}
elseif(($type == 'checkbox' or $type == 'tag') and !$done_this)
{
?>
<div class="checkbox-wp">
    <input type="checkbox" class="wpl_c_<?php echo $field->table_column; ?>" id="wpl_c_<?php echo $field->id; ?>" name="<?php echo $field->table_column; ?>" value="1" <?php if($value) echo 'checked="checked"'; ?> onchange="if(this.checked) value = 1; else value = 0; ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>', value, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');wpl_field_specific_changed('<?php echo $field->id; ?>');" <?php echo ((isset($options['readonly']) and $options['readonly'] == 1) ? 'disabled="disabled"' : ''); ?> data-specific="<?php echo $specified_children; ?>" />
	<label class="checkbox-label" for="wpl_c_<?php echo $field->id; ?>"><?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?><?php if(in_array($mandatory, array(1, 2))): ?><span class="required-star">*</span><?php endif; ?></label>
	<span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="ajax-inline-save"></span>
</div>
<?php
    $done_this = true;
}
elseif($type == 'feature' and !$done_this)
{
    $checked = (isset($values[$field->table_column]) and $values[$field->table_column] == '1') ? 'checked="checked"' : '';
    $style = (isset($values[$field->table_column]) and $values[$field->table_column] == '1') ? '' : 'display:none;';
?>
<div class="checkbox-wp">
	<input type="checkbox" id="wpl_c_<?php echo $field->id; ?>" name="<?php echo $field->table_column; ?>" <?php echo $checked; ?> onchange="wplj('#wpl_span_feature_<?php echo $field->id; ?>').slideToggle(400); ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>', this.value, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');wpl_field_specific_changed('<?php echo $field->id; ?>');" data-specific="<?php echo $specified_children; ?>" />
	<label class="checkbox-label" for="wpl_c_<?php echo $field->id; ?>"><?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?><?php if (in_array($mandatory, array(1, 2))): ?><span class="required-star">*</span><?php endif; ?></label>
<?php
	if($options['type'] != 'none')
	{
		// setting the current value
		$value = trim($values[$field->table_column.'_options'], ', ');
		
		if($options['type'] == 'single')
		{
			echo '<div class="options-wp" id="wpl_span_feature_' . $field->id . '" style="' . $style . '">';
			echo '<select id="wpl_cf_' . $field->id . '" name="'.$field->table_column.'_options" onchange="ajax_save(\'' . $field->table_name . '\', \''.$field->table_column.'_options\', \',\'+this.value+\',\', \'' . $item_id . '\', \'' . $field->id . '\', \'#wpl_cf_' . $field->id . '\');">';
			echo '<option value="0">' . __('Select', 'real-estate-listing-realtyna-wpl') . '</option>';
	
			foreach($options['values'] as $select)
			{
                if(isset($select['enabled']) and !$select['enabled']) continue;

				$selected = $value == $select['key'] ? 'selected="selected"' : '';
				echo '<option value="' . $select['key'] . '" ' . $selected . '>' . stripslashes(__($select['value'], 'real-estate-listing-realtyna-wpl')) . '</option>';
			}
			
			echo '</select>';
			echo '</div>';
		}
		elseif($options['type'] == 'multiple')
		{
			$value_array = explode(',', $value);
		
			echo '<div class="options-wp" id="wpl_span_feature_' . $field->id . '" style="' . $style . '">';
			echo '<select multiple="multiple" id="wpl_cf_' . $field->id . '" name="'.$field->table_column.'_options" onchange="ajax_save(\'' . $field->table_name . '\', \''.$field->table_column.'_options\', \',\'+wplj(this).val()+\',\', \'' . $item_id . '\', \'' . $field->id . '\', \'#wpl_cf_' . $field->id . '\');">';
	
			foreach($options['values'] as $select)
			{
                if(isset($select['enabled']) and !$select['enabled']) continue;

				$selected = in_array($select['key'], $value_array) ? 'selected="selected"' : '';
				echo '<option value="' . $select['key'] . '" ' . $selected . '>' . stripslashes(__($select['value'], 'real-estate-listing-realtyna-wpl')) . '</option>';
			}
		
			echo '</select>';
			echo '</div>';
		}
	}
?>
</div>
<span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="wpl_listing_saved_span"></span>
<?php
    $done_this = true;
}
elseif($type == 'listings' and !$done_this)
{
	$listings = wpl_global::get_listings();
	$current_user = wpl_users::get_wpl_user();
	$lrestrict = isset($current_user->maccess_lrestrict) ? $current_user->maccess_lrestrict : NULL;
	$rlistings = explode(',', (isset($current_user->maccess_listings) ? $current_user->maccess_listings : NULL));
?>
<label for="wpl_c_<?php echo $field->id; ?>"><?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?><?php if(in_array($mandatory, array(1, 2))): ?><span class="required-star">*</span><?php endif; ?></label>
<select class="wpl_c_<?php echo $field->table_column; ?>" id="wpl_c_<?php echo $field->id; ?>" name="<?php echo $field->table_column; ?>" onchange="wpl_listing_changed(this.value); ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>', this.value, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');">
    <option value="-1"><?php echo __('Select', 'real-estate-listing-realtyna-wpl'); ?></option>
    <?php foreach($listings as $listing): if($lrestrict and !in_array($listing['id'], $rlistings)) continue; ?>
    <option value="<?php echo $listing['id']; ?>" <?php if($listing['id'] == $value) echo 'selected="selected"'; ?>><?php echo __($listing['name'], 'real-estate-listing-realtyna-wpl'); ?></option>
    <?php endforeach; ?>
</select>
<span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="wpl_listing_saved_span"></span>
<?php
	$done_this = true;
}
elseif($type == 'neighborhood' and !$done_this)
{
    $checked = (isset($values[$field->table_column]) and $values[$field->table_column] == '1') ? 'checked="checked"' : '';
    $style = (isset($values[$field->table_column]) and $values[$field->table_column] == '1') ? '' : 'display:none;';
?>
<div class="checkbox-wp">
	<input type="checkbox" id="wpl_c_<?php echo $field->id; ?>" name="<?php echo $field->table_column; ?>" <?php echo $checked; ?> onchange="wpl_neighborhood_select('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>', this.value, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');wpl_field_specific_changed('<?php echo $field->id; ?>');" data-specific="<?php echo $specified_children; ?>" />
	<label class="checkbox-label" for="wpl_c_<?php echo $field->id; ?>"><?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?><?php if (in_array($mandatory, array(1, 2))): ?><span class="required-star">*</span><?php endif; ?></label>
	<div class="distance-wp distance_items_box" id="wpl_span_dis_<?php echo $field->id; ?>" style="<?php echo $style; ?>">
		<div class="distance-item distance-value">
			<input type="text" id="wpl_c_<?php echo $field->id; ?>_distance" name="<?php echo $field->table_column; ?>_distance" class="wpl_distance_text" value="<?php echo $values[$field->table_column.'_distance']; ?>" size='3' maxlength="4" onBlur="ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column.'_distance'; ?>', this.value, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>', '#n_<?php echo $field->id; ?>_distance');"  />
		</div>
		<div class="distance-item minute-by">
			<?php echo __('Minutes', 'real-estate-listing-realtyna-wpl') . ' ' . __('By', 'real-estate-listing-realtyna-wpl'); ?>
		</div>
		<div class="distance-item with-walk">
			<div class="radio-wp">
				<input type="radio" id="wpl_c_<?php echo $field->id; ?>_distance0" name="n_<?php echo $field->id; ?>_distance_by" <?php if ($values[$field->table_column."_distance_by"] == '1') echo 'checked="checked"'; ?> value='1' onchange="wpl_neighborhood_distance_type_select('<?php echo $field->table_name; ?>', '<?php echo $field->table_column.'_distance_by'; ?>', 1, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>', 'wpl_c_<?php echo $field->id; ?>_distance0')" />
				<label for="wpl_c_<?php echo $field->id; ?>_distance0"><?php echo __('Walk', 'real-estate-listing-realtyna-wpl'); ?></label>
			</div>
		</div>
		<div class="distance-item with-car">
			<div class="radio-wp">
				<input type="radio" id="wpl_c_<?php echo $field->id; ?>_distance1" name="n_<?php echo $field->id; ?>_distance_by" <?php if ($values[$field->table_column."_distance_by"] == '2') echo 'checked="checked"'; ?> value='2' onchange="wpl_neighborhood_distance_type_select('<?php echo $field->table_name; ?>', '<?php echo $field->table_column.'_distance_by'; ?>', 2, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>', 'wpl_c_<?php echo $field->id; ?>_distance1')" />
				<label for="wpl_c_<?php echo $field->id; ?>_distance1"><?php echo __('Car', 'real-estate-listing-realtyna-wpl'); ?></label>
			</div>
		</div>
		<div class="distance-item with-train">
			<div class="radio-wp">
				<input type="radio" id="wpl_c_<?php echo $field->id; ?>_distance2" name="n_<?php echo $field->id; ?>_distance_by" <?php if ($values[$field->table_column."_distance_by"] == '3') echo 'checked="checked"'; ?> value='3' onchange="wpl_neighborhood_distance_type_select('<?php echo $field->table_name; ?>', '<?php echo $field->table_column.'_distance_by'; ?>', 3, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>', 'wpl_c_<?php echo $field->id; ?>_distance2')" />
				<label for="wpl_c_<?php echo $field->id; ?>_distance2"><?php echo __('Train', 'real-estate-listing-realtyna-wpl'); ?></label>
			</div>
		</div>
	</div>
	<span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="ajax-inline-save"></span>
</div>
<?php
    $done_this = true;
}
elseif($type == 'number' and !$done_this)
{
?>
<label for="wpl_c_<?php echo $field->id; ?>"><?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?><?php if(in_array($mandatory, array(1, 2))): ?><span class="required-star">*</span><?php endif; ?></label>
<input type="number" step="any" lang="en" class="wpl_c_<?php echo $field->table_column; ?>" id="wpl_c_<?php echo $field->id; ?>" name="<?php echo $field->table_column; ?>" value="<?php echo $value; ?>" onchange="ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>', this.value, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');" <?php echo ((isset($options['readonly']) and $options['readonly'] == 1) ? 'disabled="disabled"' : ''); ?> />
<span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="wpl_listing_saved_span"></span>
<?php
	$done_this = true;
}
elseif($type == 'mmnumber' and !$done_this)
{
    $value_max = isset($values[$field->table_column.'_max']) ? $values[$field->table_column.'_max'] : 0;
?>
<label for="wpl_c_<?php echo $field->id; ?>"><?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?><?php if(in_array($mandatory, array(1, 2))): ?><span class="required-star">*</span><?php endif; ?></label>
<input type="number" step="any" lang="en" class="wpl_minmax_textbox wpl_c_<?php echo $field->table_column; ?>" id="wpl_c_<?php echo $field->id; ?>" name="<?php echo $field->table_column; ?>" value="<?php echo $value; ?>" onchange="ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>', this.value, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');" <?php echo ((isset($options['readonly']) and $options['readonly'] == 1) ? 'disabled="disabled"' : ''); ?> />
 - <input type="number" step="any" lang="en" class="wpl_minmax_textbox wpl_c_<?php echo $field->table_column; ?>_max" id="wpl_c_<?php echo $field->id; ?>_max" name="<?php echo $field->table_column; ?>_max" value="<?php echo $value_max; ?>" onchange="ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>_max', this.value, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');" <?php echo ((isset($options['readonly']) and $options['readonly'] == 1) ? 'disabled="disabled"' : ''); ?> />
<span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="wpl_listing_saved_span"></span>
<?php
	$done_this = true;
}
elseif($type == 'property_types' and !$done_this)
{
	$property_types = wpl_global::get_property_types();
	$current_user = wpl_users::get_wpl_user();
    
	$ptrestrict = isset($current_user->maccess_ptrestrict) ? $current_user->maccess_ptrestrict : NULL;
	$rproperty_types = explode(',', (isset($current_user->maccess_property_types) ? $current_user->maccess_property_types : NULL));
    
    $current_category = wpl_property_types::get_parent($value);
    $categories = wpl_property_types::get_property_type_categories();

	$category_field = wpl_flex::get_fields('', '', '', '', '', "AND `enabled` >= 1 AND `kind` = '{$kind}' AND `type` = 'ptcategory'");
	$category_field = reset($category_field);
	
    if($category_field and isset($category_field->accesses) and trim($category_field->accesses) != '' and wpl_global::check_addon('membership'))
	{
		$accesses = explode(',', trim($category_field->accesses, ', '));
        $cur_membership_id = wpl_users::get_user_membership();
        
		if(!in_array($cur_membership_id, $accesses) and trim($category_field->accesses_message) == '')
		{
			// Show nothing
		}
        elseif(!in_array($cur_membership_id, $accesses) and trim($category_field->accesses_message) != '')
        {
            echo '<div class="prow wpl_listing_field_container prow-'.$type.'" id="wpl_listing_field_container'.$category_field->id.'" style="'.$display.'">';
            echo '<label for="wpl_c_'.$category_field->id.'">'.__($label, 'real-estate-listing-realtyna-wpl').'</label>';
            echo '<span class="wpl-access-blocked-message">'.__($category_field->accesses_message, 'real-estate-listing-realtyna-wpl').'</span>';
            echo '</div>';
        }
        else
        {
        ?>
	        <div>
			    <label for="wpl_c_<?php echo $field->id; ?>_ptcategory"><?php echo __('Category', 'real-estate-listing-realtyna-wpl'); ?></label>
			    <select class="wpl_c_<?php echo $field->table_column; ?>_ptcategory" id="wpl_c_<?php echo $field->id; ?>_ptcategory">
			        <option value="-1"><?php echo __('Select', 'real-estate-listing-realtyna-wpl'); ?></option>
			        <?php foreach($categories as $category): ?>
			        <option value="<?php echo $category['id']; ?>" <?php if($category['id'] == $current_category) echo 'selected="selected"'; ?>><?php echo __($category['name'], 'real-estate-listing-realtyna-wpl'); ?></option>
			        <?php endforeach; ?>
			    </select>
			</div>
        <?php
    	}
	}
?>
<div>
    <label for="wpl_c_<?php echo $field->id; ?>"><?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?><?php if(in_array($mandatory, array(1, 2))): ?><span class="required-star">*</span><?php endif; ?></label>
    <select class="wpl_c_<?php echo $field->table_column; ?>" id="wpl_c_<?php echo $field->id; ?>" name="<?php echo $field->table_column; ?>" onchange="wpl_property_type_changed(this.value); ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>', this.value, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');">
        <option value="-1"><?php echo __('Select', 'real-estate-listing-realtyna-wpl'); ?></option>
        <?php foreach($property_types as $property_type): if($ptrestrict and !in_array($property_type['id'], $rproperty_types)) continue; ?>
        <option class="wpl-ptcategory-option wpl-ptcategory-<?php echo $property_type['parent']; ?>" value="<?php echo $property_type['id']; ?>" <?php if($property_type['id'] == $value) echo 'selected="selected"'; ?>><?php echo __($property_type['name'], 'real-estate-listing-realtyna-wpl'); ?></option>
        <?php endforeach; ?>
    </select>
    <span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="wpl_listing_saved_span"></span>
</div>
<script type="text/javascript">
jQuery(document).ready(function()
{
    wplj('#wpl_c_<?php echo $field->id; ?>_ptcategory').on('change', function(e, auto)
    {
        var cat = wplj('#wpl_c_<?php echo $field->id; ?>_ptcategory').val();
        
        // If category changed by user, remove the previous selected property type
        if(typeof auto == 'object') wplj('#wpl_c_<?php echo $field->id; ?>').find('option:selected').removeAttr('selected');
        
        wplj('.wpl-ptcategory-option').hide();
        
        if(cat != '' && cat != '-1') wplj('.wpl-ptcategory-'+cat).show();
        else wplj('.wpl-ptcategory-option').show();
        
        wplj('#wpl_c_<?php echo $field->id; ?>').trigger('chosen:updated');
    });
    
    wplj('#wpl_c_<?php echo $field->id; ?>_ptcategory').trigger('change', true);
});
</script>
<?php
	$done_this = true;
}
elseif($type == 'select' and !$done_this)
{
?>
<label for="wpl_c_<?php echo $field->id; ?>"><?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?><?php if(in_array($mandatory, array(1, 2))): ?><span class="required-star">*</span><?php endif; ?></label>
<select class="wpl_c_<?php echo $field->table_column; ?>" id="wpl_c_<?php echo $field->id; ?>" name="<?php echo $field->table_column; ?>" onchange="ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>', this.value, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');">
    <option value="-1"><?php echo __('Select', 'real-estate-listing-realtyna-wpl'); ?></option>

    <?php if(isset($options['params']) and is_array($options['params'])): ?>
        <?php foreach($options['params'] as $key=>$select): if(!$select['enabled']) continue; ?>
        <option value="<?php echo $select['key']; ?>" <?php if($select['key'] == $value) echo 'selected="selected"'; ?>><?php echo stripslashes(__($select['value'], 'real-estate-listing-realtyna-wpl')); ?></option>
        <?php endforeach; ?>
    <?php endif; ?>

</select>
<span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="wpl_listing_saved_span"></span>
<?php
	$done_this = true;
}
elseif($type == 'separator' and !$done_this)
{
?>
    <div class="seperator-wp" id="wpl_listing_separator<?php echo $field->id; ?>">
    	<?php echo (isset($options['show_label']) and $options['show_label'] == "1") ? __($label, 'real-estate-listing-realtyna-wpl') : ''; ?>
    </div>
<?php
	$done_this = true;
}
elseif(in_array($type, array('price', 'volume', 'area', 'length')) and !$done_this)
{
    $current_unit = $values[$field->table_column.'_unit'];
    
	if($type == 'price') $units = wpl_units::get_units(4, 1, " AND `type`='4' AND (`enabled`>='1' OR `id`='$current_unit')");
	if($type == 'volume') $units = wpl_units::get_units(3, 1, " AND `type`='3' AND (`enabled`>='1' OR `id`='$current_unit')");
	if($type == 'area') $units = wpl_units::get_units(2, 1, " AND `type`='2' AND (`enabled`>='1' OR `id`='$current_unit')");
	if($type == 'length') $units = wpl_units::get_units(1, 1, " AND `type`='1' AND (`enabled`>='1' OR `id`='$current_unit')");
?>
<label for="wpl_c_<?php echo $field->id; ?>"><?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?><?php if(in_array($mandatory, array(1, 2))): ?><span class="required-star">*</span><?php endif; ?></label>
<input onkeyup="wpl_thousand_sep('wpl_c_<?php echo $field->id; ?>')" type="text" id="wpl_c_<?php echo $field->id; ?>" name="<?php echo $field->table_column; ?>" value="<?php echo number_format($value, 2); ?>" onblur="ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>', wpl_de_thousand_sep(this.value), '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');" <?php echo ((isset($options['readonly']) and $options['readonly']) == 1 ? 'disabled="disabled"' : ''); ?> />
<?php
    if(count($units) <= 1) echo $units[0]['name'];
    else
    {
        echo '<select onchange="ajax_save(\'' .$field->table_name. '\', \'' .$field->table_column. '_unit\', this.value, \''.$item_id.'\', \''.$field->id.'\');">';
        foreach($units as $unit) echo '<option value="'.$unit['id'].'" ' .( $current_unit == $unit['id'] ? 'selected="selected"' : ''). '>' .$unit['name']. '</option>';
        echo '</select>';
    }
?>
<span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="wpl_listing_saved_span"></span>
<?php
	$done_this = true;
}
elseif(in_array($type, array('mmprice', 'mmvolume', 'mmarea', 'mmlength')) and !$done_this)
{
    $current_unit = $values[$field->table_column.'_unit'];
    
	if($type == 'mmprice') $units = wpl_units::get_units(4, 1, " AND `type`='4' AND (`enabled`>='1' OR `id`='$current_unit')");
	if($type == 'mmvolume') $units = wpl_units::get_units(3, 1, " AND `type`='3' AND (`enabled`>='1' OR `id`='$current_unit')");
	if($type == 'mmarea') $units = wpl_units::get_units(2, 1, " AND `type`='2' AND (`enabled`>='1' OR `id`='$current_unit')");
	if($type == 'mmlength') $units = wpl_units::get_units(1, 1, " AND `type`='1' AND (`enabled`>='1' OR `id`='$current_unit')");
    
    $value_max = isset($values[$field->table_column.'_max']) ? $values[$field->table_column.'_max'] : 0;
?>
<label for="wpl_c_<?php echo $field->id; ?>"><?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?><?php if(in_array($mandatory, array(1, 2))): ?><span class="required-star">*</span><?php endif; ?></label>
<input onkeyup="wpl_thousand_sep('wpl_c_<?php echo $field->id; ?>')" type="text" id="wpl_c_<?php echo $field->id; ?>" name="<?php echo $field->table_column; ?>" value="<?php echo number_format($value, 2); ?>" onblur="ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>', wpl_de_thousand_sep(this.value), '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');" <?php echo ((isset($options['readonly']) and $options['readonly']) == 1 ? 'disabled="disabled"' : ''); ?> />
<input onkeyup="wpl_thousand_sep('wpl_c_<?php echo $field->id; ?>_max')" type="text" id="wpl_c_<?php echo $field->id; ?>_max" name="<?php echo $field->table_column; ?>_max" value="<?php echo number_format($value_max, 2); ?>" onblur="ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>_max', wpl_de_thousand_sep(this.value), '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');" <?php echo ((isset($options['readonly']) and $options['readonly']) == 1 ? 'disabled="disabled"' : ''); ?> />
<?php
    if(count($units) <= 1) echo $units[0]['name'];
    else
    {
        echo '<select onchange="ajax_save(\'' .$field->table_name. '\', \'' .$field->table_column. '_unit\', this.value, \''.$item_id.'\', \''.$field->id.'\');">';
        foreach($units as $unit) echo '<option value="'.$unit['id'].'" ' .( $current_unit == $unit['id'] ? 'selected="selected"' : ''). '>' .$unit['name']. '</option>';
        echo '</select>';
    }
?>
<span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="wpl_listing_saved_span"></span>
<?php
	$done_this = true;
}
elseif($type == 'url' and !$done_this)
{
?>
<label for="wpl_c_<?php echo $field->id; ?>"><?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?><?php if(in_array($mandatory, array(1, 2))): ?><span class="required-star">*</span><?php endif; ?></label>
<input type="text" id="wpl_c_<?php echo $field->id; ?>" name="<?php echo $field->table_column; ?>" value="<?php echo $value; ?>" onchange="ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>', this.value, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');" <?php echo ((isset($options['readonly']) and $options['readonly'] == 1) ? 'disabled="disabled"' : ''); ?> />
<span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="wpl_listing_saved_span"></span>
<?php
	$done_this = true;
}
elseif($type == 'meta_key' and !$done_this)
{
    $current_language = wpl_global::get_current_language();
    if(isset($field->multilingual) and $field->multilingual == 1 and wpl_global::check_multilingual_status()): wp_enqueue_script('jquery-effects-clip', false, array('jquery-effects-core'));
?>
<label class="wpl-multiling-label wpl-multiling-text">
    <?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?>
    <?php if(in_array($mandatory, array(1, 2))): ?><span class="required-star">*</span><?php endif; ?>
</label>
<div class="wpl-multiling-field wpl-multiling-text">

    <div class="wpl-multiling-flags-wp">
        <div class="wpl-multiling-flag-cnt">
            <?php foreach($wpllangs as $wpllang): $lang_column = wpl_addon_pro::get_column_lang_name($field->table_column, $wpllang, false); ?>
            <div data-wpl-field="wpl_c_<?php echo $field->id; ?>_<?php echo strtolower($wpllang); ?>" data-wpl-title="<?php echo $wpllang; ?>" class="wpl-multiling-flag wpl-multiling-flag-<?php echo strtolower(substr($wpllang,-2)); echo empty($values[$lang_column])? ' wpl-multiling-empty': ''; ?>"></div>
            <?php endforeach; ?>
        </div>
        <div class="wpl-multiling-edit-btn"></div>
        <div class="wpl-multilang-field-cnt">
            <?php foreach($wpllangs as $wpllang): $lang_column = wpl_addon_pro::get_column_lang_name($field->table_column, $wpllang, false); ?>
            <div class="wpl-lang-cnt" id="wpl_langs_cnt_<?php echo $field->id; ?>_<?php echo strtolower($wpllang); ?>">
                <label for="wpl_c_<?php echo $field->id; ?>_<?php echo strtolower($wpllang); ?>"><?php echo $wpllang; ?></label>
                <textarea class="wpl_c_<?php echo $field->table_column; ?>_<?php echo strtolower($wpllang); ?>" id="wpl_c_<?php echo $field->id; ?>_<?php echo strtolower($wpllang); ?>" name="<?php echo $field->table_column; ?>_<?php echo strtolower($wpllang); ?>" rows="<?php echo $options['rows']; ?>" cols="<?php echo $options['cols']; ?>" onblur="ajax_multilingual_save('<?php echo $field->id; ?>', '<?php echo strtolower($wpllang); ?>', this.value, '<?php echo $item_id; ?>');"><?php echo (isset($values[$lang_column]) ? $values[$lang_column] : ''); ?></textarea>
                <span id="wpl_listing_saved_span_<?php echo $field->id; ?>_<?php echo strtolower($wpllang); ?>" class="wpl_listing_saved_span"></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php else: ?>
<label for="wpl_c_<?php echo $field->id; ?>"><?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?><?php if (in_array($mandatory, array(1, 2))): ?><span class="wpl_red_star">*</span><?php endif; ?></label>
<div id="wpl_c_<?php echo $field->id; ?>_container" class="wpl-meta-wp">
    <div class="wpl-top-row-wp">
        <input type="checkbox" id="wpl_c_<?php echo $field->id; ?>_manual" name="meta_keywords_manual" onchange="meta_key_manual();" <?php if (isset($values['meta_keywords_manual']) and $values['meta_keywords_manual']) echo 'checked="checked"'; ?> />
        <label for="wpl_c_<?php echo $field->id; ?>_manual"><?php echo __('Manually insert meta keywords', 'real-estate-listing-realtyna-wpl'); ?></label>
    </div>
    <textarea id="wpl_c_<?php echo $field->id; ?>" rows="<?php echo $options['rows']; ?>" cols="<?php echo $options['cols']; ?>" onchange="metatag_key_creator(true);" <?php echo(($options['readonly'] == 1 and (!isset($values['meta_keywords_manual']) or (isset($values['meta_keywords_manual']) and !$values['meta_keywords_manual']))) ? 'disabled="disabled"' : ''); ?>><?php echo $value; ?></textarea>
</div>
<span id="wpl_c_<?php echo $field->id; ?>_message"></span>
<span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="wpl_listing_saved_span"></span>
<script type="text/javascript">
function metatag_key_creator(force)
{
    if(!force) force = 0;
    
    var meta = '';

    /** Don't regenerate meta keywords if user want to manually insert it **/
    if (wplj("#wpl_c_<?php echo $field->id; ?>_manual").is(':checked'))
    {
        if(force)
        {
            meta = wplj("#wpl_c_<?php echo $field->id; ?>").val();
            ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>', meta, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');
        }
    
        return true;
    }
}

var meta_key_pro_addon = <?php echo (wpl_global::check_addon('pro') ? '1' : '0'); ?>;
function meta_key_manual()
{
    if (!wplj("#wpl_c_<?php echo $field->id; ?>_manual").is(':checked'))
    {
        wplj("#wpl_c_<?php echo $field->id; ?>").attr('disabled', 'disabled');

        if (meta_key_pro_addon) {
            ajax_save('<?php echo $field->table_name; ?>', 'meta_keywords_manual', '0', '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');
            metatag_key_creator();
        }

        return false;
    }

    if (!meta_key_pro_addon)
    {
        wpl_show_messages("<?php echo addslashes(__('Pro addon must be installed for this!', 'real-estate-listing-realtyna-wpl')); ?>", '#wpl_c_<?php echo $field->id; ?>_message', 'wpl_red_msg');
        setTimeout(function () {
            wpl_remove_message('#wpl_c_<?php echo $field->id; ?>_message');
        }, 3000);

        wplj("#wpl_c_<?php echo $field->id; ?>_manual").removeAttr('checked');
        return false;
    }

    wplj("#wpl_c_<?php echo $field->id; ?>").removeAttr('disabled');
    ajax_save('<?php echo $field->table_name; ?>', 'meta_keywords_manual', '1', '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');
}
</script>
<?php endif; ?>
<?php
    $done_this = true;
}
elseif($type == 'meta_desc' and !$done_this)
{
    $current_language = wpl_global::get_current_language();
    if(isset($field->multilingual) and $field->multilingual == 1 and wpl_global::check_multilingual_status()): wp_enqueue_script('jquery-effects-clip', false, array('jquery-effects-core'));
?>
<label class="wpl-multiling-label wpl-multiling-text">
    <?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?>
    <?php if(in_array($mandatory, array(1, 2))): ?><span class="required-star">*</span><?php endif; ?>
</label>
<div class="wpl-multiling-field wpl-multiling-text">

    <div class="wpl-multiling-flags-wp">
        <div class="wpl-multiling-flag-cnt">
            <?php foreach($wpllangs as $wpllang): $lang_column = wpl_addon_pro::get_column_lang_name($field->table_column, $wpllang, false); ?>
            <div data-wpl-field="wpl_c_<?php echo $field->id; ?>_<?php echo strtolower($wpllang); ?>" data-wpl-title="<?php echo $wpllang; ?>" class="wpl-multiling-flag wpl-multiling-flag-<?php echo strtolower(substr($wpllang,-2)); echo empty($values[$lang_column])? ' wpl-multiling-empty': ''; ?>"></div>
            <?php endforeach; ?>
        </div>
        <div class="wpl-multiling-edit-btn"></div>
        <div class="wpl-multilang-field-cnt">
            <?php foreach($wpllangs as $wpllang): $lang_column = wpl_addon_pro::get_column_lang_name($field->table_column, $wpllang, false); ?>
            <div class="wpl-lang-cnt" id="wpl_langs_cnt_<?php echo $field->id; ?>_<?php echo strtolower($wpllang); ?>">
                <label for="wpl_c_<?php echo $field->id; ?>_<?php echo strtolower($wpllang); ?>"><?php echo $wpllang; ?></label>
                <textarea class="wpl_c_<?php echo $field->table_column; ?>_<?php echo strtolower($wpllang); ?>" id="wpl_c_<?php echo $field->id; ?>_<?php echo strtolower($wpllang); ?>" name="<?php echo $field->table_column; ?>_<?php echo strtolower($wpllang); ?>" rows="<?php echo $options['rows']; ?>" cols="<?php echo $options['cols']; ?>" onblur="ajax_multilingual_save('<?php echo $field->id; ?>', '<?php echo strtolower($wpllang); ?>', this.value, '<?php echo $item_id; ?>');"><?php echo (isset($values[$lang_column]) ? $values[$lang_column] : ''); ?></textarea>
                <span id="wpl_listing_saved_span_<?php echo $field->id; ?>_<?php echo strtolower($wpllang); ?>" class="wpl_listing_saved_span"></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php else: ?>
<label for="wpl_c_<?php echo $field->id; ?>"><?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?><?php if (in_array($mandatory, array(1, 2))): ?><span class="wpl_red_star">*</span><?php endif; ?></label>
<div id="wpl_c_<?php echo $field->id; ?>_container" class="wpl-meta-wp">
    <div class="wpl-top-row-wp">
        <input type="checkbox" id="wpl_c_<?php echo $field->id; ?>_manual" name="meta_description_manual" onchange="meta_desc_manual();" <?php if (isset($values['meta_description_manual']) and $values['meta_description_manual']) echo 'checked="checked"'; ?> />
        <label for="wpl_c_<?php echo $field->id; ?>_manual"><?php echo __('Manually insert meta descriptions', 'real-estate-listing-realtyna-wpl'); ?></label>
    </div>
    <textarea id="wpl_c_<?php echo $field->id; ?>" rows="<?php echo $options['rows']; ?>" cols="<?php echo $options['cols']; ?>" onchange="metatag_desc_creator(true);" <?php echo(($options['readonly'] == 1 and (!isset($values['meta_description_manual']) or (isset($values['meta_description_manual']) and !$values['meta_description_manual']))) ? 'disabled="disabled"' : ''); ?>><?php echo $value; ?></textarea>
</div>
<span id="wpl_c_<?php echo $field->id; ?>_message"></span>
<span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="wpl_listing_saved_span"></span>
<script type="text/javascript">
function metatag_desc_creator(force)
{
    if(!force) force = 0;
    
    var meta = '';

    /** Don't regenerate meta keywords if user want to manually insert it **/
    if (wplj("#wpl_c_<?php echo $field->id; ?>_manual").is(':checked'))
    {
        if(force)
        {
            meta = wplj("#wpl_c_<?php echo $field->id; ?>").val();
            ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>', meta, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');
        }
        
        return true;
    }
}    

var meta_desc_pro_addon = <?php echo (wpl_global::check_addon('pro') ? '1' : '0'); ?>;
function meta_desc_manual()
{
    if (!wplj("#wpl_c_<?php echo $field->id; ?>_manual").is(':checked'))
    {
        wplj("#wpl_c_<?php echo $field->id; ?>").attr('disabled', 'disabled');

        if (meta_desc_pro_addon) {
            ajax_save('<?php echo $field->table_name; ?>', 'meta_description_manual', '0', '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');
            metatag_desc_creator();
        }

        return false;
    }

    if (!meta_desc_pro_addon)
    {
        wpl_show_messages("<?php echo addslashes(__('Pro addon must be installed for this!', 'real-estate-listing-realtyna-wpl')); ?>", '#wpl_c_<?php echo $field->id; ?>_message', 'wpl_red_msg');
        setTimeout(function () {
            wpl_remove_message('#wpl_c_<?php echo $field->id; ?>_message');
        }, 3000);

        wplj("#wpl_c_<?php echo $field->id; ?>_manual").removeAttr('checked');
        return false;
    }

    wplj("#wpl_c_<?php echo $field->id; ?>").removeAttr('disabled');
    ajax_save('<?php echo $field->table_name; ?>', 'meta_description_manual', '1', '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');
}
</script>
<?php endif; ?>
<?php
    $done_this = true;
}
elseif($type == 'multiselect' and !$done_this)
{
	$multiselect_values = explode(',', $value);
	if(trim($multiselect_values[0]) == '') $multiselect_values = array();
	?>
	<label for="wpl_c_<?php echo $field->id; ?>"><?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?><?php if(in_array($mandatory, array(1, 2))): ?><span class="required-star">*</span><?php endif; ?></label>
	<select class="wpl_c_<?php echo $field->table_column; ?>" id="wpl_c_<?php echo $field->id; ?>" name="<?php echo $field->table_column; ?>" multiple="multiple">
	    <?php foreach($options['params'] as $key=>$select): if(!$select['enabled']) continue; ?>
	    	<option value="<?php echo $select['key']; ?>" <?php if(in_array($select['key'], $multiselect_values)) echo 'selected="selected"'; ?>><?php echo stripslashes(__($select['value'], 'real-estate-listing-realtyna-wpl')); ?></option>
	    <?php endforeach; ?>
	</select>
	<span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="wpl_listing_saved_span"></span>
	<script type="text/javascript">
    jQuery(document).ready(function()
    {
        wplj("#wpl_c_<?php echo $field->id; ?>").change(function(e)
        {
            ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>', wplj(this).val() || [].join(), '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');
        });
    });
	</script>
	<?php
	$done_this = true;
}
elseif($type == 'multiselect_agent' and !$done_this)
{
	// Get data from database
	$query = "SELECT * FROM `#__wpl_users` WHERE `id` > 0 AND `membership_type`='1'";
	$result = wpl_db::select($query, 'loadObjectList');
	// Create option fields
	foreach ($result as $resultkey => $resultvalue) {
		// isset($resultvalue->first_name)
		$options['params'][$resultkey]['value'] =  $resultvalue->first_name .' '. $resultvalue->last_name;
		$options['params'][$resultkey]['key'] =  $resultvalue->id;
		$options['params'][$resultkey]['enabled'] =  1;
	}

	$multiselect_values = explode(',', $value);
	if(trim($multiselect_values[0]) == '') $multiselect_values = array();
	?>
	<label for="wpl_c_<?php echo $field->id; ?>"><?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?><?php if(in_array($mandatory, array(1, 2))): ?><span class="required-star">*</span><?php endif; ?></label>
	<select class="wpl_c_<?php echo $field->table_column; ?>" id="wpl_c_<?php echo $field->id; ?>" name="<?php echo $field->table_column; ?>" multiple="multiple">
	    <?php foreach($options['params'] as $key=>$select): if(!$select['enabled']) continue; ?>
	    	<option value="<?php echo $select['key']; ?>" <?php if(in_array($select['key'], $multiselect_values)) echo 'selected="selected"'; ?>><?php echo stripslashes(__($select['value'], 'real-estate-listing-realtyna-wpl')); ?></option>
	    <?php endforeach; ?>
	</select>
	<span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="wpl_listing_saved_span"></span>
	<script type="text/javascript">
    jQuery(document).ready(function()
    {
        wplj("#wpl_c_<?php echo $field->id; ?>").change(function(e)
        {
            ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>', wplj(this).val() || [].join(), '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');
        });
    });
	</script>
	<?php
	$done_this = true;
}