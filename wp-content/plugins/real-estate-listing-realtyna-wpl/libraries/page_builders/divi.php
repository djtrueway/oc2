<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Divi Builder Compatibility
 * @author Howard <howard@realtyna.com>
 * @package WPL PRO
 */
class wpl_page_builders_divi extends DiviExtension
{
    public $gettext_domain = 'wpl';
    public $name = 'WPL';

    /**
     * Constructor method
     * @author Howard <howard@realtyna.com>
     * @param string $name
     * @param array $args
     */
    public function __construct($name = 'WPL', $args = array())
    {
        $this->plugin_dir = WPL_ABSPATH . 'libraries'.DS.'page_builders'.DS.'divi'.DS;
        $this->plugin_dir_url = wpl_global::get_wpl_url().'libraries/page_builders/divi/';

        parent::__construct($name, $args);
    }

    public function register_modules()
    {
        // Divi Builder is not installed or activated
        if(!class_exists('ET_Builder_Module')) return;

        // Include libraries
        _wpl_import('libraries.sort_options');

        // Property Listing Shortcode
        _wpl_import('libraries.page_builders.divi.includes.modules.property_listing.property_listing');
        new wpl_page_builders_divi_property_listing();

        // Property Show Shortcode
        _wpl_import('libraries.page_builders.divi.includes.modules.property_show.property_show');
        new wpl_page_builders_divi_property_show();

        // Profile Listing Shortcode
        _wpl_import('libraries.page_builders.divi.includes.modules.profile_listing.profile_listing');
        new wpl_page_builders_divi_profile_listing();

        // Profile Show Shortcode
        _wpl_import('libraries.page_builders.divi.includes.modules.profile_show.profile_show');
        new wpl_page_builders_divi_profile_show();

        // Profile Wizard Shortcode
        _wpl_import('libraries.page_builders.divi.includes.modules.profile_wizard.profile_wizard');
        new wpl_page_builders_divi_profile_wizard();

        // PRO Addon Modules
        if(wpl_global::check_addon('pro'))
        {
            // User Links Shortcode
            _wpl_import('libraries.page_builders.divi.includes.modules.user_links.user_links');
            if(class_exists('wpl_page_builders_divi_user_links')) new wpl_page_builders_divi_user_links();

            // WPL Favorites Widget
            _wpl_import('libraries.page_builders.divi.includes.modules.widget_favorites.widget_favorites');
            if(class_exists('wpl_page_builders_divi_widget_favorites')) new wpl_page_builders_divi_widget_favorites();

            // WPL Unit Switcher Widget
            _wpl_import('libraries.page_builders.divi.includes.modules.widget_unit_switcher.widget_unit_switcher');
            if(class_exists('wpl_page_builders_divi_widget_unit_switcher')) new wpl_page_builders_divi_widget_unit_switcher();
        }

        // Addon Save Searches Shortcode
        if(wpl_global::check_addon('save_searches'))
        {
            _wpl_import('libraries.page_builders.divi.includes.modules.addon_save_searches.addon_save_searches');
            if(class_exists('wpl_page_builders_divi_addon_save_searches')) new wpl_page_builders_divi_addon_save_searches();
        }

        // WPL Search Widget
        _wpl_import('libraries.page_builders.divi.includes.modules.widget_search.widget_search');
        new wpl_page_builders_divi_widget_search();

        // WPL Carousel Widget
        _wpl_import('libraries.page_builders.divi.includes.modules.widget_carousel.widget_carousel');
        new wpl_page_builders_divi_widget_carousel();

        // Tags Addon Modules
        if(wpl_global::check_addon('tags'))
        {
            // WPL Tags Widget
            _wpl_import('libraries.page_builders.divi.includes.modules.widget_tags.widget_tags');
            if(class_exists('wpl_page_builders_divi_widget_tags')) new wpl_page_builders_divi_widget_tags();
        }

        // WPL Google Maps Widget
        _wpl_import('libraries.page_builders.divi.includes.modules.widget_googlemap.widget_googlemap');

        _wpl_import('widgets.googlemap.main');
        if(class_exists('wpl_googlemap_widget')) new wpl_page_builders_divi_widget_googlemap();

        // WPL Agents Widget
        _wpl_import('libraries.page_builders.divi.includes.modules.widget_agents.widget_agents');
        new wpl_page_builders_divi_widget_agents();

        // APS Addon Modules
        if(wpl_global::check_addon('aps'))
        {
            // WPL Summary Widget
            _wpl_import('libraries.page_builders.divi.includes.modules.widget_summary.widget_summary');
            if(class_exists('wpl_page_builders_divi_widget_summary')) new wpl_page_builders_divi_widget_summary();
        }
    }
}

$divi = new wpl_page_builders_divi();
add_action('et_builder_ready', array($divi, 'register_modules'));

/* Overriding frontend-builder-plugin-style.css style file of Divi builder and removing global Css from this file */
add_action('wp_enqueue_scripts', 'wpl_et_builder_remove_modules_styles');
function wpl_et_builder_remove_modules_styles()
{
    if(function_exists('et_builder_load_modules_styles'))
    {
        remove_action('wp_enqueue_scripts', 'et_builder_load_modules_styles', 11);
        add_action('wp_enqueue_scripts', 'wpl_et_builder_load_modules_styles', 1000);
    }
}

