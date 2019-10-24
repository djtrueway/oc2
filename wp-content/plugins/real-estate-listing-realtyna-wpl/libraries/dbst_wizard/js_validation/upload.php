<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

if(in_array($mandatory, array(1, 2)))
{
	$js_string .=
	'
	if(!wplj("#preview_upload'.$field->id.'").length)
	{
		wpl_alert("'.__('Upload a valid', 'real-estate-listing-realtyna-wpl').' '.__($label, 'real-estate-listing-realtyna-wpl').'!");
		if(go_to_error === true) wpl_notice_required_fields(wplj("#wpl_c_'.$field->id.'"), "'.$field->category.'");
		return false;
	}
	';
}