<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Visual Composer Compatibility
 * @author Howard <howard@realtyna.com>
 * @package WPL PRO
 */
class wpl_page_builders_vc
{
    /**
     * Constructor method
     * @author Howard <howard@realtyna.com>
     */
    public function __construct()
    {
    }
    
    public function register_elements()
    {
        // Visual Composer is not installed or activated
        if(!defined('WPB_VC_VERSION')) return;
        
        // Include libraries
        _wpl_import('libraries.sort_options');
        
        // Property Listing Shortcode
        _wpl_import('libraries.page_builders.vc.property_listing');
        new wpl_page_builders_vc_property_listing();
        
        // Property Show Shortcode
        _wpl_import('libraries.page_builders.vc.property_show');
        new wpl_page_builders_vc_property_show();
        
        // Profile Listing Shortcode
        _wpl_import('libraries.page_builders.vc.profile_listing');
        new wpl_page_builders_vc_profile_listing();
        
        // Profile Show Shortcode
        _wpl_import('libraries.page_builders.vc.profile_show');
        new wpl_page_builders_vc_profile_show();
        
        // Profile Wizard Shortcode
        _wpl_import('libraries.page_builders.vc.profile_wizard');
        new wpl_page_builders_vc_profile_wizard();
        
        // PRO Addon Elements
        if(wpl_global::check_addon('pro'))
        {
            // User Links Shortcode
            _wpl_import('libraries.page_builders.vc.user_links');
            if(class_exists('wpl_page_builders_vc_user_links')) new wpl_page_builders_vc_user_links();
            
            // WPL Favorites Widget
            _wpl_import('libraries.page_builders.vc.widget_favorites');
            if(class_exists('wpl_page_builders_vc_widget_favorites')) new wpl_page_builders_vc_widget_favorites();
            
            // WPL Unit Switcher Widget
            _wpl_import('libraries.page_builders.vc.widget_unit_switcher');
            if(class_exists('wpl_page_builders_vc_widget_unit_switcher')) new wpl_page_builders_vc_widget_unit_switcher();
        }
        
        // Addon Save Searches Elements
        if(wpl_global::check_addon('save_searches'))
        {
            _wpl_import('libraries.page_builders.vc.addon_save_searches');
            if(class_exists('wpl_page_builders_vc_addon_save_searches')) new wpl_page_builders_vc_addon_save_searches();
        }
        
        // WPL Search Widget
        _wpl_import('libraries.page_builders.vc.widget_search');
        new wpl_page_builders_vc_widget_search();
        
        // WPL Carousel Widget
        _wpl_import('libraries.page_builders.vc.widget_carousel');
        new wpl_page_builders_vc_widget_carousel();
        
        // Tags Addon Elements
        if(wpl_global::check_addon('tags'))
        {
            // WPL Tags Widget
            _wpl_import('libraries.page_builders.vc.widget_tags');
            if(class_exists('wpl_page_builders_vc_widget_tags')) new wpl_page_builders_vc_widget_tags();
        }
        
        // WPL Google Maps Widget
        _wpl_import('libraries.page_builders.vc.widget_googlemap');
        
        _wpl_import('widgets.googlemap.main');
        if(class_exists('wpl_googlemap_widget')) new wpl_page_builders_vc_widget_googlemap();
        
        // WPL Agents Widget
        _wpl_import('libraries.page_builders.vc.widget_agents');
        new wpl_page_builders_vc_widget_agents();
        
        // APS Addon Elements
        if(wpl_global::check_addon('aps'))
        {
            // WPL Summary Widget
            _wpl_import('libraries.page_builders.vc.widget_summary');
            if(class_exists('wpl_page_builders_vc_widget_summary')) new wpl_page_builders_vc_widget_summary();
        }
    }
}

$vc = new wpl_page_builders_vc();
add_action('init', array($vc, 'register_elements'));