<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//XML
Header('Content-type: text/xml');

//Remove primary category
remove_filter( 'post_link_category', 'seopress_titles_primary_cat_hook', 10, 3 ); 

//WPML
function seopress_remove_wpml_home_url_filter( $home_url, $url, $path, $orig_scheme, $blog_id ) {
    return $url;
}
add_filter( 'wpml_get_home_url', 'seopress_remove_wpml_home_url_filter', 20, 5 );

add_filter( 'seopress_sitemaps_single_query', function( $args ) {
    global $sitepress, $sitepress_settings;

    $sitepress_settings['auto_adjust_ids'] = 0;
    remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
    remove_filter( 'category_link', array( $sitepress, 'category_link_adjust_id' ), 1 );

    return $args;
});

add_action( 'the_post', function( $post ) {
	$language = apply_filters(
		'wpml_element_language_code',
		null,
		array( 'element_id' => $post->ID, 'element_type' => 'page' )
	);
	do_action( 'wpml_switch_language', $language );
});

function seopress_xml_sitemap_img_enable_option() {
	$seopress_xml_sitemap_img_enable_option = get_option("seopress_xml_sitemap_option_name");
	if ( ! empty ( $seopress_xml_sitemap_img_enable_option ) ) {
		foreach ($seopress_xml_sitemap_img_enable_option as $key => $seopress_xml_sitemap_img_enable_value)
			$options[$key] = $seopress_xml_sitemap_img_enable_value;
		 if (isset($seopress_xml_sitemap_img_enable_option['seopress_xml_sitemap_img_enable'])) { 
		 	return $seopress_xml_sitemap_img_enable_option['seopress_xml_sitemap_img_enable'];
		 }
	}
}

