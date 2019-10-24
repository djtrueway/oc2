<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

if(in_array($mandatory, array(1, 2)))
{
	$js_string .=
	'
	var wpl_required_fields = new Array();
	var location_temp = true;
	wplj("#wpl_listing_all_location_container'.$field->id.' select.wpl_location_indicator_selectbox").each(function(ind, elm)
	{
		if((elm.value <= 0) && wplj("#wpl_listing_all_location_container'.$field->id.'").css("display") != "none" && elm.length > 1)
		{
			wpl_required_fields.push(elm);
			location_temp = false;
		}
	});
	
	if(!location_temp)
	{
		wpl_alert("'.__('Location data is mandatory', 'real-estate-listing-realtyna-wpl').'!");
		if(go_to_error === true) wpl_notice_required_fields(wpl_required_fields, "'.$field->category.'");
		return false;
	}
	';
}