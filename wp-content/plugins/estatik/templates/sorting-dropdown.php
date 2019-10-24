<?php if ( $list = Es_Archive_Sorting::get_sorting_dropdown_values() ):

	$shortcode_identifier = ! empty( $shortcode_identifier ) ? $shortcode_identifier : '';
	$shortcode_identifier_temp = $shortcode_identifier ? '-' . $shortcode_identifier : '';
    $current_value = sanitize_key( filter_input( INPUT_GET, 'view_sort' . $shortcode_identifier_temp ) ); ?>

    <form method="GET" action="<?php echo es_get_current_url(); ?>" class="es-dropdown-container">
        <label>
            <select class="js-es-select2-base js-es-change-submit" name="redirect_view_sort">
                <?php foreach ( $list as $key => $value ): ?>
                    <option value="<?php echo $key; ?>" <?php selected( $current_value, $key ); ?>><?php echo $value; ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <input type="hidden" name="shortcode_identifier" value="<?php echo $shortcode_identifier; ?>"/>
    </form>
<?php endif;
