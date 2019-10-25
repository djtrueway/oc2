<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//Titles & metas
//=================================================================================================
//Titles
//Titles & Metas
//Separator
function seopress_titles_sep_option() {
	$seopress_titles_sep_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_sep_option ) ) {
		foreach ($seopress_titles_sep_option as $key => $seopress_titles_sep_value)
			$options[$key] = $seopress_titles_sep_value;
		 if (isset($seopress_titles_sep_option['seopress_titles_sep'])) { 
		 	return $seopress_titles_sep_option['seopress_titles_sep'];
		 }
	}
};

//Homepage Title
function seopress_titles_home_site_title_option() {
	$seopress_titles_home_site_title_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_home_site_title_option ) ) {
		foreach ($seopress_titles_home_site_title_option as $key => $seopress_titles_home_site_title_value)
			$options[$key] = $seopress_titles_home_site_title_value;
		 if (isset($seopress_titles_home_site_title_option['seopress_titles_home_site_title'])) { 
		 	return $seopress_titles_home_site_title_option['seopress_titles_home_site_title'];
		 }
	}
};

//Single CPT Titles
function seopress_titles_single_titles_option() {
	global $post;
	$seopress_get_current_cpt = get_post_type($post);

	$seopress_titles_single_titles_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_single_titles_option ) ) {
		foreach ($seopress_titles_single_titles_option as $key => $seopress_titles_single_titles_value)
			$options[$key] = $seopress_titles_single_titles_value;
		 if (isset($seopress_titles_single_titles_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['title'])) { 
		 	return $seopress_titles_single_titles_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['title'];
		 }
	}
};

//Archive CPT Titles
function seopress_titles_archive_titles_option() {
	global $post;
	$seopress_get_current_cpt = get_post_type($post);

	$seopress_titles_archive_titles_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_archive_titles_option ) ) {
		foreach ($seopress_titles_archive_titles_option as $key => $seopress_titles_archive_titles_value)
			$options[$key] = $seopress_titles_archive_titles_value;
		 if (isset($seopress_titles_archive_titles_option['seopress_titles_archive_titles'][$seopress_get_current_cpt]['title'])) { 
		 	return $seopress_titles_archive_titles_option['seopress_titles_archive_titles'][$seopress_get_current_cpt]['title'];
		 }
	}
};

//Tax archives Titles
function seopress_titles_tax_titles_option() {
	$queried_object = get_queried_object();
	$seopress_get_current_tax = $queried_object->taxonomy;

	$seopress_titles_tax_titles_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_tax_titles_option ) ) {
		foreach ($seopress_titles_tax_titles_option as $key => $seopress_titles_tax_titles_value)
			$options[$key] = $seopress_titles_tax_titles_value;
		 if (isset($seopress_titles_tax_titles_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['title'])) { 
		 	return $seopress_titles_tax_titles_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['title'];
		 }
	}
}

//Author archive Titles
function seopress_titles_archives_author_title_option() {
	$seopress_titles_archives_author_title_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_archives_author_title_option ) ) {
		foreach ($seopress_titles_archives_author_title_option as $key => $seopress_titles_archives_author_title_value)
			$options[$key] = $seopress_titles_archives_author_title_value;
		 if (isset($seopress_titles_archives_author_title_option['seopress_titles_archives_author_title'])) { 
		 	return $seopress_titles_archives_author_title_option['seopress_titles_archives_author_title'];
		 }
	}
};

//Date archive Titles
function seopress_titles_archives_date_title_option() {
	$seopress_titles_archives_date_title_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_archives_date_title_option ) ) {
		foreach ($seopress_titles_archives_date_title_option as $key => $seopress_titles_archives_date_title_value)
			$options[$key] = $seopress_titles_archives_date_title_value;
		 if (isset($seopress_titles_archives_date_title_option['seopress_titles_archives_date_title'])) { 
		 	return $seopress_titles_archives_date_title_option['seopress_titles_archives_date_title'];
		 }
	}
};

//Search archive Titles
function seopress_titles_archives_search_title_option() {
	$seopress_titles_archives_search_title_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_archives_search_title_option ) ) {
		foreach ($seopress_titles_archives_search_title_option as $key => $seopress_titles_archives_search_title_value)
			$options[$key] = $seopress_titles_archives_search_title_value;
		 if (isset($seopress_titles_archives_search_title_option['seopress_titles_archives_search_title'])) { 
		 	return $seopress_titles_archives_search_title_option['seopress_titles_archives_search_title'];
		 }
	}
};

//404 archive Titles
function seopress_titles_archives_404_title_option() {
	$seopress_titles_archives_404_title_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_archives_404_title_option ) ) {
		foreach ($seopress_titles_archives_404_title_option as $key => $seopress_titles_archives_404_title_value)
			$options[$key] = $seopress_titles_archives_404_title_value;
		 if (isset($seopress_titles_archives_404_title_option['seopress_titles_archives_404_title'])) { 
		 	return $seopress_titles_archives_404_title_option['seopress_titles_archives_404_title'];
		 }
	}
};

//Link rel prev/next
function seopress_titles_paged_rel_option() {
	$seopress_titles_paged_rel_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_paged_rel_option ) ) {
		foreach ($seopress_titles_paged_rel_option as $key => $seopress_titles_paged_rel_value)
			$options[$key] = $seopress_titles_paged_rel_value;
		 if (isset($seopress_titles_paged_rel_option['seopress_titles_paged_rel'])) { 
		 	return $seopress_titles_paged_rel_option['seopress_titles_paged_rel'];
		 }
	}
};

//Homepage Description
function seopress_titles_home_site_desc_option() {
	$seopress_titles_home_site_desc_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_home_site_desc_option ) ) {
		foreach ($seopress_titles_home_site_desc_option as $key => $seopress_titles_home_site_desc_value)
			$options[$key] = $seopress_titles_home_site_desc_value;
		 if (isset($seopress_titles_home_site_desc_option['seopress_titles_home_site_desc'])) { 
		 	return $seopress_titles_home_site_desc_option['seopress_titles_home_site_desc'];
		 }
	}
};

//Single CPT Description
function seopress_titles_single_desc_option() {
	global $post;
	$seopress_get_current_cpt = get_post_type($post);

	$seopress_titles_single_desc_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_single_desc_option ) ) {
		foreach ($seopress_titles_single_desc_option as $key => $seopress_titles_single_desc_value)
			$options[$key] = $seopress_titles_single_desc_value;
		 if (isset($seopress_titles_single_desc_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['description'])) { 
		 	return $seopress_titles_single_desc_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['description'];
		 }
	}
};

//Archive CPT Description
function seopress_titles_archive_desc_option() {
	global $post;
	$seopress_get_current_cpt = get_post_type($post);

	$seopress_titles_archive_desc_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_archive_desc_option ) ) {
		foreach ($seopress_titles_archive_desc_option as $key => $seopress_titles_archive_desc_value)
			$options[$key] = $seopress_titles_archive_desc_value;
		 if (isset($seopress_titles_archive_desc_option['seopress_titles_archive_titles'][$seopress_get_current_cpt]['description'])) { 
		 	return $seopress_titles_archive_desc_option['seopress_titles_archive_titles'][$seopress_get_current_cpt]['description'];
		 }
	}
};

//Tax archives Desc
function seopress_titles_tax_desc_option() {
	$queried_object = get_queried_object();
	$seopress_get_current_tax = $queried_object->taxonomy;

	$seopress_titles_tax_desc_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_tax_desc_option ) ) {
		foreach ($seopress_titles_tax_desc_option as $key => $seopress_titles_tax_desc_value)
			$options[$key] = $seopress_titles_tax_desc_value;
		 if (isset($seopress_titles_tax_desc_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['description'])) { 
		 	return $seopress_titles_tax_desc_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['description'];
		 }
	}
};

