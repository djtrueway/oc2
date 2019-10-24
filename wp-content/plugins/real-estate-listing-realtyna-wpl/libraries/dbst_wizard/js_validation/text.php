<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

if(in_array($mandatory, array(1, 2)))
{
    if(isset($field->multilingual) and $field->multilingual == 1 and wpl_global::check_multilingual_status())
    {
        $default_language = wpl_addon_pro::get_default_language();
        $js_string .=
        '
        if(wplj.trim(wplj("#wpl_c_'.$field->id.'_'.strtolower($default_language).'").val()) == "" && wplj("#wpl_listing_field_container'.$field->id.'").css("display") != "none")
        {
            wpl_alert("'.sprintf(__('Enter a valid %s for %s!', 'real-estate-listing-realtyna-wpl'), __($label, 'real-estate-listing-realtyna-wpl'), $default_language).'");
            if(go_to_error === true) wpl_notice_required_fields(wplj("#wpl_c_'.$field->id.'_'.strtolower($default_language).'"), "'.$field->category.'");
            return false;
        }
        ';
    }
    else
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
}