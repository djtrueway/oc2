<?php

/**
 * Class Es_Demo_Content_Page
 */
class Es_Demo_Content_Page extends Es_Object {

	/**
	 * @return void
	 */
	public static function render() {

		$step = filter_input( INPUT_GET, 'step' );

		wp_enqueue_script( 'es-demo-script', ES_ADMIN_CUSTOM_SCRIPTS_URL . 'demo.js' );

		switch ( $step ) {

			case 'demo':
				$path = static::get_step_template( $step );

				break;

            case 'finished':
                $path = static::get_step_template( $step );

                break;

			default:
				$step = 'start';
				$path = static::get_step_template( $step );

		}

		require_once $path;
	}

	/**
	 * @param $step
	 * @return string
	 */
	public static function get_step_template( $step ) {

		$path = ES_ADMIN_TEMPLATES . 'demo-content' . ES_DS . $step . '-' . 'step.php';
		return apply_filters( 'es_demo_content_step_template_path', $path );
	}

	/**
	 * @param $template_name
	 * @return string
	 */
	public static function get_partials_template( $template_name ) {

		$path = ES_ADMIN_TEMPLATES . 'demo-content' . ES_DS . 'partials' . ES_DS . $template_name . '.php';
		return apply_filters( 'es_demo_content_step_template_path', $path );
	}
}