function seopress_xml_sitemap_single() {
	if( get_query_var( 'seopress_cpt') !== '' ) {
		$path = get_query_var( 'seopress_cpt');
	}

	$offset = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), ".xml");
	$offset = preg_match_all('/\d+/', $offset, $matches);
	$offset = end($matches[0]);

	//Max posts per paginated sitemap
	$max = 1000;
	$max = apply_filters('seopress_sitemaps_max_posts_per_sitemap', $max);

	if (isset($offset) && absint($offset) && $offset !='' && $offset !=0) {
		$offset = (($offset-1)*$max);
	} else {
		$offset = 0;
	}


	$seopress_sitemaps = '<?xml version="1.0" encoding="UTF-8"?>';
	$seopress_sitemaps .='<?xml-stylesheet type="text/xsl" href="'.get_home_url().'/sitemaps_xsl.xsl"?>';
	$seopress_sitemaps .= "\n";
	$seopress_sitemaps .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
	$seopress_sitemaps .= "\n";

			if (get_post_type_archive_link($path) ==true && $offset ==0) {
				function seopress_titles_cpt_noindex_option($path) {
					$seopress_titles_cpt_noindex_option = get_option("seopress_titles_option_name");
					if ( ! empty ( $seopress_titles_cpt_noindex_option ) ) {
						foreach ($seopress_titles_cpt_noindex_option as $key => $seopress_titles_cpt_noindex_value)
							$options[$key] = $seopress_titles_cpt_noindex_value;
						 if (isset($seopress_titles_cpt_noindex_option['seopress_titles_archive_titles'][$path]['noindex'])) { 
						 	return $seopress_titles_cpt_noindex_option['seopress_titles_archive_titles'][$path]['noindex'];
						 }
					}
				}
				if (seopress_titles_cpt_noindex_option($path) !='1') {
					$seopress_sitemaps .= '<url>';
				  	$seopress_sitemaps .= "\n";
					$seopress_sitemaps .= '<loc>';
					$seopress_sitemaps .= htmlspecialchars(urldecode(get_post_type_archive_link($path)));
					$seopress_sitemaps .= '</loc>';
					$seopress_sitemaps .= "\n";
					$seopress_sitemaps .= '</url>';
					$seopress_sitemaps .= "\n";
				}
			}
	
				$args = array( 'posts_per_page' => 1000, 'offset' => $offset, 'order' => 'DESC', 'orderby' => 'modified', 'post_type' => $path, 'post_status' => 'publish', 'meta_query' => array( array( 'key' => '_seopress_robots_index', 'value' => 'yes', 'compare' => 'NOT EXISTS' ) ), 'fields' => 'ids', 'lang' => '', 'has_password' => false );

				$args = apply_filters('seopress_sitemaps_single_query', $args, $path);

				$postslist = get_posts( $args );

				foreach ( $postslist as $post ) {
				  	setup_postdata( $post );

				  	$seopress_sitemaps .= '<url>';
				  	$seopress_sitemaps .= "\n";
					$seopress_sitemaps .= '<loc>';
					$seopress_sitemaps .= htmlspecialchars(urldecode(get_permalink($post)));
					$seopress_sitemaps .= '</loc>';
					$seopress_sitemaps .= "\n";
					$seopress_sitemaps .= '<lastmod>';
					$seopress_sitemaps .= get_the_modified_date('c', $post);
					$seopress_sitemaps .= '';
					$seopress_sitemaps .= '</lastmod>';
					$seopress_sitemaps .= "\n";
					
					//XML Image Sitemaps
					if (seopress_xml_sitemap_img_enable_option() =='1') {
						
						//Standard images
						if (get_post_field('post_content', $post) !='') {
							$dom = new domDocument;
							$internalErrors = libxml_use_internal_errors(true);
							$post_content = do_shortcode(get_post_field('post_content', $post));

						    if (function_exists('mb_convert_encoding')) {
						    	$dom->loadHTML(mb_convert_encoding($post_content, 'HTML-ENTITIES', 'UTF-8'));
						    } else {
						    	$dom->loadHTML('<?xml encoding="utf-8" ?>'.$post_content);
							}

							$dom->preserveWhiteSpace = false;

							if ($dom->getElementsByTagName('img') !='') {
								$images = $dom->getElementsByTagName('img');
							}
							libxml_use_internal_errors($internalErrors);
						}

						//WooCommerce
						global $product;
						if ($product !='' && method_exists($product, 'get_gallery_image_ids')) {
							$product_img = $product->get_gallery_image_ids();
						}

						//Galleries
						if (get_post_galleries_images($post) !='') {
							$galleries = get_post_galleries_images($post);
						}

						//Post Thumbnail
						$post_thumbnail = get_the_post_thumbnail_url($post);

						if ((isset($images) && !empty ($images) && $images->length>=1) || (isset($galleries) && !empty($galleries)) || (isset($product) && !empty($product_img)) || $post_thumbnail !='') { 
							
							//Standard img
							if (isset($images) && !empty ($images)) {
								if ($images->length>=1) {
									foreach($images as $img) {
								        $url = $img->getAttribute('src');
								        if ($url !='') {
									        //Exclude Base64 img
											if (strpos($url, 'data:image/') === false) {
										        if (seopress_is_absolute($url) ===true) {
										        	//do nothing
										        } else {
										        	$url = get_home_url().$url;
												}
												
												//cleaning url
												$url = htmlspecialchars(urldecode(esc_attr(wp_filter_nohtml_kses($url))));
																		
												//remove query strings
												$parse_url = wp_parse_url($url);

												$seopress_sitemaps .= '<image:image>';
										        $seopress_sitemaps .= "\n";
										       	$seopress_sitemaps .= '<image:loc>';
												
												if (!empty($parse_url['scheme']) && !empty($parse_url['host'])	&& !empty($parse_url['path'])) {
													$seopress_sitemaps .= '<![CDATA['.$parse_url['scheme'].'://'.$parse_url['host'].$parse_url['path'].']]>';
												} else {
													$seopress_sitemaps .= '<![CDATA['.$url.']]>';
												}
												
										        $seopress_sitemaps .= '</image:loc>';
												$seopress_sitemaps .= "\n";

												if ($img->getAttribute('title') !='') {
													$title = $img->getAttribute('title');

													$seopress_sitemaps .= '<image:title>';
													$seopress_sitemaps .= '<![CDATA['.$title.']]>';
													$seopress_sitemaps .= '</image:title>';
													$seopress_sitemaps .= "\n";
												}
												
												if ($img->getAttribute('alt') !='') {
													$caption = $img->getAttribute('alt');

													$seopress_sitemaps .= '<image:caption>';
													$seopress_sitemaps .= '<![CDATA['.$caption.']]>';
													$seopress_sitemaps .= '</image:caption>';
													$seopress_sitemaps .= "\n";
												}

												$seopress_sitemaps .= '</image:image>';
											}
										}
									}
								}
							}
							//Galleries
							if ($galleries !='') {
								foreach( $galleries as $gallery ) {
									foreach( $gallery as $url ) {
										if (seopress_is_absolute($url) ===true) {
								        	//do nothing
								        } else {
								        	$url = get_home_url().$url;
								        }
										$seopress_sitemaps .= '<image:image>';
										$seopress_sitemaps .= "\n";
								       	$seopress_sitemaps .= '<image:loc>';
										$seopress_sitemaps .= '<![CDATA['.htmlspecialchars(urldecode(esc_attr(wp_filter_nohtml_kses($url)))).']]>';
								        $seopress_sitemaps .= '</image:loc>';
								        $seopress_sitemaps .= "\n";
								        $seopress_sitemaps .= '</image:image>';
									}
								}
							}
							//WooCommerce img
							if ($product !='' && $product_img !='') {
								foreach( $product_img as $product_attachment_id ) {
									$seopress_sitemaps .= '<image:image>';
									$seopress_sitemaps .= "\n";
							       	$seopress_sitemaps .= '<image:loc>';
									$seopress_sitemaps .= '<![CDATA['.esc_attr(wp_filter_nohtml_kses(wp_get_attachment_url( $product_attachment_id ))).']]>';
							        $seopress_sitemaps .= '</image:loc>';
							        $seopress_sitemaps .= "\n";
							        $seopress_sitemaps .= '</image:image>';
								}
							}
							//Post thumbnail
							if ($post_thumbnail !='') {
								$seopress_sitemaps .= '<image:image>';
								$seopress_sitemaps .= "\n";
						       	$seopress_sitemaps .= '<image:loc>';
								$seopress_sitemaps .= '<![CDATA['.$post_thumbnail.']]>';
						        $seopress_sitemaps .= '</image:loc>';
						        $seopress_sitemaps .= "\n";
						        $seopress_sitemaps .= '</image:image>';
							}

							$seopress_sitemaps .= "\n";
							
							$seopress_sitemaps = apply_filters('seopress_sitemaps_single_img', $seopress_sitemaps, $post);
						}
					}
					$seopress_sitemaps .= '</url>';
					$seopress_sitemaps .= "\n";
				}
				wp_reset_postdata();

	$seopress_sitemaps .= '</urlset>';
	return $seopress_sitemaps;
} 
echo seopress_xml_sitemap_single();

?>