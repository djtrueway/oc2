<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Elementor Compatibility
 * @author Howard <howard@realtyna.com>
 * @package WPL PRO
 */
class wpl_page_builders_elementor
{
    /**
     * Constructor method
     * @author Howard <howard@realtyna.com>
     */
    public function __construct()
    {
    }

    /**
     * Initialize the Elementor Compatibility
     * @author Howard <howard@realtyna.com>
     */
    public function init()
    {
        // Register WPL Category
        add_action('elementor/elements/categories_registered', array($this, 'register_category'));

        // Register Widgets
        add_action('elementor/widgets/widgets_registered', array($this, 'register_widgets'), 10);

        // Unregister Original Search Widget
        add_action('elementor/widgets/widgets_registered', array($this, 'unregister_widgets'), 25);
    }

    /**
     * Register WPL Category
     * @author Howard <howard@realtyna.com>
     * @param $elements_manager
     */
    public function register_category($elements_manager)
    {
        $elements_manager->add_category('wpl', array(
            'title' => __('WPL', 'real-estate-listing-realtyna-wpl'),
            'icon' => 'fa fa-plug',
        ));
    }

    /**
     * Unregister Unwanted Widgets
     * @param $widget_manager
     */
    public function unregister_widgets($widget_manager)
    {
        // Unregister Default Search Widget
        $widget_manager->unregister_widget_type('wp-widget-wpl_search_widget');
    }

    /**
     * Register Other Widgets
     * @param $widget_manager
     */
    public function register_widgets($widget_manager)
    {
        // Profile Wizard Shortcode
        _wpl_import('libraries.page_builders.elementor.profile_wizard');
        $widget_manager->register_widget_type(new wpl_page_builders_elementor_profile_wizard());

        // Profile Listing Shortcode
        _wpl_import('libraries.page_builders.elementor.profile_listing');
        $widget_manager->register_widget_type(new wpl_page_builders_elementor_profile_listing());

        // Profile Show Shortcode
        _wpl_import('libraries.page_builders.elementor.profile_show');
        $widget_manager->register_widget_type(new wpl_page_builders_elementor_profile_show());

        // Property Show Shortcode
        _wpl_import('libraries.page_builders.elementor.property_show');
        $widget_manager->register_widget_type(new wpl_page_builders_elementor_property_show());

        // Property Listing Shortcode
        _wpl_import('libraries.page_builders.elementor.property_listing');
        $widget_manager->register_widget_type(new wpl_page_builders_elementor_property_listing());

        // Search Widget Shortcode
        _wpl_import('libraries.page_builders.elementor.search_widget');
        $widget_manager->register_widget_type(new wpl_page_builders_elementor_search_widget());

        // PRO Addon Elements
        if(wpl_global::check_addon('pro'))
        {
            // User Links Shortcode
            _wpl_import('libraries.page_builders.elementor.user_links');
            $widget_manager->register_widget_type(new wpl_page_builders_elementor_user_links());
        }

        // Addon Save Searches Elements
        if(wpl_global::check_addon('save_searches'))
        {
            _wpl_import('libraries.page_builders.elementor.addon_save_searches');
            $widget_manager->register_widget_type(new wpl_page_builders_elementor_addon_save_searches());
        }
    }
}

// Initialize the Elementor Compatibility
$elementor = new wpl_page_builders_elementor();
$elementor->init();