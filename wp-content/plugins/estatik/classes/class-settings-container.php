<?php

/**
 * Class Es_Settings_Container.
 *
 * @property bool $powered_by_link
 * @property int $properties_per_page
 * @property bool $show_price
 * @property bool $show_labels
 * @property string $title_address
 * @property bool $show_address
 * @property string $date_format
 * @property string $theme_style
 * @property string $listing_layout
 * @property string $single_layout
 * @property string $currency
 * @property string $price_format
 * @property string $currency_position
 * @property string $recaptcha_secret_key
 * @property string $recaptcha_site_key
 * @property string $google_api_key
 * @property string $main_color
 * @property string $property_slug
 * @property string $frame_color
 * @property string $secondary_color
 * @property string $reset_button_color
 * @property integer $thumbnail_attachment_id
 * @property bool $disable_og_meta_tags
 * @property bool $wp_autop
 * @property bool $date_added
 * @property bool $hide_property_top_bar
 * @property string $all_listings_page_id
 * @property array $property_removed_fields
 * @property bool $privacy_policy_checkbox
 * @property integer $term_of_use_page_id
 * @property integer $privacy_policy_page_id
 * @property integer $disable_featured_image
 * @property integer $search_page_id
 * @property integer $email_logo_attachment_id
 * @property integer $is_wishlist_enabled
 * @property integer $buyers_enabled
 * @property string $unit
 * @property string $recaptcha_version
 */
class Es_Settings_Container
{
    /**
     * Prefix for settings. Example {SETTING_PREFIX}powered_by_link.
     */
    const SETTING_PREFIX = 'es_';

