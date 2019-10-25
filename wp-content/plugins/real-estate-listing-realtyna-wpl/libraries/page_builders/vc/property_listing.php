<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Property Listing Shortcode for VC
 * @author Howard <howard@realtyna.com>
 * @package WPL PRO
 */
class wpl_page_builders_vc_property_listing
{
    public $settings;

    public function __construct()
    {
        // Global WPL Settings
		$this->settings = wpl_global::get_settings();
        
        vc_map(array
        (
            'name' => __('Property Listing', 'real-estate-listing-realtyna-wpl'),
            //'custom_markup' => '<strong>'.__('WPL Property Listing', 'real-estate-listing-realtyna-wpl').'</strong>',
            'description' => __('Property Listing Pages. (only one per page)', 'real-estate-listing-realtyna-wpl'),
            'base' => "WPL",
            'class' => '',
            'controls' => 'full',
            'icon' => 'wpb-wpl-icon',
            'show_settings_on_create' => true,
            'category' => __('WPL', 'real-estate-listing-realtyna-wpl'),
            'params' => $this->get_fields()
        ));
	}
    
    public function get_fields()
    {
        // Module Fields
        $fields = array();
        
        $kinds = wpl_flex::get_kinds('wpl_properties');
        
        $kinds_options = array();
        foreach($kinds as $kind) $kinds_options[esc_html__($kind['name'], 'real-estate-listing-realtyna-wpl')] = $kind['id'];
        
        $fields[] = array(
            'heading'         => esc_html__('Kind', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'kind',
            'value'           => $kinds_options,
            'admin_label'     => true,
            'description'     => esc_html__('Kind/Entity for filtering listings', 'real-estate-listing-realtyna-wpl'),
        );
        
        $listings = wpl_global::get_listings();
        
        $listings_options = array();
        $listings_options['-----'] = '';
        
        foreach($listings as $listing) $listings_options[esc_html__($listing['name'], 'real-estate-listing-realtyna-wpl')] = $listing['id'];
        
        $fields[] = array(
            'heading'         => esc_html__('Listing Type', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'sf_select_listing',
            'value'           => $listings_options,
            'std'             => '',
            'admin_label'     => false,
        );
        
        $property_types = wpl_global::get_property_types();
        
        $property_types_options = array();
        $property_types_options['-----'] = '';
        
        foreach($property_types as $property_type) $property_types_options[esc_html__($property_type['name'], 'real-estate-listing-realtyna-wpl')] = $property_type['id'];
        
        $fields[] = array(
            'heading'         => esc_html__('Property Type', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'sf_select_property_type',
            'value'           => $property_types_options,
            'std'             => '',
        );
        
        $property_listing_layouts = wpl_global::get_layouts('property_listing', array('message.php'), 'frontend');
        
        $property_listing_layouts_options = array();
		$property_listing_layouts_options['-----'] = '';
        foreach($property_listing_layouts as $property_listing_layout) $property_listing_layouts_options[esc_html__($property_listing_layout, 'real-estate-listing-realtyna-wpl')] = $property_listing_layout;
        
        $fields[] = array(
            'heading'         => esc_html__('Layout', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'tpl',
            'value'           => $property_listing_layouts_options,
            'std'             => '',
            'description'     => esc_html__('Layout of the page', 'real-estate-listing-realtyna-wpl'),
        );
        
        $location_settings = wpl_global::get_settings('3'); # location settings
        
        $fields[] = array(
            'heading'         => esc_html__('Location', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'textfield',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'sf_locationtextsearch',
            'value'           => '',
            'description'     => esc_html__($location_settings['locationzips_keyword'].', '.$location_settings['location3_keyword'].', '.$location_settings['location1_keyword'], 'real-estate-listing-realtyna-wpl'),
        );
        
        $units = wpl_units::get_units(4);
        
        $price_unit_options = array();
        foreach($units as $unit) $price_unit_options[esc_html__($unit['name'], 'real-estate-listing-realtyna-wpl')] = $unit['id'];
        
        $fields[] = array(
            'heading'         => esc_html__('Price (Min)', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'textfield',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'sf_min_price',
            'description'     => esc_html__('Minimum Price of listings', 'real-estate-listing-realtyna-wpl'),
        );
        
        $fields[] = array(
            'heading'         => esc_html__('Price (Max)', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'textfield',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'sf_max_price',
            'description'     => esc_html__('Maximum Price of listings', 'real-estate-listing-realtyna-wpl'),
        );
        
        $fields[] = array(
            'heading'         => esc_html__('Price Unit', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'value'           => $price_unit_options,
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'sf_unit_price',
            'description'     => esc_html__('Price Unit', 'real-estate-listing-realtyna-wpl'),
        );
        
        $tags = wpl_flex::get_tag_fields(0);
        foreach($tags as $tag)
        {
            $fields[] = array(
                'heading'         => esc_html__($tag->name, 'real-estate-listing-realtyna-wpl'),
                'type'            => 'dropdown',
                'holder'          => 'div',
                'class'           => '',
                'param_name'      => 'sf_select_'.$tag->table_column,
                'value'           => array(
                    esc_html__('Any', 'real-estate-listing-realtyna-wpl') => '-1',
                    esc_html__('No', 'real-estate-listing-realtyna-wpl') => '0',
                    esc_html__('Yes', 'real-estate-listing-realtyna-wpl') => '1',
                ),
                'std'             => '-1',
            );
        }
        
        $wpl_users = wpl_users::get_wpl_users();
        
        $wpl_users_options = array();
        $wpl_users_options['-----'] = '';
        
        foreach($wpl_users as $wpl_user) $wpl_users_options[esc_html__($wpl_user->user_login.((trim($wpl_user->first_name) != '' or trim($wpl_user->last_name) != '') ? ' ('.$wpl_user->first_name.' '.$wpl_user->last_name.')' : ''), 'real-estate-listing-realtyna-wpl')] = $wpl_user->ID;
        
        $fields[] = array(
            'heading'         => esc_html__('User', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'sf_select_user_id',
            'value'           => $wpl_users_options,
            'std'             => '',
            'description'     => esc_html__('Filter the listings by a certain agent', 'real-estate-listing-realtyna-wpl'),
        );
        
        $pages = wpl_global::get_wp_pages();
        
        $pages_options = array();
        $pages_options['-----'] = '';
        
        foreach($pages as $page) $pages_options[esc_html__($page->post_title, 'real-estate-listing-realtyna-wpl')] = $page->ID;
        
        $fields[] = array(
            'heading'         => esc_html__('Target Page', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'value'           => $pages_options,
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'wpltarget',
            'std'             => '',
        );
        
        $page_sizes = explode(',', trim($this->settings['page_sizes'], ', '));
        
        $page_sizes_options = array();
        foreach($page_sizes as $page_size) $page_sizes_options[esc_html__($page_size, 'real-estate-listing-realtyna-wpl')] = $page_size;
        
        $fields[] = array(
            'heading'         => esc_html__('Page Size', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'limit',
            'value'           => $page_sizes_options,
        );
        
        $fields[] = array(
            'heading'         => esc_html__('Pagination', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'wplpagination',
            'value'           => array(
                '-----' => '',
                esc_html__('Scroll Pagination', 'real-estate-listing-realtyna-wpl') => 'scroll',
            ),
            'std'             => '',
        );
        
        $sorts = wpl_sort_options::render(wpl_sort_options::get_sort_options(0, 1));
        
        $sorts_options = array();
        foreach($sorts as $sort) $sorts_options[esc_html__($sort['name'], 'real-estate-listing-realtyna-wpl')] = $sort['field_name'];
        
        $fields[] = array(
            'heading'         => esc_html__('Order By', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'orderby',
            'value'           => $sorts_options,
        );
        
        $fields[] = array(
            'heading'         => esc_html__('Order', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'order',
            'value'           => array(
                esc_html__('Ascending', 'real-estate-listing-realtyna-wpl') => 'ASC',
                esc_html__('Descending', 'real-estate-listing-realtyna-wpl') => 'DESC',
            ),
            'std'             => 'DESC',
        );
        
        $fields[] = array(
            'heading'         => esc_html__('Columns Count', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'wplcolumns',
            'value'           => array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '6' => '6',
            ),
            'std'             => '3',
        );

		return $fields;
	}
}