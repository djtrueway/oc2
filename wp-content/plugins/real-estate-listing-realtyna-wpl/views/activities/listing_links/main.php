<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** activity class **/
class wpl_activity_main_listing_links extends wpl_activity
{
    public $tpl_path	= 'views.activities.listing_links.tmpl';
    
	public function start($layout, $params)
	{
		$gre_enable = wpl_global::get_setting('gre_enable');
		$g_site_key = wpl_global::get_setting('gre_site_key');
		$report_listing = wpl_global::get_setting('gre_report_listing');
		$send_to_friend = wpl_global::get_setting('gre_send_to_friend');
		$request_visit  = wpl_global::get_setting('gre_request_visit');

		if($gre_enable === '1' and ($report_listing === '1' or $send_to_friend === '1' or $request_visit === '1'))
		{
			$locale = wpl_global::get_current_language();

			// Include Google recaptcha Library
			$javascript = (object) array('param1'=>'google-recaptcha-wpl', 'param2'=>'//www.google.com/recaptcha/api.js?siteKey='.$g_site_key.'&hl='.str_replace('_', '-', $locale) , 'param4'=>'1', 'external'=>true);
			wpl_extensions::import_javascript($javascript, false);
		}

        /** include layout **/
		$layout_path = _wpl_import($layout, true, true);
		include $layout_path;
	}
}