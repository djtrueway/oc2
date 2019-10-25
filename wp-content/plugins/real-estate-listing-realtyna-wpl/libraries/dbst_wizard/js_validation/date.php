<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

if(in_array($mandatory, array(1, 2)))
{
	$js_string .=
	'
    if(wplj.trim(wplj("#wpl_c_'.$field->id.'").val()) == "" && wplj("#wpl_listing_field_container'.$field->id.'").css("display") != "none")
    {
        wpl_alert("'.sprintf(__('Enter a valid %s!', 'real-estate-listing-realtyna-wpl'), __($label, 'real-estate-listing-realtyna-wpl')).'");
        if(go_to_error === true) wpl_notice_required_fields(wplj("#wpl_c_'.$field->id.'"), "'.$field->category.'");
        return false;
    }
	';
}