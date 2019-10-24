<?php

/**
 * Class Es_Property_Single_Page
 */
class Es_Property_Archive_Page extends Es_Object
{
    /**
     * Adding actions for archive properties page.
     */
    public function actions()
    {
        add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), 100 );
//        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
//        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_footer', array( $this, 'map_popup' ) );
        add_action( 'es_before_content_list', array( $this, 'before_content_list' ) );
        add_action( 'es_after_content_list', array( $this, 'after_content_list' ) );
    }

    /**
     * Enqueue scripts for archive properties page.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
	    global $es_settings;

	    $custom = 'assets/js/custom/';

	    $deps = array( 'jquery', 'es-front-script', 'es-magnific-script' );

	    if ( ! empty( $es_settings->google_api_key ) ) {
		    $deps[] = 'es-admin-map-script';
	    }

	    wp_enqueue_script( 'es-front-archive-script', ES_PLUGIN_URL . $custom . 'front-archive.min.js', $deps );
	    wp_localize_script( 'es-front-archive-script', 'Estatik', Estatik::register_js_variables() );
    }

    /**
     * Enqueue styles for archive properties page.
     *
     * @return void
     */
    public function enqueue_styles()
    {
        $custom = 'assets/css/custom/';

        wp_register_style( 'es-front-archive-style', ES_PLUGIN_URL . $custom . 'front-archive.min.css', array( 'es-magnific-style' ) );
        wp_enqueue_style( 'es-front-archive-style' );
    }

    /**
     * Set posts per page number.
     *
     * @param WP_Query $query
     */
    public function pre_get_posts( $query )
    {
        global $es_settings;

        if ( is_admin() )
            return;

	    if ( $query->is_post_type_archive( Es_Property::get_post_type_name() ) || is_tax() ) {
		    $this->enqueue_scripts();
		    $this->enqueue_styles();
	    }

        if ( $query->is_post_type_archive( Es_Property::get_post_type_name() ) && $query->is_main_query() ) {
            $query->set( 'posts_per_page', $es_settings->properties_per_page );
            return;
        } else if ( ( $term =  $query->get_queried_object() ) instanceof WP_Term && $query->is_main_query() ) {
            $taxonomies = apply_filters( 'es_archive_properties_categories', Es_Taxonomy::get_taxonomies_list() );

            if ( in_array( $term->taxonomy, $taxonomies ) && ! $query->get( 'posts_per_page' ) ) {
                $query->set( 'posts_per_page', $es_settings->properties_per_page );
                return;
            }
        }
    }

    /**
     * Render map popup block.
     *
     * @return void
     */
    public function map_popup()
    {
        echo apply_filters( 'es_map_popup', '<style>.mfp-hide{display: none;}</style><div id="es-map-popup" class="mfp-hide">
            <div id="es-map-inner" class="mfp-with-anim"></div></div>' );
    }

    /**
     * Render wrappers for standard themes.
     *
     * @return void
     */
    public function before_content_list()
    {
        $template = get_option( 'template' );

        if ( 'twentyfifteen' == $template ) {
            echo '<div class="hentry">';
        } else if ( 'twentyfourteen' == $template || 'twentysixteen' == $template ) {
            echo '<div class="entry-content">';
        }
    }

    /**
     * Render wrappers for standard themes.
     *
     * @return void
     */
    public function after_content_list()
    {
        $template = get_option( 'template' );

        if ( 'twentyfifteen' == $template || 'twentyfourteen' == $template || 'twentysixteen' == $template ) {
            echo '</div>';
        }
    }
}
