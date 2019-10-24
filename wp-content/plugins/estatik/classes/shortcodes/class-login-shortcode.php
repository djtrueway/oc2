<?php

/**
 * Class Es_Login_Shortcode
 */
class Es_Login_Shortcode extends Es_Shortcode {
	/**
	 * @return string
	 */
	public function get_shortcode_title() {
		return __( 'Login form', 'es-plugin' );
	}
	/**
	 * Function used for build shortcode.
	 * @see add_shortcode
	 *
	 * @param array $atts Shortcode attributes array.
	 *
	 * @return mixed
	 */
	public function build( $atts = array() ) {

		ob_start();

		$hook = 'es_login_shortcode_template_path';
		es_load_template(  'shortcodes/login.php', 'front', $hook );
		do_action( 'es_shortcode_after', $this->get_shortcode_name() );

		return ob_get_clean();
	}

	/**
	 * Return shortcode name.
	 *
	 * @return string
	 */
	public function get_shortcode_name() {

		return 'es_login';
	}
}
