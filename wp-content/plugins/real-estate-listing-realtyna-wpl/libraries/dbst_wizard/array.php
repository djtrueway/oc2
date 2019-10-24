<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

if($type == 'array' and !$done_this)
{
	$explode_array_value = stristr($value, '|') ? explode('|', $value) : array($value);
	?>
		<label for="wpl_c_<?php echo $field->id; ?>"><?php echo __($label, 'real-estate-listing-realtyna-wpl'); ?><?php if(in_array($mandatory, array(1, 2))): ?><span class="required-star">*</span><?php endif; ?></label>
		<input type="hidden" id="wpl_c_<?php echo $field->id; ?>">

	<div id="wpl_array_field_value_box<?php echo $field->id; ?>" class="wpl-array-field-values">
        <div class="fanc-row">
            <input class="wpl-button button-1" type="button" onclick="wpl_array_add_value_<?php echo $field->id; ?>()" value="<?php echo __('Add new', 'real-estate-listing-realtyna-wpl') ?>">
        </div>
	<?php
	$i = 0;

	foreach ($explode_array_value as $value): $i++?>
		<div class="fanc-row" id="wpl_array_value_row_<?php echo $field->id; ?>_<?php echo $i; ?>">
		    <input type="text" id="wpl_c_<?php echo $field->id; ?>_index<?php echo $i; ?>" name="wpl_c_<?php echo $field->id; ?>_index<?php echo $i; ?>" value="<?php echo $value; ?>">
            <input class="wpl-button button-1" type="button" onclick="wpl_array_remove_value_<?php echo $field->id; ?>(<?php echo $i; ?>);" value="<?php echo __('Remove', 'real-estate-listing-realtyna-wpl') ?>">
        </div>
	<?php endforeach; ?>

	</div>

    <input class="wpl-button button-1 wpl-save-btn" type="button" onclick="wpl_array_save_values_<?php echo $field->id; ?>()" value="<?php echo __('Save', 'real-estate-listing-realtyna-wpl') ?>">
	<span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="wpl_listing_saved_span"></span>

	<script type="text/javascript">
	var wpl_array_param_counter_<?php echo $field->id; ?> = <?php echo $i; ?>;

	function wpl_array_add_value_<?php echo $field->id; ?>()
	{
		wpl_array_param_counter_<?php echo $field->id; ?>++;

		html = '<div class="fanc-row" id="wpl_array_value_row_<?php echo $field->id; ?>_'+wpl_array_param_counter_<?php echo $field->id; ?>+'">'+
				'<input type="text" id="wpl_c_<?php echo $field->id; ?>_index'+wpl_array_param_counter_<?php echo $field->id; ?>+'" name="wpl_c_<?php echo $field->id; ?>_index'+wpl_array_param_counter_<?php echo $field->id; ?>+'" />'+
                '<input class="wpl-button button-1" type="button" onclick="wpl_array_remove_value_<?php echo $field->id; ?>('+wpl_array_param_counter_<?php echo $field->id; ?>+');" value="<?php echo __('Remove', 'real-estate-listing-realtyna-wpl') ?>"></div>';

		wplj('#wpl_array_field_value_box<?php echo $field->id; ?>').append(html);
	}

	function wpl_array_remove_value_<?php echo $field->id; ?>(index)
	{
		wplj('#wpl_array_value_row_<?php echo $field->id; ?>_'+index).remove();
	}

	function wpl_array_save_values_<?php echo $field->id; ?>()
	{
		value = '';

		wplj('#wpl_array_field_value_box<?php echo $field->id; ?> input[type="text"]').each(function(ind,element)
		{
			element_value = wplj(this).val().replaceAll('|',' ');
			value += element_value+'|';
		});

		render_value = value.substring(0, value.length - 1);

		wplj("#wpl_c_<?php echo $field->id; ?>").val(render_value);

		ajax_save('<?php echo $field->table_name; ?>', '<?php echo $field->table_column; ?>', render_value, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>');
	}

	</script>

	<style>
		.wpl-array-field-values {
			display: inline-block;
		}
		.wpl-array-field-values-save {
			margin: 20px;
		}
	</style>
	<?php
	$done_this = true;
}