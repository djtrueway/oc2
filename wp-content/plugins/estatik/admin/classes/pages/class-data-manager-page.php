<?php

/**
 * Class Es_Data_Manager_Page
 */
class Es_Data_Manager_Page extends Es_Object
{
    /**
     * Register actions for data manager page.
     *
     * @return void
     */
    public function actions()
    {
        add_action( 'wp_ajax_es_ajax_data_manager_add_term', array( 'Es_Data_Manager_Term_Item', 'save' ) );
        add_action( 'wp_ajax_es_ajax_data_manager_add_option', array( 'Es_Data_Manager_Item', 'save' ) );
	    add_action( 'wp_ajax_es_ajax_data_manager_add_label', array( 'Es_Data_Manager_Label_Item', 'save' ) );
	    add_action( 'wp_ajax_es_ajax_data_manager_label_color', array( 'Es_Data_Manager_Label_Item', 'change_color' ) );
        add_action( 'wp_ajax_es_ajax_data_manager_remove_term', array( 'Es_Data_Manager_Term_Item', 'remove' ) );
        add_action( 'wp_ajax_es_ajax_data_manager_remove_option', array( 'Es_Data_Manager_Item', 'remove' ) );
        add_action( 'wp_ajax_es_ajax_data_manager_check_option', array( 'Es_Data_Manager_Item', 'check' ) );

        add_action( 'wp_ajax_es_ajax_data_manager_add_currency', array( 'Es_Data_Manager_Currency_Item', 'save' ) );
        add_action( 'wp_ajax_es_ajax_data_manager_remove_currency', array( 'Es_Data_Manager_Currency_Item', 'remove' ) );
        add_action( 'wp_ajax_es_ajax_data_manager_check_currency', array( 'Es_Data_Manager_Currency_Item', 'check' ) );
    }

    /**
     * Render data manager page content.add
     *
     * @return void
     */
    public static function render()
    {
        $template = apply_filters( 'es_data_manager_page_template', ES_PLUGIN_PATH . '/admin/templates/data-manager/data-manager.php' );

        if ( file_exists( $template ) ) {
            include_once( $template );
        }
    }

    /**
     * Return tabs of the data manager page.
     *
     * @return array
     */
    public static function get_tabs()
    {
        return apply_filters( 'es_data_manager_get_tabs', array(
            'properties-details' => array(
                'label' => __( 'Properties details', 'es-plugin' ),
                'template' => ES_PLUGIN_PATH . '/admin/templates/data-manager/properties-details-tab.php'
            ),
            'features' => array(
                'label' => __( 'Features', 'es-plugin' ),
                'template' => ES_PLUGIN_PATH . '/admin/templates/data-manager/features-tab.php'
            ),
            'labels' => array(
	            'label' => __( 'Labels', 'es-plugin' ),
	            'template' => ES_PLUGIN_PATH . '/admin/templates/data-manager/labels-tab.php'
            ),
            'currencies' => array(
                'label' => __( 'Currencies', 'es-plugin' ),
                'template' => ES_PLUGIN_PATH . '/admin/templates/data-manager/currencies-tab.php'
            ),
            'dimensions' => array(
	            'label' => __( 'Dimensions', 'es-plugin' ),
	            'template' => ES_PLUGIN_PATH . '/admin/templates/data-manager/dimensions-tab.php'
            ),
            'addresses' => array(
	            'label' => __( 'Addresses', 'es-plugin' ),
	            'template' => ES_PLUGIN_PATH . '/admin/templates/data-manager/addresses-tab.php'
            ),
        ) );
    }
}
