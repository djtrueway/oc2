<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Property Listing Shortcode for Elementor
 * @author Howard <howard@realtyna.com>
 * @package WPL PRO
 */
class wpl_page_builders_elementor_property_listing extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'wpl_property_listing';
    }

    public function get_title()
    {
        return __('Property Listing', 'real-estate-listing-realtyna-wpl');
    }

    public function get_icon()
    {
        return 'fa fa-list';
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

        // Kind Options
        $kinds = wpl_flex::get_kinds('wpl_properties');

        $kinds_options = array();
        foreach($kinds as $kind) $kinds_options[$kind['id']] = esc_html__($kind['name'], 'real-estate-listing-realtyna-wpl');

        $this->add_control('kind', array(
            'label' => esc_html__('Kind', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $kinds_options,
            'default' => 0,
        ));

        // Listing Options
        $listings = wpl_global::get_listings();

        $listings_options = array();
        foreach($listings as $listing) $listings_options[$listing['id']] = esc_html__($listing['name'], 'real-estate-listing-realtyna-wpl');

        $this->add_control('sf_select_listing', array(
            'label' => esc_html__('Listing Type', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $listings_options,
        ));

        $property_types = wpl_global::get_property_types();

        $property_types_options = array();
        foreach($property_types as $property_type) $property_types_options[$property_type['id']] = esc_html__($property_type['name'], 'real-estate-listing-realtyna-wpl');

        $this->add_control('sf_select_property_type', array(
            'label' => esc_html__('Property Type', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $property_types_options,
        ));

        // Location Text
        $location_settings = wpl_global::get_settings('3'); # location settings

        $this->add_control('sf_locationtextsearch', array(
            'label' => esc_html__('Location', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'description' => esc_html__($location_settings['locationzips_keyword'].', '.$location_settings['location3_keyword'].', '.$location_settings['location1_keyword'], 'real-estate-listing-realtyna-wpl'),
        ));

        // Price Options
        $units = wpl_units::get_units(4);

        $default_unit = NULL;
        $price_unit_options = array();

        $p = 1;
        foreach($units as $unit)
        {
            if($p == 1) $default_unit = $unit['id'];

            $price_unit_options[$unit['id']] = esc_html__($unit['name'], 'real-estate-listing-realtyna-wpl');
            $p++;
        }

        $this->add_control('sf_min_price', array(
            'label' => esc_html__('Price (Min)', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => '',
        ));

        $this->add_control('sf_max_price', array(
            'label' => esc_html__('Price (Max)', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => '',
        ));

        $this->add_control('sf_unit_price', array(
            'label' => esc_html__('Price Unit', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $price_unit_options,
            'default' => $default_unit,
        ));

        // Tags Options
        $tags = wpl_flex::get_tag_fields(0);
        foreach($tags as $tag)
        {
            $this->add_control('sf_select_'.$tag->table_column, array(
                'label' => esc_html__($tag->name, 'real-estate-listing-realtyna-wpl'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => array(
                    esc_html__('Any', 'real-estate-listing-realtyna-wpl') => '-1',
                    esc_html__('No', 'real-estate-listing-realtyna-wpl') => '0',
                    esc_html__('Yes', 'real-estate-listing-realtyna-wpl') => '1',
                ),
            ));
        }

        // Users Options
        $wpl_users = wpl_users::get_wpl_users();

        $wpl_users_options = array();
        foreach($wpl_users as $wpl_user) $wpl_users_options[$wpl_user->ID] = esc_html__($wpl_user->user_login.((trim($wpl_user->first_name) != '' or trim($wpl_user->last_name) != '') ? ' ('.$wpl_user->first_name.' '.$wpl_user->last_name.')' : ''), 'real-estate-listing-realtyna-wpl');

        $this->add_control('sf_select_user_id', array(
            'label' => esc_html__('User', 'real-estate-listing-realtyna-wpl'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $wpl_users_options,
        ));

        $this->end_controls_section();

        $this->start_controls_section('display_section', array(
            'label' => __('Display', 'real-estate-listing-realtyna-wpl'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ));

        // Layouts Options
        $layouts = wpl_global::get_layouts('property_listing', array('message.php'), 'frontend');

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
        $sorts = wpl_sort_options::render(wpl_sort_options::get_sort_options(0, 1));

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
            $possible_fields = array('tpl', 'kind', 'sf_select_listing', 'sf_select_property_type', 'sf_locationtextsearch', 'sf_min_price', 'sf_max_price', 'sf_unit_price', 'sf_select_user_id', 'wpltarget', 'limit', 'wplpagination', 'orderby', 'order', 'wplcolumns');

            $tags = wpl_flex::get_tag_fields(0);
            foreach($tags as $tag) $possible_fields[] = 'sf_select_'.$tag->table_column;

            if(!in_array($key, $possible_fields) or trim($value) == '') continue;
            $atts .= $key.'="'.$value.'" ';
        }

        echo do_shortcode('[WPL '.trim($atts).']');
    }
}