function wpl_et_builder_load_modules_styles()
{
    $current_page_id = apply_filters('et_is_ab_testing_active_post_id', get_the_ID());
    $is_fb_enabled = function_exists('et_fb_enabled') ? et_fb_enabled() : false;
    $is_ab_testing = function_exists( 'et_is_ab_testing_active' ) ? et_is_ab_testing_active() : false;
    
    wp_register_script( 'google-maps-api', esc_url_raw( add_query_arg( array( 'v' => 3, 'key' => et_pb_get_google_api_key() ), is_ssl() ? 'https://maps.googleapis.com/maps/api/js' : 'http://maps.googleapis.com/maps/api/js' ) ), array(), ET_BUILDER_VERSION, true );
    wp_register_script( 'hashchange', ET_BUILDER_URI . '/scripts/jquery.hashchange.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
    wp_register_script( 'salvattore', ET_BUILDER_URI . '/scripts/salvattore.min.js', array(), ET_BUILDER_VERSION, true );
    wp_register_script( 'easypiechart', ET_BUILDER_URI . '/scripts/jquery.easypiechart.js', array( 'jquery' ), ET_BUILDER_VERSION, true );

    wp_enqueue_script( 'divi-fitvids', ET_BUILDER_URI . '/scripts/jquery.fitvids.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
    wp_enqueue_script( 'waypoints', ET_BUILDER_URI . '/scripts/waypoints.min.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
    wp_enqueue_script( 'magnific-popup', ET_BUILDER_URI . '/scripts/jquery.magnific-popup.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
    wp_enqueue_script( 'et-jquery-touch-mobile', ET_BUILDER_URI . '/scripts/jquery.mobile.custom.min.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
    wp_enqueue_script( 'et-builder-modules-script', ET_BUILDER_URI . '/scripts/frontend-builder-scripts.js', apply_filters( 'et_pb_frontend_builder_scripts_dependencies', array( 'jquery', 'et-jquery-touch-mobile' ) ), ET_BUILDER_VERSION, true );
    wp_enqueue_style( 'magnific-popup', ET_BUILDER_URI . '/styles/magnific_popup.css', array(), ET_BUILDER_VERSION );

    // Load modules wrapper on CPT
    if ( et_builder_post_is_of_custom_post_type() ) {
        wp_enqueue_script( 'et-builder-cpt-modules-wrapper', ET_BUILDER_URI . '/scripts/cpt-modules-wrapper.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
    }

    if ( function_exists('et_builder_has_limitation') and et_builder_has_limitation( 'register_fittext_script') ) {
        wp_register_script( 'fittext', ET_BUILDER_URI . '/scripts/jquery.fittext.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
    }
    /**
     * Builder script handle name
     *
     * @since 3.??
     *
     * @param string
     */
    $builder_modules_script_handle = apply_filters( 'et_builder_modules_script_handle', 'et-builder-modules-script' );

    // Load main styles CSS file only if the Builder plugin is active
    if ( et_is_builder_plugin_active() ) {
        $style_suffix = et_load_unminified_styles() ? '' : '.min';
        wp_enqueue_style( 'et-builder-modules-style', ET_BUILDER_URI . '/styles/frontend-builder-plugin-style' . $style_suffix . '.css', array(), ET_BUILDER_VERSION );
    }

    // Load visible.min.js only if AB testing active on current page OR VB (because post settings is synced between VB and BB)
    if ( $is_ab_testing || $is_fb_enabled ) {
        wp_enqueue_script( 'et-jquery-visible-viewport', ET_BUILDER_URI . '/scripts/ext/jquery.visible.min.js', array( 'jquery', 'et-builder-modules-script' ), ET_BUILDER_VERSION, true );
    }

    wp_localize_script($builder_modules_script_handle, 'et_pb_custom', array(
        'ajaxurl'                => is_ssl() ? admin_url( 'admin-ajax.php' ) : admin_url( 'admin-ajax.php', 'http' ),
        'images_uri'             => get_template_directory_uri() . '/images',
        'builder_images_uri'     => ET_BUILDER_URI . '/images',
        'et_frontend_nonce'      => wp_create_nonce( 'et_frontend_nonce' ),
        'subscription_failed'    => esc_html__( 'Please, check the fields below to make sure you entered the correct information.', 'et_builder' ),
        'et_ab_log_nonce'        => wp_create_nonce( 'et_ab_testing_log_nonce' ),
        'fill_message'           => esc_html__( 'Please, fill in the following fields:', 'et_builder' ),
        'contact_error_message'  => esc_html__( 'Please, fix the following errors:', 'et_builder' ),
        'invalid'                => esc_html__( 'Invalid email', 'et_builder' ),
        'captcha'                => esc_html__( 'Captcha', 'et_builder' ),
        'prev'                   => esc_html__( 'Prev', 'et_builder' ),
        'previous'               => esc_html__( 'Previous', 'et_builder' ),
        'next'                   => esc_html__( 'Next', 'et_builder' ),
        'wrong_captcha'          => esc_html__( 'You entered the wrong number in captcha.', 'et_builder' ),
        'ignore_waypoints'       => et_is_ignore_waypoints() ? 'yes' : 'no',
        'is_divi_theme_used'     => function_exists( 'et_divi_fonts_url' ),
        'widget_search_selector' => apply_filters( 'et_pb_widget_search_selector', '.widget_search' ),
        'is_ab_testing_active'   => $is_ab_testing,
        'page_id'                => $current_page_id,
        'unique_test_id'         => get_post_meta( $current_page_id, '_et_pb_ab_testing_id', true ),
        'ab_bounce_rate'         => '' !== get_post_meta( $current_page_id, '_et_pb_ab_bounce_rate_limit', true ) ? get_post_meta( $current_page_id, '_et_pb_ab_bounce_rate_limit', true ) : 5,
        'is_cache_plugin_active' => false === et_pb_detect_cache_plugins() ? 'no' : 'yes',
        'is_shortcode_tracking'  => get_post_meta( $current_page_id, '_et_pb_enable_shortcode_tracking', true ),
        'tinymce_uri'            => defined( 'ET_FB_ASSETS_URI' ) ? ET_FB_ASSETS_URI . '/vendors' : '',
    ));

    if(is_et_pb_preview())
    {
        // Set fixed protocol for preview URL to prevent cross origin issue
        $preview_scheme = is_ssl() ? 'https' : 'http';

        // Get home url, then parse it
        $preview_origin_component = parse_url(home_url('', $preview_scheme));

        // Rebuild origin URL, strip sub-directory address if there's any (postMessage e.origin doesn't pass sub-directory address)
        $preview_origin = "";

        // Perform check, prevent unnecessary error
        if(isset($preview_origin_component['scheme']) && isset($preview_origin_component['host']))
        {
            $preview_origin = "{$preview_origin_component['scheme']}://{$preview_origin_component['host']}";

            // Append port number if different port number is being used
            if(isset($preview_origin_component['port']))
            {
                $preview_origin = "{$preview_origin}:{$preview_origin_component['port']}";
            }
        }

        // Enqueue theme's style.css if it hasn't been enqueued (possibly being hardcoded by theme)
        if ( ! et_builder_has_theme_style_enqueued() && et_builder_has_limitation( 'force_enqueue_theme_style' ) ) {
            wp_enqueue_style( 'et-builder-theme-style-css', get_stylesheet_uri(), array() );
        }

        wp_enqueue_style('et-builder-preview-style', ET_BUILDER_URI . '/styles/preview.css', array(), ET_BUILDER_VERSION);
        wp_enqueue_script('et-builder-preview-script', ET_BUILDER_URI . '/scripts/frontend-builder-preview.js', array('jquery'), ET_BUILDER_VERSION, true);
        wp_localize_script('et-builder-preview-script', 'et_preview_params', array
        (
            'preview_origin' => esc_url($preview_origin),
            'alert_origin_not_matched' => sprintf(
                esc_html__('Unauthorized access. Preview cannot be accessed outside %1$s.', 'et_builder'),
                esc_url(home_url('', $preview_scheme))
            ),
        ));
    }
}

add_filter('et_pb_admin_excluded_shortcodes', 'wpl_exclude_divi_shortcodes');
function wpl_exclude_divi_shortcodes($shortcodes)
{
    $shortcodes[] = 'WPL';
    $shortcodes[] = 'et_pb_wpl_property_listing';

    $shortcodes[] = 'wpl_addon_save_searches';
    $shortcodes[] = 'et_pb_wpl_addon_save_searches';

    $shortcodes[] = 'wpl_my_profile';
    $shortcodes[] = 'et_pb_wpl_profile_wizard';

    $shortcodes[] = 'wpl_property_show';
    $shortcodes[] = 'et_pb_wpl_property_show';

    $shortcodes[] = 'wpl_profile_listing';
    $shortcodes[] = 'et_pb_wpl_profile_listing';

    $shortcodes[] = 'wpl_profile_show';
    $shortcodes[] = 'et_pb_wpl_profile_show';

    $shortcodes[] = 'wpl_user_links';
    $shortcodes[] = 'et_pb_wpl_user_links';

    $shortcodes[] = 'wpl_user_links';
    $shortcodes[] = 'et_pb_wpl_user_links';

    $shortcodes[] = 'et_pb_wpl_widget_search';
    $shortcodes[] = 'et_pb_wpl_widget_carousel';
    $shortcodes[] = 'wpl_widget_instance';

    $shortcodes[] = 'et_pb_wpl_widget_agents';
    $shortcodes[] = 'et_pb_wpl_widget_favorites';
    $shortcodes[] = 'et_pb_wpl_widget_googlemap';
    $shortcodes[] = 'et_pb_wpl_widget_summary';
    $shortcodes[] = 'et_pb_wpl_widget_tags';
    $shortcodes[] = 'et_pb_wpl_widget_unit_switcher';

    return $shortcodes;
}