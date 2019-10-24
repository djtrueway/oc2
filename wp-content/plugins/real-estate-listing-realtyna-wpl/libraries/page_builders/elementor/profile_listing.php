<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Profile Listing Shortcode for Elementor
 * @author Howard <howard@realtyna.com>
 * @package WPL PRO
 */
class wpl_page_builders_elementor_profile_listing extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'wpl_profile_listing';
    }

    public function get_title()
    {
        return __('Profile/Agent Listing', 'real-estate-listing-realtyna-wpl');
    }

    public function get_icon()
    {
        return 'fa fa-users';
    }

    public function get_categories()
    {
        return array('wpl');
    }

    protected function _register_controls()
    {
        // Global WPL Settings
        $wpl_settings = wpl_global::get_settings();

        $this->start_controls_section('filter_section', array(
            'label' => __('Filter', 'real-estate-listing-realtyna-wpl'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ));

        // User Type options
        $user_types = wpl_users::get_user_types();

        $user_types_options = array();
        foreach($user_types as $user_type) $user_types_options[$user_type->id] = esc_html__($user_type->name, 'real-estate-listing-realtyna-wpl');

        $this->add_control('sf_select_membership_type', array(
            'label' => esc_html__('User Type', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $user_types_options,
        ));

        // Membership Options
        $memberships = wpl_users::get_wpl_memberships();

        $memberships_options = array();
        foreach($memberships as $membership) $memberships_options[$membership->id] = esc_html__($membership->membership_name, 'real-estate-listing-realtyna-wpl');

        $this->add_control('sf_select_membership_id', array(
            'label' => esc_html__('Membership', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $memberships_options,
        ));

        $this->end_controls_section();

        $this->start_controls_section('display_section', array(
            'label' => __('Display', 'real-estate-listing-realtyna-wpl'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ));

        // Layouts Options
        $layouts = wpl_global::get_layouts('profile_listing', array('message.php'), 'frontend');

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

        // Page Size Options
        $page_sizes = explode(',', trim($wpl_settings['page_sizes'], ', '));

        $page_sizes_options = array();
        foreach($page_sizes as $page_size) $page_sizes_options[$page_size] = esc_html__($page_size, 'real-estate-listing-realtyna-wpl');

        $this->add_control('limit', array(
            'label' => esc_html__('Page Size', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $page_sizes_options,
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

        // Order Options
        $sorts = wpl_sort_options::render(wpl_sort_options::get_sort_options(2, 1));

        $sorts_options = array();
        foreach($sorts as $sort) $sorts_options[$sort['field_name']] = esc_html__($sort['name'], 'real-estate-listing-realtyna-wpl');

        $this->add_control('orderby', array(
            'label' => esc_html__('Order By', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $sorts_options,
        ));

        // Order By Options
        $this->add_control('order', array(
            'label' => esc_html__('Order', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => array(
                'ASC' => esc_html__('Ascending', 'real-estate-listing-realtyna-wpl'),
                'DESC' => esc_html__('Descending', 'real-estate-listing-realtyna-wpl'),
            ),
        ));

        // Columns Options
        $this->add_control('wplcolumns', array(
            'label' => esc_html__('Columns Count', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => array(
                '1' => 1,
                '2' => 2,
                '3' => 3,
                '4' => 4,
                '5' => 5,
            ),
            'description' => esc_html__("Number of items per row.", 'real-estate-listing-realtyna-wpl'),
        ));

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $atts = '';
        foreach($settings as $key=>$value)
        {
            if(!in_array($key, array('tpl', 'sf_select_membership_type', 'sf_select_membership_id', 'wpltarget', 'limit', 'wplpagination', 'orderby', 'order', 'wplcolumns')) or trim($value) == '') continue;
            $atts .= $key.'="'.$value.'" ';
        }

        echo do_shortcode('[wpl_profile_listing '.trim($atts).']');
    }
}