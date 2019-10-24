<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

if($type == 'seo_patterns' and !$done_this)
{
    $values = json_decode($setting_record->setting_value, true);
    if(!$values) $values = array();
    
    $kinds = wpl_flex::get_kinds('wpl_properties');
    $property_types = wpl_global::get_property_types();
    $patterns = array(
        'property_alias_pattern'=>__('Listing Link Pattern', 'real-estate-listing-realtyna-wpl'),
        'property_title_pattern'=>__('Listing Title', 'real-estate-listing-realtyna-wpl'),
        'property_page_title_pattern'=>__('Listing Page Title', 'real-estate-listing-realtyna-wpl'),
        'meta_description_pattern'=>__('Meta Description', 'real-estate-listing-realtyna-wpl'),
        'meta_keywords_pattern'=>__('Meta Keywords', 'real-estate-listing-realtyna-wpl')
    );
?>
<div class="prow wpl_setting_form_container wpl_st_type<?php echo $setting_record->type; ?> wpl_st_<?php echo $setting_record->setting_name; ?>" id="wpl_st_<?php echo $setting_record->id; ?>">
    <div class="wpl-seo-patterns-wp" id="seo_patterns_wp_<?php echo $setting_record->id; ?>">
        <div class="wpl-js-tab-system">
            <div class="wpl-seo-patterns-tab wpl-gen-tab-wp">
                <ul class="wpl-tabs">
                    <?php foreach($kinds as $kind): ?>
                        <li>
                            <a href="#wpl-seo-tab-content-<?php echo $kind['id']; ?>" class="" data-for="wpl_seo_patterns_kind<?php echo $kind['id']; ?>"><?php echo $kind['name']; ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="wpl-seo-patterns-tab-content">
                <div class="wpl-btns-wp">
                    <div class="clearfix">
                        <button onclick="wpl_seo_patterns_save();" class="wpl-button button-1">
                            <?php echo __('Save', 'real-estate-listing-realtyna-wpl'); ?>
                            <span class="ajax-inline-save" id="wpl_ajax_loader_<?php echo $setting_record->id; ?>"></span>
                        </button>
                    </div>
                    <div id="wpl_seo_patterns_show_message" class="wpl-notify-msg"></div>
                </div>
                <div class="wpl-gen-tab-contents-wp">
                    <?php foreach($kinds as $kind): ?>
                        <div class="wpl-gen-tab-content wpl-seo-patterns-kind-wp" id="wpl-seo-tab-content-<?php echo $kind['id']; ?>">
                            <?php if(in_array($kind['id'], array('1', '0'))): ?>
                                <div class="wpl-gen-tab-content" id="wpl_seo_patterns_kind<?php echo $kind['id']; ?>">
                                    <?php foreach($property_types as $property_type): ?>
                                        <div class="wpl-gen-accordion">
                                            <h4 class="wpl-gen-accordion-title" data-for="wpl_seo_patterns_ptype<?php echo $kind['id']; ?>_<?php echo $property_type['id']; ?>">
                                                <span><?php echo __($property_type['name'], 'real-estate-listing-realtyna-wpl'); ?></span>
                                            </h4>
                                            <div class="wpl-gen-accordion-cnt" id="wpl_seo_patterns_ptype<?php echo $kind['id']; ?>_<?php echo $property_type['id']; ?>">
                                                <?php foreach($patterns as $key=>$pattern): ?>
                                                    <div class="prow">
                                                        <div class="text-wp">
                                                            <label for="wpl_seo_patterns_ptype<?php echo $kind['id']; ?>_<?php echo $property_type['id']; ?>_<?php echo $key; ?>"><?php echo $pattern; ?></label>
                                                            <textarea class="long" name="seo_patterns[<?php echo $kind['id']; ?>][<?php echo $property_type['id']; ?>][<?php echo $key; ?>]" id="wpl_seo_patterns_ptype<?php echo $kind['id']; ?>_<?php echo $property_type['id']; ?>_<?php echo $key; ?>"><?php echo ((isset($values[$kind['id']]) and isset($values[$kind['id']][$property_type['id']]) and isset($values[$kind['id']][$property_type['id']][$key])) ? $values[$kind['id']][$property_type['id']][$key] : ''); ?></textarea>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div id="wpl_seo_patterns_kind<?php echo $kind['id']; ?>">
                                    <?php foreach($patterns as $key=>$pattern): ?>
                                        <div class="prow">
                                            <div class="text-wp">
                                                <label for="wpl_seo_patterns_ptype<?php echo $kind['id']; ?>_<?php echo $key; ?>"><?php echo $pattern; ?></label>
                                                <textarea class="long" name="seo_patterns[<?php echo $kind['id']; ?>][<?php echo $key; ?>]" id="wpl_seo_patterns_ptype<?php echo $kind['id']; ?>_<?php echo $key; ?>"><?php echo ((isset($values[$kind['id']]) and isset($values[$kind['id']][$key])) ? $values[$kind['id']][$key] : ''); ?></textarea>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
function wpl_seo_patterns_save()
{
    wpl_remove_message();
    
    var ajax_loader_element = "#wpl_ajax_loader_<?php echo $setting_record->id; ?>";
	wplj(ajax_loader_element).html('<img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader3.gif'); ?>" />');
    
    var wpl_patterns = '';
    var request_str = '';
    
    /** general options **/
	wplj("#seo_patterns_wp_<?php echo $setting_record->id; ?> textarea").each(function(index, element)
	{
		wpl_patterns += element.name+"="+wplj(element).val()+"&";
	});
    
    request_str = 'wpl_format=b:settings:ajax&wpl_function=save_seo_patterns&'+wpl_patterns+'&_wpnonce=<?php echo $nonce; ?>';
	
	/** run ajax query **/
	wplj.ajax(
	{
		type: "POST",
		url: '<?php echo wpl_global::get_full_url(); ?>',
		data: request_str,
        dataType: 'json',
		success: function(data)
		{
            if(data.success)
            {
                wpl_show_messages(data.message, '#wpl_seo_patterns_show_message', 'wpl_green_msg');
                wplj(ajax_loader_element).html('');
            }
            else
            {
                wpl_show_messages(data.message, '#wpl_seo_patterns_show_message', 'wpl_red_msg');
                wplj(ajax_loader_element).html('');
            }
		},
		error: function(jqXHR, textStatus, errorThrown)
		{
            wpl_show_messages('<?php echo __('Error Occured.', 'real-estate-listing-realtyna-wpl'); ?>', '#wpl_seo_patterns_show_message', 'wpl_red_msg');
			wplj(ajax_loader_element).html('');
		}
	});
}
</script>
<?php
    $done_this = true;
}