//Author archives Desc
function seopress_titles_archives_author_desc_option() {
	$seopress_titles_archives_author_desc_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_archives_author_desc_option ) ) {
		foreach ($seopress_titles_archives_author_desc_option as $key => $seopress_titles_archives_author_desc_value)
			$options[$key] = $seopress_titles_archives_author_desc_value;
		 if (isset($seopress_titles_archives_author_desc_option['seopress_titles_archives_author_desc'])) { 
		 	return $seopress_titles_archives_author_desc_option['seopress_titles_archives_author_desc'];
		 }
	}
};

//Date archives Desc
function seopress_titles_archives_date_desc_option() {
	$seopress_titles_archives_date_desc_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_archives_date_desc_option ) ) {
		foreach ($seopress_titles_archives_date_desc_option as $key => $seopress_titles_archives_date_desc_value)
			$options[$key] = $seopress_titles_archives_date_desc_value;
		 if (isset($seopress_titles_archives_date_desc_option['seopress_titles_archives_date_desc'])) { 
		 	return $seopress_titles_archives_date_desc_option['seopress_titles_archives_date_desc'];
		 }
	}
};

//Search archives Desc
function seopress_titles_archives_search_desc_option() {
	$seopress_titles_archives_search_desc_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_archives_search_desc_option ) ) {
		foreach ($seopress_titles_archives_search_desc_option as $key => $seopress_titles_archives_search_desc_value)
			$options[$key] = $seopress_titles_archives_search_desc_value;
		 if (isset($seopress_titles_archives_search_desc_option['seopress_titles_archives_search_desc'])) { 
		 	return $seopress_titles_archives_search_desc_option['seopress_titles_archives_search_desc'];
		 }
	}
};

//404 archives Desc
function seopress_titles_archives_404_desc_option() {
	$seopress_titles_archives_404_desc_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_archives_404_desc_option ) ) {
		foreach ($seopress_titles_archives_404_desc_option as $key => $seopress_titles_archives_404_desc_value)
			$options[$key] = $seopress_titles_archives_404_desc_value;
		 if (isset($seopress_titles_archives_404_desc_option['seopress_titles_archives_404_desc'])) { 
		 	return $seopress_titles_archives_404_desc_option['seopress_titles_archives_404_desc'];
		 }
	}
};

//THE Title Tag
function seopress_titles_the_title() {
	$variables = NULL;
	$variables = apply_filters('seopress_dyn_variables_fn', $variables);

	$post = $variables['post'];
	$term = $variables['term'];
	$seopress_titles_title_template = $variables['seopress_titles_title_template'];
	$seopress_titles_description_template = $variables['seopress_titles_description_template'];
	$seopress_paged = $variables['seopress_paged'];
	$the_author_meta = $variables['the_author_meta'];
	$sep = $variables['sep'];
	$seopress_excerpt = $variables['seopress_excerpt'];
	$post_category = $variables['post_category'];
	$post_tag = $variables['post_tag'];
	$get_search_query = $variables['get_search_query'];
	$woo_single_cat_html = $variables['woo_single_cat_html'];
	$woo_single_tag_html = $variables['woo_single_tag_html'];
	$woo_single_price = $variables['woo_single_price'];
	$woo_single_price_exc_tax = $variables['woo_single_price_exc_tax'];
	$woo_single_sku = $variables['woo_single_sku'];
	$author_bio = $variables['author_bio'];
	$seopress_get_the_excerpt = $variables['seopress_get_the_excerpt'];
	$seopress_titles_template_variables_array = $variables['seopress_titles_template_variables_array'];
	$seopress_titles_template_replace_array = $variables['seopress_titles_template_replace_array'];
	$seopress_excerpt_length = $variables['seopress_excerpt_length'];

	if ( is_front_page() && is_home() && isset($post) && get_post_meta($post->ID,'_seopress_titles_title',true) =='') { //HOMEPAGE
		if (seopress_titles_home_site_title_option() !='') {
			$seopress_titles_the_title = esc_attr(seopress_titles_home_site_title_option());

			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_title);
		}
	} elseif ( is_front_page() && isset($post) && get_post_meta($post->ID,'_seopress_titles_title',true) ==''){ //STATIC HOMEPAGE
		if (seopress_titles_home_site_title_option() !='') {
			$seopress_titles_the_title = esc_attr(seopress_titles_home_site_title_option());

			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_title);
		}
	} elseif ( is_home() && get_post_meta(get_option( 'page_for_posts' ),'_seopress_titles_title',true) !=''){ //BLOG PAGE
		if (get_post_meta(get_option( 'page_for_posts' ),'_seopress_titles_title',true)) { //IS METABOXE
			$seopress_titles_the_title = esc_attr(get_post_meta(get_option( 'page_for_posts' ),'_seopress_titles_title',true));

			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_title);
		}
	} elseif ( is_home() && (get_option( 'show_on_front' ) =='posts')) { //YOUR LATEST POSTS
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( is_plugin_active( 'polylang/polylang.php' ) || is_plugin_active( 'polylang-pro/polylang.php' )) {

		}
		if (seopress_titles_home_site_title_option() !='') {
			$seopress_titles_the_title = esc_attr(seopress_titles_home_site_title_option());

			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_title);
		}
	} elseif (is_singular()) { //IS SINGULAR
		if (get_post_meta($post->ID,'_seopress_titles_title',true)) { //IS METABOXE
			$seopress_titles_the_title = esc_attr(get_post_meta($post->ID,'_seopress_titles_title',true));

			preg_match_all('/%%_cf_(.*?)%%/', $seopress_titles_the_title, $matches); //custom fields

			if (!empty($matches)) {
				$seopress_titles_cf_template_variables_array = array();
				$seopress_titles_cf_template_replace_array = array();

				foreach ($matches['0'] as $key => $value) {
					$seopress_titles_cf_template_variables_array[] = $value;
				}

				foreach ($matches['1'] as $key => $value) {
					$seopress_titles_cf_template_replace_array[] = esc_attr(get_post_meta($post->ID,$value,true));
				}
			}

			preg_match_all('/%%_ct_(.*?)%%/', $seopress_titles_the_title, $matches2); //custom terms taxonomy

			if (!empty($matches2)) {
				$seopress_titles_ct_template_variables_array = array();
				$seopress_titles_ct_template_replace_array = array();

				foreach ($matches2['0'] as $key => $value) {
					$seopress_titles_ct_template_variables_array[] = $value;
				}

				foreach ($matches2['1'] as $key => $value) {
					$term = wp_get_post_terms( $post->ID, $value );
					if (!is_wp_error($term)) {
						$seopress_titles_ct_template_replace_array[] = esc_attr($term[0]->name);
					}
				}
			}

			//Default
			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_title);

			//Custom fields
			if (!empty($matches) && !empty($seopress_titles_cf_template_variables_array) && !empty($seopress_titles_cf_template_replace_array)) {
				$seopress_titles_title_template = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $seopress_titles_title_template);
			}

			//Custom terms taxonomy
			if (!empty($matches2) && !empty($seopress_titles_ct_template_variables_array) && !empty($seopress_titles_ct_template_replace_array)) {
				$seopress_titles_title_template = str_replace($seopress_titles_ct_template_variables_array, $seopress_titles_ct_template_replace_array, $seopress_titles_title_template);
			}
		}
		else { //DEFAULT GLOBAL

			$seopress_titles_single_titles_option = esc_attr(seopress_titles_single_titles_option());

			preg_match_all('/%%_cf_(.*?)%%/', $seopress_titles_single_titles_option, $matches); //custom fields

			if (!empty($matches)) {
				$seopress_titles_cf_template_variables_array = array();
				$seopress_titles_cf_template_replace_array = array();

				foreach ($matches['0'] as $key => $value) {
					$seopress_titles_cf_template_variables_array[] = $value;
				}

				foreach ($matches['1'] as $key => $value) {
					$seopress_titles_cf_template_replace_array[] = esc_attr(get_post_meta($post->ID,$value,true));
				}
			}

			preg_match_all('/%%_ct_(.*?)%%/', $seopress_titles_single_titles_option, $matches2); //custom terms taxonomy

			if (!empty($matches2)) {
				$seopress_titles_ct_template_variables_array = array();
				$seopress_titles_ct_template_replace_array = array();

				foreach ($matches2['0'] as $key => $value) {
					$seopress_titles_ct_template_variables_array[] = $value;
				}

				foreach ($matches2['1'] as $key => $value) {
					$term = wp_get_post_terms( $post->ID, $value );
					if (!is_wp_error($term)) {
						$seopress_titles_ct_template_replace_array[] = esc_attr($term[0]->name);
					}
				}
			}

			//Default
			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_single_titles_option);
			
			//Custom fields
			if (!empty($matches) && !empty($seopress_titles_cf_template_variables_array) && !empty($seopress_titles_cf_template_replace_array)) {
				$seopress_titles_title_template = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $seopress_titles_title_template);
			}

			//Custom terms taxonomy
			if (!empty($matches2) && !empty($seopress_titles_ct_template_variables_array) && !empty($seopress_titles_ct_template_replace_array)) {
				$seopress_titles_title_template = str_replace($seopress_titles_ct_template_variables_array, $seopress_titles_ct_template_replace_array, $seopress_titles_title_template);
			}
		}
	} elseif (is_post_type_archive() && seopress_titles_archive_titles_option()) { //IS POST TYPE ARCHIVE
		$seopress_titles_archive_titles_option = esc_attr(seopress_titles_archive_titles_option());

		$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_archive_titles_option);
	} elseif ((is_tax() || is_category() || is_tag()) && seopress_titles_tax_titles_option()) { //IS TAX
		$seopress_titles_tax_titles_option = esc_attr(seopress_titles_tax_titles_option());
		
		if (get_term_meta(get_queried_object()->{'term_id'},'_seopress_titles_title',true)) {
			$seopress_titles_title_template = esc_attr(get_term_meta(get_queried_object()->{'term_id'},'_seopress_titles_title',true));
			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_title_template);
		} else {
			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_tax_titles_option);
		}		
	} elseif (is_author() && seopress_titles_archives_author_title_option()) { //IS AUTHOR
		$seopress_titles_archives_author_title_option = esc_attr(seopress_titles_archives_author_title_option());

		$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_archives_author_title_option);
	} elseif (is_date() && seopress_titles_archives_date_title_option()) { //IS DATE
		$seopress_titles_archives_date_title_option = esc_attr(seopress_titles_archives_date_title_option());

		$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_archives_date_title_option);
	} elseif (is_search() && seopress_titles_archives_search_title_option()) { //IS SEARCH
		$seopress_titles_archives_search_title_option = esc_attr(seopress_titles_archives_search_title_option());
		
		$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_archives_search_title_option);
	} elseif (is_404() && seopress_titles_archives_404_title_option()) { //IS 404
		$seopress_titles_archives_404_title_option = esc_attr(seopress_titles_archives_404_title_option());
		
		$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_archives_404_title_option);
	}

	//Hook on Title tag - 'seopress_titles_title'
	if (has_filter('seopress_titles_title')) {
		$seopress_titles_title_template = apply_filters('seopress_titles_title', $seopress_titles_title_template);
    }
    //Return Title tag
	return $seopress_titles_title_template;
}
add_filter( 'pre_get_document_title', 'seopress_titles_the_title', 10 );

