<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('widgets.search.main');

/**
 * Search Widget Shortcode for VC
 * @author Howard <howard@realtyna.com>
 * @package WPL PRO
 */
class wpl_page_builders_vc_widget_search
{
    public $settings;

    public function __construct()
    {
        // Global WPL Settings
		$this->settings = wpl_global::get_settings();
        
        // VC Widget Shortcode
        add_shortcode('wpl_vc_search_widget', array($this, 'shortcode_callback'));
        
        vc_map(array
        (
            'name' => __('WPL Search Widget', 'real-estate-listing-realtyna-wpl'),
            //'custom_markup' => '<strong>'.__('WPL Search Widget', 'real-estate-listing-realtyna-wpl').'</strong>',
            'description' => __('WPL Search Widget', 'real-estate-listing-realtyna-wpl'),
            'base' => 'wpl_vc_search_widget',
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
        
        $widgets_list = wpl_widget::get_existing_widgets();
        
        $widgets_list_options = array();
        foreach($widgets_list as $sidebar=>$widgets)
        {
            if($sidebar == 'wp_inactive_widgets') continue;
            
            foreach($widgets as $widget)
            {
                if(strpos($widget['id'], 'wpl_search_widget') === false) continue;
                $widgets_list_options[esc_html__(ucwords(str_replace('_', ' ', $widget['id'])), 'real-estate-listing-realtyna-wpl')] = $widget['id'];
            }
        }
        
        $fields[] = array(
            'heading'         => esc_html__('Widget', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'id',
            'value'           => $widgets_list_options,
            'std'             => '',
            'admin_label'     => true,
            'description'     => esc_html__('Select your desired search widget to show. if there is no widget in the list, Please configure some in Appearance->Widgets menu. You can put them inside of WPL Hidden sidebar.', 'real-estate-listing-realtyna-wpl'),
        );
        
		return $fields;
	}
    
    public function shortcode_callback($atts)
    {
        $shortcode_atts = '';
        foreach($atts as $key=>$value)
        {
            if(trim($value) == '' or $value == '-1') continue;
            
            $shortcode_atts .= $key.'="'.$value.'" ';
        }
        
        return do_shortcode('[wpl_widget_instance'.(trim($shortcode_atts) ? ' '.trim($shortcode_atts) : '').']');
    }
}