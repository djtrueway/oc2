<?php

/**
 * Class Es_Property
 *
 * @property string $address
 * @property float $price
 * @property bool $call_for_price
 * @property bool $bedrooms
 * @property bool $bathrooms
 * @property bool $floors
 * @property array $gallery
 * @property float $lot_size
 * @property float $area
 * @property float $year_built
 * @property float $latitude
 * @property float $longitude
 * @property string $address_components
 */
class Es_Property extends Es_Post
{
    /**
     * @inheritdoc
     */
    public function get_entity_prefix()
    {
        return apply_filters( 'es_property_entity_prefix', 'es_property_' );
    }

    /**
     * Save property address components.

     * @param $data
     */
    public function save_address_components( $data )
    {
        if ( ! empty( $data ) ) {
            foreach ( $data as $item ) {
                $component_id = ES_Address_Components::save_component( $item );
                if ( ! empty( $component_id ) ) {
                    ES_Address_Components::save_property_component( $this->getID(), $component_id );
                }
            }
        }
    }

    /**
     * Return custom fields data.
     *
     * @return mixed
     */
    public function get_custom_data()
    {
        return get_post_meta( $this->_id, 'es_custom_data' );
    }

	/**
	 * @param $section_id
	 *
	 * @return array
	 */
	public static function get_fields_by_section( $section_id ) {
		$fields = static::get_fields();
		$result = array();

		foreach ( $fields as $id => $field ) {
			if ( ! empty( $field['section'] ) && $field['section'] == $section_id ) {
				$result[ $id ] = $field;
			}
		}

		return $result;
	}

    /**
     * Save property fields.
     *
     * @param $data
     */
    public function save_fields( $data )
    {
        if ( ! empty( $data ) ) {
            $units = array();
            $fields = static::get_fields();
            $data = apply_filters( 'es_before_save_property_data', $data, $this );

            if ( ! empty( $data['address_components'] ) && $this->address_components != $data['address_components'] ) {
                $this->delete_last_address_components();
            }

            // Save address components.
            if ( ! empty( $data['address_components'] ) ) {
                $this->save_address_components( json_decode( $data['address_components'] ) );
            }

            // Save another fields.
            foreach ( $fields as $key => $field ) {
                $value = isset( $data[ $key ] ) ? $data[$key] : null;

                $value = $key == 'call_for_price' && ! $value ? 0 : $value;
                $value = $key == 'video' ? esc_attr( $value ) : $value;

                $default_value = isset( $field['default_value'] ) ? $field['default_value'] : '';

                $value = $value ? $value : $default_value;

	            if ( ! empty( $field['type'] ) && $field['type'] == 'file' ) {
		            $file_arr = array();

		            if ( ! empty( $_FILES[ $this->get_base_field_name() ]['name'][ $key ] ) ) {
			            foreach ( $_FILES[ $this->get_base_field_name() ] as $file_key => $field_value ) {
				            if ( ! empty( $field_value[ $key ] ) ) {
					            $file_arr[ $file_key ] = $field_value[ $key ];
				            }
			            }

			            if ( ! empty( $file_arr ) ) {
				            $value = media_handle_sideload( $file_arr, $this->getID() );

				            if ( ! is_wp_error( $value ) && ! empty( $this->{$key} ) ) {
					            wp_delete_attachment( $this->{$key}, true );
				            }
			            }
		            } else {
			            if ( ! isset( $data[ $key ] ) ) {
				            $value = '';
				            if ( ! empty( $this->{$key} ) ) {
					            wp_delete_attachment( $this->{$key}, true );
				            }
			            } else {
				            if ( isset( $data[ $key ] ) ) {
					            $value = $data[ $key ];
				            } else {
					            $value = $this->{$key};
				            }
			            }
		            }
	            }

	            if ( ! $value && empty( $field['required_to_save'] ) ) {
	            	$this->delete_field_value( $key );
	            } else {
		            $this->save_field_value( $key, $value );
	            }

                if ( ! empty( $field['units'] ) && ! empty( $value ) ) {
                   $units[ $key ] = array(
                       'units' => $fields[ $field[ 'units' ] ]['values'],
                       'value' => $value,
                       'unit' => $data[ $field[ 'units' ] ],
                   );
                }
            }

            if ( ! empty( $data['gallery'] ) ) {
                foreach ( $data['gallery'] as $index => $attachment_id ) {
                    $attachment = get_post( $attachment_id );

                    if ( ! $index ) {
	                    set_post_thumbnail( $this->getID(), $attachment_id );
                    }

                    if ( empty( $attachment->post_parent ) ) {
                        wp_update_post( array(
                            'ID' => $attachment_id,
                            'post_parent' => $this->getID(),
                        ) );
                    }
                }
            }

            if ( ! empty( $units ) ) {
                $this->save_units( $units );
            }
        }
    }

