<?php

/**
 * Class Es_Property_Single_Page
 */
class Es_Property_Single_Page extends Es_Object
{
	/**
	 * Adding actions for single property page.
	 */
	public function actions() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'es_property_single_features', array( $this, 'single_features' ) );
		add_action( 'es_single_gallery', array( $this, 'single_gallery' ) );
		add_action( 'es_single_share', array( $this, 'single_share' ) );
		add_action( 'es_single_fields', array( $this, 'single_fields' ) );
		add_action( 'es_single_tabs', array( $this, 'single_tabs' ) );
		add_action( 'es_map', array( $this, 'map' ) );
		add_action( 'es_single_info', array( $this, 'single_info' ) );
		add_action( 'es_single_top_button', array( $this, 'single_top_button' ) );
		add_action( 'wp_head', array( $this, 'es_open_graph' ), 5 );
		add_action( 'es_single_description_tab', array( $this, 'single_description_tab' ) );
		add_action( 'es_single_map_tab', array( $this, 'single_map_tab' ) );
		add_action( 'es_single_features', array( $this, 'single_features' ) );
		add_action( 'es_single_video_tab', array( $this, 'single_video_tab' ) );
		add_action( 'es_single_render_tab', array( $this, 'single_render_tab' ), 10, 2 );
		add_filter( 'post_thumbnail_html', array( $this, 'single_featured_image_filter' ) );
	}

	/**
	 * @param $html
	 *
	 * @return string
	 */
	public function single_featured_image_filter( $html ) {

		global $es_settings;

		if ( is_singular( 'properties' ) ) {
			if ( ! $es_settings->disable_featured_image ) {
				return $html;
			} else {
				return '';
			}

		}

		return $html;
	}

	/**
	 * @return Es_Property
	 */
	public function get_property() {
		global $post;

		return es_get_property( $post->ID );
	}

	/**
	 * Render custom section action.
	 *
	 * @param $id integer
	 *    Section ID.
	 * @param $section array
	 *    Section config array.
	 */
	public function single_render_tab( $id, $section )
	{
		if ( ! empty( $section['fbuilder'] ) ) {
			$property = $this->get_property();
			$fields = $property::get_fields_by_section( $id );

			ob_start(); do_action( 'es_single_tabbed_content_after', $id ); $content = ob_get_clean();

			if ( $fields && $content ) : ?>
                <div class="es-tabbed-item <?php echo $id; ?>" id="<?php echo $id; ?>">
                    <h3><?php echo $section['label']; ?></h3>
					<?php echo $content; ?>
                </div>
			<?php endif;
		}
	}

	/**
	 * Render property description tab content.
	 *
	 * @return void
	 */
	function single_description_tab() {

		ob_start(); do_action( 'es_single_tabbed_content_after', 'es-description' ); $content = ob_get_clean();

		if ( get_the_content() || $content ) : ?>
            <div class="es-tabbed-item es-description" id="es-description">
                <h3><?php _e( 'Description', 'es-plugin' ); ?></h3>
				<?php es_the_content(); ?>

	            <?php echo $content; ?>
            </div>
		<?php endif;
	}

	/**
	 * Render content of the map tab.
	 *
	 * @return void
	 */
	public function single_map_tab() {
		global $post, $es_settings;
		$es_property = es_get_property( $post->ID );

		ob_start(); do_action( 'es_single_tabbed_content_after', 'es-map' ); $content = ob_get_clean();

		if ( ( ! empty( $es_property->latitude ) && ! empty( $es_settings->google_api_key ) ) || $content ) : ?>
            <div class="es-tabbed-item es-map" id="es-map">
                <h3><?php _e( 'View on map / Neighborhood', 'es-plugin' ); ?></h3>
				<?php do_action( 'es_map' ); ?>
	            <?php echo $content; ?>
            </div>
		<?php endif;
	}

	/**
	 * Render video tab.
	 *
	 * @return void
	 */
	public function single_video_tab() {
		global $post;
		$es_property = es_get_property( $post->ID );

		ob_start(); do_action( 'es_single_tabbed_content_after', 'es-video' ); $content = ob_get_clean();

		if ( $es_property->video || $content ) : ?>
            <div class="es-tabbed-item es-video" id="es-video">
                <h3><?php _e( 'Video', 'es-plugin' ); ?></h3>
	            <?php if ( $es_property->video ) : ?>
                    <?php echo htmlspecialchars_decode( $es_property->video ); ?>
                <?php endif; ?>
                <?php echo $content; ?>
            </div>
		<?php endif;
	}

	/**
	 * Render features list.
	 *
	 * @return void
	 */
	public function single_features()
	{
		$data = self::get_features_data();

		$template = apply_filters( 'es_features_list_template_path', ES_TEMPLATES . '/property/features-list.php' );

		ob_start();
		    do_action( 'es_single_tabbed_content_after', 'es-features' );
		$content_after = ob_get_clean();

        if ( $data || $content_after ) : ?>
            <div class="es-tabbed-item es-features" id="es-features">
                <h3><?php _e( 'Features', 'es-plugin' ); ?></h3>
	            <?php if ( $data ) : ?>
                    <?php foreach ( $data as $features_list_title => $features_list ) : ?>
                        <?php include $template; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php echo $content_after; ?>
            </div>
		<?php endif;
	}

	/**
	 * Adding filters for single property page.
	 *
	 * @return void
	 */
	public function filters()
	{
		add_filter( 'language_attributes', array( __CLASS__, 'open_graph_doctype' ) );
		add_filter( 'the_content', array( $this, 'the_content' ), 1 );
		add_filter( 'es_single_property_tabs', array( $this, 'disable_tabs' ), 3 );
	}

	/**
	 * @param $tabs
	 * @return mixed
	 */
	function disable_tabs( $tabs ) {

		global $es_settings;

		return $es_settings->hide_property_top_bar ? array() : $tabs;
	}

	/**
	 * Register OpenGraph schema.
	 *
	 * @param $output
	 * @return string
	 */
	public static function open_graph_doctype( $output )
	{
		global $post_type, $es_settings;

		if ( is_single() && $post_type == Es_Property::get_post_type_name() && ! $es_settings->disable_og_meta_tags ) {
			$output = $output . '
        xmlns:og="http://opengraphprotocol.org/schema/"
        xmlns:fb="http://www.facebook.com/2008/fbml"';
		}

		return $output;
	}

	/**
	 * Add open graph tags for single property page.
	 *
	 * @return void.
	 */
	public function es_open_graph()
	{
		global $post, $es_settings;

		if( is_single() && $post->post_type == Es_Property::get_post_type_name() && ! $es_settings->disable_og_meta_tags ) {
			$property = es_get_property( $post->ID );

			if ( ! empty( $property->gallery[0] ) ) {
				$img_src = wp_get_attachment_image_src( $property->gallery[0], 'es-image-size-archive' );
			}

			if ( $excerpt = $post->post_excerpt ) {
				$excerpt = strip_tags( $post->post_excerpt );
			} else {
				$excerpt = get_bloginfo( 'description' );
			}

			$title = es_get_the_title();

			if ( $title ) : ?>
                <meta property="og:title" content="<?php echo strip_tags( $title ); ?>"/>
			<?php endif; ?>

            <meta property="og:description" content="<?php echo $excerpt; ?>"/>
            <meta property="og:type" content="article"/>
            <meta property="og:url" content="<?php the_permalink(); ?>"/>
            <meta property="og:site_name" content="<?php echo get_bloginfo(); ?>"/>

			<?php if ( ! empty( $img_src[0] ) ) : ?>
                <meta property="og:image" content="<?php echo $img_src[0]; ?>"/>
			<?php endif; ?>

			<?php
		}
	}

	/**
	 * Enqueue scripts for single property page.
	 *
	 * @return void
	 */
	public function enqueue_scripts()
	{
		global $es_settings;

		$custom = 'assets/js/custom/';

		wp_register_script( 'es-share-script', 'https://static.addtoany.com/menu/page.js' );

		$deps = array ( 'jquery', 'es-front-script', 'es-magnific-script', 'es-slick-script', 'es-share-script' );

		if ( ! empty( $es_settings->google_api_key ) ) {
			$deps[] = 'es-admin-map-script';
		}

		wp_register_script( 'es-front-single-script', ES_PLUGIN_URL . $custom . 'front-single.min.js', $deps );

//		wp_enqueue_script( 'es-front-single-script' );
	}

	/**
	 * Enqueue styles for single property page.
	 *
	 * @return void
	 */
	public function enqueue_styles()
	{
		$custom = 'assets/css/custom/';
		$vendor_admin = 'admin/assets/css/vendor/';

		wp_register_style( 'es-rating-style', ES_PLUGIN_URL . $vendor_admin . 'star-rating-svg.css' );

		wp_register_style( 'es-front-single-style', ES_PLUGIN_URL . $custom . 'front-single.min.css', array( 'es-slick-style', 'es-rating-style', 'es-magnific-style' ) );
	}

	/**
	 * Return single property page tabs.
	 *
	 * @param bool $show_all
	 * @return array
	 */
	public static function get_tabs( $show_all = false )
	{
		global $es_property;

		$tabs = array(
			'es-info' => __( 'Basic facts', 'es-plugin' ),
			'es-map' => __( 'Neighborhood', 'es-plugin' ),
			'es-features' => __( 'Features', 'es-plugin' ),
			'es-video' => __( 'Video', 'es-plugin' ),
		);

		if ( ! $es_property->video && ! $show_all ) {
			unset( $tabs['es-video'] );
		}

		return apply_filters( 'es_single_property_tabs', $tabs );
	}

	/**
	 * Return single property page tabs.
	 *
	 * @return array
	 */
	public static function get_sections() {
		global $es_property;

		$sections = array(
			'es-info' => array(
				'machine_name' => 'es-info',
				'label' => __( 'Basic facts', 'es-plugin' ),
				'render_action' => 'es_single_info_tab',
				'sortable' => false,
				'show_tab' => true,
			),
			'es-description' => array(
				'machine_name' => 'es-description',
				'label' => __( 'Description', 'es-plugin' ),
				'render_action' => 'es_single_description_tab',
				'show_tab' => true,
			),
			'es-map' => array(
				'machine_name' => 'es-map',
				'label' => __( 'Neighborhood', 'es-plugin' ),
				'render_action' => 'es_single_map_tab',
				'show_tab' => true,
			),
			'es-video' => array(
				'machine_name' => 'es-video',
				'label' => __( 'Video', 'es-plugin' ),
				'render_action' => 'es_single_video_tab',
				'show_tab' => true,
			),
			'es-features' => array(
				'machine_name' => 'es-features',
				'label' => __( 'Features', 'es-plugin' ),
				'render_action' => 'es_single_features',
				'show_tab' => true,
			),
		);

		if ( ! $es_property->video ) {
			unset( $sections['es-video'] );
		}

		return apply_filters( 'es_property_sections', $sections );
	}

	/**
	 * Return features data.
	 *
	 * @return array
	 */
	public static function get_features_data()
	{
		$data = array();

		if ( $features = es_get_the_features() ) {
			$data[ __( 'Features', 'es-plugin' ) ] = $features;
		}

		if ( $features = es_get_the_amenities() ) {
			$data[ __( 'Amenities', 'es-plugin' ) ] = $features;
		}

		return apply_filters( 'es_single_features_data', $data );
	}

	/**
	 * Render gallery on property single page.
	 *
	 * @return void
	 */
	public function single_gallery()
	{
		es_load_template( 'property/gallery.php', 'front', 'es_single_gallery_template_path' );
	}

	/**
	 * Render property fields.
	 *
	 * @return void
	 */
	public function single_fields()
	{
		es_load_template( 'property/fields.php', 'front', 'es_single_gallery_fields_path' );
	}

	/**
	 * Return fields for render.
	 *
	 * @return mixed|array
	 */
	public static function get_single_fields_data()
	{
		global $es_property;
		$custom = $es_property->get_custom_data();

		$data = array(
			array( __( 'Date added', 'es-plugin' ) => es_the_date('', '', false ) ),
			array( __( 'Area size', 'es-plugin' ) => es_the_formatted_area( '', ' ', false ) ),
			array( __( 'Lot size', 'es-plugin' ) => es_the_formatted_lot_size( '', ' ', false ) ),
			array( __( 'Rent period', 'es-plugin' ) => es_the_rent_period('', ' ', '', false ) ),
			array( __( 'Type', 'es-plugin' ) => es_the_types('', ' ', '', false ) ),
			array( __( 'Status', 'es-plugin' ) => es_the_status_list('', ' ', '', false ) ),
			array( __( 'Bedrooms', 'es-plugin' ) => es_get_the_property_field( 'bedrooms' ) ),
			array( __( 'Bathrooms', 'es-plugin' ) => es_get_the_property_field( 'bathrooms' ) ),
			array( __( 'Floors', 'es-plugin' ) => es_get_the_property_field( 'floors' ) ),
			array( __( 'Year built', 'es-plugin' ) => es_get_the_property_field( 'year_built' ) ),
		);

		// Include custom fields.
		if ( ! empty( $custom ) ) {
			foreach ( $custom as $value ) {
				$data[] = array( __( key( $value ), 'es-plugin' ) => __( reset( $value ), 'es-plugin' ) );
			}
		}

		return apply_filters( 'es_single_fields_data', $data );
	}

	/**
	 * @param $fields
	 * @return array
	 */
	public static function get_fields_render_data( $fields ) {
		$data = array();

		if ( $fields ) {
			foreach ( $fields as $key => $field ) {
				if ( ! empty( $field['section'] ) ) {
					$value = ! empty( $field['formatter'] ) ?
						es_get_the_formatted_field( $key, $field['formatter'] ) : es_get_the_property_field( $key );

					if ( is_array( $value ) ) {
					    if ( ! empty( $value['markup'] ) ) {
					        $data[] = array( __( $field['label'], 'es-plugin' ) => array( 'markup' => $value['markup'] ) );
                        } else {
					        foreach ( $value as $k => $v ) {
					            $value[ $k ] = __( $v, 'es-plugin' );
                            }
						    $data[] = array(  __( $field['label'], 'es-plugin' ) => implode( ', ', $value ) );
                        }
                    } else if ( strlen( $value ) ) {
						$data[] = array(  __( $field['label'], 'es-plugin' ) => $value );
                    }
				}
			}
		}

		return apply_filters( 'es_property_get_fields_render_data', $data, $fields );
	}

	/**
	 * Render single property tabs.
	 *
	 * @return void
	 */
	public function single_tabs()
	{
		es_load_template( 'property/tabs.php', 'front', 'es_single_tabs_template_path' );
	}

	/**
	 * Render single property map.
	 *
	 * @return void
	 */
	public function map()
	{
		es_the_map();
	}

	/**
	 * Render single property info.
	 *
	 * @return void
	 */
	public function single_info()
	{
		global $es_settings;

		if ( $es_settings->single_layout == 'right' ) {
			do_action( 'es_single_fields' );
			do_action( 'es_single_gallery' );

		} else {
			do_action( 'es_single_gallery' );
			do_action( 'es_single_fields' );
		}
	}

	/**
	 * Render Top button.
	 *
	 * @return void
	 */
	public function single_top_button()
	{
		ob_start(); ?>
        <div class="es-top-arrow">
        <a href="#" class="es-top-link"><?php _e( 'To top', 'es-plugin' ); ?></a>
        </div><?php
		$result = ob_get_clean();

		echo apply_filters( 'es_single_top_button_markup', $result );
	}

	/**
	 * Render share buttons.
	 */
	public function single_share()
	{
		es_load_template( 'property/share.php', 'front', 'es_single_share_template_path' );
	}

	/**
	 * @param $content
	 * @return mixed
	 */
	public function the_content( $content = null )
	{
	    global $post;

		if ( is_singular( Es_Property::get_post_type_name() ) && $post && $post->post_type == Es_Property::get_post_type_name() ) {
			return do_shortcode( '[es_single]' );
		}

		return $content;
	}
}
