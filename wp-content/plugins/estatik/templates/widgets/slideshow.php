<?php

/**
 * @var string $uid
 * @var WP_Post[] $posts
 * @var array $atts
 */

global $post;

$temp = $post;
$classes = $atts['slides_to_show'] > 1 && count($posts) > 1 ? ' es-slideshow-slide-margin' : '';
$uid = uniqid(); ?>

<style>
	#es-slideshow__<?php echo $uid; ?> .slick-list {
		margin: 0 -<?php echo $atts['margin']; ?>px;
	}

	<?php if ( ! $atts['show_arrows'] ) : ?>
	#es-slideshow__<?php echo $uid; ?> .slick-dots {
		display: none !important;
	}
	<?php endif; ?>
</style>

<div class="js-es-slideshow es-slideshow es-slideshow__<?php echo $atts['layout'] . $classes; ?>" id="#es-slideshow__<?php echo $uid; ?>" data-args='<?php echo json_encode( $atts, JSON_HEX_QUOT | JSON_HEX_TAG  ); ?>'>
    <?php foreach ( $posts as $_post ) : $post = $_post; $property = es_get_property( $post->ID ); ?>
        <?php if ( ! empty( $property->gallery ) ) : ?>
            <div>
                <div class="es-slide es-slide__<?php the_ID(); ?>" style="margin: <?php echo $atts['margin']; ?>px">
                    <div class="es-slide__image">
                        <a href="<?php the_permalink(); ?>">
                            <?php es_the_post_thumbnail( 'es-image-size-archive' ); ?>
                        </a>
                    </div>
                    <div class="es-slide__content">
                        <div class="es-slide__top">
                            <span class="es-property-slide-categories"><?php es_the_categories(); ?></span>
                            <?php es_the_formatted_price(); ?>
                        </div>
                        <div class="es-slide__bottom">
                            <?php es_the_formatted_area( '<span class="es-bottom-icon"><i class="es-icon es-squirefit" aria-hidden="true"></i> ', '</span>' ); ?>
                            <?php es_the_formatted_bedrooms( '<span class="es-bottom-icon"><i class="es-icon es-bed" aria-hidden="true"></i> ', '</span>' ); ?>
                            <?php es_the_formatted_bathrooms( '<span class="es-bottom-icon"><i class="es-icon es-bath" aria-hidden="true"></i> ', '</span>' ); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; $post = $temp; ?>
</div>
