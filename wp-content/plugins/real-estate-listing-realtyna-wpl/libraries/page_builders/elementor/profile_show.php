<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Profile Show Shortcode for Elementor
 * @author Howard <howard@realtyna.com>
 * @package WPL PRO
 */
class wpl_page_builders_elementor_profile_show extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'wpl_profile_show';
    }

    public function get_title()
    {
        return __('Profile/Agent Show', 'real-estate-listing-realtyna-wpl');
    }

    public function get_icon()
    {
        return 'fa fa-user';
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

        // Users Options
        $wpl_users = wpl_users::get_wpl_users();

        $wpl_users_options = array();
        foreach($wpl_users as $wpl_user) $wpl_users_options[$wpl_user->ID] = esc_html__($wpl_user->user_login.((trim($wpl_user->first_name) != '' or trim($wpl_user->last_name) != '') ? ' ('.$wpl_user->first_name.' '.$wpl_user->last_name.')' : ''), 'real-estate-listing-realtyna-wpl');

        $this->add_control('uid', array(
            'label' => esc_html__('User', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $wpl_users_options,
            'description' => esc_html__("Select an agent to show.", 'real-estate-listing-realtyna-wpl'),
        ));

        $this->end_controls_section();

        $this->start_controls_section('display_section', array(
            'label' => __('Display', 'real-estate-listing-realtyna-wpl'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ));

        // Layouts Options
        $layouts = wpl_global::get_layouts('profile_show', array('message.php'), 'frontend');

        $layouts_options = array();
        foreach($layouts as $layout) $layouts_options[$layout] = esc_html__($layout, 'real-estate-listing-realtyna-wpl');

        $this->add_control('tpl', array(
            'label' => esc_html__('Layout', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $layouts_options,
        ));

        // Target Options
        $pages = wpl_global::get_wp_pages();

        $pages_options = array();
        foreach($pages as $page) $pages_options[$page->ID] = esc_html__($page->post_title, 'real-estate-listing-realtyna-wpl');

        $this->add_control('wpltarget', array(
            'label' => esc_html__('Target Page', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $pages_options,
            'description' => esc_html__("You don't need to select a target page in most of cases.", 'real-estate-listing-realtyna-wpl'),
        ));

        // Pagination
        $this->add_control('wplpagination', array(
            'label' => esc_html__('Pagination', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => array(
                '' => '-----',
                'scroll' => esc_html__('Scroll Pagination', 'real-estate-listing-realtyna-wpl'),
            ),
        ));

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $atts = '';
        foreach($settings as $key=>$value)
        {
            if(!in_array($key, array('uid', 'tpl', 'wpltarget', 'wplpagination')) or trim($value) == '') continue;
            $atts .= $key.'="'.$value.'" ';
        }

        echo do_shortcode('[wpl_profile_show '.trim($atts).']');
    }
}