//THE Meta Description
function seopress_titles_the_description_content() {
	$variables = NULL;
	$variables = apply_filters('seopress_dyn_variables_fn', $variables);

	$post = $variables['post'];
	$term = $variables['term'];
	$seopress_titles_title_template = $variables['seopress_titles_title_template'];
	$seopress_titles_description_template = $variables['seopress_titles_description_template'];
	$seopress_paged = $variables['seopress_paged'];
	$the_author_meta = $variables['the_author_meta'];
	$sep = $variables['sep'];
	$seopress_excerpt = $variables['seopress_excerpt'];
	$post_category = $variables['post_category'];
	$post_tag = $variables['post_tag'];
	$get_search_query = $variables['get_search_query'];
	$woo_single_cat_html = $variables['woo_single_cat_html'];
	$woo_single_tag_html = $variables['woo_single_tag_html'];
	$woo_single_price = $variables['woo_single_price'];
	$woo_single_price_exc_tax = $variables['woo_single_price_exc_tax'];
	$woo_single_sku = $variables['woo_single_sku'];
	$author_bio = $variables['author_bio'];
	$seopress_get_the_excerpt = $variables['seopress_get_the_excerpt'];
	$seopress_titles_template_variables_array = $variables['seopress_titles_template_variables_array'];
	$seopress_titles_template_replace_array = $variables['seopress_titles_template_replace_array'];
	$seopress_excerpt_length = $variables['seopress_excerpt_length'];

	if ( is_front_page() && is_home() && isset($post) && get_post_meta($post->ID,'_seopress_titles_desc',true) =='' ) { //HOMEPAGE
		if (seopress_titles_home_site_desc_option() !='') { //IS GLOBAL
			$seopress_titles_the_description = esc_attr(seopress_titles_home_site_desc_option());

			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
		}
	} elseif ( is_front_page() && isset($post) && get_post_meta($post->ID,'_seopress_titles_desc',true) ==''){ //STATIC HOMEPAGE
		if (seopress_titles_home_site_desc_option() !='' && get_post_meta($post->ID,'_seopress_titles_desc',true) =='') { //IS GLOBAL
			$seopress_titles_the_description = esc_attr(seopress_titles_home_site_desc_option());

			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
		}
	} elseif ( is_home() && get_post_meta(get_option( 'page_for_posts' ),'_seopress_titles_desc',true) !=''){ //BLOG PAGE
		if (get_post_meta(get_option( 'page_for_posts' ),'_seopress_titles_desc',true)) {
			$seopress_titles_the_description_meta = esc_html(get_post_meta(get_option( 'page_for_posts' ),'_seopress_titles_desc',true));
			$seopress_titles_the_description = $seopress_titles_the_description_meta;
			
			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
		}
	} elseif ( is_home() && (get_option( 'show_on_front' ) =='posts')) { //YOUR LATEST POSTS
		if (seopress_titles_home_site_desc_option() !='') { //IS GLOBAL
			$seopress_titles_the_description = esc_attr(seopress_titles_home_site_desc_option());

			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
		}
	} elseif (is_singular()) { //IS SINGLE
		if (get_post_meta($post->ID,'_seopress_titles_desc',true)) { //IS METABOXE
			$seopress_titles_the_description = esc_attr(get_post_meta($post->ID,'_seopress_titles_desc',true));

			preg_match_all('/%%_cf_(.*?)%%/', $seopress_titles_the_description, $matches); //custom fields

			if (!empty($matches)) {
				$seopress_titles_cf_template_variables_array = array();
				$seopress_titles_cf_template_replace_array = array();

				foreach ($matches['0'] as $key => $value) {
					$seopress_titles_cf_template_variables_array[] = $value;
				}

				foreach ($matches['1'] as $key => $value) {
					$seopress_titles_cf_template_replace_array[] = esc_attr(get_post_meta($post->ID,$value,true));
				}
			}

			preg_match_all('/%%_ct_(.*?)%%/', $seopress_titles_the_description, $matches2); //custom terms taxonomy

			if (!empty($matches2)) {
				$seopress_titles_ct_template_variables_array = array();
				$seopress_titles_ct_template_replace_array = array();

				foreach ($matches2['0'] as $key => $value) {
					$seopress_titles_ct_template_variables_array[] = $value;
				}

				foreach ($matches2['1'] as $key => $value) {
					$term = wp_get_post_terms( $post->ID, $value );
					if (!is_wp_error($term)) {
						$seopress_titles_ct_template_replace_array[] = esc_attr($term[0]->name);
					}
				}
			}

			//Default
			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);

			//Custom fields
			if (!empty($matches) && !empty($seopress_titles_cf_template_variables_array) && !empty($seopress_titles_cf_template_replace_array)) {
				$seopress_titles_description_template = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $seopress_titles_description_template);
			}

			//Custom terms taxonomy
			if (!empty($matches2) && !empty($seopress_titles_ct_template_variables_array) && !empty($seopress_titles_ct_template_replace_array)) {
				$seopress_titles_description_template = str_replace($seopress_titles_ct_template_variables_array, $seopress_titles_ct_template_replace_array, $seopress_titles_description_template);
			}
		} elseif (seopress_titles_single_desc_option() !='') { //IS GLOBAL
			$seopress_titles_the_description = esc_attr(seopress_titles_single_desc_option());
			
			preg_match_all('/%%_cf_(.*?)%%/', $seopress_titles_the_description, $matches); //custom fields

			if (!empty($matches)) {
				$seopress_titles_cf_template_variables_array = array();
				$seopress_titles_cf_template_replace_array = array();

				foreach ($matches['0'] as $key => $value) {
					$seopress_titles_cf_template_variables_array[] = $value;
				}

				foreach ($matches['1'] as $key => $value) {
					$seopress_titles_cf_template_replace_array[] = esc_attr(get_post_meta($post->ID,$value,true));
				}
			}

			preg_match_all('/%%_ct_(.*?)%%/', $seopress_titles_the_description, $matches2); //custom terms taxonomy

			if (!empty($matches2)) {
				$seopress_titles_ct_template_variables_array = array();
				$seopress_titles_ct_template_replace_array = array();

				foreach ($matches2['0'] as $key => $value) {
					$seopress_titles_ct_template_variables_array[] = $value;
				}

				foreach ($matches2['1'] as $key => $value) {
					$term = wp_get_post_terms( $post->ID, $value );
					if (!is_wp_error($term)) {
						$seopress_titles_ct_template_replace_array[] = esc_attr($term[0]->name);
					}
				}
			}

			//Default
			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);

			//Custom fields
			if (!empty($matches) && !empty($seopress_titles_cf_template_variables_array) && !empty($seopress_titles_cf_template_replace_array)) {
				$seopress_titles_description_template = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $seopress_titles_description_template);
			}

			//Custom terms taxonomy
			if (!empty($matches2) && !empty($seopress_titles_ct_template_variables_array) && !empty($seopress_titles_ct_template_replace_array)) {
				$seopress_titles_description_template = str_replace($seopress_titles_ct_template_variables_array, $seopress_titles_ct_template_replace_array, $seopress_titles_description_template);
			}
		} else {
			setup_postdata( $post );
			if ($seopress_get_the_excerpt !='' || get_the_content() !='') { //DEFAULT EXCERPT OR THE CONTENT
				$seopress_titles_the_description = wp_trim_words(stripslashes_deep(wp_filter_nohtml_kses($seopress_get_the_excerpt)), $seopress_excerpt_length);
				
				$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
			}
		}
	} elseif (is_post_type_archive() && seopress_titles_archive_desc_option()) { //IS POST TYPE ARCHIVE
		$seopress_titles_the_description = esc_attr(seopress_titles_archive_desc_option());
		
		$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
	} elseif ((is_tax() || is_category() || is_tag()) && seopress_titles_tax_desc_option()) { //IS TAX
		$seopress_titles_the_description = esc_attr(seopress_titles_tax_desc_option());

		if (get_term_meta(get_queried_object()->{'term_id'},'_seopress_titles_desc',true)) {
			$seopress_titles_description_template = esc_attr(get_term_meta(get_queried_object()->{'term_id'},'_seopress_titles_desc',true));
			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_description_template);
		} else {	
			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
		}
	} elseif (is_author() && seopress_titles_archives_author_desc_option()) { //IS AUTHOR
		$seopress_titles_the_description = esc_attr(seopress_titles_archives_author_desc_option());
		
		$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
	} elseif (is_date() && seopress_titles_archives_date_desc_option()) { //IS DATE
		$seopress_titles_the_description = esc_attr(seopress_titles_archives_date_desc_option());

		$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
	} elseif (is_search() && seopress_titles_archives_search_desc_option()) { //IS SEARCH
		$seopress_titles_the_description = esc_attr(seopress_titles_archives_search_desc_option());

		$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
	} elseif (is_404() && seopress_titles_archives_404_desc_option()) { //IS 404
		$seopress_titles_the_description = esc_attr(seopress_titles_archives_404_desc_option());
		
		$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
	}
	//Hook on meta description - 'seopress_titles_desc'
	if (has_filter('seopress_titles_desc')) {
		$seopress_titles_description_template = apply_filters('seopress_titles_desc', $seopress_titles_description_template);
    }
    //Return meta desc tag
	return $seopress_titles_description_template;
}
function seopress_titles_the_description() {
	if (seopress_titles_the_description_content() !='') {
		$html = '<meta name="description" content="'.seopress_titles_the_description_content().'" />';
		$html .= "\n";
		echo $html;
	}
}
add_action( 'wp_head', 'seopress_titles_the_description', 1 );