    /**
     * Return list of available settings.
     *
     * @return array|mixed
     */
    public static function get_available_settings()
    {
        return apply_filters( 'es_get_available_settings', array(

            'powered_by_link' => array(
                'default_value' => 1,
	            'validate_callback' => 'intval',
            ),

            'unit' => array(
                'default_value' => 'sq_ft',
                'values' => array(
                    'sq_ft'    => __( 'sq ft', 'es-plugin' ),
                    'sq_m'     => __( 'm²', 'es-plugin' ),
                    'acres'    => __( 'acres', 'es-plugin' ),
                    'hectares' => __( 'hectares', 'es-plugin' ),
                    'm3'       => __( 'm³', 'es-plugin' ),
                ),
            ),

            'properties_per_page' => array(
                'default_value' => 10,
                'validate_callback' => 'intval',
            ),

            'show_price' => array(
                'default_value' => 1,
                'validate_callback' => 'intval',
            ),

            'wp_autop' => array(
                'default_value' => 1,
                'validate_callback' => 'intval',
            ),

            'show_address' => array(
                'default_value' => 0,
                'validate_callback' => 'intval',
            ),

            'date_added' => array(
                'default_value' => 1,
                'validate_callback' => 'intval',
            ),

            'listing_layout' => array(
                'default_value' => 'list',
                'values' => array(
                    'list' => __( 'List', 'es-plugin' ),
                    '2_col' => __( '2 Columns', 'es-plugin' ),
                    '3_col' => __( '3 Columns', 'es-plugin' ),
                ) ),

            'single_layout' => array(
                'default_value' => 'left',
                'values' => array(
                    'left' => __( 'Left layout', 'es-plugin' ) ,
                    'right' => __( 'Right layout', 'es-plugin' ),
                    'center' => __( 'Center layout', 'es-plugin' ),
                ) ),

            'show_labels' => array(
                'default_value' => 1,
	            'validate_callback' => 'intval',
            ),

            'currency' => array(
                'default_value' => 'USD',
                'values' => array(
                    'USD' => '$',
                    'EUR' => '€',
                    'GBP' => '£',
                    'RUB' => 'RUB',
                ),
            ),

            'price_format' => array(
                'default_value' => ',.',
                'values' => array( ',.' => '19,999.00', '.,' => '19.999,00', ' ' => '19 999', ',' => '19,999', '.' => '19.999' ),
            ),

            'currency_position' => array(
                'default_value' => 'before',
                'values' => array(
                    'after' => __( 'After price', 'es-plugin' ),
                    'before' => __( 'Before price', 'es-plugin' ),
                ) ),

            'title_address' => array(
                'default_value' => 'address',
                'values' => array( 'title' => __( 'Title', 'es-plugin' ), 'address' => __( 'Address', 'es-plugin' ) ),
            ),

            'date_format' => array(
                'default_value' => 'm/d/y',
                'values' => array(
                	'd/m/y' =>  date( 'd/m/y' ),
	                'm/d/y' =>  date( 'm/d/y' ),
	                'd.m.y' =>  date( 'd.m.y' ),
	                'Y.m.d' =>  date( 'Y.m.d' ),
	                'Y-m-d' =>  date( 'Y-m-d' ),
                ),
            ),

            'theme_style' => array(
                'default_value' => 'light',
                'values' => array( 'light' => __( 'Light', 'es-plugin' ), 'dark' => __( 'Dark', 'es-plugin' ) ),
            ),

            'google_api_key' => array(
                'default_value' => '',
            ),

            'thumbnail_attachment_id' => array(
                'default_value' => '',
                'validate_callback' => 'intval',
            ),

            'share_twitter' => array(
                'default_value' => 1,
                'validate_callback' => 'intval',
            ),

            'share_linkedin' => array(
                'default_value' => 1,
                'validate_callback' => 'intval',
            ),

            'share_facebook' => array(
                'default_value' => 1,
                'validate_callback' => 'intval',
            ),

            'share_google_plus' => array(
                'default_value' => 1,
                'validate_callback' => 'intval',
            ),

	        'recaptcha_site_key' => array(
		        'default_value' => '',
	        ),

            'recaptcha_secret_key' => array(
	            'default_value' => '',
            ),

            'recaptcha_version' => array(
	            'default_value' => 'v2',
	            'values' => array(
	            	'v2' => 'v2',
	            	'v3' => 'v3',
	            ),
            ),

            'registration_page_id' => array( 'default_value' => '', 'validate_callback' => 'intval', ),
            'login_page_id' => array( 'default_value' => '', 'validate_callback' => 'intval', ),
            'reset_password_page_id' => array( 'default_value' => '', 'validate_callback' => 'intval', ),

            'all_listings_page_id' => array(
	            'default_value' => '',
	            'validate_callback' => 'intval',
            ),

            'property_removed_fields' => array(
	            'default_value' => array(),
	            'validate_type' => 'array_strings'
            ),

	        'demo_executed' => array(
	        	'default_value' => '',
		        'validate_callback' => 'intval',
	        ),

            'single_featured_listing' => array(
	        	'default_value' => '',
	            'validate_type' => 'array_strings'
	        ),

            'main_color' => array(
	        	'default_value' => '#ff9600',
	            'validate_callback' => 'sanitize_hex_color'
	        ),

            'secondary_color' => array(
	        	'default_value' => '#f0f0f0',
		        'validate_callback' => 'sanitize_hex_color'
	        ),

            'reset_button_color' => array(
	        	'default_value' => '#9e9e9e',
		        'validate_callback' => 'sanitize_hex_color'
	        ),

            'frame_color' => array(
	        	'default_value' => '#1d1d1d',
		        'validate_callback' => 'sanitize_hex_color'
	        ),

            'property_slug' => array(
                'default_value' => 'property',
            ),

            'property_name' => array(
	            'default_value' => 'Property',
            ),

            'hide_property_top_bar' => array(
                'default_value' => '',
                'validate_callback' => 'intval',
            ),

            'disable_og_meta_tags' => array(
                'default_value' => '',
                'validate_callback' => 'intval',
            ),

            'privacy_policy_checkbox' => array(
                'default_value' => 'required',
                'values' => array(
                    'required' => __( 'Required', 'es-plugin' ),
                    'optional' => __( 'Optional', 'es-plugin' ),
                ),
            ),

            'term_of_use_page_id' => array(
                'default_value' => '',
                'validate_callback' => 'intval',
            ),

            'privacy_policy_page_id' => array(
                'default_value' => '',
                'validate_callback' => 'intval',
            ),

            'disable_featured_image' => array(
                'default_value' => 1,
                'validate_callback' => 'intval',
            ),

            'search_page_id' => array(
                'default_value' => '',
                'validate_callback' => 'intval',
            ),

            'email_logo_attachment_id' => array(
		        'default_value' => '',
		        'validate_callback' => 'intval',
	        ),

            'is_wishlist_enabled' => array(
	            'default_value' => 1,
	            'validate_callback' => 'intval',
            ),

            'buyers_enabled' => array(
	            'default_value' => 1,
	            'validate_callback' => 'intval',
            ),
        ) );
    }

