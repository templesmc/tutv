<?php

// Set post default terms and values to speed data entry and avoid errors


// [Disabled] Define default terms for custom taxonomies
//
// When left blank, custom taxonomies will be given a default value. This currently includes:
// the taxonomy 'show' defaults to 'other'
// Based on script by Michael Fields (http://wordpress.mfields.org/)
function tutv_set_default_object_terms( $post_id, $post ) {
	if( 'publish' === $post->post_status ) {
		$defaults = array(
			'shows' => array( 'other' )
		);
		$taxonomies = get_object_taxonomies( $post->post_type );
		foreach( (array) $taxonomies as $taxonomy ) {
			$terms = wp_get_post_terms( $post_id, $taxonomy );
			if( empty( $terms ) && array_key_exists( $taxonomy, $defaults ) ) {
				wp_set_object_terms( $post_id, $defaults[$taxonomy], $taxonomy );
			}
		}
	}
}
// add_action( 'save_post', 'tutv_set_default_object_terms', 100, 2 );

// Set a posts genre based on the show's default genre (set in taxonomy meta) if none is set
function tutv_set_default_genre( $post_id, $post ) {
	if( 'publish' != $post->post_status )
		return;

	$shows = wp_get_post_terms( $post_id, 'shows' );
	$genres = wp_get_post_terms( $post_id, 'genre' );

	if( !empty( $genres ) || !function_exists( 'get_term_meta' ) )
		return;
	if( isset( $shows[0]->term_id ) ) {
		$genre = get_term_meta( $shows[0]->term_id, 'genre', true );

		if( !empty( $genre ) ) {
			wp_set_object_terms( $post_id, $genre, 'genre' );
		}
	}

}
add_action( 'save_post', 'tutv_set_default_genre', 15, 2 );

// Define event show and genre based on connected episode info
function tutv_set_event_terms( $post_id, $post ) {
	if ( $post->post_type != 'events' )
		return;
	if ( function_exists( 'p2p_type' ) )
		$connected = p2p_type('schedule_event')->get_connected($post_id);

	// No posts if there's no page connecting them
	if ( empty($connected) || !$connected->have_posts() )
		return;

	// Get the page ids
	$connected = wp_list_pluck( $connected->posts, 'ID' );

	$taxonomies = get_object_taxonomies( $post->post_type );
	foreach( (array) $taxonomies as $taxonomy ) {

		$terms = wp_get_post_terms( $post_id, $taxonomy );
		if( empty( $terms ) && !empty( $connected[0] ) ) {
			$episode_terms = wp_get_object_terms( $connected[0], $taxonomy, 'fields=names' );

			if( !empty( $episode_terms ) )
				wp_set_object_terms( $post_id, $episode_terms, $taxonomy );
		}
	} /* foreach */
}
add_action( 'save_post', 'tutv_set_event_terms', 10, 2 );

// Programmatically duplicate schedule posts and set the title to "show: episode"
//
// The duplicate posts are created for exactly 8 and 16 hours after an entry
function tutv_set_events_insert_post( $post_id, $post ) {
	if ( $post->post_type != 'events' || $post->post_title != 'Auto Draft' )
		return;

 	if ( function_exists( 'p2p_type' ) )
		$connected = p2p_type('schedule_event')->get_connected($post_id);

	// No posts if there's no page connecting them
	if ( !$connected->have_posts() )
		return array();

	// Get the page ids
	$connected = wp_list_pluck( $connected->posts, 'ID' );

	$episode = get_post( $connected[0] );

	if( !empty( $episode ) ) {
		// set the post title to be more helpful
		$post->post_title = get_the_show( $post_id ) . $episode->post_title;
	}

	wp_update_post( $post );

	// get the schedule time of the current post
	$scheduled_time = get_post_meta($post_id, 'date_value', true);

	// copy the post and clear its ID so it will generate a new post
	$duplicate_post = $post;
	$duplicate_post->ID = null;

	// foreach new schedule entry, add a duplicate entry with a date value 8 hours and 16 hours in advance
	foreach( array($scheduled_time + 60 * 60 * 8, $scheduled_time + 60 * 60 * 16) as $new_time) {
		// insert the post. if its successful, it will return the new post id
		$duplicate_post_id = wp_insert_post( $duplicate_post );

		if( $duplicate_post_id ) {

			// update schedule time
			update_post_meta($duplicate_post_id, 'date_value', $new_time, $scheduled_time);

			// update connected episode
			if ( function_exists( 'p2p_type' ) ) {
				p2p_type('schedule_event')->connect($duplicate_post_id, $episode->ID);
			}

			// find and set show term
			tutv_set_event_terms($duplicate_post_id, $duplicate_post);
		}
	}
}
add_action( 'wp_insert_post', 'tutv_set_events_insert_post', 11, 2 );

