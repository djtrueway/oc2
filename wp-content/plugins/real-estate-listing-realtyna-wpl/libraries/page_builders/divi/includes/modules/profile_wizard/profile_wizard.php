<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Profile Wizard Shortcode for Divi Builder
 * @author Howard <howard@realtyna.com>
 * @package WPL PRO
 */
class wpl_page_builders_divi_profile_wizard extends ET_Builder_Module
{
    public $fields_defaults;
    public $settings;

    public function init()
    {
        $this->name = __('My Profile', 'real-estate-listing-realtyna-wpl');
        $this->slug = 'et_pb_wpl_profile_wizard';

		$this->fields_defaults = array();
        
        // Global WPL Settings
		$this->settings = wpl_global::get_settings();
	}
    
    public function get_fields()
    {
        // Module Fields
        $fields = array();

		return $fields;
	}
    
    public function render($atts, $content = NULL, $function_name = NULL)
    {
        $shortcode_atts = '';
        foreach($atts as $key=>$value)
        {
            if(trim($value) == '' or $value == '-1') continue;
            if($key == 'tpl' and $value == 'default') continue;
            
            $shortcode_atts .= $key.'="'.$value.'" ';
        }
        
        return do_shortcode('[wpl_my_profile'.(trim($shortcode_atts) ? ' '.trim($shortcode_atts) : '').']');
    }
}