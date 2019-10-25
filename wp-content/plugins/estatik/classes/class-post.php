<?php

/**
 * Class Es_Post.
 */
abstract class Es_Post extends Es_Entity
{
    /**
     * @inheritdoc
     */
    public function get_entity()
    {
        return get_post( $this->getID() );
    }

    /**
     * @inheritdoc
     */
    public function get_field_value( $field, $single = true )
    {
        return get_post_meta( $this->getID(), $this->get_entity_prefix() . $field, $single );
    }

    /**
     * @inheritdoc
     */
    public function save_field_value( $field, $value )
    {
	    $value = is_string( $value ) ? sanitize_text_field( $value ) : $value;

        $value = apply_filters( 'es_save_property_field_value', $value, $field, $this->getID() );
        update_post_meta( $this->getID(), $this->get_entity_prefix() . $field, $value );
    }

	/**
	 * @param $field
	 * @param string $value
	 */
    public function delete_field_value( $field, $value = '' ) {
    	delete_post_meta( $this->getID(), $this->get_entity_prefix() . $field, $value );
    }

	/**
	 * @param $data
	 */
    public function save_fields( $data ) {

    	foreach ( $data as $key => $value ) {
    		$this->save_field_value( $key, $value );
	    }
    }
}
