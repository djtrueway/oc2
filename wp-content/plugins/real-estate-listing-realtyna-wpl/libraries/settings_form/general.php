<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

if($type == 'checkbox' and !$done_this)
{
?>
<div class="prow wpl_st_type<?php echo $setting_record->type; ?> wpl_st_<?php echo $setting_record->setting_name; ?>" id="wpl_st_<?php echo $setting_record->id; ?>">
    <div class="checkbox-wp">

        <label for="wpl_st_form_element<?php echo $setting_record->id; ?>"><?php echo $setting_title; ?></label>
        <label class="wpl-switch" >
            <input type="hidden" name="wpl_st_form_val" value="off" />
            <input type="checkbox" name="wpl_st_form<?php echo $setting_record->id; ?>" id="wpl_st_form_element<?php echo $setting_record->id; ?>" autocomplete="off" <?php if($value) echo 'checked="checked"'; ?> onchange="wpl_setting_save('<?php echo $setting_record->id; ?>', '<?php echo $setting_record->setting_name; ?>', this.value, '<?php echo $setting_record->category; ?>');" />
            <span class="wpl-slider wpl-round"></span>
        </label>
        <?php if(isset($params['tooltip'])): ?>
            <span class="wpl_setting_form_tooltip wpl_help" id="wpl_setting_form_tooltip_container<?php echo $setting_record->id; ?>">
                <span class="wpl_help_description" style="display: none;"><?php echo __($params['tooltip'], 'real-estate-listing-realtyna-wpl'); ?></span>
            </span>
        <?php endif; ?>
        <span class="ajax-inline-save" id="wpl_ajax_loader_<?php echo $setting_record->id; ?>"></span>

    </div>
</div>



<?php
    $done_this = true;
}
elseif($type == 'text' and !$done_this)
{
?>
<div class="prow wpl_setting_form_container wpl_st_type<?php echo $setting_record->type; ?> wpl_st_<?php echo $setting_record->setting_name; ?>" id="wpl_st_<?php echo $setting_record->id; ?>">
	<div class="text-wp">
		<label for="wpl_st_form_element<?php echo $setting_record->id; ?>"><?php echo $setting_title; ?>&nbsp;<span class="wpl_st_citation">:</span></label>
        <input class="<?php echo isset($params['html_class']) ? $params['html_class'] : ''; ?>" type="text" name="wpl_st_form<?php echo $setting_record->id; ?>" id="wpl_st_form_element<?php echo $setting_record->id; ?>" value="<?php echo htmlentities($setting_record->setting_value, ENT_COMPAT, "UTF-8"); ?>" placeholder="<?php echo  ((isset($params['placeholder']) and $params['placeholder']) ? __($params['placeholder'], 'real-estate-listing-realtyna-wpl') : ''); ?>" onchange="<?php if(isset($options['show_shortcode']) and $options['show_shortcode']): ?>wpl_setting_show_shortcode('<?php echo $setting_record->id; ?>', '<?php echo $options['shortcode_key']; ?>', this.value);<?php endif; ?> wpl_setting_save('<?php echo $setting_record->id; ?>', '<?php echo $setting_record->setting_name; ?>', this.value, '<?php echo $setting_record->category; ?>');" autocomplete="off" <?php echo isset($params['readonly']) ? 'readonly="readonly"' : ''; ?> />

		<?php if(isset($options['show_shortcode'])): ?>
        <div class="shortcode-wp" id="wpl_setting_form_shortcode_container<?php echo $setting_record->id; ?>">
            <span title="<?php echo __('Shortcode', 'real-estate-listing-realtyna-wpl'); ?>" id="wpl_st_<?php echo $setting_record->id; ?>_shortcode_value"><?php echo $options['shortcode_key'] . '="' . $value . '"'; ?></span>
        </div>
		<?php endif; ?>

		<?php if(isset($params['tooltip'])): ?>
        <span class="wpl_setting_form_tooltip wpl_help" id="wpl_setting_form_tooltip_container<?php echo $setting_record->id; ?>">
            <span class="wpl_help_description" style="display: none;"><?php echo __($params['tooltip'], 'real-estate-listing-realtyna-wpl'); ?></span>
        </span>
		<?php endif; ?>

		<span class="ajax-inline-save" id="wpl_ajax_loader_<?php echo $setting_record->id; ?>"></span>
	</div>
</div>
<?php
    $done_this = true;
}
elseif($type == 'separator' and !$done_this)
{
?>
<div class="prow wpl_setting_form_container wpl_st_type<?php echo $setting_record->type; ?> wpl_st_<?php echo $setting_record->setting_name; ?>" id="wpl_st_<?php echo $setting_record->id; ?>">
    <h3 class="separator-name"><?php echo $setting_title; ?></h3>
	<hr />
</div>
<?php
    $done_this = true;
}
elseif($type == 'select' and !$done_this)
{
	$show_empty = isset($options['show_empty']) ? $options['show_empty'] : NULL;
	$show_shortcode = isset($options['show_shortcode']) ? $options['show_shortcode'] : NULL;
    $values = isset($options['query']) ? wpl_db::select($options['query'], 'loadAssocList') : $options['values'];
?>
<div class="prow wpl_setting_form_container wpl_st_type<?php echo $setting_record->type; ?> wpl_st_<?php echo $setting_record->setting_name; ?>" id="wpl_st_<?php echo $setting_record->id; ?>">
    <div class="select-wp">
        <label for="wpl_st_form_element<?php echo $setting_record->id; ?>"><?php echo $setting_title; ?>&nbsp;<span class="wpl_st_citation">:</span></label>
        <select name="wpl_st_form<?php echo $setting_record->id; ?>" id="wpl_st_form_element<?php echo $setting_record->id; ?>" onchange="<?php if($show_shortcode): ?>wpl_setting_show_shortcode('<?php echo $setting_record->id; ?>', '<?php echo $options['shortcode_key']; ?>', this.value);<?php endif; ?> wpl_setting_save('<?php echo $setting_record->id; ?>', '<?php echo $setting_record->setting_name; ?>', this.value, '<?php echo $setting_record->category; ?>');" <?php if(isset($params['width'])): ?>data-chosen-opt="width: <?php echo $params['width']; ?>"<?php endif; ?> autocomplete="off">
            <?php if($show_empty): ?><option value="">---</option><?php endif; ?>
            <?php foreach ($values as $value_array): ?>
            <option value="<?php echo $value_array['key']; ?>" <?php if($value_array['key'] == $value) echo 'selected="selected"' ?>><?php echo $value_array['value']; ?></option>
            <?php endforeach; ?>
        </select>

        <?php if($show_shortcode): ?>
        <div class="shortcode-wp" id="wpl_setting_form_shortcode_container<?php echo $setting_record->id; ?>">
            <span title="<?php echo __('Shortcode', 'real-estate-listing-realtyna-wpl'); ?>" id="wpl_st_<?php echo $setting_record->id; ?>_shortcode_value"><?php echo $options['shortcode_key'] . '="' . $value . '"'; ?></span>
        </div>
        <?php endif; ?>

        <?php if(isset($params['tooltip'])): ?>
        <span class="wpl_setting_form_tooltip wpl_help" id="wpl_setting_form_tooltip_container<?php echo $setting_record->id; ?>">
            <span class="wpl_help_description" style="display: none;"><?php echo __($params['tooltip'], 'real-estate-listing-realtyna-wpl'); ?></span>
        </span>
        <?php endif; ?>

        <span class="ajax-inline-save" id="wpl_ajax_loader_<?php echo $setting_record->id; ?>"></span>
    </div>
</div>
<?php
	$done_this = true;
}
elseif($type == 'sort_option' and !$done_this)
{
    $kind = trim($options['kind']) != '' ? $options['kind'] : 1;
    
    _wpl_import('libraries.sort_options');
    $sort_options = wpl_sort_options::render(wpl_sort_options::get_sort_options($options['kind'], 1)); /** getting enaled sort options **/
?>
<div class="prow wpl_setting_form_container wpl_st_type<?php echo $setting_record->type; ?> wpl_st_<?php echo $setting_record->setting_name; ?>" id="wpl_st_<?php echo $setting_record->id; ?>">
	<div class="select-wp">
		<label for="wpl_st_form_element<?php echo $setting_record->id; ?>"><?php echo $setting_title; ?><span class="wpl_st_citation">:</span></label>
		<select name="wpl_st_form<?php echo $setting_record->id; ?>" id="wpl_st_form_element<?php echo $setting_record->id; ?>" onchange="wpl_setting_show_shortcode('<?php echo $setting_record->id; ?>', '<?php echo $options['shortcode_key']; ?>', this.value);
				wpl_setting_save('<?php echo $setting_record->id; ?>', '<?php echo $setting_record->setting_name; ?>', this.value, '<?php echo $setting_record->category; ?>');" autocomplete="off">
					<?php foreach ($sort_options as $value_array): ?>
				<option value="<?php echo $value_array['field_name']; ?>" <?php if($value_array['field_name'] == $value) echo 'selected="selected"' ?>><?php echo $value_array['name']; ?></option>
			<?php endforeach; ?>
		</select>

		<?php if(isset($options['show_shortcode'])): ?>
        <div class="shortcode-wp" id="wpl_setting_form_shortcode_container<?php echo $setting_record->id; ?>">
            <span title="<?php echo __('Shortcode', 'real-estate-listing-realtyna-wpl'); ?>" id="wpl_st_<?php echo $setting_record->id; ?>_shortcode_value"><?php echo $options['shortcode_key'] . '="' . $value . '"'; ?></span>
        </div>
		<?php endif; ?>

		<?php if(isset($params['tooltip'])): ?>
        <span class="wpl_setting_form_tooltip wpl_help" id="wpl_setting_form_tooltip_container<?php echo $setting_record->id; ?>">
            <span class="wpl_help_description" style="display: none;"><?php echo __($params['tooltip'], 'real-estate-listing-realtyna-wpl'); ?></span>
        </span>
		<?php endif; ?>

		<span class="ajax-inline-save" id="wpl_ajax_loader_<?php echo $setting_record->id; ?>"></span>
	</div>
</div>
<?php
    $done_this = true;
}
elseif($type == 'wppages' and !$done_this)
{
	$show_empty = isset($options['show_empty']) ? $options['show_empty'] : NULL;
	$wp_pages = wpl_global::get_wp_pages();
?>
<div class="prow wpl_setting_form_container wpl_st_type<?php echo $setting_record->type; ?> wpl_st_<?php echo $setting_record->setting_name; ?>" id="wpl_st_<?php echo $setting_record->id; ?>">
    <div class="select-wp">
        <label for="wpl_st_form_element<?php echo $setting_record->id; ?>"><?php echo $setting_title; ?>&nbsp;<span class="wpl_st_citation">:</span></label>
        <select name="wpl_st_form<?php echo $setting_record->id; ?>" id="wpl_st_form_element<?php echo $setting_record->id; ?>" onchange="wpl_setting_save('<?php echo $setting_record->id; ?>', '<?php echo $setting_record->setting_name; ?>', this.value, '<?php echo $setting_record->category; ?>');" autocomplete="off">
            <?php if($show_empty): ?><option value="">---</option><?php endif; ?>
            <?php foreach ($wp_pages as $wp_page): ?>
            <option value="<?php echo $wp_page->ID; ?>" <?php if($wp_page->ID == $value) echo 'selected="selected"'; ?>><?php echo $wp_page->post_title; ?></option>
            <?php endforeach; ?>
        </select>

        <?php if(isset($params['tooltip'])): ?>
        <span class="wpl_setting_form_tooltip wpl_help" id="wpl_setting_form_tooltip_container<?php echo $setting_record->id; ?>">
            <span class="wpl_help_description" style="display: none;"><?php echo __($params['tooltip'], 'real-estate-listing-realtyna-wpl'); ?></span>
        </span>
        <?php endif; ?>

        <span class="ajax-inline-save" id="wpl_ajax_loader_<?php echo $setting_record->id; ?>"></span>
    </div>
</div>
<?php
	$done_this = true;
}
elseif($type == 'upload' and !$done_this)
{
    $src = wpl_global::get_wpl_asset_url('img/system/' . $setting_record->setting_value);
    $activity_params = array('html_element_id'=>$params['html_element_id'], 'html_ajax_loader'=>'#wpl_ajax_loader_'.$setting_record->id, 'request_str'=>$params['request_str'].'&_wpnonce='.$nonce);
?>
<div class="prow wpl_setting_form_container wpl_st_type<?php echo $setting_record->type; ?> wpl_st_<?php echo $setting_record->setting_name; ?>" id="wpl_st_<?php echo $setting_record->id; ?>">
	<div class="upload-wp">
		<label for="wpl_st_form_element<?php echo $setting_record->id; ?>"><?php echo $setting_title; ?><span class="wpl_st_citation">:</span></label>
		<?php wpl_global::import_activity('ajax_file_upload', '', $activity_params); ?>
		<span class="ajax-inline-save" id="wpl_ajax_loader_<?php echo $setting_record->id; ?>"></span>
        <?php if($setting_record->setting_value): ?>
		<div class="upload-preview wpl-upload-setting">
			<img id="wpl_upload_image<?php echo $setting_record->id; ?>" src="<?php echo $src; ?>" />
            <div class="preview-remove-button">
                <span class="action-btn icon-recycle" onclick="wpl_remove_upload<?php echo $setting_record->id; ?>();"></span>
            </div>
		</div>
        <?php endif; ?>
	</div>
</div>
<script type="text/javascript">
function wpl_remove_upload<?php echo $setting_record->id; ?>()
{
    request_str = 'wpl_format=b:settings:ajax&wpl_function=remove_upload&setting_name=<?php echo addslashes($setting_record->setting_name); ?>&_wpnonce=<?php echo $nonce; ?>';

    /** run ajax query **/
    ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str);
    ajax.success(function(data)
    {
        if(data.success == 1)
        {
            wplj("#wpl_st_<?php echo $setting_record->id; ?> .upload-preview").remove();
        }
        else if(data.success != 1)
        {
        }
    });
}
</script>
<?php
    $done_this = true;
}
elseif($type == 'textarea' and !$done_this)
{
?>
<div class="prow wpl_setting_form_container wpl_st_type<?php echo $setting_record->type; ?> wpl_st_<?php echo $setting_record->setting_name; ?>" id="wpl_st_<?php echo $setting_record->id; ?>">
	<div class="text-wp">
		<label for="wpl_st_form_element<?php echo $setting_record->id; ?>"><?php echo $setting_title; ?>&nbsp;<span class="wpl_st_citation">:</span></label>
        <textarea class="long" name="wpl_st_form<?php echo $setting_record->id; ?>" id="wpl_st_form_element<?php echo $setting_record->id; ?>" onchange="wpl_setting_save('<?php echo $setting_record->id; ?>', '<?php echo $setting_record->setting_name; ?>', this.value, '<?php echo $setting_record->category; ?>');"><?php echo $setting_record->setting_value; ?></textarea>

		<?php if(isset($params['tooltip'])): ?>
        <span class="wpl_setting_form_tooltip wpl_help" id="wpl_setting_form_tooltip_container<?php echo $setting_record->id; ?>">
            <span class="wpl_help_description" style="display: none;"><?php echo __($params['tooltip'], 'real-estate-listing-realtyna-wpl'); ?></span>
        </span>
		<?php endif; ?>

		<span class="ajax-inline-save" id="wpl_ajax_loader_<?php echo $setting_record->id; ?>"></span>
	</div>
</div>
<?php
    $done_this = true;
}
elseif($type == 'pattern' and !$done_this)
{
?>
<div class="prow wpl_setting_form_container wpl_st_type<?php echo $setting_record->type; ?> wpl_st_<?php echo $setting_record->setting_name; ?>" id="wpl_st_<?php echo $setting_record->id; ?>">
	<div class="text-wp">
		<label for="wpl_st_form_element<?php echo $setting_record->id; ?>"><?php echo $setting_title; ?>&nbsp;<span class="wpl_st_citation">:</span></label>
        <textarea class="long" style="height: 60px;" name="wpl_st_form<?php echo $setting_record->id; ?>" id="wpl_st_form_element<?php echo $setting_record->id; ?>" placeholder="<?php echo  ((isset($params['placeholder']) and $params['placeholder']) ? __($params['placeholder'], 'real-estate-listing-realtyna-wpl') : ''); ?>" onchange="wpl_setting_save('<?php echo $setting_record->id; ?>', '<?php echo $setting_record->setting_name; ?>', this.value, '<?php echo $setting_record->category; ?>');" autocomplete="off"><?php echo $setting_record->setting_value; ?></textarea>

		<?php if(isset($params['tooltip'])): ?>
        <span class="wpl_setting_form_tooltip wpl_help" id="wpl_setting_form_tooltip_container<?php echo $setting_record->id; ?>">
            <span class="wpl_help_description" style="display: none;"><?php echo __($params['tooltip'], 'real-estate-listing-realtyna-wpl'); ?></span>
        </span>
		<?php endif; ?>

		<span class="ajax-inline-save" id="wpl_ajax_loader_<?php echo $setting_record->id; ?>"></span>
	</div>
</div>
<?php
    $done_this = true;
}
elseif($type == 'multiple' and !$done_this)
{
    $items = json_decode($setting_record->setting_value, true);
?>
<div class="prow wpl_setting_form_container wpl_st_type<?php echo $setting_record->type; ?> wpl_st_<?php echo $setting_record->setting_name; ?>" id="wpl_st_<?php echo $setting_record->id; ?>">
    <div class="text-wp">
        <label for="wpl_st_form_element<?php echo $setting_record->id; ?>"><?php echo $setting_title; ?>&nbsp;<span class="wpl_st_citation">:</span></label>
       
        <input class="<?php echo isset($params['html_class']) ? $params['html_class'] : ''; ?>" type="text"
            name="wpl_st_form<?php echo $setting_record->id; ?>" id="wpl_st_form_element<?php echo $setting_record->id; ?>" 
            placeholder="<?php echo  ((isset($params['placeholder']) and $params['placeholder']) ? __($params['placeholder'], 'real-estate-listing-realtyna-wpl') : ''); ?>"
            onchange="wpl_setting_save('<?php echo $setting_record->id; ?>', '<?php echo $setting_record->setting_name; ?>', this.value, '<?php echo $setting_record->category; ?>');"
            autocomplete="off" value="<?php echo htmlentities($setting_record->setting_value); ?>" data-realtyna-tagging />

        <?php if(isset($params['tooltip'])): ?>
        <span class="wpl_setting_form_tooltip wpl_help" id="wpl_setting_form_tooltip_container<?php echo $setting_record->id; ?>">
            <span class="wpl_help_description" style="display: none;"><?php echo __($params['tooltip'], 'real-estate-listing-realtyna-wpl'); ?></span>
        </span>
        <?php endif; ?>

        <span class="ajax-inline-save" id="wpl_ajax_loader_<?php echo $setting_record->id; ?>"></span>
    </div>
</div>
<?php
    $done_this = true;
}
elseif($type == 'colorpicker' and !$done_this)
{
?>
<div class="prow wpl_setting_form_container wpl_st_type<?php echo $setting_record->type; ?> wpl_st_<?php echo $setting_record->setting_name; ?>" id="wpl_st_<?php echo $setting_record->id; ?>">
    <div class="color-picker-wp">
        <label for="wpl_st_form_element<?php echo $setting_record->id; ?>"><?php echo $setting_title; ?>&nbsp;<span class="wpl_st_citation">:</span></label>
        <input class="wpl-color-picker-field <?php echo isset($params['html_class']) ? $params['html_class'] : ''; ?>" type="text"
               data-default-color="<?php echo htmlentities($setting_record->setting_value, ENT_COMPAT, "UTF-8"); ?>"
               name="wpl_st_form<?php echo $setting_record->id; ?>"
               id="wpl_st_form_element<?php echo $setting_record->id; ?>"
               value="<?php echo htmlentities($setting_record->setting_value, ENT_COMPAT, "UTF-8"); ?>"
               autocomplete="off" <?php echo isset($params['readonly']) ? 'readonly="readonly"' : ''; ?>
        />
        <?php if(isset($params['tooltip'])): ?>
            <span class="wpl_setting_form_tooltip wpl_help" id="wpl_setting_form_tooltip_container<?php echo $setting_record->id; ?>">
                <span class="wpl_help_description" style="display: none;"><?php echo __($params['tooltip'], 'real-estate-listing-realtyna-wpl'); ?></span>
            </span>
        <?php endif; ?>
        <input class="wpl-button button-1 wpl-save-btn wpl-color-picker-save" type="button" onclick="wpl_color_picker_save<?php echo $setting_record->id; ?>();" value="<?php echo __('Save', 'real-estate-listing-realtyna-wpl'); ?>" />
    </div>
</div>
<script type="text/javascript">
    wplj('document').ready(function(){
        wplj('#wpl_st_form_element<?php echo $setting_record->id; ?>').wpColorPicker();
    });
    function wpl_color_picker_save<?php echo $setting_record->id; ?>(){
        wpl_setting_save('<?php echo $setting_record->id; ?>', '<?php echo $setting_record->setting_name; ?>', wplj("#wpl_st_form_element<?php echo $setting_record->id; ?>").val(), '<?php echo $setting_record->category; ?>');
    }
</script>
<?php
    $done_this = true;
}
elseif($type == 'currency' and !$done_this)
{
    $values = wpl_units::get_units();
?>
<div class="prow wpl_setting_form_container wpl_st_type<?php echo $setting_record->type; ?> wpl_st_<?php echo $setting_record->setting_name; ?>" id="wpl_st_<?php echo $setting_record->id; ?>">
    <div class="select-wp">
        <label for="wpl_st_form_element<?php echo $setting_record->id; ?>"><?php echo $setting_title; ?>&nbsp;<span class="wpl_st_citation">:</span></label>
        <select name="wpl_st_form<?php echo $setting_record->id; ?>" id="wpl_st_form_element<?php echo $setting_record->id; ?>" onchange="wpl_setting_save('<?php echo $setting_record->id; ?>', '<?php echo $setting_record->setting_name; ?>', this.value, '<?php echo $setting_record->category; ?>');" autocomplete="off">
            <?php foreach($values as $value_array): ?>
            <option value="<?php echo $value_array['extra']; ?>" <?php if($value_array['extra'] == $value) echo 'selected="selected"' ?>><?php echo $value_array['extra']; ?></option>
            <?php endforeach; ?>
        </select>

        <?php if(isset($params['tooltip'])): ?>
        <span class="wpl_setting_form_tooltip wpl_help" id="wpl_setting_form_tooltip_container<?php echo $setting_record->id; ?>">
            <span class="wpl_help_description" style="display: none;"><?php echo __($params['tooltip'], 'real-estate-listing-realtyna-wpl'); ?></span>
        </span>
        <?php endif; ?>

        <span class="ajax-inline-save" id="wpl_ajax_loader_<?php echo $setting_record->id; ?>"></span>
    </div>
</div>
<?php
    $done_this = true;
}
elseif($type == 'imagepicker' and !$done_this)
{
    $images = isset($options['images']) ? $options['images'] : array();
?>
<div class="prow wpl_setting_form_container wpl_st_type<?php echo $setting_record->type; ?> wpl_st_<?php echo $setting_record->setting_name; ?>" id="wpl_st_<?php echo $setting_record->id; ?>">
    <div class="select-wp">
        <label for="wpl_st_form_element<?php echo $setting_record->id; ?>"><?php echo $setting_title; ?>&nbsp;<span class="wpl_st_citation">:</span></label>
        
        <div class="wpl-imagepicker-images-wp" id="wpl_st_form_element<?php echo $setting_record->id; ?>">
            
            <?php foreach($images as $image): ?>
            <div id="wpl_st_form_element<?php echo $setting_record->id; ?>_val_<?php echo $image['value']; ?>" class="wpl-imagepicker-image-wp <?php echo ($image['value'] == $value ? 'wpl-imagepicker-active' : ''); ?>">
                
                <?php if(isset($image['path'])): ?>
                <img src="<?php echo wpl_global::get_wpl_asset_url($image['path']); ?>" onclick="wpl_imagepicker_save('<?php echo $setting_record->id; ?>', '<?php echo $setting_record->setting_name; ?>', '<?php echo $image['value']; ?>', '<?php echo $setting_record->category; ?>');">
                <?php elseif(isset($image['url'])): ?>
                <img src="<?php echo $image['url']; ?>" onclick="wpl_imagepicker_save('<?php echo $setting_record->id; ?>', '<?php echo $setting_record->setting_name; ?>', '<?php echo $image['value']; ?>', '<?php echo $setting_record->category; ?>');">
                <?php endif; ?>
                
            </div>
            <?php endforeach; ?>
            
        </div>

        <?php if(isset($params['tooltip'])): ?>
        <span class="wpl_setting_form_tooltip wpl_help" id="wpl_setting_form_tooltip_container<?php echo $setting_record->id; ?>">
            <span class="wpl_help_description" style="display: none;"><?php echo __($params['tooltip'], 'real-estate-listing-realtyna-wpl'); ?></span>
        </span>
        <?php endif; ?>

    </div>
</div>
<?php
    $done_this = true;
}
elseif($type == 'advanced_markers' and !$done_this)
{
    wp_enqueue_style('wp-color-picker');
	wp_enqueue_script('wp-color-picker');
    
    $listing_types = wpl_global::get_listings();
    $property_types = wpl_global::get_property_types();
    
    $icons = wpl_global::get_icons(WPL_ABSPATH.'assets'.DS.'img'.DS.'property_types'.DS, '.svg$');
    
    $advanced_markers = json_decode((trim($value) ? $value : '{}'), true);
    if(!is_array($advanced_markers)) $advanced_markers = array();
?>
<div class="prow wpl_setting_form_container wpl_st_type<?php echo $setting_record->type; ?> wpl_st_<?php echo $setting_record->setting_name; ?>" id="wpl_st_<?php echo $setting_record->id; ?>">
    <div class="advanced-markers-wp">
        
        <p><?php echo __("You can enable advanced map markers simply here. You just need to configure the color and icons and then check Google Maps on your website! Don't forget to click save button below.", 'real-estate-listing-realtyna-wpl'); ?></p>
        
        <div class="wpl-advanced-markers-status-wp">
            <label for="wpl_am_status"><?php echo __('Status', 'real-estate-listing-realtyna-wpl'); ?></label>
            <select name="wpl_advanced_markers[status]" id="wpl_am_status">
                <option value="0" <?php echo (isset($advanced_markers['status']) and $advanced_markers['status'] == '0') ? 'selected="selected"' : ''; ?>><?php echo __('Disabled', 'real-estate-listing-realtyna-wpl'); ?></option>
                <option value="1" <?php echo (isset($advanced_markers['status']) and $advanced_markers['status'] == '1') ? 'selected="selected"' : ''; ?>><?php echo __('Enabled', 'real-estate-listing-realtyna-wpl'); ?></option>
            </select>
        </div>
        
        <div class="wpl-advanced-markers-wp" id="wpl_advanced_markers_options_wp">
            
            <div class="wpl-advanced-markers-listing-types-wp">
                <?php foreach($listing_types as $listing_type): $color = (isset($advanced_markers['listing_types']) and isset($advanced_markers['listing_types'][$listing_type['id']]) and trim($advanced_markers['listing_types'][$listing_type['id']])) ? $advanced_markers['listing_types'][$listing_type['id']] : '#29a9df'; ?>
                <div>
                    <label for="wpl_am_lt_<?php echo $listing_type['id']; ?>"><?php echo $listing_type['name']; ?></label>
                    <input type="text" name="wpl_advanced_markers[listing_types][<?php echo $listing_type['id']; ?>]" value="<?php echo $color; ?>" class="wpl-color-field" data-default-color="<?php echo $color; ?>" id="wpl_am_lt_<?php echo $listing_type['id']; ?>" />
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="wpl-advanced-markers-property-types-wp">
                <?php foreach($property_types as $property_type): $current_icon = (isset($advanced_markers['property_types']) and isset($advanced_markers['property_types'][$property_type['id']]) and trim($advanced_markers['property_types'][$property_type['id']])) ? $advanced_markers['property_types'][$property_type['id']] : ''; ?>
                <div class="">
                    <label for="wpl_am_pt_<?php echo $property_type['id']; ?>"><?php echo $property_type['name']; ?></label>
                    <input type="hidden" name="wpl_advanced_markers[property_types][<?php echo $property_type['id']; ?>]" value="<?php echo $current_icon; ?>" id="wpl_am_pt_<?php echo $property_type['id']; ?>" />
                    
                    <div class="wpl-advanced-markers-property-types-images wpl-setting-select-img" id="wpl_am_pt_icons<?php echo $property_type['id']; ?>">
                        <?php foreach($icons as $icon): ?>
                            <img src="<?php echo wpl_global::get_wpl_asset_url('img/property_types/'.$icon); ?>" class="wpl-am-pt-icon <?php echo ($current_icon == $icon) ? 'wpl-am-pt-icon-active' : ''; ?>" data-icon="<?php echo $icon; ?>" data-pt-id="<?php echo $property_type['id']; ?>">
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
        </div>
        <div class="wpl-advanced-markers-save-button-wp">
            <label></label>
            <button onclick="wpl_advanced_markers_save(<?php echo $setting_record->id; ?>);" class="wpl-button button-1">
                <?php echo __('Save', 'real-estate-listing-realtyna-wpl'); ?>
            </button>
            <div id="wpl_advanced_markers_show_message" class="wpl-notify-msg"></div>
        </div>

    </div>
</div>
<script type="text/javascript">
jQuery(document).ready(function()
{
    wplj('#wpl_st_<?php echo $setting_record->id; ?> .wpl-color-field').wpColorPicker();
});
</script>
<?php
    $done_this = true;
} 
