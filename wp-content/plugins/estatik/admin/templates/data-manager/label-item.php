<?php

/**
 * @var stdClass $taxonomy
 * @var WP_Term[] $terms
 */

$colors = Es_Property::get_label_colors(); ?>

<div class="es-data-manager-item item-<?php echo $this->_taxonomy->name; ?>">
	<form method="post" data-remove-action="es_ajax_data_manager_remove_term">
		<h3><?php _e( $this->_taxonomy->label, 'es-plugin' ); ?></h3>
		<ul class="es-dm-labels__list">
			<?php if ( $terms = $this->getItems() ) : ?>
				<?php foreach ( $terms as $term ) : $term_color = get_term_meta( $term->term_id, 'es_color', true ) ?>
					<li><label><?php echo __( $term->name, 'es-plugin' ); ?></label>

						<?php if ( ! empty( $colors ) ) : ?>
							<div class="es-data-manager-colors-wrap">
								<?php foreach ( $colors as $key => $color ) : ?>
									<span class="es-label-color__wrap">
                                <input type="radio"
                                       data-action="es_ajax_data_manager_label_color"
                                       id="es_label_color-<?php echo $term->term_id . $key; ?>"
                                       name="es_label_color[<?php echo $term->term_id; ?>]"
                                       class="js-color-item es-radio-color es-radio-color-<?php echo str_replace('#', '', $color); ?>"
                                       value="<?php echo $color; ?>"
	                                <?php checked( $term_color, $color ); ?>
	                                   data-id="<?php echo $term->term_id; ?>"/>
                                <label for="es_label_color-<?php echo $term->term_id . $key; ?>"></label>
                                    </span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<?php if ( $term->slug != 'featured' ) : ?>
							<a href="#" class="es-item-remove js-item-remove"
							   data-id="<?php echo $term->term_id; ?>"
							   data-action="es_ajax_data_manager_remove_term"><span class="es-sprite es-sprite-close"></span></a>
						<?php else: ?>
							<a href="#" class="es-item-remove js-item-remove"
							   data-id="<?php echo $term->term_id; ?>"
							   data-action="es_ajax_data_manager_remove_term"><span class="es-sprite es-sprite-close" style="visibility: hidden;"></span></a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>
		</ul>

		<span class="es-data-manager-item-msg"></span>

		<div class="es-data-manager-item-nav">
			<label><input type="text" name="item_name" required placeholder="<?php _e( 'text/number', 'es-plugin' ); ?>"/></label>
			<a href="" class="es-button-add-item es-data-manager-submit"><?php _e( 'Add new item', 'es-plugin' ); ?></a>
		</div>

		<input type="hidden" name="taxonomy" value="<?php echo $this->_taxonomy->name; ?>"/>
		<?php wp_nonce_field( 'es_add_data_manager_label', 'es_add_data_manager_label' ); ?>
		<input type="hidden" name="action" value="es_ajax_data_manager_add_label"/>
	</form>
</div>
