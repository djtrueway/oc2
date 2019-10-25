<?php global $es_property, $es_settings; ?>
<style>.es-gallery br{display: none;}</style>
<div class="es-gallery">
	<?php do_action( 'es_property_gallery_before_inner', $es_property ); ?>
	<?php if ( $gallery = $es_property->gallery ) : ?>
        <div class="es-gallery-inner">
            <div class="es-gallery-image">
				<?php foreach ( $gallery as $value ) : ?>
                    <div>
                        <a href="<?php echo wp_get_attachment_image_url( $value, 'full' ); ?>">
							<?php echo wp_get_attachment_image( $value, 'es-image-size-archive' ); ?>
                        </a>
                    </div>
				<?php endforeach; ?>
            </div>

            <div class="es-gallery-image-pager-wrap">
                <a href="#" class="es-single-gallery-arrow es-single-gallery-slick-prev">1</a>
                <div class="es-gallery-image-pager">
					<?php foreach ( $gallery as $value ) : ?>
                        <div><?php echo wp_get_attachment_image( $value, 'thumbnail' ); ?></div>
					<?php endforeach; ?>
                </div>
                <a href="#" class="es-single-gallery-arrow es-single-gallery-slick-next">2</a>
            </div>
        </div>
	<?php elseif ( $image = es_get_default_thumbnail( 'es-image-size-archive' ) ): ?>
		<?php echo $image; ?>
	<?php endif; ?>
</div>