    /**
     * @param $units
     */
    public function save_units( array $units )
    {
        if ( ! empty( $units ) ) {
            foreach ( $units as $field => $item ) {
                if ( empty( $item['units'] ) ) continue;

                foreach ( $item['units'] as $unit => $label ) {
                    if ( $item['unit'] == $unit ) {
                        $value = $item['value'];
                    } else {
                        $func = apply_filters( 'es_prepare_unit_callback', 'es_prepare_unit', $unit, $item, $units, $this );
                        if ( function_exists( $func ) ) {
                            $value = call_user_func( $func, $item['unit'], $unit, $item['value'] );
                        }
                    }

                    if ( ! empty( $unit ) && ! empty( $value ) ) {
                        $this->save_field_value( $field . '_' . $unit, $value );
                    }
                }
            }
        }
    }

    /**
     * Save custom property fields.
     *
     * @param $data
     */
    public function save_custom_data( $data )
    {
	    delete_post_meta( $this->getID(), 'es_custom_data' );

        if ( ! empty( $data ) ) {
            foreach ( $data as $key => $value ) {
                if ( ! empty( $key ) && ! empty( $value ) ) {
                    add_post_meta( $this->getID(), 'es_custom_data', array( $key => $value ), false );
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function get_fields()
    {
        /** @var $es_settings Es_Settings_Container */
        /** @var $session_storage Es_Session_Storage */
        global $es_settings, $session_storage;

        $fields = wp_cache_get( 'property_fields', 'ES_PROPERTY_FIELDS' );

        if ( ! $fields ) {

            $fields = array(

                'date_added' => array(
                    'section' => 'es-info',
                    'label' => __( 'Date added', 'es-plugin' ),
                ),

                'post_modified' => array(
					'label' => __( 'Post Updated', 'es-plugin' ),
					'system' => true,
					'section' => 'es-info',
                ),

                'price' => array(
                    'type' => 'number',
                    'tab' => 'es-info',
                    'label' => __('Price', 'es-plugin'),
                    'formatter' => 'price',
                    'search_range_mode' => true,
	                'options' => array(
	                	'step' => 'any',
	                ),
	                'required_to_save' => true,
                ),

                'price_note' => array(
	                'type' => 'text',
	                'tab' => 'es-info',
	                'label' => __('Price note', 'es-plugin'),
                ),

                'call_for_price' => array(
                    'type' => 'checkbox',
                    'tab' => 'es-info',
                    'label' => __('Call for price', 'es-plugin'),
                    'options' => array( 'value' => 1, 'class' => 'es-switch-input' ),
                    'default_value' => 0,
	                'required_to_save' => true
                ),

                'bedrooms' => array(
                    'type' => 'number',
                    'label' => __('Bedrooms', 'es-plugin'),
                    'tab' => 'es-info',
                    'search_range_mode' => true,
                    'section' => 'es-info',
                ),

                'bathrooms' => array(
                    'type' => 'number',
                    'label' => __('Bathrooms', 'es-plugin'),
                    'tab' => 'es-info',
                    'search_range_mode' => true,
                    'options' => array('step' => 0.5),
                    'section' => 'es-info',
                ),

                'floors' => array(
                    'type' => 'number',
                    'label' => __('Floors', 'es-plugin'),
                    'tab' => 'es-info',
                    'search_range_mode' => true,
                    'section' => 'es-info',
                ),

                'area' => array(
                    'type' => 'text',
                    'label' => __( 'Area', 'es-plugin' ),
                    'tab' => 'es-info',
                    'search_range_mode' => true,
                    'units' => 'area_unit',
                    'options' => array( 'pattern' => '[0-9.]+' ),
                    'formatter' => 'area',
                    'loop_callback' => array(
                        'callback' => 'es_the_formatted_area',
                        'args' => array( '', ' ', false ),
                    ),
                    'section' => 'es-info',
                ),

                'area_unit' => array(
                    'type' => 'list',
                    'values' => $es_settings::get_setting_values('unit'),
                    'template' => true,
                    'label' => false,
                    'skip_search' => true,
	                'default_value' => $es_settings->unit,
                ),

                'lot_size' => array(
                    'type' => 'text',
                    'label' => __( 'Lot size', 'es-plugin' ),
                    'tab' => 'es-info',
                    'search_range_mode' => true,
                    'units' => 'lot_size_unit',
                    'formatter' => 'area',
                    'loop_callback' => array(
                        'callback' => 'es_the_formatted_lot_size',
                        'args' => array( '', ' ', false ),
                    ),
                    'options' => array( 'pattern' => '[0-9.]+' ),
                    'section' => 'es-info',
                ),

                'lot_size_unit' => array(
                    'type' => 'list',
                    'values' => $es_settings::get_setting_values('unit'),
                    'template' => true,
                    'label' => false,
                    'skip_search' => true,
                    'default_value' => $es_settings->unit,
                ),

                'year_built' => array(
                    'type' => 'text',
                    'label' => __('Year built', 'es-plugin'),
                    'tab' => 'es-info',
                    'section' => 'es-info',
                ),

                'address' => array(
                    'type' => 'autocomplete',
                    'label' => __( 'Address', 'es-plugin' ),
                    'tab' => 'es-address',
                    'options' => array( 'placeholder' => __( 'Address, City, ZIP', 'es-plugin' ) ),
                    'autocomplete_action' => 'es_address_autocomplete',
                ),

                'keywords' => array(
	                'type' => 'text',
                ),

                'latitude' => array(
                    'type' => 'number',
                    'label' => __('Latitude', 'es-plugin'),
                    'tab' => 'es-address', 'options' => array('step' => 'any'),
                ),

                'video' => array(),

                'longitude' => array(
                    'type' => 'number',
                    'tab' => 'es-address',
                    'label' => __('Longitude', 'es-plugin'),
                    'options' => array('step' => 'any'),
                ),

                'address_components' => array(
                    'type' => 'hidden',
                    'tab' => 'es-address',
                    'label' => false,
                ),

                'gallery' => array(
                    'type' => 'custom',
                    'tab' => 'es-media',
                    'template' => ES_PLUGIN_PATH . ES_DS . 'admin' . ES_DS . 'templates' .
                        ES_DS . 'property' . ES_DS . 'media.php',
                ),

                'country' => array(
                    'type' => 'list',
                    'label' => __( 'Country', 'es-plugin' ),
                    'component' => 'address_component',
                    'options' => array(
                        'class' => 'js-es-location',
                        'data-type' => Es_Search_Location::LOCATION_COUNTRY_TYPE,
                        'disabled' => 'disabled',
	                    'data-placeholder' => __( 'Select location', 'es-plugin' )
                    ),
                ),

                'state' => array(
                    'type' => 'list',
                    'label' => __( 'State', 'es-plugin' ),
                    'component' => 'address_component',
                    'options' => array(
                        'class' => 'js-es-location',
                        'data-type' => Es_Search_Location::LOCATION_STATE_TYPE,
                        'disabled' => 'disabled',
                        'data-placeholder' => __( 'Select location', 'es-plugin' )
                    ),
                ),

                'city' => array(
                    'type' => 'list',
                    'label' => __( 'City', 'es-plugin' ),
                    'component' => 'address_component',
                    'options' => array(
                        'class' => 'js-es-location',
                        'data-type' => Es_Search_Location::LOCATION_CITY_TYPE,
                        'disabled' => 'disabled',
                        'data-placeholder' => __( 'Select location', 'es-plugin' )
                    ),
                ),

                'neighborhood' => array(
                    'type' => 'list',
                    'values' => array(__('Neighborhood', 'es-plugin')),
                    'label' => __( 'Neighborhood', 'es-plugin' ),
                    'component' => 'address_component',
                    'options' => array(
                        'class' => 'js-es-location',
                        'data-type' => Es_Search_Location::LOCATION_NEIGHBORHOOD_TYPE,
                        'disabled' => 'disabled'
                    )
                ),

                'street' => array(
                    'type' => 'list',
                    'values' => array(__('Street', 'es-plugin')),
                    'label' => __( 'Street', 'es-plugin' ),
                    'component' => 'address_component',
                    'options' => array(
                        'class' => 'js-es-location',
                        'data-type' => Es_Search_Location::LOCATION_STREET_TYPE,
                        'disabled' => 'disabled'
                    ),
                ),
            );

            // Add taxonomies as property fields (for search).
            $taxonomies = Es_Taxonomy::get_taxonomies_list();

            unset( $taxonomies['es_labels'] );

            if ( ! empty( $taxonomies ) ) {
                foreach ( $taxonomies as $taxonomy) {
                    $tax = new Es_Taxonomy( $taxonomy );

                    $loop_callback = $taxonomy == 'es_status' ? array( 'callback' => 'es_the_status_list', 'args' => array(
                        '', ' ', '', false
                    ) ) : null;

                    $loop_callback = $taxonomy == 'es_type' ? array( 'callback' => 'es_the_types', 'args' => array(
                        '', ' ', '', false
                    ) ) : $loop_callback;

                    $loop_callback = $taxonomy == 'es_rent_period' ? array( 'callback' => 'es_the_rent_period', 'args' => array(
                        '', ' ', '', false
                    ) ) : $loop_callback;

                    $fields[ $taxonomy ] = array(
                        'label' => $tax->get_name(),
                        'system_type' => 'taxonomy',
                        'type' => 'list',
                        'section' => in_array( $taxonomy, array( 'es_rent_period', 'es_status', 'es_type' ) ) ? 'es-info' : null,
                        'loop_callback' => $loop_callback,
                        'values_callback' => array(
                        	'callback' => 'get_terms',
	                        'args' => array( $taxonomy, array( 'hide_empty' => false, 'fields' => 'id=>name' ) ),
                        ),
                        'search_operator' => 'IN',
                        'options' => array(
                            'class' => 'es-select2-tags',
                            'multiple' => 'multiple',
                            'data-placeholder' => $tax->get_name(),
                        ),
                    );

                    if ( $taxonomy == 'es_feature' || $taxonomy == 'es_amenities' ) {
                        $fields[ $taxonomy ]['search_operator'] = 'AND';
                    }
                }
            }

            $labels = self::get_labels_list();

            if ( ! empty( $labels ) ) {
                foreach ( $labels as $term ) {
                	if ( ! empty( $term->name ) ) {
		                $fields = Es_Object::push_column( array(
			                $term->slug => array(
				                'type' => 'checkbox',
				                'tab' => 'es-info',
				                'label' => __( $term->name, 'es-plugin' ),
				                'required_to_save' => true,
				                'options' => array(
					                'value' => 1,
					                'class' => 'es-switch-input'
				                ),
			                ),
		                ), $fields, 3 );
	                }
                }
            }

	        $builder_fields = Es_FBuilder_Helper::get_fields( 'property' );

            $builder_fields['custom'] = array(
                'type' => 'custom',
                'tab' => 'es-info',
                'template' => ES_PLUGIN_PATH . ES_DS . 'admin' . ES_DS . 'templates' .
                    ES_DS . 'property' . ES_DS . 'custom-fields.php',
            );

            $fields = array_merge( $fields, $builder_fields );
            wp_cache_set( 'property_fields', $fields, 'ES_PROPERTY_FIELDS', 120 );
        }
        return apply_filters( 'es_property_get_fields', $fields );
    }

    /**
     * Return list of labels.
     *
     * @return array|int|WP_Error
     */
    public static function get_labels_list()
    {
        return get_terms( array( 'taxonomy' => 'es_labels', 'hide_empty' => false ) );
    }

	/**
	 * Return es_labels taxonomy colors.
	 *
	 * @return mixed
	 */
	public static function get_label_colors()
	{
		$standard_labels = es_get_standard_label_names();
		$colors = ! empty ( $standard_labels ) ? array_keys( $standard_labels ) : array();
		$colors[] = '#c40808';
		return apply_filters( 'es_get_label_colors', $colors );
	}

    /**
     * Save property data. Used for save_post hook.
     *
     * @param $post_id
     * @param $post
     *
     * @return void
     */
    public static function save( $post_id, $post )
    {
        if ( $post->post_type == static::get_post_type_name() ) {
            // Initialize property object.
            $property = new static( $post_id );
            // Get property fields data from the post request.
            $data = filter_input( INPUT_POST, 'property', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
            // Save these fields.
            $property->save_fields( $data );

            // Saving custom property data fields (that created manually).
            $keys = filter_input(INPUT_POST, 'es_custom_key', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $values = filter_input(INPUT_POST, 'es_custom_value', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

            $keys = $keys ? $keys : array();
            $values = $values ? $values : array();

            $custom_data = array_combine( $keys, $values );
            $property->save_custom_data( $custom_data );
        }
    }

    /**
     * @param $data
     */
    public function save_terms( $data )
    {
        $taxonomies = Es_Taxonomy::get_taxonomies_list();
        foreach ( $taxonomies as $taxonomy_name ) {
            if ( ! empty( $data[ $taxonomy_name ] ) ) {
                if ( ! $data[ $taxonomy_name ] ) continue;

                $data[ $taxonomy_name ] = is_string( $data[ $taxonomy_name ] ) ? array( $data[ $taxonomy_name ] ) : $data[ $taxonomy_name ];

                $data[ $taxonomy_name ] = array_map( function( $value ) {
                    return is_numeric( $value ) ? intval( $value ) : $value;
                }, $data[ $taxonomy_name ] );

                wp_set_object_terms( $this->getID(), $data[ $taxonomy_name ], $taxonomy_name );
            }
        }
    }

    /**
     * Return post type name.
     *
     * @return mixed
     */
    public static function get_post_type_name()
    {
        return apply_filters( 'es_property_post_type_name', 'properties' );
    }

    /**
     * Find properties ids using address.
     *
     * @param $address
     * @return array
     */
    public static function find_by_address( $address )
    {
        global $wpdb;

        $post_ids = $wpdb->get_col( "SELECT post_id 
            FROM $wpdb->postmeta 
            WHERE meta_key = 'es_property_address' 
            AND LOWER(`meta_value`) 
            LIKE '%" . strtolower( $address ) . "%'" );

        if ( empty( $post_ids ) ) {
            $components = ES_Address_Components::find_components( $address );

            if ( $components ) {
                $meta_query = array( 'relation' => 'OR' );
                foreach ( $components as $component ) {
                    $meta_query[] = array(
                        'key' => '_address_component_' . $component->type,
                        'value' => $component->id,
                        'compare' => '='
                    );
                }
                $posts = new WP_Query( array(
                    'post_type' => Es_Property::get_post_type_name(),
                    'post_status' => 'publish',
                    'meta_query' => $meta_query,
                    'fields' => 'ids',
                ) );

                $post_ids = $posts->posts;
            }
        }

        return $post_ids;
    }

    /**
     * Delete post meta data.
     *
     * @param $post_id
     */
    public static function delete( $post_id ) {
        $post = get_post( $post_id );

        if ( $post instanceof WP_Post && $post->post_type == self::get_post_type_name() ) {
            $property = es_get_property( $post->ID );
            $gallery = $property->gallery;

            // Remove attachments.
            if ( $gallery ) {
                array_walk( $gallery, function( $id ) use ( $post_id ) {
                    $check = false;

                    global $wpdb;
                    $results = $wpdb->get_results( "SELECT meta_value FROM {$wpdb->postmeta} 
                        WHERE meta_key='es_property_gallery' 
                        AND post_id NOT IN ($post_id) 
                        AND meta_value IS NOT NULL 
                        AND meta_value LIKE '%" . $id . "%'" );

                    if ( ! empty( $results ) ) {
                        foreach ( $results as $result ) {
                            $meta = ! empty( $result->meta_value ) ? unserialize( $result->meta_value ) : array();

                            if ( is_array( $meta ) && in_array( $id, $meta ) ) {
                                $check = true;
                                break;
                            }
                        }
                    }

                    if ( ! $check ) {
                        wp_delete_attachment( $id, true );
                    }
                } );
            }

            // Remove address components.
            $components = json_decode( $property->address_components );
            if ( $components ) {
                global $wpdb;
                $prefix = '_address_component_';

                foreach ( $components as $component ) {
                    if ( empty( $component->types[0] ) ) continue;

                    $type = $component->types[0];
                    $key = $prefix . $type;
                    $component = ES_Address_Components::get_property_component( $property->getID(), $type );

                    if ( $component ) {
                        $check_meta = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->postmeta} 
                                                      WHERE meta_key = '{$key}' AND meta_value = '{$component->id}'" );

                        if ( $check_meta == 1 ) {
                            ES_Address_Components::delete_component( $component->id );
                        }
                    }
                }
            }

            $property->delete_last_address_components();
        }
    }

    public function delete_last_address_components() {
        // Remove address components.
        $components = json_decode( $this->address_components );
        if ( $components ) {
            global $wpdb;
            $prefix = '_address_component_';

            foreach ( $components as $component ) {
                if ( empty( $component->types[0] ) ) continue;

                $type = $component->types[0];
                $key = $prefix . $type;
                $component = ES_Address_Components::get_property_component( $this->getID(), $type );

                if ( $component ) {
                    $check_meta = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->postmeta} 
                                                      WHERE meta_key = '{$key}' AND meta_value = '{$component->id}'" );

                    if ( $check_meta == 1 ) {
                        ES_Address_Components::delete_component( $component->id );
                    }
                }
            }
        }
    }

	/**
	 * Return entity name.
	 *
	 * @return string
	 */
	public function get_entity_name() {

		return 'property';
	}

	/**
	 * @return string
	 */
	public function get_base_field_name() {
		return 'property';
	}
}
