<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Search Widget for Elementor
 * @author Howard <howard@realtyna.com>
 * @package WPL PRO
 */
class wpl_page_builders_elementor_search_widget extends \Elementor\Widget_Base
{
	public function get_name()
	{
		return 'wpl_search_widget';
	}

	public function get_title()
	{
		return __('(WPL) Search', 'real-estate-listing-realtyna-wpl');
	}

	public function get_icon()
	{
		return 'eicon-wordpress';
	}

	public function get_categories()
	{
        return array('wordpress');
	}

	protected function _register_controls()
	{
        $widgets_list = wpl_widget::get_existing_widgets();

        $widgets_list_options = array();
        foreach($widgets_list as $sidebar=>$widgets)
        {
            if($sidebar == 'wp_inactive_widgets') continue;

            foreach($widgets as $widget)
            {
                if(strpos($widget['id'], 'wpl_search_widget') === false) continue;

                $widgets_list_options[$widget['id']] = esc_html__(ucwords(str_replace('_', ' ', $widget['id'])), 'real-estate-listing-realtyna-wpl');
            }
        }

        $this->start_controls_section('content_section', array(
            'label' => __('Content', 'real-estate-listing-realtyna-wpl'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ));

        $this->add_control('wplid', array(
            'label' => esc_html__('Widget', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $widgets_list_options,
            'description' => esc_html__("You can configure your search widget in Appearance -> Widgets menu first and then select it here.", 'real-estate-listing-realtyna-wpl'),
        ));

        $this->end_controls_section();
    }

	protected function render()
	{
        $settings = $this->get_settings_for_display();

		echo do_shortcode('[wpl_widget_instance id="'.$settings['wplid'].'"]');
	}
}