<?php

/**
 * Get a post excerpt outside of the loop
 *
 * @param int $post_id
 */
function get_the_excerpt_here( $post_id ) {
	global $wpdb;
	
	$query = "SELECT post_excerpt FROM wp_posts WHERE ID = {$post_id} LIMIT 1";
	$result = $wpdb->get_results($query, ARRAY_A);
	
	return $result[0]['post_excerpt'];
}

/**
 * Ouput the permalink to a specific show page
 *
 * @param int|object|string $show_id Term ID for a show
 */
function get_show_link( $show ) {
	
	return get_term_link( $show, 'shows' );
}

/**
 * Get show background 
 *
 * @param string $show A show slug
 * @returns URL of show background image
 */
function get_show_background( $show ) {

	//filetypes to search for (in order or importance)
	$filetypes = array('jpg', 'png');
	
	//naming template of show backgrounds within the theme folder
	$path = "/images/show-backgrounds/{$show}.";
	
	$show_background = false;
	
	foreach($filetypes as $filetype) {
		if( file_exists( get_stylesheet_directory() . $path . $filetype ) ) {
			$show_background = get_stylesheet_directory_uri() . $path . $filetype;
			break;
		}
	}
			
	return $show_background;
}

// Return thumbnail image HTML for a given post's show
function get_posts_show_thumbnail( $post_id, $thumbnail_size = 'thumbnail-square' ) {
	global $taxonomy_images_plugin;
 	
	 if( !isset( $post_id ) ) {
	 	return '';
	}
	
	$terms = wp_get_object_terms($post_id, 'shows');
	
	if( !isset($terms[0]) ) {
		return '';
	}
	
	$term = $terms[0];

	return $taxonomy_images_plugin->get_image_html( $thumbnail_size, $term->term_taxonomy_id );
}

// Return video thumbnail from featured image or show image
function get_video_thumbnail( $post_id ) {
 	
	 if( !isset( $post_id ) )
	 	return '';
	 
	$content = '<span class="thumbnail alignleft">';
	if( has_post_thumbnail( $post_id ) ) {
		$content .= get_the_post_thumbnail($post_id, 'thumbnail'); 
	} else {
		$content .= get_posts_show_thumbnail($post_id);
	}
	$content .= '</span>';
	return $content;
 }

// Display video thumbnail from featured image or show image
function the_video_thumbnail( $post_id ) {
	echo get_video_thumbnail( $post_id );
}

/**
 * Test whether a show is set to the default term
 *
 * @param object $term A wordpress term object
 * @return boolean            
 */
function has_no_show( $term ) {
	if( $term && $term->slug == '' ) /* originally 'other'. modified 09-21-2011 PWinnick */ {
		return true;
	} else {
		return false;
	}
}

/**
 * Test whether a show is set to be hidden from display.
 *
 * @param object $term A wordpress term object
 * @return boolean            
 */
function is_hidden_show( $term = '' ) {
	
	if( isset( $term->slug ) )
		$term = $term->slug;
		
	$hidden_shows = array('other');
	
	$shows = get_terms('shows');
	foreach( $shows as $show ) {
		if ( get_term_meta( $show->term_id, 'is_hidden', true ) )
			$hidden_shows[] = $show->slug;
	} 
	
	if( $term && in_array( $term, $hidden_shows ) ) {
		return true;
	} else {
		return false;
	}
}

// for a given post_id, get the first show term object
function get_the_show_term( $post_id ) {
	$terms = wp_get_object_terms($post_id, 'shows');
	if ( !is_wp_error($terms) && isset($terms[0]) ) {
		return $terms[0];
	}
	return false;
}

/**
 * Output the first show name based on post ID
 * 
 * @param int $post_id The post id to return a show for.
 * @param int $before Text to display before the show name.
 * @param int $after Text to display after the show name.
 * @return string The show string
 */
function get_the_show( $post_id, $before = '', $after = ': ' ) {
	$output = '';
	$term = get_the_show_term($post_id);
	if ( !empty($term) ) {
		if( !has_no_show( $term ) && !is_hidden_show( $term ) ) {
			$output = $before . $term->name . $after;
		}
	}
	
	return $output;
}

/**
 * Print the first show name for a given post
 * 
 * For use in The Loop only.
 *
 * @param int $before Text to display before the show name.
 * @param int $after Text to display after the show name.
 */
function the_show( $before = '', $after = ': ' ) {
	global $post;
	echo get_the_show( $post->ID, $before, $after );
}

