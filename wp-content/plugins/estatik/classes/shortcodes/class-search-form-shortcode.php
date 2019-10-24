<?php

if ( ! defined( 'WPINC' ) ) die;

/**
 * Class Es_Search_Page_Shortcode.
 */
class Es_Search_Form_Shortcode extends Es_Shortcode
{
    protected $_widget_name = 'Es_Search_Widget';

	/**
	 * @return string
	 */
	public function get_shortcode_title() {
		return __( 'Search form', 'es-plugin' );
	}

    /**
     * Function used for build shortcode.
     * @see add_shortcode
     *
     * @param array $atts Shortcode attributes array.
     *
     * @return mixed
     */
    public function build( $atts = array() )
    {
        $atts = shortcode_atts( $this->get_shortcode_default_atts(), $atts, $this->get_shortcode_name() );
        $atts['fields'] = explode( ',', $atts['fields'] );

        ob_start();

        the_widget( $this->_widget_name, $atts );

        return ob_get_clean();
    }

    /**
     * @inheritdoc
     */
    public function get_shortcode_default_atts()
    {
    	global $es_settings;
        return array(
            'fields' => implode( ',', Es_Search_Widget::get_widget_fields() ), // Fields separated by comma.
            'title' => null, // Widget title.
            'layout' => 'vertical', // Also *vertical* is available.
            'page_id' => $es_settings->search_page_id,
        );
    }

    /**
     * Return shortcode name.
     *
     * @return string
     */
    public function get_shortcode_name()
    {
        return 'es_search_form';
    }
}
