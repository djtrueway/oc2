<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('widgets.googlemap.main');

/**
 * Google Maps Widget Shortcode for VC
 * @author Howard <howard@realtyna.com>
 * @package WPL Core
 */
class wpl_page_builders_vc_widget_googlemap
{
    public $settings;

    public function __construct()
    {
        // Global WPL Settings
		$this->settings = wpl_global::get_settings();
        
        // VC Widget Shortcode
        add_shortcode('wpl_vc_googlemap_widget', array($this, 'shortcode_callback'));
        
        vc_map(array
        (
            'name' => __('WPL Google Maps Widget', 'real-estate-listing-realtyna-wpl'),
            //'custom_markup' => '<strong>'.__('WPL Google Maps Widget', 'real-estate-listing-realtyna-wpl').'</strong>',
            'description' => __('WPL Google Maps Widget', 'real-estate-listing-realtyna-wpl'),
            'base' => 'wpl_vc_googlemap_widget',
            'class' => '',
            'controls' => 'full',
            'icon' => 'wpb-wpl-icon',
            'category' => __('WPL', 'real-estate-listing-realtyna-wpl'),
            'params' => $this->get_fields()
        ));
	}
    
    public function get_fields()
    {
        $googlemap = new wpl_googlemap_widget();
        
        // Module Fields
        $fields = array();
        
        $fields[] = array(
            'heading'         => esc_html__('Title', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'textfield',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'title',
            'value'           => '',
            'admin_label'     => true,
            'description'     => esc_html__('The widget title', 'real-estate-listing-realtyna-wpl'),
        );
        
        $widget_layouts = $googlemap->get_layouts('googlemap');
        
        $widget_layouts_options = array();
        foreach($widget_layouts as $widget_layout) $widget_layouts_options[esc_html__(ucfirst(str_replace('.php', '', $widget_layout)), 'real-estate-listing-realtyna-wpl')] = str_replace('.php', '', $widget_layout);
        
        $fields[] = array(
            'heading'         => esc_html__('Layout', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'tpl',
            'value'           => $widget_layouts_options,
            'std'             => '',
            'description'     => esc_html__('Layout of the widget', 'real-estate-listing-realtyna-wpl'),
        );
        
        $fields[] = array(
            'heading'         => esc_html__('CSS Class', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'textfield',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'css_class',
            'value'           => '',
        );
        
		return $fields;
	}
    
    public function shortcode_callback($atts)
    {
        ob_start();
        
        $googlemap = new wpl_googlemap_widget();
        $googlemap->widget(array(
            'before_widget'=>'',
            'after_widget'=>'',
            'before_title'=>'',
            'after_title'=>'',
        ),
        array
        (
            'title'=>isset($atts['title']) ? $atts['title'] : '',
            'layout'=>isset($atts['tpl']) ? $atts['tpl'] : '',
            'data'=>array(
                'css_class'=>isset($atts['css_class']) ? $atts['css_class'] : '',
            )
        ));
        
        return ob_get_clean();
    }
}