// Get a post thumbnail
//
// If a post doesn't have a featured thumbnail, get the first image attached to the post
// Takes attributes $post_ID and optionally String $size: A registered image size
function get_thumb ( $post_ID, $size = 'thumbnail' ){
	if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post_ID ) ) {
		return get_the_post_thumbnail( $post_ID, $size );
	} else {
		$thumbargs = array(
			'post_type' => 'attachment',
		    'numberposts' => 1,
		    'post_status' => null,
		    'post_parent' => $post_ID,
	    );
	    $thumb = get_posts( $thumbargs );
	    if ($thumb) 
	        return wp_get_attachment_image( $thumb[0]->ID, $size );
    }
} 

/**
 * Output navigation elements on a show page
 *
 * Find the post type for the current query and generate navigation links that also 
 * keep the page number set in the query variables. For instance, '?show_notes_page=1&clip_page=2&episodes_page=1#clip'
 * is on the second page for the clips box and the first page for show_notes and episodes.
 *
 * @author Sam Marguiles
 * @author Chris Montgomery <mont.chr@gmail.com>
 * @since 1.0.0
 * @version 1.0.1
 * 
 * @param array $args Custom post type page number values to use in generating the navigation
 */

function get_shows_nav( $args = '' ) {		
    global $wp_query;
    
    $current_post_type = get_query_var('post_type');
        
    $defaults = array(
    	'show_notes_page' => ( isset( $_GET['show_notes_page'] ) ) ? $_GET['show_notes_page'] : 1,
    	'clip_page' => ( isset( $_GET['clip_page'] ) ) ? $_GET['clip_page'] : 1,
    	'episodes_page' => ( isset( $_GET['episodes_page'] ) ) ? $_GET['episodes_page'] : 1
    );
    
    $args = wp_parse_args($args, $defaults);
    
    $post_type_paged = $args[$current_post_type . '_page'];
    
    $max_pages = $wp_query->max_num_pages;
	
	echo "<div class='shows_nav archive-navigation navigation' id='$current_post_type" . "_nav'>";
					
    if( $post_type_paged <= $max_pages && $post_type_paged != 1 ) {
    	$next_args = $args;
    	$next_args[$current_post_type . '_page'] = $post_type_paged - 1;
    	$next_link = '?' . http_build_query($next_args, '', '&amp;') . '#' . $current_post_type;
    	
    	echo "<div class='nav-next nav-item'><a href='$next_link'> &larr; Newer</a></div>";
    }
    
    if( $post_type_paged < $max_pages ) {
    	$previous_args = $args;
    	$previous_args[$current_post_type . '_page'] = $post_type_paged + 1;
    	$previous_link = '?' . http_build_query($previous_args, '', '&amp;') . '#' . $current_post_type;

    	echo "<div class='nav-previous nav-item'><a href='$previous_link'>Older &rarr;</a></div>";
    }
    
    echo "</div>";
}
/**
 * Echo get_shows_nav()
 * 
 * $args array Custom post type page number values to use in generating the navigation
 */
function the_shows_nav( $args = '' ) {
	echo get_shows_nav( $args );
}

// For use with usort(), allows ordering terms by a weight property
function sort_by_weight( $a, $b ) { 
	if(  $a->weight == $b->weight ){ return 0 ; } 
	return ($a->weight < $b->weight) ? -1 : 1;
}

function order_production_types( $a, $b ) {
	global $production_types;
	return ($production_types[$a]['order'] > $production_types[$b]['order']) ? true : false;
}

// Return get_terms(), adding a weight property and sorting by it
// 
// Used on page-shows-grid.php         
function get_weighted_terms( $taxonomy ) {
	
	$terms = get_terms( $taxonomy );
	foreach( $terms as $term ) {
		$term_weight = (int) get_term_meta( $term->term_id, 'weight', true );
		$term->weight = ($term_weight) ? $term_weight : 0;
	}
	usort($terms, 'sort_by_weight');
	
	//$terms = wp_cache_set( $cache_key, $terms, 'weighted_terms', 86400 ); // one day

	return $terms;
}

/**
 * Get an odd or even class name for a given integer
 *
 */
function get_class_odd_or_even( $num ) {
	$output = ($num%2) ? ' item-odd' : ' item-even';
	return $output;
}

/**
 * Output an odd or even class name for a given integer
 *
 */
function class_odd_or_even( $num ) {
	echo ($num%2) ? ' item-odd' : ' item-even';
}