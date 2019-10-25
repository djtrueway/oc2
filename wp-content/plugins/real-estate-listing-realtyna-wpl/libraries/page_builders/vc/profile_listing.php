<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Profile Listing Shortcode for VC
 * @author Howard <howard@realtyna.com>
 * @package WPL PRO
 */
class wpl_page_builders_vc_profile_listing
{
    public $settings;

    public function __construct()
    {
        // Global WPL Settings
		$this->settings = wpl_global::get_settings();
        
        vc_map(array
        (
            'name' => __('Profile Listing', 'real-estate-listing-realtyna-wpl'),
            //'custom_markup' => '<strong>'.__('WPL Profile Listing', 'real-estate-listing-realtyna-wpl').'</strong>',
            'description' => __('Profile Listing Pages.', 'real-estate-listing-realtyna-wpl'),
            'base' => "wpl_profile_listing",
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
        
        $layouts = wpl_global::get_layouts('profile_listing', array('message.php'), 'frontend');
        
        $layouts_options = array();
        foreach($layouts as $layout) $layouts_options[esc_html__($layout, 'real-estate-listing-realtyna-wpl')] = $layout;
        
        $fields[] = array(
            'heading'         => esc_html__('Layout', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'tpl',
            'value'           => $layouts_options,
            'std'             => '',
            'description'     => esc_html__('Layout of the page', 'real-estate-listing-realtyna-wpl'),
        );
        
        $user_types = wpl_users::get_user_types();
        
        $user_types_options = array();
        $user_types_options['-----'] = '';
        
        foreach($user_types as $user_type) $user_types_options[esc_html__($user_type->name, 'real-estate-listing-realtyna-wpl')] = $user_type->id;
        
        $fields[] = array(
            'heading'         => esc_html__('User Type', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'sf_select_membership_type',
            'value'           => $user_types_options,
            'std'             => '',
            'admin_label'     => true,
            'description'     => esc_html__('You can select different user type for filtering the users', 'real-estate-listing-realtyna-wpl'),
        );
        
        $memberships = wpl_users::get_wpl_memberships();
        
        $memberships_options = array();
        $memberships_options['-----'] = '';
        
        foreach($memberships as $membership) $memberships_options[esc_html__($membership->membership_name, 'real-estate-listing-realtyna-wpl')] = $membership->id;
        
        $fields[] = array(
            'heading'         => esc_html__('Membership', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'sf_select_membership_id',
            'value'           => $memberships_options,
            'std'             => '',
            'description'     => esc_html__('You can filter the users by their membership package', 'real-estate-listing-realtyna-wpl'),
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