//Advanced
//noindex
//Single CPT noindex
function seopress_titles_single_cpt_noindex_option() {
	global $post;
	$seopress_get_current_cpt = get_post_type($post);

	$seopress_titles_single_cpt_noindex_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_single_cpt_noindex_option ) ) {
		foreach ($seopress_titles_single_cpt_noindex_option as $key => $seopress_titles_single_cpt_noindex_value)
			$options[$key] = $seopress_titles_single_cpt_noindex_value;
		 if (isset($seopress_titles_single_cpt_noindex_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['noindex'])) { 
		 	return $seopress_titles_single_cpt_noindex_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['noindex'];
		 }
	}
};

//Archive CPT noindex
function seopress_titles_archive_cpt_noindex_option() {
	global $post;
	$seopress_get_current_cpt = get_post_type($post);

	$seopress_titles_archive_cpt_noindex_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_archive_cpt_noindex_option ) ) {
		foreach ($seopress_titles_archive_cpt_noindex_option as $key => $seopress_titles_archive_cpt_noindex_value)
			$options[$key] = $seopress_titles_archive_cpt_noindex_value;
		 if (isset($seopress_titles_archive_cpt_noindex_option['seopress_titles_archive_titles'][$seopress_get_current_cpt]['noindex'])) { 
		 	return $seopress_titles_archive_cpt_noindex_option['seopress_titles_archive_titles'][$seopress_get_current_cpt]['noindex'];
		 }
	}
};

//Tax archive noindex
function seopress_titles_tax_noindex_option() {
	$queried_object = get_queried_object();
	$seopress_get_current_tax = $queried_object->taxonomy;

	if (get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_index',true) == 'yes') {
		return get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_index',true);
	} else {
		$seopress_titles_tax_noindex_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_tax_noindex_option ) ) {
			foreach ($seopress_titles_tax_noindex_option as $key => $seopress_titles_tax_noindex_value)
				$options[$key] = $seopress_titles_tax_noindex_value;
			 if (isset($seopress_titles_tax_noindex_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['noindex'])) { 
			 	return $seopress_titles_tax_noindex_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['noindex'];
			 }
		}
	}
};

//noindex Author archives
function seopress_titles_archives_author_noindex_option() {
	$seopress_titles_archives_author_noindex_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_archives_author_noindex_option ) ) {
		foreach ($seopress_titles_archives_author_noindex_option as $key => $seopress_titles_archives_author_noindex_value)
			$options[$key] = $seopress_titles_archives_author_noindex_value;
		 if (isset($seopress_titles_archives_author_noindex_option['seopress_titles_archives_author_noindex'])) { 
		 	return $seopress_titles_archives_author_noindex_option['seopress_titles_archives_author_noindex'];
		 }
	}
};

