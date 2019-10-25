<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Property Show Shortcode for Divi Builder
 * @author Howard <howard@realtyna.com>
 * @package WPL PRO
 */
class wpl_page_builders_divi_property_show extends ET_Builder_Module
{
    public $slug       = 'et_pb_wpl_property_show';
    public $vb_support = 'on';

    public function init()
    {
        $this->name = __('Property Show', 'real-estate-listing-realtyna-wpl');
        $this->slug = 'et_pb_wpl_property_show';
		$this->fields_defaults = array();

        // Global WPL Settings
		$this->settings = wpl_global::get_settings();
	}

    public function get_fields()
    {
        // Module Fields
        $fields = array();

        $property_show_layouts = wpl_global::get_layouts('property_show', array('message.php'), 'frontend');

        $property_show_layouts_options = array();
        foreach($property_show_layouts as $property_show_layout) $property_show_layouts_options[$property_show_layout] = esc_html__($property_show_layout, 'real-estate-listing-realtyna-wpl');

        $fields['tpl'] = array(
            'label'           => esc_html__('Layout', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'select',
            'option_category' => 'basic_option',
            'options'         => $property_show_layouts_options,
            'description'     => esc_html__('Layout of the page', 'real-estate-listing-realtyna-wpl'),
        );

        $fields['mls_id'] = array(
            'label'           => esc_html__('Listing ID', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'text',
            'option_category' => 'basic_option',
            'description'     => esc_html__('Insert the Listing ID that you want to show', 'real-estate-listing-realtyna-wpl'),
        );

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

        return do_shortcode('[wpl_property_show'.(trim($shortcode_atts) ? ' '.trim($shortcode_atts) : '').']');
    }
}