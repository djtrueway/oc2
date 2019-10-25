<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Profile Show Shortcode for VC
 * @author Howard <howard@realtyna.com>
 * @package WPL PRO
 */
class wpl_page_builders_vc_profile_show
{
    public $settings;

    public function __construct()
    {
        // Global WPL Settings
		$this->settings = wpl_global::get_settings();
        
        vc_map(array
        (
            'name' => __('Profile/Agent Show', 'real-estate-listing-realtyna-wpl'),
            //'custom_markup' => '<strong>'.__('WPL Profile/Agent Show', 'real-estate-listing-realtyna-wpl').'</strong>',
            'description' => __('Profile/Agent Show Pages.', 'real-estate-listing-realtyna-wpl'),
            'base' => "wpl_profile_show",
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
        
        $layouts = wpl_global::get_layouts('profile_show', array('message.php'), 'frontend');
        
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
        
        $wpl_users = wpl_users::get_wpl_users();
        
        $wpl_users_options = array();
        foreach($wpl_users as $wpl_user) $wpl_users_options[esc_html__($wpl_user->user_login.((trim($wpl_user->first_name) != '' or trim($wpl_user->last_name) != '') ? ' ('.$wpl_user->first_name.' '.$wpl_user->last_name.')' : ''), 'real-estate-listing-realtyna-wpl')] = $wpl_user->ID;
        
        $fields[] = array(
            'heading'         => esc_html__('User', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'uid',
            'value'           => $wpl_users_options,
            'std'             => '',
            'admin_label'     => true,
            'description'     => esc_html__('The agent to show', 'real-estate-listing-realtyna-wpl'),
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

		return $fields;
	}
}