//noindex Date archives
function seopress_titles_archives_date_noindex_option() {
	$seopress_titles_archives_date_noindex_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_archives_date_noindex_option ) ) {
		foreach ($seopress_titles_archives_date_noindex_option as $key => $seopress_titles_archives_date_noindex_value)
			$options[$key] = $seopress_titles_archives_date_noindex_value;
		 if (isset($seopress_titles_archives_date_noindex_option['seopress_titles_archives_date_noindex'])) { 
		 	return $seopress_titles_archives_date_noindex_option['seopress_titles_archives_date_noindex'];
		 }
	}
};

//noindex Search
function seopress_titles_archives_search_title_noindex_option() {
	$seopress_titles_archives_search_title_noindex_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_archives_search_title_noindex_option ) ) {
		foreach ($seopress_titles_archives_search_title_noindex_option as $key => $seopress_titles_archives_search_title_noindex_value)
			$options[$key] = $seopress_titles_archives_search_title_noindex_value;
		 if (isset($seopress_titles_archives_search_title_noindex_option['seopress_titles_archives_search_title_noindex'])) { 
		 	return $seopress_titles_archives_search_title_noindex_option['seopress_titles_archives_search_title_noindex'];
		 }
	}
};	

//noindex Global Advanced tab
function seopress_titles_noindex_option() {
	$seopress_titles_noindex_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_noindex_option ) ) {
		foreach ($seopress_titles_noindex_option as $key => $seopress_titles_noindex_value)
			$options[$key] = $seopress_titles_noindex_value;
		 if (isset($seopress_titles_noindex_option['seopress_titles_noindex'])) { 
		 	return $seopress_titles_noindex_option['seopress_titles_noindex'];
		 }
	}
};

//noindex single CPT
function seopress_titles_noindex_post_option() {
	$_seopress_robots_index = get_post_meta(get_the_ID(),'_seopress_robots_index',true);
	if ($_seopress_robots_index == 'yes') {
		return $_seopress_robots_index;
	}
};

//noindex WooCommerce page
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
	//Cart page
	function seopress_woocommerce_cart_page_no_index_option() {
		$seopress_woocommerce_cart_page_no_index_option = get_option("seopress_pro_option_name");
		if ( ! empty ( $seopress_woocommerce_cart_page_no_index_option ) ) {
			foreach ($seopress_woocommerce_cart_page_no_index_option as $key => $seopress_woocommerce_cart_page_no_index_value)
				$options[$key] = $seopress_woocommerce_cart_page_no_index_value;
			 if (isset($seopress_woocommerce_cart_page_no_index_option['seopress_woocommerce_cart_page_no_index'])) { 
			 	return $seopress_woocommerce_cart_page_no_index_option['seopress_woocommerce_cart_page_no_index'];
			 }
		}
	}
	//Checkout page
	function seopress_woocommerce_checkout_page_no_index_option() {
		$seopress_woocommerce_checkout_page_no_index_option = get_option("seopress_pro_option_name");
		if ( ! empty ( $seopress_woocommerce_checkout_page_no_index_option ) ) {
			foreach ($seopress_woocommerce_checkout_page_no_index_option as $key => $seopress_woocommerce_checkout_page_no_index_value)
				$options[$key] = $seopress_woocommerce_checkout_page_no_index_value;
			 if (isset($seopress_woocommerce_checkout_page_no_index_option['seopress_woocommerce_checkout_page_no_index'])) { 
			 	return $seopress_woocommerce_checkout_page_no_index_option['seopress_woocommerce_checkout_page_no_index'];
			 }
		}
	}
	//Customer Account
	function seopress_woocommerce_customer_account_page_no_index_option() {
		$seopress_woocommerce_customer_account_page_no_index_option = get_option("seopress_pro_option_name");
		if ( ! empty ( $seopress_woocommerce_customer_account_page_no_index_option ) ) {
			foreach ($seopress_woocommerce_customer_account_page_no_index_option as $key => $seopress_woocommerce_customer_account_page_no_index_value)
				$options[$key] = $seopress_woocommerce_customer_account_page_no_index_value;
			 if (isset($seopress_woocommerce_customer_account_page_no_index_option['seopress_woocommerce_customer_account_page_no_index'])) { 
			 	return $seopress_woocommerce_customer_account_page_no_index_option['seopress_woocommerce_customer_account_page_no_index'];
			 }
		}
	}
}

function seopress_titles_noindex_bypass() {
	//init
	$seopress_titles_noindex ='';
	if(post_password_required() ===true){ //if password required, set noindex
		$seopress_titles_noindex = "noindex";
	} else {
		if (seopress_titles_noindex_option()) { //Single CPT Global Advanced tab
			$seopress_titles_noindex = seopress_titles_noindex_option();
		} elseif (is_singular() && seopress_titles_single_cpt_noindex_option()) { //Single CPT Global
			$seopress_titles_noindex = seopress_titles_single_cpt_noindex_option();
		} elseif (is_singular() && seopress_titles_noindex_post_option() ) { //Single CPT Metaboxe
			$seopress_titles_noindex = seopress_titles_noindex_post_option();
		} elseif ( is_home() && get_post_meta(get_option( 'page_for_posts' ),'_seopress_robots_index',true) !=''){ //BLOG PAGE
			$seopress_titles_noindex = get_post_meta(get_option( 'page_for_posts' ),'_seopress_robots_index',true);
		} elseif (is_post_type_archive() && seopress_titles_archive_cpt_noindex_option() ) { //IS POST TYPE ARCHIVE
			$seopress_titles_noindex = seopress_titles_archive_cpt_noindex_option();
		} elseif ((is_tax() || is_category() || is_tag()) && seopress_titles_tax_noindex_option()) { //IS TAX
			$seopress_titles_noindex = seopress_titles_tax_noindex_option();
		} elseif (is_author() && seopress_titles_archives_author_noindex_option()) { //IS Author archive
			$seopress_titles_noindex = seopress_titles_archives_author_noindex_option();
		} elseif (is_date() && seopress_titles_archives_date_noindex_option()) { //IS Date archive
			$seopress_titles_noindex = seopress_titles_archives_date_noindex_option();
		} elseif (is_search() && seopress_titles_archives_search_title_noindex_option()) {
			$seopress_titles_noindex = seopress_titles_archives_search_title_noindex_option();
		} elseif (function_exists('is_cart') && function_exists('seopress_woocommerce_cart_page_no_index_option') && (is_cart() && seopress_woocommerce_cart_page_no_index_option())) { //IS WooCommerce Cart page
			$seopress_titles_noindex = seopress_woocommerce_cart_page_no_index_option();
		} elseif (function_exists('is_checkout') && function_exists('seopress_woocommerce_checkout_page_no_index_option') && (is_checkout() && seopress_woocommerce_checkout_page_no_index_option())) { //IS WooCommerce Checkout page
			$seopress_titles_noindex = seopress_woocommerce_checkout_page_no_index_option();
		} elseif (function_exists('is_account_page') && function_exists('seopress_woocommerce_customer_account_page_no_index_option') && (is_account_page() && seopress_woocommerce_customer_account_page_no_index_option())) { //IS WooCommerce Customer account pages
			$seopress_titles_noindex = seopress_woocommerce_customer_account_page_no_index_option();
		} elseif (function_exists('is_wc_endpoint_url') && function_exists('seopress_woocommerce_customer_account_page_no_index_option') && (is_wc_endpoint_url() && seopress_woocommerce_customer_account_page_no_index_option())) { //IS WooCommerce Customer account pages
			$seopress_titles_noindex = seopress_woocommerce_customer_account_page_no_index_option();
		} elseif(is_404()) { //Is 404 page
			$seopress_titles_noindex = "noindex";
		}
	}
    //Return noindex tag
	return $seopress_titles_noindex;
}

