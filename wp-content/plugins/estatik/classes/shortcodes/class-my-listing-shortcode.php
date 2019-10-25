<?php

global $properties_shortcode_counter;
$properties_shortcode_counter = 0;

/**
 * Class Es_My_Listing_Shortcode for [es_my_listing] shortcode.
 */
class Es_My_Listing_Shortcode extends Es_Shortcode
{
	/**
	 * @var string Loop Identifier.
	 */
	public $uid;

	/**
	 * @return string
	 */
	public function get_shortcode_title() {

		return __( 'My listings', 'es-plugin' );
	}

    /**
     * @inheritdoc
     */
    public function build( $atts = array() )
    {
    	global $properties_shortcode_counter;
	    $properties_shortcode_counter++;

    	// Set Unique Loop Identifier.
    	$this->uid = $properties_shortcode_counter;

        // Merge shortcode attributes,
        $atts = $this->merge_shortcode_atts( $atts );

        // Prepare layout names from prev. plugin version.
        if ( ! empty( $atts['layout'] ) ) {
            switch ( $atts['layout'] ) {
                case 'table':
                    $atts['layout'] = '3_col';
                    break;
                case '2columns':
                    $atts['layout'] = '2_col';
                    break;
            }
        }

        return $this->property_loop(
            $this->build_query_args( $atts ),
            $atts
        );
    }

    /**
     * Merge shortcode attributes (default / input).
     *
     * @param $atts
     * @return array
     */
    public function merge_shortcode_atts( $atts )
    {
        return shortcode_atts( $this->get_shortcode_default_atts(), $atts, $this->get_shortcode_name() );
    }

    /**
     * @inheritdoc
     */
    public function get_shortcode_name()
    {
        return 'es_my_listing';
    }

    /**
     * @inheritdoc
     */
    public function get_shortcode_default_atts()
    {
        global $es_settings;

        return apply_filters( $this->get_shortcode_name() . '_default_atts', array(
            // list, 2_col, 3_col
            'layout' => $es_settings->listing_layout,
            'posts_per_page' => $es_settings->properties_per_page,
            // recent, highest_price, lowest_price, most_popular
            'sort' => null,
            // Taxonomies.
            'status' => null,
            'type' => null,
            'rent_period' => null,
            'category' => null,
            // 1,2,3,...n
            'prop_id' => null,
            // Show filter dropdown with sort values.
            'show_filter' => 1,
            // Simple address string.
            'address' => null,
            'feature' => null,
            'amenities' => null,
            'labels' => null,
            'city' => null,
            'state' => null,
            'neighborhood' => null,
            'country' => null,
            'strict_address' => false,
	        'price_min' => null,
	        'price_max' => null,
	        'limit' => null,
        ) );
    }