    /**
     * Return list if available values using setting name.
     *
     * @param $name
     * @return null
     */
    public static function get_setting_values( $name ) {
        $settings = static::get_available_settings();
        $name = sanitize_key( $name );

        $stored_values = get_option( self::SETTING_PREFIX . $name . '_values', array() );
        $defined_values = ! empty( $settings[ $name ]['values'] ) ? $settings[ $name ]['values'] : array();

        $values = array_merge( $defined_values, $stored_values );

        return $values ? $values : null;
    }

    /**
     * Return option value using setting name.
     *
     * @param $name
     * @return string|null
     */
    public function __get( $name )
    {
        $settings = static::get_available_settings();
        $name = sanitize_key( $name );

        return isset( $settings[ $name ]['default_value'] ) ?
            get_option( static::SETTING_PREFIX . $name, $settings[ $name ]['default_value'] ) : null;

    }

    /**
     * Return field default value.
     *
     * @param $name
     * @return null
     */
    public function get_default_value( $name ) {

        $settings = static::get_available_settings();
        return ! emptY( $settings[ $name ]['default_value'] ) ? $settings[ $name ]['default_value'] : null;
    }

    /**
     * Magic method for empty and isset methods.
     *
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        $value = $this->__get( $name );
        return ! empty( $value );
    }

    /**
     * Save one settings.
     *
     * @param $name
     * @param $value
     *
     * @return void
     */
    public function saveOne( $name, $value )
    {
    	$name = sanitize_key( $name );
        $value = static::validate_input( $name, $value );

    	update_option( static::SETTING_PREFIX . $name, $value );
    }

	/**
	 * Validate user input.
	 *
	 * @param $name
	 * @param $value
	 *
	 * @return mixed
	 */
    public static function validate_input( $name, $value ) {

    	$fields = static::get_available_settings();

    	$callback = ! empty( $fields[ $name ]['validate_callback'] ) ? $fields[ $name ]['validate_callback'] : 'sanitize_text_field';

    	$callback = apply_filters( 'es_settings_field_validation_callback', $callback, $name, $value );

    	if ( ! empty( $fields[ $name ]['validate_type'] ) ) {

    		switch( $fields[ $name ]['validate_type'] ) {

			    case 'array_strings':
			    	$new_value = array_map( 'sanitize_text_field', $value );
			    	break;

			    default:
				    $new_value = sanitize_text_field( $value );
		    }

	    } else if ( function_exists( $callback ) ) {
    		$new_value = call_user_func( $callback, $value );
	    } else {

    		// If callback is empty - validate using function below.
		    $new_value = sanitize_text_field( $value );
	    }

    	return $value == ' ' ? $value : $new_value;
    }

    /**
     * Save settings list.
     *
     * @param array $data
     * @see update_option
     */
    public function save( array $data )
    {
        if ( ! empty( $data ) ) {
            $settings = static::get_available_settings();

            foreach ( $settings as $name => $setting ) {
            	$name = sanitize_key( $name );
                if ( isset( $data[ $name ] ) ) {
                	if ( ( is_string($data[ $name ]) && $data[ $name ] != ' ' ) || is_array( $data[ $name ] ) ) {
		                $data[ $name ] = static::validate_input( $name, $data[ $name ] );
	                }
                    update_option( static::SETTING_PREFIX . $name, $data[ $name ] );
                }
            }
        }
    }

    /**
     * Return label of the value.
     *
     * @param $name
     * @param $value
     * @return null
     */
    public function get_label( $name, $value )
    {
        $default = static::get_setting_values( $name );
        return ! empty( $default[ $value ] ) ? $default[ $value ] : null;
    }
}