//nofollow
//Single CPT nofollow
function seopress_titles_single_cpt_nofollow_option() {
	global $post;
	$seopress_get_current_cpt = get_post_type($post);

	$seopress_titles_single_cpt_nofollow_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_single_cpt_nofollow_option ) ) {
		foreach ($seopress_titles_single_cpt_nofollow_option as $key => $seopress_titles_single_cpt_nofollow_value)
			$options[$key] = $seopress_titles_single_cpt_nofollow_value;
		 if (isset($seopress_titles_single_cpt_nofollow_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['nofollow'])) { 
		 	return $seopress_titles_single_cpt_nofollow_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['nofollow'];
		 }
	}
};

//Archive CPT nofollow
function seopress_titles_archive_cpt_nofollow_option() {
	global $post;
	$seopress_get_current_cpt = get_post_type($post);

	$seopress_titles_archive_cpt_nofollow_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_archive_cpt_nofollow_option ) ) {
		foreach ($seopress_titles_archive_cpt_nofollow_option as $key => $seopress_titles_archive_cpt_nofollow_value)
			$options[$key] = $seopress_titles_archive_cpt_nofollow_value;
		 if (isset($seopress_titles_archive_cpt_nofollow_option['seopress_titles_archive_titles'][$seopress_get_current_cpt]['nofollow'])) { 
		 	return $seopress_titles_archive_cpt_nofollow_option['seopress_titles_archive_titles'][$seopress_get_current_cpt]['nofollow'];
		 }
	}
};

//Tax archive nofollow
function seopress_titles_tax_nofollow_option() {
	$queried_object = get_queried_object();
	$seopress_get_current_tax = $queried_object->taxonomy;

	if (get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_follow',true) == 'yes') {
		return get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_follow',true);
	} else {
		$seopress_titles_tax_nofollow_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_tax_nofollow_option ) ) {
			foreach ($seopress_titles_tax_nofollow_option as $key => $seopress_titles_tax_nofollow_value)
				$options[$key] = $seopress_titles_tax_nofollow_value;
			 if (isset($seopress_titles_tax_nofollow_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['nofollow'])) { 
			 	return $seopress_titles_tax_nofollow_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['nofollow'];
			 }
		}
	}
};

//nofollow Global Avanced tab
function seopress_titles_nofollow_option() {
	$seopress_titles_nofollow_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_nofollow_option ) ) {
		foreach ($seopress_titles_nofollow_option as $key => $seopress_titles_nofollow_value)
			$options[$key] = $seopress_titles_nofollow_value;
		 if (isset($seopress_titles_nofollow_option['seopress_titles_nofollow'])) { 
		 	return $seopress_titles_nofollow_option['seopress_titles_nofollow'];
		 }
	}
};

function seopress_titles_nofollow_post_option() {
	$_seopress_robots_follow = get_post_meta(get_the_ID(),'_seopress_robots_follow',true);
	if ($_seopress_robots_follow == 'yes') {
		return $_seopress_robots_follow;
	}
};

function seopress_titles_nofollow_bypass() {
	//init
	$seopress_titles_nofollow ='';

	if (seopress_titles_nofollow_option()) { //Single CPT Global Advanced tab
		$seopress_titles_nofollow = seopress_titles_nofollow_option();
	} elseif (is_singular() && seopress_titles_single_cpt_nofollow_option()) { //Single CPT Global
		$seopress_titles_nofollow = seopress_titles_single_cpt_nofollow_option();
	} elseif (is_singular() && seopress_titles_nofollow_post_option() ) { //Single CPT Metaboxe
		$seopress_titles_nofollow = seopress_titles_nofollow_post_option();
	} elseif ( is_home() && get_post_meta(get_option( 'page_for_posts' ),'_seopress_robots_follow',true) !=''){ //BLOG PAGE
		$seopress_titles_nofollow = get_post_meta(get_option( 'page_for_posts' ),'_seopress_robots_follow',true);
	} elseif (is_post_type_archive() && seopress_titles_archive_cpt_nofollow_option() ) { //IS POST TYPE ARCHIVE
		$seopress_titles_nofollow = seopress_titles_archive_cpt_nofollow_option();
	} elseif ((is_tax() || is_category() || is_tag()) && seopress_titles_tax_nofollow_option()) { //IS TAX
		$seopress_titles_nofollow = seopress_titles_tax_nofollow_option();
	}
	return $seopress_titles_nofollow;
};

//date in SERPs
function seopress_titles_single_cpt_date_option() {
	global $post;
	$seopress_get_current_cpt = get_post_type($post);

	$seopress_titles_single_cpt_date_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_single_cpt_date_option ) ) {
		foreach ($seopress_titles_single_cpt_date_option as $key => $seopress_titles_single_cpt_date_value)
			$options[$key] = $seopress_titles_single_cpt_date_value;
		 if (isset($seopress_titles_single_cpt_date_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['date'])) { 
		 	return $seopress_titles_single_cpt_date_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['date'];
		 }
	}
};

function seopress_titles_single_cpt_date_hook() {
	if (!is_front_page() && !is_home()) {
		if (is_singular() && seopress_titles_single_cpt_date_option() =='1') {
			$seopress_get_current_pub_post_date = get_the_date('c');
			$seopress_get_current_up_post_date = get_the_modified_date('c');
			$html = '<meta property="article:published_time" content="'.$seopress_get_current_pub_post_date.'" />';
			$html .= "\n";

			$html = apply_filters('seopress_titles_article_published_time', $html);

			echo $html;

			$html = '<meta property="article:modified_time" content="'.$seopress_get_current_up_post_date.'" />';
			$html .= "\n";

			$html = apply_filters('seopress_titles_article_modified_time', $html);

			echo $html;

			$html = '<meta property="og:updated_time" content="'.$seopress_get_current_up_post_date.'" />';
			$html .= "\n";
			
			$html = apply_filters('seopress_titles_og_updated_time', $html);

			echo $html;
		}
	}
}
add_action( 'wp_head', 'seopress_titles_single_cpt_date_hook', 1 );

//thumbnail in Google Custom Search 
function seopress_titles_single_cpt_thumb_gcs_option() {
	global $post;
	$seopress_get_current_cpt = get_post_type($post);

	$seopress_titles_single_cpt_thumb_gcs_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_single_cpt_thumb_gcs_option ) ) {
		foreach ($seopress_titles_single_cpt_thumb_gcs_option as $key => $seopress_titles_single_cpt_thumb_gcs_value)
			$options[$key] = $seopress_titles_single_cpt_thumb_gcs_value;
		 if (isset($seopress_titles_single_cpt_thumb_gcs_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['thumb_gcs'])) { 
		 	return $seopress_titles_single_cpt_thumb_gcs_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['thumb_gcs'];
		 }
	}
};

function seopress_titles_single_cpt_thumb_gcs() {
	if (!is_front_page() && !is_home()) {
		if (is_singular() && seopress_titles_single_cpt_thumb_gcs_option() =='1') {
			if (get_the_post_thumbnail_url(get_the_ID())) {
				$html = '<meta name="thumbnail" content="'.get_the_post_thumbnail_url(get_the_ID(), 'thumbnail').'" />';
				$html .= "\n";

				$html = apply_filters('seopress_titles_gcs_thumbnail', $html);

				echo $html;
			}
		}
	}
}
add_action( 'wp_head', 'seopress_titles_single_cpt_thumb_gcs', 1 );

//noodp
function seopress_titles_noodp_option() {
	$seopress_titles_noodp_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_noodp_option ) ) {
		foreach ($seopress_titles_noodp_option as $key => $seopress_titles_noodp_value)
			$options[$key] = $seopress_titles_noodp_value;
		 if (isset($seopress_titles_noodp_option['seopress_titles_noodp'])) { 
		 	return $seopress_titles_noodp_option['seopress_titles_noodp'];
		 }
	}
};

