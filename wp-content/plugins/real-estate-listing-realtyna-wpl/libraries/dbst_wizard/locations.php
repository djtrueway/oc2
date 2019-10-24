<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

if($type == 'locations' and !$done_this)
{
    $location_settings = wpl_global::get_settings('3'); # location settings
    
    // Auto suggestion is enabled for location text fields
    if(wpl_global::check_addon('pro') and $location_settings['location_auto_suggest'])
    {
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-autocomplete');
    }
?>
<div class="location-wp wpl_listing_all_location_container_<?php echo $field->table_column; ?>" id="wpl_listing_all_location_container<?php echo $field->id; ?>">
	<?php
	for($i = 1; $i <= 7; $i++)
	{
		if($i != 1 and !trim($values['location' . ($i - 1) . '_id']) and $location_settings['location_method'] == 2) continue;
		if($location_settings['location_method'] == 1 and trim($location_settings['location' . $i . '_keyword']) == '') continue;

		$parent = ($i != 1 and isset($values['location' . ($i - 1) . '_id'])) ? $values['location' . ($i - 1) . '_id'] : '';
		$current_location_id = isset($values['location' . $i . '_id']) ? $values['location' . $i . '_id'] : NULL;
		$current_location_name = isset($values['location' . $i . '_name']) ? stripslashes($values['location' . $i . '_name']) : NULL;
		$enabled = $i != 1 ? '' : '1';

		$locations = wpl_locations::get_locations($i, $parent, $enabled, '', '`name` ASC', '');

		if(!count($locations) and $location_settings['location_method'] == 2) break;
		if(!count($locations) and $location_settings['location_method'] == 1 and $i <= 2) continue;
		?>
		<div class="location-part" id="wpl_listing_location_level_container<?php echo $field->id.'_'.$i; ?>">
			<label class="title"><?php echo __($location_settings['location' . $i . '_keyword'], 'real-estate-listing-realtyna-wpl'); ?> <?php if (in_array($mandatory, array(1, 2)) and $i <= 2): ?><span class="required-star">*</span><?php endif; ?></label>
			<?php if($location_settings['location_method'] == 1 and $i >= 3): ?>
				<div class="value-wp text-wp">
					<input type="text" class="wpl_location_indicator_textbox" data-column="location<?php echo $i; ?>_name" value="<?php echo $current_location_name; ?>" name="location<?php echo $i; ?>_name" id="wpl_listing_location<?php echo $i; ?>_select" onchange="wpl_listing_location_change('<?php echo $field->id; ?>', '<?php echo $i; ?>', this.value);" />
				</div>
			<?php elseif($location_settings['location_method'] == 2 or ($location_settings['location_method'] == 1 and $i <= 2)): ?>
				<div class="value-wp select-wp">
					<select name="location<?php echo $i; ?>_id" id="wpl_listing_location<?php echo $i; ?>_select" onchange="wpl_listing_location_change('<?php echo $field->id; ?>', '<?php echo $i; ?>', this.value);" class="<?php echo ($i <= 2 ? 'wpl_location_indicator_selectbox' : ''); ?>" style="width: 180px;">
						<option value="0"><?php echo __('Select', 'real-estate-listing-realtyna-wpl'); ?></option>
						<?php foreach($locations as $location): ?>
							<option value="<?php echo $location->id; ?>" <?php echo ($current_location_id == $location->id ? 'selected="selected"' : ''); ?>><?php echo __($location->name, 'real-estate-listing-realtyna-wpl'); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>
			<span id="wpl_listing_saved_span_<?php echo $field->id; ?>" class="ajax-inline-save"></span>
		</div>
		<?php
	}

	if(($values['zip_id'] or $location_settings['location_method'] == 1) and trim($location_settings['locationzips_keyword']) != '')
	{
		$parent = $values['location' . ($location_settings['zipcode_parent_level']) . '_id'];
		$current_location_id = $values['zip_id'];
		$current_location_name = $values['zip_name'];

		$locations = wpl_locations::get_locations('zips', $parent, '');

		if(count($locations) or $location_settings['location_method'] == 1)
		{
			?>
			<div class="location-part" id="wpl_listing_location_level_container<?php echo $field->id; ?>_zips">
				<label class="title"><?php echo __($location_settings['locationzips_keyword'], 'real-estate-listing-realtyna-wpl'); ?> </label>
				<?php if($location_settings['location_method'] == 1): ?>
					<div class="value-wp text-wp">
						<input type="text" class="wpl_location_indicator_textbox" data-column="zip_name" value="<?php echo $current_location_name; ?>" name="zip_name" id="wpl_listing_locationzips_select" onchange="wpl_listing_location_change('<?php echo $field->id; ?>', 'zips', this.value);" />
					</div>
				<?php elseif($location_settings['location_method'] == 2): ?>
					<div class="value-wp select-wp">
						<select name="zip_id" id="wpl_listing_locationzips_select" onchange="wpl_listing_location_change('<?php echo $field->id; ?>', 'zips', this.value);" class="wpl_location_indicator_selectbox" style="width: 180px;">
							<option value="0"><?php echo __('Select', 'real-estate-listing-realtyna-wpl'); ?></option>
							<?php foreach ($locations as $location): ?>
								<option value="<?php echo $location->id; ?>" <?php echo ($current_location_id == $location->id ? 'selected="selected"' : ''); ?>><?php echo __($location->name, 'real-estate-listing-realtyna-wpl'); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				<?php endif; ?>
			</div>
			<?php
		}
	}
	?>
</div>

<script type="text/javascript">
var zipcode_parent_level = '<?php echo $location_settings['zipcode_parent_level']; ?>';
var location_method = '<?php echo $location_settings['location_method']; ?>';

function wpl_listing_location_change(field_id, location_level, value)
{
	var next_level = parseInt(location_level) + 1;

	/** Remove zipcode level **/
	if(location_level != 'zips' && location_method == '2')
	{
		if(wplj("#wpl_listing_location_level_container" + field_id + "_zips").length)
		{
			/** remove form element and reset current data **/
			wplj("#wpl_listing_location_level_container" + field_id + "_zips").remove();
			ajax_save('<?php echo $field->table_name; ?>', 'zip_id', '0', '<?php echo $item_id; ?>', '<?php echo $field->id; ?>', '', 'location_save');
		}
	}

	/** Remove next location levels **/
	for(var i = next_level; i <= 7; i++)
	{
		if(!(wplj("#wpl_listing_location_level_container" + field_id + '_' + i).length > 0)) continue;
		if(i >= 3 && location_method == '1') break;

		/** remove form element and reset current data **/
		wplj("#wpl_listing_location_level_container" + field_id + '_' + i).remove();
		ajax_save('<?php echo $field->table_name; ?>', 'location' + i + '_id', '0', '<?php echo $item_id; ?>', '<?php echo $field->id; ?>', '', 'location_save');
	}

	/** load zipcodes **/
	if(next_level > zipcode_parent_level) next_level = 'zips';

	/** load next level **/
    wpl_load_location_select(field_id, next_level, value);

	/** save current location level **/
	ajax_save('<?php echo $field->table_name; ?>', (location_level != 'zips' ? 'location' + location_level + '_id' : 'zip_id'), value, '<?php echo $item_id; ?>', '<?php echo $field->id; ?>', '', 'location_save');
}

function wpl_load_location_select(field_id, location_level, parent)
{
	if(!location_level) return;

	var parent_level = location_level - 1;
    
	var html = "";
	var request_str = 'wpl_format=b:listing:ajax&wpl_function=get_locations&location_level=' + location_level + '&parent=' + parent + '&field_id=' + field_id+'&_wpnonce=<?php echo $nonce; ?>';

	/** run ajax query **/
	var ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str);
	ajax.success(function(data)
	{
		if(data.success == 1 && !(wplj("#wpl_listing_location_level_container" + field_id + '_' + location_level).length > 0))
		{
			html += '<div class="location-part" id="wpl_listing_location_level_container' + field_id + '_' + location_level + '">';
			html += '<label class="title">' + data.keyword + (data.mandatory ? '<span class="required-star">*</span>' : '') + '</label>';
			html += '<div class="value-wp select-wp">';
			html += data.html;
			html += '</div>';
			html += '<span id="wpl_listing_saved_span_' + field_id + '" class="ajax-inline-save"></span>';
			html += '</div>';

			if(location_level != 'zips') wplj("#wpl_listing_location_level_container" + field_id + '_' + parent_level).after(html);
			else if(location_level != 'zips' && location_method == '2') wplj("#wpl_listing_all_location_container"+field_id).append(html);
			else if(location_level == 'zips') wplj("#wpl_listing_all_location_container"+field_id).append(html);
		}
		else if(data.success != 1)
		{
		}
	});
}

jQuery(document).ready(function()
{
    <?php if(wpl_global::check_addon('pro') and $location_settings['location_auto_suggest']): ?>
    wplj(".wpl_location_indicator_textbox").autocomplete(
    {
        source: function(request, response)
        {
            var request_str = "wpl_format=b:listing:ajax&wpl_function=get_suggestions&column="+wplj(this.element).data('column')+"&kind=<?php echo $field->kind; ?>&term="+request.term+"&_wpnonce=<?php echo $nonce; ?>";
            
            var ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str);
            ajax.success(function(data)
            {
                response(data);
            });
        },
        minLength: 2,
        select: function(event, ui)
        {
            wplj(this).trigger('change');
        },
        change: function(event, ui)
        {
            wplj(this).trigger('change');
        }
    });
    <?php endif; ?>
});
</script>
<?php
    $done_this = true;
}