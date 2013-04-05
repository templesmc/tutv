<?php

/**
 * Create custom content structures including all custom content types (eg. 'episodes'
 * and 'clips') and taxonomies (eg. 'shows'.)
 *            
 */
function tutv_taxonomies_and_post_types() {
	register_post_type( 'episodes', array( 
		'labels' => array(
			'name' => 'Episodes', 
			'singular_name' => 'Episode'
		), 
		'public' => true, 
		'show_ui' => true,
		'supports' => array(
			'title', 'editor', 'thumbnail', 'custom-fields', 'excerpt'
		),
		'rewrite' => array(
			'slug' => 'shows/%show_name%',
			'with_front' => false
		)
	));
	register_post_type( 'clip', array( 
		'label' => __('Clips'), 
		'public' => true, 
		'show_ui' => true,
		'supports' => array(
			'title', 'editor', 'thumbnail', 'custom-fields'
		),
		'rewrite' => array(
			'slug' => 'shows/%show_name%/clip',
			'with_front' => false
		)
	));
	
	/**
	 * Not displayed in the front-end, this internal taxonomy is used 
	 * to determine what content to display on the homepage slideshow,
	 * allowing different content types to be mingled. Implemented using a 
	 * meta box below.
	 *
	 */
	register_taxonomy( 'featured', array('post'), array( 
		'hierarchical' => false, 
		'labels' => array(
			'name' => 'Featured', 
			'singular_name' => 'Featured'
		), 
		'query_var' => true, 
		'rewrite' => false,
		'public' => true, 
		'show_ui' => false,  
	));
	// Added 01-13-2012 by PWinnick
	register_taxonomy( 'featured-video', array('episodes', 'clip'), array( 
		'hierarchical' => false, 
		'labels' => array(
			'name' => 'Featured Video', 
			'singular_name' => 'Featured Video'
		), 
		'query_var' => true, 
		'rewrite' => false,
		'public' => true, 
		'show_ui' => false,  
	));
	register_taxonomy( 'shows', array('episodes', 'show_notes', 'clip', 'events', 'show_page'), array( 
		'hierarchical' => false, 
		'labels' => array(
			'name' => 'Shows', 
			'singular_name' => 'Show'
		), 
		'query_var' => true, 
		'rewrite' => array(
			'slug' => 'shows'
		) 
	));
	register_post_type( 'show_notes', array( 
		'labels' => array(
			'name' => 'Show Blog',
			'singular_name' => 'Show Post'
		), 
		'public' => true, 
		'show_ui' => true,
		'supports' => array(
			'title', 'editor', 'thumbnail', 'author', 'comments', 'revisions'
		),
		'rewrite' => array(
			'slug' => 'shows/%show_name%/blog',
			'with_front' => false
		)
	));
	register_post_type( 'show_page', array( 
		'hierarchical' => true, 
		'labels' => array(
			'name' => 'Supplemental Show Pages',
			'singular_name' => 'Supplemental Show Page'
		), 
		'public' => true,
		'show_ui' => true,
		'supports' => array(
			'custom-fields', 'editor', 'title', 'page-attributes'
		),
		'rewrite' => array(
			'slug' => 'shows/%show_name%/about',
			'with_front' => false
		)
	));
	register_post_type( 'events', array( 
		'labels' => array(
			'name' => 'Scheduled Shows',
			'singular_name' => 'Schedule'
		), 
		'public' => false,
		'show_ui' => true,
		'supports' => array(
			'custom-fields'
		)
	));
	register_taxonomy( 'genre', array( 'episodes', 'clip', 'events' ), array( 
		'hierarchical' => false,
		'labels' => array(
			'name' => 'Genres',
			'singular_name' => 'Genre'
		),
		'query_var' => true,
		'rewrite' => array(
			'slug' => 'genres'
		) 
	));
}
add_action( 'init', 'tutv_taxonomies_and_post_types', 0 );

/**
 * Define the custom permalink structure
 * 
 * This specifically modifies how wordpress internally generates links
 * and not the rewrite rules. Must be consistent with tutv_rewrite_rules_array() rules or else
 * wordpress generated links will not work.
 *           
 */
function tutv_post_type_link( $post_link, $id = 0, $leavename = false ) {

    if ( strpos( '%show_name%', $post_link ) === 0 ) {
    
		return $post_link;
		
    }
    
    $post = get_post( $id );
    
    // For these post types replace '%show_name%' (specified when registering the taxonomies) with 
    // its show's slug
    if ( is_object( $post ) || $post->post_type == ( 'episodes' || 'show_notes' ||  'clip' || 'show_page' ) ) {
	    
	    $terms = wp_get_object_terms( $post->ID, 'shows' );
	    
	    if ( !$terms ) {
			return str_replace( 'shows/%show_name%/', '', $post_link );
	    }
	    
	    return str_replace( '%show_name%', $terms[0]->slug, $post_link );
	    
    } else {
    
    	return $post_link;
    	
	}
}
add_filter('post_type_link', 'tutv_post_type_link', 1, 3);

/**
 * Define the custom rewrites
 * 
 * This specifically modifies how the server redirects pages and knows what content
 * to display for each page. 
 *            
 */
function tutv_rewrite_rules_array( $rules ) {
	$newrules = array();
	$newrules['shows/(.+)/clip/(.+)$'] = 'index.php?clip=$matches[2]';
	$newrules['shows/(.+)/blog/(.+)$'] = 'index.php?show_notes=$matches[2]';
	$newrules['shows/(.+)/about/(.+)$'] = 'index.php?show_page=$matches[2]';
	$newrules['shows/(.+)/(.+)$'] = 'index.php?episodes=$matches[2]';
	//$newrules['(.+)$'] = 'index.php?shows=$matches[2]'; /* 10-17-2011 PWinnick */
	return $newrules + $rules;
}
add_filter('rewrite_rules_array', 'tutv_rewrite_rules_array');
	
/**
 * Create post connections using scribu's 'post to post' plugin
 *
 */
function tutv_init_post_relationships() {
	if ( !function_exists( 'p2p_register_connection_type' ) ) 
		return;
	
	// Episodes of a show have events, which are used to create
	// a tv schedule
	/*p2p_register_connection_type( 'events', 'episodes', false );*/
	p2p_register_connection_type( array(
		'id' => 'schedule_event',
		'from' => 'events',
		'to' => 'episodes'
	) );
	
	// Clips are associated with episodes (and vise-versa) to establish
	// their connection: clips are segments of an episode. Used to generate
	// related videos.
	/*p2p_register_connection_type( 'clip', 'episodes', true );*/
	p2p_register_connection_type( array(
		'id' => 'episode_clip',
		'from' => 'clip',
		'to' => 'episodes'
	) );
}
add_action( 'init', 'tutv_init_post_relationships', 100 );