    /**
     * Build query_args array for wp_query class.
     *
     * @param $atts
     * @return array
     */
    public function build_query_args( $atts )
    {
        // Get property class.
        $property = es_get_property( null );

	    $page_num = ! empty( $_GET[ 'paged-' . $this->uid ] ) ? $_GET[ 'paged-' . $this->uid ] : 1;
	    $page_num = intval( $page_num );

	    $query_args = array(
		    'post_type'           => $property::get_post_type_name(),
		    'post_status'         => 'publish',
		    'posts_per_page'      => $atts[ 'posts_per_page' ],
		    'paged' => $page_num,
	    );

	    if ( ! empty( $atts['limit'] ) ) {
		    unset( $query_args['paged'] );
		    $query_args['no_found_rows'] = true;
		    $query_args['posts_per_page'] = $atts['limit'];
	    }

        $taxonomies = apply_filters( 'es_registered_' . $this->get_shortcode_name() . '_taxonomies', array(
            'es_category', 'es_status', 'es_type', 'es_rent_period',
        ) );

        if ( ! empty( $_GET['view_sort' . '-' . $this->uid]  ) ) {
	        $atts['sort'] = sanitize_key( $_GET['view_sort' . '-' . $this->uid ]  );
        }

        switch ( $atts['sort'] ) {
            case 'recent':
                $query_args['orderby'] = 'post_date';
                $query_args['order'] = 'DESC';
                break;

            case 'highest_price':
                $query_args['orderby'] = 'meta_value_num';
                $query_args['meta_key'] = 'es_property_price';
                $query_args['order'] = 'DESC';
                $query_args['meta_query']['call_for_price']['call_for_price'] = array(
	                array( 'compare' => '=', 'key' => $property->get_entity_prefix() . 'call_for_price', 'value' => '0' ),
                );
                break;

            case 'lowest_price':
                $query_args['orderby'] = 'meta_value_num';
                $query_args['meta_key'] = 'es_property_price';
                $query_args['order'] = 'ASC';
                $query_args['meta_query']['call_for_price'] = array(
	                array( 'compare' => '=', 'key' => $property->get_entity_prefix() . 'call_for_price', 'value' => '0' ),
                );
                break;

            case 'featured':
                $query_args['orderby'] = 'meta_value_num';
                $query_args['meta_key'] = 'es_property_featured';
                $query_args['order'] = 'DESC';
                break;

            case 'most_popular':
                $query_args['meta_query'][] = array( 'key' => 'es_property_featured', 'value' => 1 );
                break;

            case 'title':
                $query_args['orderby'] = 'post_title';
                $query_args['order'] = 'ASC';
                break;

            default:
                $query_args['orderby'] = 'post_date';
                $query_args['order'] = 'DESC';
        }

	    if ( ! empty( $atts ) ) {
		    foreach ( $atts as $key => $value ) {
			    $tax_name = apply_filters( 'es_taxonomy_shortcode_name', 'es_' . $key );
			    if ( in_array( $tax_name, $taxonomies ) && taxonomy_exists( $tax_name ) ) {
				    if ( ! empty( $value ) ) {
					    if ( $tax_name == 'es_labels' ) {
						    $value = explode( ',', $value );

						    if ( $value ) {
							    foreach ( $value as $name ) {
								    $term = get_term_by( 'name', $name, $tax_name );
								    $query_args['meta_query'][] = array( 'compare' => '=', 'key' => 'es_property_' . $term->slug, 'value' => 1 );
							    }
						    }
					    } else {
						    $query_args['tax_query'][] = array( 'taxonomy' => $tax_name, 'field' => 'name', 'terms' => explode( ',', $value ) );
					    }
				    }
			    }

			    $field_info = Es_Property::get_field_info( $key );

			    if ( ! empty( $field_info['fbuilder'] ) ) {
				    if ( ! empty( $value ) ) {
					    if ( ! empty( $field_info['values'] ) ) {

					    	if ( is_string( $value ) ) {
							    $value = array_map( 'trim', explode( ',', $value ) );
						    }

						    foreach ( $value as $_key => $_value ) {
							    $query_args['meta_query'][] = array(
								    'key'     => 'es_property_' . $key,
								    'value'   => $_value,
								    'compare' => 'LIKE',
							    );
						    }
					    } else {
						    $field_values = array_map( 'trim', explode( ',', $value ) );
						    $query_args['meta_query'][ $key ]['relation'] = 'OR';

						    foreach ( $field_values as $field_key => $field_value ) {
							    $query_args['meta_query'][$key][] = array( 'key' => $property->get_entity_prefix() . $key, 'value' => $field_value );
						    }
					    }
				    }
			    }
		    }
	    }

	    if ( ! empty( $atts['state'] ) ) {
		    $query_args['meta_query'][] = array( 'key' => '_address_component_administrative_area_level_1', 'value' => $atts['state'] );
	    }

	    if ( ! empty( $atts['country'] ) ) {
		    $query_args['meta_query'][] = array( 'key' => '_address_component_country', 'value' => $atts['country'] );
	    }

	    if ( ! empty( $atts['city'] ) ) {
		    $query_args['meta_query'][] = array( 'key' => '_address_component_locality', 'value' => $atts['city'] );
	    }

	    if ( ! empty( $atts['neighborhood'] ) ) {
		    $query_args['meta_query'][] = array( 'key' => '_address_component_neighborhood', 'value' => $atts['neighborhood'] );
	    }

        if ( ! empty( $atts['address'] ) ) {

        	if ( ! empty( $atts['strict_address'] ) ) {
        		$query_args['meta_query'][] = array( 'key' => 'es_property_address', 'value' => $atts['address'], 'compare' => 'LIKE' );
	        } else {
		        if ( $output = preg_split( "/[,\s]/", $atts['address'] ) ) {

			        $ids = array();

			        foreach ( $output as $key => $address_part ) {
				        if ( empty( $address_part ) ) continue;
				        $ids = array_merge( $ids, Es_Property::find_by_address( $address_part ) );
			        }

			        if ( ! empty( $ids ) ) {
				        $atts['prop_id'] = $ids;
			        } else {
				        $atts['prop_id'] = array( -1 );
			        }
		        }
	        }
        }

        if ( ! empty( $atts['price_min'] ) || ! empty( $atts['price_max'] ) ) {
	        $query_args['meta_query']['call_for_price'] = array( 'compare' => '=', 'key' => $property->get_entity_prefix() . 'call_for_price', 'value' => '0' );
        }

	    if ( ! empty( $atts['price_min'] ) && empty( $atts['price_max'] ) ) {
		    $query_args['meta_query'][] = array(
			    'key' => $property->get_entity_prefix() . 'price',
			    'value' => (int) $atts['price_min'],
			    'compare' => '>=',
			    'type' => 'NUMERIC',
		    );
	    }

	    if ( ! empty( $atts['price_max'] ) && empty( $atts['price_min'] ) ) {
		    $query_args['meta_query'][] = array(
			    'key' => $property->get_entity_prefix() . 'price',
			    'value' => (int) $atts['price_max'],
			    'compare' => '<=',
			    'type' => 'NUMERIC',
		    );
	    }

	    if ( ! empty( $atts['price_min'] ) && ! empty( $atts['price_max'] ) ) {
		    $query_args['meta_query'][] = array(
			    'key' => $property->get_entity_prefix() . 'price',
			    'value' => array( $atts['price_min'], $atts['price_max'] ),
			    'compare' => 'BETWEEN',
			    'type' => 'NUMERIC',
		    );
	    }

        if ( ! empty( $atts['prop_id'] ) ) {
            if ( is_array( $atts['prop_id'] ) ) {
                $query_args['post__in'] = $atts['prop_id'];
            } else {
                $query_args['post__in'] = array_map( 'trim', explode( ',', $atts['prop_id'] ) );
            }

            if ( empty( $atts['sort'] ) ) {
                $query_args['orderby'] = 'post__in';
                $query_args['order'] = null;
            }
        }

         return apply_filters( $this->get_shortcode_name() . '_query_args', $query_args, $atts, $this );
    }

	/**
	 * Return tax names for filtering properties.
	 *
	 * @return mixed
	 */
	public function get_tax_names()
	{
		return apply_filters( 'es_registered_' . $this->get_shortcode_name() . '_taxonomies', array(
			'es_category', 'es_status', 'es_type', 'es_rent_period', 'es_labels', 'es_amenities', 'es_feature',
		) );
	}

    /**
     * Display listings using
     *
     * @param $query_args
     * @param $atts
     * @return string
     */
    protected function property_loop( $query_args, $atts )
    {
        $properties = new WP_Query( $query_args );
        $properties->properties_loop_identifier = $this->uid;
        $properties->query_vars['properties_loop_identifier'] = $this->uid;

        ob_start();

	    include es_locate_template( 'shortcodes/my-listing.php', 'front', 'es_get_my_listings_template_path' );

        return ob_get_clean();
    }
}