function seopress_titles_noodp_post_option() {
	$_seopress_robots_odp = get_post_meta(get_the_ID(),'_seopress_robots_odp',true);
	if ($_seopress_robots_odp == 'yes') {
		return $_seopress_robots_odp;
	}
};

function seopress_titles_noodp_bypass() {
	if (seopress_titles_noodp_option()) {
		return seopress_titles_noodp_option(); 
	}
	elseif (is_singular() && seopress_titles_noodp_post_option()) {
		return seopress_titles_noodp_post_option();
	} elseif ( is_home() && get_post_meta(get_option( 'page_for_posts' ),'_seopress_robots_odp',true) !=''){ //BLOG PAGE
		return get_post_meta(get_option( 'page_for_posts' ),'_seopress_robots_odp',true);
	} elseif (is_tax() || is_category() || is_tag()) {
		if (get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_odp',true) == 'yes') {
			return get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_odp',true);
		}
	}
};

//noarchive
function seopress_titles_noarchive_option() {
	$seopress_titles_noarchive_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_noarchive_option ) ) {
		foreach ($seopress_titles_noarchive_option as $key => $seopress_titles_noarchive_value)
			$options[$key] = $seopress_titles_noarchive_value;
		 if (isset($seopress_titles_noarchive_option['seopress_titles_noarchive'])) { 
		 	return $seopress_titles_noarchive_option['seopress_titles_noarchive'];
		 }
	}
};

function seopress_titles_noarchive_post_option() {
	$_seopress_robots_archive = get_post_meta(get_the_ID(),'_seopress_robots_archive',true);
	if ($_seopress_robots_archive == 'yes') {
		return $_seopress_robots_archive;
	}
};

function seopress_titles_noarchive_bypass() {
	if (seopress_titles_noarchive_option()) {
		return seopress_titles_noarchive_option(); 
	} elseif (is_singular() && seopress_titles_noarchive_post_option()) {
		return seopress_titles_noarchive_post_option();
	} elseif ( is_home() && get_post_meta(get_option( 'page_for_posts' ),'_seopress_robots_archive',true) !=''){ //BLOG PAGE
		return get_post_meta(get_option( 'page_for_posts' ),'_seopress_robots_archive',true);
	} elseif (is_tax() || is_category() || is_tag()) {
		if (get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_archive',true) == 'yes') {
			return get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_archive',true);
		}
	}
};

//nosnippet
function seopress_titles_nosnippet_option() {
	$seopress_titles_nosnippet_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_nosnippet_option ) ) {
		foreach ($seopress_titles_nosnippet_option as $key => $seopress_titles_nosnippet_value)
			$options[$key] = $seopress_titles_nosnippet_value;
		if (isset($seopress_titles_nosnippet_option['seopress_titles_nosnippet'])) { 
		return $seopress_titles_nosnippet_option['seopress_titles_nosnippet'];
		}
	}
};

function seopress_titles_nosnippet_post_option() {
	$_seopress_robots_snippet = get_post_meta(get_the_ID(),'_seopress_robots_snippet',true);
	if ($_seopress_robots_snippet == 'yes') {
		return $_seopress_robots_snippet;
	}
};

function seopress_titles_nosnippet_bypass() {
	if (seopress_titles_nosnippet_option()) {
		return seopress_titles_nosnippet_option(); 
	} elseif (is_singular() && seopress_titles_nosnippet_post_option()) {
		return seopress_titles_nosnippet_post_option();
	} elseif ( is_home() && get_post_meta(get_option( 'page_for_posts' ),'_seopress_robots_snippet',true) !=''){ //BLOG PAGE
		return get_post_meta(get_option( 'page_for_posts' ),'_seopress_robots_snippet',true);
	} elseif (is_tax() || is_category() || is_tag()) {
		if (get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_snippet',true) == 'yes') {
			return get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_snippet',true);
		}
	}
}
if (get_option('blog_public') !='0') {// Discourage search engines from indexing this site is OFF
	//if (seopress_titles_noindex_bypass() || seopress_titles_nofollow_bypass() || seopress_titles_noodp_bypass() || seopress_titles_noarchive_bypass() || seopress_titles_nosnippet_bypass() || has_filter('seopress_titles_robots')) {
		function seopress_titles_advanced_robots_hook() {

			$seopress_comma_array = array();

			if (seopress_titles_noindex_bypass() !='') {
				$seopress_titles_noindex = 'noindex';
				//Hook on meta robots noindex - 'seopress_titles_noindex'
				if (has_filter('seopress_titles_noindex')) {
					$seopress_titles_noindex = apply_filters('seopress_titles_noindex', $seopress_titles_noindex);
			    }
				array_push($seopress_comma_array, $seopress_titles_noindex);
			}
			if (seopress_titles_nofollow_bypass() !='') {
				$seopress_titles_nofollow = 'nofollow';
				//Hook on meta robots nofollow - 'seopress_titles_nofollow'
				if (has_filter('seopress_titles_nofollow')) {
					$seopress_titles_nofollow = apply_filters('seopress_titles_nofollow', $seopress_titles_nofollow);
			    }
				array_push($seopress_comma_array, $seopress_titles_nofollow);
			}
			if (seopress_titles_noodp_bypass() !='') {
				$seopress_titles_noodp = 'noodp';
				//Hook on meta robots noodp - 'seopress_titles_noodp'
				if (has_filter('seopress_titles_noodp')) {
					$seopress_titles_noodp = apply_filters('seopress_titles_noodp', $seopress_titles_noodp);
			    }
				array_push($seopress_comma_array, $seopress_titles_noodp);
			}
			if (seopress_titles_noarchive_bypass() !='') {
				$seopress_titles_noarchive = 'noarchive';
				//Hook on meta robots noarchive - 'seopress_titles_noarchive'
				if (has_filter('seopress_titles_noarchive')) {
					$seopress_titles_noarchive = apply_filters('seopress_titles_noarchive', $seopress_titles_noarchive);
			    }
				array_push($seopress_comma_array, $seopress_titles_noarchive);
			}
			if (seopress_titles_nosnippet_bypass() !='') {
				$seopress_titles_nosnippet = 'nosnippet';
				//Hook on meta robots nosnippet - 'seopress_titles_nosnippet'
				if (has_filter('seopress_titles_nosnippet')) {
					$seopress_titles_nosnippet = apply_filters('seopress_titles_nosnippet', $seopress_titles_nosnippet);
			    }
				array_push($seopress_comma_array, $seopress_titles_nosnippet);
			}

			//new meta robots
			if (!in_array('noindex', $seopress_comma_array) && !in_array('nosnippet', $seopress_comma_array)) {
				$seopress_titles_max_snippet = 'max-snippet:-1, max-image-preview:large, max-video-preview:-1';
				array_push($seopress_comma_array, $seopress_titles_max_snippet);
			}

			$seopress_titles_robots = '<meta name="robots" content="';

			$seopress_comma_count = count($seopress_comma_array);
			for ($i = 0; $i < $seopress_comma_count; $i++) {
				$seopress_titles_robots .= $seopress_comma_array[$i];
			   	if ($i < ($seopress_comma_count - 1)) {
			    	$seopress_titles_robots .= ', ';
			   	}
			}

			$seopress_titles_robots .= '" />'; 
			$seopress_titles_robots .= "\n";

			//Hook on meta robots all - 'seopress_titles_robots'
			if (has_filter('seopress_titles_robots')) {
				$seopress_titles_robots = apply_filters('seopress_titles_robots', $seopress_titles_robots);
		    }
		    echo $seopress_titles_robots;
		}	
		add_action( 'wp_head', 'seopress_titles_advanced_robots_hook', 1 );
	//}
}

