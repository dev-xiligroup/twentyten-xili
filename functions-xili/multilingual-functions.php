<?php
/**
 * ***** Functions to improve xili-language *****
 * ** selection for twentyten-xili child of twentyten **
 * @updated 3.0 - 2013-05-05
 */
 

 
 

/**
 * ***** BreadCrump ******
 * @since 20101111
 *
 * can be adapted with two end params
 */
function xiliml_adjacent_join_filter($join, $in_same_cat, $excluded_categories) {
	global $post, $wpdb;
	$curlang = xiliml_get_lang_object_of_post( $post->ID );
	// in join p is $wpdb->posts AS p in get_adjacent_post of lin_template.php
	if ($curlang) { // only when language is defined !
		$join .= " LEFT JOIN $wpdb->term_relationships as xtr ON (p.ID = xtr.object_id) LEFT JOIN $wpdb->term_taxonomy as xtt ON (xtr.term_taxonomy_id = xtt.term_taxonomy_id) ";
	}	
return $join;
}

function xiliml_adjacent_where_filter($where, $in_same_cat, $excluded_categories) {
	global $post;
	$curlang = xiliml_get_lang_object_of_post( $post->ID );
	if ( $curlang ) {
		$wherereqtag = $curlang->term_id; 
		$where .= " AND xtt.taxonomy = '".TAXONAME."' ";
		$where .= " AND xtt.term_id = $wherereqtag "; 
	}
	return $where;
}

if ( class_exists('xili_language') ) {
	
	add_filter('get_next_post_join','xiliml_adjacent_join_filter',10,3);
	add_filter('get_previous_post_join','xiliml_adjacent_join_filter',10,3);
	
	add_filter('get_next_post_where','xiliml_adjacent_where_filter',10,3);
	add_filter('get_previous_post_where','xiliml_adjacent_where_filter',10,3);
	
}


/**
 * add search other languages in form - see functions.php when fired
 *
 */
function my_langs_in_search_form ( $the_form ) {
	
	$form = '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >
	<div><label class="screen-reader-text" for="s">' . esc_attr__( "Search", the_theme_domain() ) . '</label>
	<input type="text" value="' . get_search_query() . '" name="s" id="s" />
	<input type="submit" id="searchsubmit" value="'. esc_attr__( 'Search', the_theme_domain() ) .'" />
	</div>';
	$form .= xiliml_langinsearchform ($before='', $after='', false);
	$form .= '</form>';
	return $form ;
}

/*special flags in list*/
function xiliml_infunc_the_other_posts($post_ID, $before = "Read This post in", $separator = ", ", $type = "display") {
			$outputarr = array();
			$listlanguages = get_terms(TAXONAME, array('hide_empty' => false));
			$post_lang = get_cur_language($post_ID); // to be used in multilingual loop since 1.1
			//$post_lang = $langpost['lang']; //print_r($langpost);
			foreach ($listlanguages as $language) {
				$otherpost = get_post_meta($post_ID, 'lang-'.$language->slug, true);
				
				if ($type == "display") {
					if ('' != $otherpost && $language->slug != $post_lang ) {
						$outputarr[] = "<a href='".get_permalink($otherpost)."' >".__($language->description,the_theme_domain()) ." <img src='".get_bloginfo('stylesheet_directory')."/images/flags/".$language->slug.".png' alt='' /></a>";
					}
				} elseif ($type == "array") { // here don't exclude cur lang
					if ('' != $otherpost)
						$outputarr[$language->slug] = $otherpost;
				}
			}
			if ($type == "display") {
				if (!empty($outputarr))
					$output =  (($before !="") ? __($before,the_theme_domain())." " : "" ).implode ($separator, $outputarr);
				if ('' != $output) { echo $output;}	
			} elseif ($type == "array") {
				if (!empty($outputarr)) {
					$outputarr[$post_ID] = $post_lang; 
					// add a key with curid to give his lang (empty if undefined)
					return $outputarr;
				} else {
					return false;	
				}
			}	
						
}
add_filter('xiliml_the_other_posts','xiliml_infunc_the_other_posts',10,4); // 1.1 090917


?>
