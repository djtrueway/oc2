<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Profile Wizard Shortcode for VC
 * @author Howard <howard@realtyna.com>
 * @package WPL PRO
 */
class wpl_page_builders_vc_profile_wizard
{
    public $settings;

    public function __construct()
    {
        // Global WPL Settings
		$this->settings = wpl_global::get_settings();
        
        vc_map(array
        (
            'name' => __('My Profile', 'real-estate-listing-realtyna-wpl'),
            //'custom_markup' => '<strong>'.__('WPL My Profile', 'real-estate-listing-realtyna-wpl').'</strong>',
            'description' => __('Profile Wizard Pages.', 'real-estate-listing-realtyna-wpl'),
            'base' => "wpl_my_profile",
            'class' => '',
            'controls' => 'full',
            'icon' => 'wpb-wpl-icon',
            'category' => __('WPL', 'real-estate-listing-realtyna-wpl'),
            'params' => $this->get_fields()
        ));
	}
    
    public function get_fields()
    {
        // Module Fields
        $fields = array();

		return $fields;
	}
}