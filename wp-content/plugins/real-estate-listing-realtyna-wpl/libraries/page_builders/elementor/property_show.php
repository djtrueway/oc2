<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Property Show Shortcode for Elementor
 * @author Howard <howard@realtyna.com>
 * @package WPL PRO
 */
class wpl_page_builders_elementor_property_show extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'wpl_property_show';
    }

    public function get_title()
    {
        return __('Property Show', 'real-estate-listing-realtyna-wpl');
    }

    public function get_icon()
    {
        return 'fa fa-home';
    }

    public function get_categories()
    {
        return array('wpl');
    }

    protected function _register_controls()
    {
        $this->start_controls_section('filter_section', array(
            'label' => __('Filter', 'real-estate-listing-realtyna-wpl'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ));

        $this->add_control('mls_id', array(
            'label' => esc_html__('Listing ID', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'description' => esc_html__("Insert the Listing ID that you want to show.", 'real-estate-listing-realtyna-wpl'),
        ));

        $this->end_controls_section();

        $this->start_controls_section('display_section', array(
            'label' => __('Display', 'real-estate-listing-realtyna-wpl'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ));

        // Layouts Options
        $layouts = wpl_global::get_layouts('property_show', array('message.php'), 'frontend');

        $layouts_options = array();
        foreach($layouts as $layout) $layouts_options[$layout] = esc_html__($layout, 'real-estate-listing-realtyna-wpl');

        $this->add_control('tpl', array(
            'label' => esc_html__('Layout', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $layouts_options,
        ));

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $atts = '';
        foreach($settings as $key=>$value)
        {
            if(!in_array($key, array('tpl', 'mls_id')) or trim($value) == '') continue;
            $atts .= $key.'="'.$value.'" ';
        }

        echo do_shortcode('[wpl_property_show '.trim($atts).']');
    }
}