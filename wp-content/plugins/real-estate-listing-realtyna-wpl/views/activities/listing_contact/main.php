<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** activity class **/
class wpl_activity_main_listing_contact extends wpl_activity
{
    public $tpl_path = 'views.activities.listing_contact.tmpl';
	
	public function start($layout, $params)
	{
		/** Settings **/
		$this->settings = wpl_settings::get_settings();

		/** include layout **/
		$layout_path = _wpl_import($layout, true, true);
		include $layout_path;
	}
}