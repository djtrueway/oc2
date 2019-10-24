<?php

/**
 * Class Es_Settings_Page
 */
class Es_Settings_Page
{
    /**
     * Render settings page content.
     *
     * @return void
     */
    public static function render()
    {
        $template = apply_filters( 'es_settings_template_path', ES_ADMIN_TEMPLATES . 'settings/settings.php' );
        include_once( $template );
    }

    /**
     * Return tabs of the settings page.
     *
     * @return array
     */
    public static function get_tabs()
    {
        return apply_filters( 'es_settings_get_tabs', array(
            'general' => array(
                'label' => __( 'General', 'es-plugin' ),
                'template' => ES_ADMIN_TEMPLATES . 'settings/general-tab.php'
            ),
            'google-services' => array(
	            'label' => __( 'Google APIs', 'es-plugin' ),
	            'template' => ES_ADMIN_TEMPLATES . 'settings/google-services.php',
            ),
            'layouts' => array(
                'label' => __( 'Layouts', 'es-plugin' ),
                'template' => ES_ADMIN_TEMPLATES . 'settings/layouts-tab.php'
            ),
            'currency' => array(
                'label' => __( 'Currency', 'es-plugin' ),
                'template' => ES_ADMIN_TEMPLATES . 'settings/currency-tab.php'
            ),
            'emails' => array(
	            'label' => __( 'Emails', 'es-plugin' ),
	            'template' => ES_ADMIN_TEMPLATES . 'settings/email-tab.php'
            ),
            'sharing' => array(
                'label' => __( 'Sharing', 'es-plugin' ),
                'template' => ES_ADMIN_TEMPLATES . 'settings/sharing-tab.php'
            ),
            'color' => array(
                'label' => __( 'Color', 'es-plugin' ),
                'template' => ES_ADMIN_TEMPLATES . 'settings/color-tab.php'
            ),
            'account' => array(
	            'label' => __( 'Account', 'es-plugin' ),
	            'template' => ES_ADMIN_TEMPLATES . 'settings/account-tab.php'
            ),
        ) );
    }

    /**
     * Save settings action.
     *
     * @return void
     */
    public static function save()
    {
    	$nonce_name = 'es_save_settings';
	    $nonce = sanitize_key( filter_input( INPUT_POST, $nonce_name ) );

        if ( $nonce && wp_verify_nonce( $nonce, $nonce_name ) && ! empty( $_POST['es_settings'] ) ) {

            /** @var Es_Settings_Container $es_settings */
            global $es_settings;

            // Filtering and preparing data for save.
            $data = apply_filters( 'es_before_save_settings_data', $_POST['es_settings'] );

            // Before save action.
            do_action( 'es_before_settings_save', $data );

            $es_settings->save( $data );

            // After save action.
            do_action( 'es_after_settings_save', $data );
        }
    }
}
