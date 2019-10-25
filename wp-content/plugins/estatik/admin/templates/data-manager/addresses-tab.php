<?php do_action( 'es_before_addresses_tab' );
$types = apply_filters( 'es_load_dm_address_types', array(
	'country' => 'country',
	'administrative_area_level_1' => array(
		'administrative_area_level_1',
		'administrative_area_level_2',
		'administrative_area_level_3'
	),
	'locality' => array(
		'locality',
		'postal_town',
		'administrative_area_level_4',
	),
	'neighborhood' => 'neighborhood',
) ); ?>

<?php if ( ! empty( $types ) ) : ?>
    <?php foreach ( $types as $t => $type ) : ?>
        <?php $dmi = new Es_Data_Manager_Address_Item( $type, array(
			'label' => ES_Address_Components::get_label_by_type( $t ),
			'id' => 'es-' . $t . '-dm-item',
		) ); $dmi->render(); ?>
    <?php endforeach; ?>
<?php endif;

do_action( 'es_after_addresses_tab' );