//noimageindex
function seopress_titles_noimageindex_option() {
	$seopress_titles_noimageindex_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_noimageindex_option ) ) {
		foreach ($seopress_titles_noimageindex_option as $key => $seopress_titles_noimageindex_value)
			$options[$key] = $seopress_titles_noimageindex_value;
		 if (isset($seopress_titles_noimageindex_option['seopress_titles_noimageindex'])) { 
		 	return $seopress_titles_noimageindex_option['seopress_titles_noimageindex'];
		 }
	}
}

function seopress_titles_noimageindex_post_option() {
	$_seopress_robots_imageindex = get_post_meta(get_the_ID(),'_seopress_robots_imageindex',true);
	if ($_seopress_robots_imageindex == 'yes') {
		return $_seopress_robots_imageindex;
	}
}

function seopress_titles_noimageindex_bypass() {
	if (seopress_titles_noimageindex_option()) {
		return seopress_titles_noimageindex_option(); 
	}
	elseif (is_singular() && seopress_titles_noimageindex_post_option()) {
		return seopress_titles_noimageindex_post_option();
	} elseif (is_tax() || is_category() || is_tag()) {
		if (get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_imageindex',true) == 'yes') {
			return get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_imageindex',true);
		}
	}
}

if (seopress_titles_noimageindex_bypass() || has_filter('seopress_titles_noimageindex')) {
	function seopress_titles_advanced_google_hook() {
		$seopress_titles_noimageindex = '<meta name="google" content="noimageindex" />';
		//Hook on meta robots noimageindex - 'seopress_titles_noimageindex'
		if (has_filter('seopress_titles_noimageindex')) {
			$seopress_titles_noimageindex = apply_filters('seopress_titles_noimageindex', $seopress_titles_noimageindex);
	    }
	    //Return noimageindex tag
		echo $seopress_titles_noimageindex."\n";
	}	
	add_action( 'wp_head', 'seopress_titles_advanced_google_hook', 1 );
}

//nositelinkssearchbox
function seopress_titles_nositelinkssearchbox_option() {
	$seopress_titles_nositelinkssearchbox_option = get_option("seopress_titles_option_name");
	if ( ! empty ( $seopress_titles_nositelinkssearchbox_option ) ) {
		foreach ($seopress_titles_nositelinkssearchbox_option as $key => $seopress_titles_nositelinkssearchbox_value)
			$options[$key] = $seopress_titles_nositelinkssearchbox_value;
		 if (isset($seopress_titles_nositelinkssearchbox_option['seopress_titles_nositelinkssearchbox'])) { 
		 	return $seopress_titles_nositelinkssearchbox_option['seopress_titles_nositelinkssearchbox'];
		 }
	}
}

if (seopress_titles_nositelinkssearchbox_option()) {
	function seopress_titles_nositelinkssearchbox_hook() {
		echo '<meta name="google" content="nositelinkssearchbox" />';
		echo "\n";
	}
	add_action( 'wp_head', 'seopress_titles_nositelinkssearchbox_hook', 2 );
}

//link rel prev/next
if (seopress_titles_paged_rel_option()) {
	function seopress_titles_paged_rel_hook() {
		global $paged;
		if ( get_previous_posts_link() ) { ?>
	        <link rel="prev" href="<?php echo get_pagenum_link( $paged - 1 ); ?>" />
	    <?php } if ( get_next_posts_link() ) { ?>
	        <link rel="next" href="<?php echo get_pagenum_link( $paged + 1 ); ?>" />
	    <?php }
	}
	add_action( 'wp_head', 'seopress_titles_paged_rel_hook', 9 );
}

//canonical
function seopress_titles_canonical_post_option() {
	$_seopress_robots_canonical = get_post_meta(get_the_ID(),'_seopress_robots_canonical',true);
	if ($_seopress_robots_canonical != '') {
		return $_seopress_robots_canonical;
	}
}

function seopress_titles_canonical_term_option() {
	$_seopress_robots_canonical = get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_canonical',true);
	if ($_seopress_robots_canonical != '') {
		return $_seopress_robots_canonical;
	}
}

if (function_exists('seopress_titles_noindex_bypass') && seopress_titles_noindex_bypass() !='1' && seopress_titles_noindex_bypass() !='yes') {//Remove Canonical if noindex
	if ( is_singular() && seopress_titles_canonical_post_option()) { //CUSTOM SINGLE CANONICAL
		function seopress_titles_canonical_post_hook() {
			$seopress_titles_canonical = '<link rel="canonical" href="'.htmlspecialchars(urldecode(seopress_titles_canonical_post_option())).'" />';
			//Hook on post canonical URL - 'seopress_titles_canonical'
			if (has_filter('seopress_titles_canonical')) {
				$seopress_titles_canonical = apply_filters('seopress_titles_canonical', $seopress_titles_canonical);
		    }
			echo $seopress_titles_canonical."\n";
		}	
		add_action( 'wp_head', 'seopress_titles_canonical_post_hook', 1 );
	} elseif ( is_home() && get_post_meta(get_option( 'page_for_posts' ),'_seopress_robots_canonical',true) !=''){ //BLOG PAGE
		function seopress_titles_canonical_post_hook() {
			$seopress_titles_canonical = '<link rel="canonical" href="'.htmlspecialchars(urldecode(get_post_meta(get_option( 'page_for_posts' ),'_seopress_robots_canonical',true))).'" />';
			//Hook on post canonical URL - 'seopress_titles_canonical'
			if (has_filter('seopress_titles_canonical')) {
				$seopress_titles_canonical = apply_filters('seopress_titles_canonical', $seopress_titles_canonical);
		    }
			echo $seopress_titles_canonical."\n";
		}	
		add_action( 'wp_head', 'seopress_titles_canonical_post_hook', 1 );
	} elseif ((is_tax() || is_category() || is_tag()) && seopress_titles_canonical_term_option()) { //CUSTOM TERM CANONICAL
		function seopress_titles_canonical_term_hook() {
			$seopress_titles_canonical = '<link rel="canonical" href="'.htmlspecialchars(urldecode(seopress_titles_canonical_term_option())).'" />';
			//Hook on post canonical URL - 'seopress_titles_canonical'
			if (has_filter('seopress_titles_canonical')) {
				$seopress_titles_canonical = apply_filters('seopress_titles_canonical', $seopress_titles_canonical);
		    }
			echo $seopress_titles_canonical."\n";
		}	
		add_action( 'wp_head', 'seopress_titles_canonical_term_hook', 1 );
	} else { //DEFAULT CANONICAL
		function seopress_titles_canonical_hook() {
			global $wp;
			if (seopress_advanced_advanced_trailingslash_option()) {
				$current_url = home_url(add_query_arg(array(), $wp->request));
			} else {
				$current_url = trailingslashit(home_url(add_query_arg(array(), $wp->request)));
			}
			if (is_search()) {
				$seopress_titles_canonical = '<link rel="canonical" href="'.htmlspecialchars(urldecode(get_home_url().'/search/'.get_search_query())).'" />';
			} elseif (is_paged()){
				$seopress_titles_canonical = '<link rel="canonical" href="'.htmlspecialchars(urldecode(get_pagenum_link('1'))).'" />';
			} elseif (is_singular()) {
				$seopress_titles_canonical = '<link rel="canonical" href="'.htmlspecialchars(urldecode(get_permalink())).'" />';
			} else {
				$seopress_titles_canonical = '<link rel="canonical" href="'.htmlspecialchars(urldecode($current_url)).'" />';
			}
			//Hook on post canonical URL - 'seopress_titles_canonical'
			if (has_filter('seopress_titles_canonical')) {
				$seopress_titles_canonical = apply_filters('seopress_titles_canonical', $seopress_titles_canonical);
		    }
			echo $seopress_titles_canonical."\n";
		}	
		add_action( 'wp_head', 'seopress_titles_canonical_hook', 1 );
	}
}
