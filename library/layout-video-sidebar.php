<?php

// Output a sidebar to posts with video elements that other widgets can hook into
function tutv_video_sidebar() {	
	if( is_singular( array( 'episodes', 'events', 'clip' ) ) ) {
		$content = '<div class="video-sidebar">';
		$content .=	apply_filters('tutv_video_sidebar', $content);
		$content .=	'</div>';
		echo $content;
	}
}
//add_filter('thematic_navigation_above', 'tutv_video_sidebar', 10);

// Video sidebar widget for showtimes of a current episode or event
function tutv_upcoming_showtimes() {
	global $post;
		 
	if ( !is_singular( array( 'episodes' ) ) ) 
		return;
	
	$args = array(
		'post_type'=>'events',
		'orderby' => 'meta_value',
		'meta_key' => 'date_value',
		'meta_query' => array(
			array(
				'key' => 'date_value',
				'value' => time() - 2 * 60 * 60,
				'type' => 'numeric',
				'compare' => '>='
			)
		),	  
		'connected_type' => 'schedule_event',
  		'connected_items' => get_queried_object(),
		'order' => 'ASC',
		'posts_per_page' => 5
	);
	
	$upcoming_showtimes = new WP_Query( $args );
	
	if( $upcoming_showtimes->have_posts() ) {
		
		$output .= '<div class="upcoming-showtimes">';
		$output .= '<h3 class="section-header">Upcoming Showtimes</h3>';
		$output .= '<ul>';
		
		while( $upcoming_showtimes->have_posts() ): $upcoming_showtimes->the_post();
						
			$start_time = get_post_meta( get_the_ID(), 'date_value', true );
			$formatted_time = ''; 
			
			if ( $start_time ) {					
				$formatted_time .= date( 'h:i A', $start_time );
				$current_day = date( 'w', $start_time );
				$current_day_name = date( 'l', $start_time );
				$current_date_name = date( 'F j, Y', $start_time );
	
				$output .= '<li>';
				$output .= "<span class='scheduled-date'>$current_day_name, $current_date_name</span>";
				$output .= "<span class='scheduled-time'>$formatted_time</span>";
				$output .= '</li>';
			}
		
		endwhile;
		
		wp_reset_postdata();
		
		$output .= '</ul>';
		$output .= '</div>';
		
	}
	
	return $output;
}
add_filter('tutv_video_sidebar', 'tutv_upcoming_showtimes', 1);

// Video sidebar widget for related episodes on an episode page
function tutv_episode_related_episodes( $content ) {
if ( is_singular( 'episodes' ) ) {
	global  $post;
	
	$term = wp_get_object_terms($post->ID, 'shows');
	$term = $term[0]; 

	$args = array(
		'post_type' => 'episodes',
		'posts_per_page' => '4',
		'shows' => $term->slug,
		'post__not_in' => array($post->ID)
	);
	
	$title = 'Related from ' . $term->name;
	
	$content .= tutv_video_block($term, $args, $title);
}
	return $content;		
}
add_filter('tutv_video_sidebar', 'tutv_episode_related_episodes', 3);

// Video sidebar widget for related clips on an episode page
function tutv_episode_related_clips( $content ) {
if ( is_singular( 'episodes' ) ) {
	global $post;
	$term = get_the_show_term($post->ID); 
	
	$connected_clips = p2p_type( 'episode_clip' )->get_connected( get_queried_object() );
  	
	if( $connected_clips->have_posts() ) {
		
		while ( $connected_clips->have_posts() ): $connected_clips->the_post();
			$connected_clip_ids[] = $post->ID;
		endwhile;
		
		wp_reset_postdata();
		
		$args = array(
			'post_type' => 'clip',
			'posts_per_page' => '3',
			'post__in' => $connected_clip_ids
		);
		$title = 'Clips from this episode';
	} elseif ( !empty($term) ) {
		$args = array(
			'post_type' => 'clip',
			'posts_per_page' => '3',
			'shows' => $term->slug
		);
		$title = 'Related clips from ' . $term->name;
	}
	
	$content .= tutv_video_block($term, $args, $title);
}	
	return $content;		
}	
add_filter('tutv_video_sidebar', 'tutv_episode_related_clips', 5);
	
// Video sidebar widget for related episodes on an clip page
//
// If a clip has connected episode, display that instead
function tutv_clip_related_episodes( $content ) {
if ( is_singular( 'clip' ) ) {
	global  $post;
	
	$term = get_the_show_term($post->ID); 
	
	$connected_episodes =  new WP_Query(  array(
	  'connected_type' => 'episode_clip',
	  'connected_items' => get_queried_object(),
	  'nopaging' => true
  	) );
		
	if( $connected_episodes->have_posts() ) {
		
		while ( $connected_episodes->have_posts() ): $connected_episodes->the_post();
			$connected_episode_ids[] = $post->ID;
		endwhile;
		
		wp_reset_postdata();
		
		$args = array(
			'post_type' => 'episodes',
			'posts_per_page' => '1',
			'post__in' => $connected_episode_ids
		);
		$title = 'The full episode';
	} elseif ( !empty($term) ) {
		$args = array(
			'post_type' => 'episodes',
			'posts_per_page' => '3',
			'shows' => $term->slug
		);
		$title = 'Full episodes from ' . $term->name;
	} else {
		return false;
	}
	
	$content .= tutv_video_block($term, $args, $title);
}
	return $content;		
}
add_filter('tutv_video_sidebar', 'tutv_clip_related_episodes', 7);

// Video sidebar widget for related clips on an clip page
function tutv_clip_related_clips($content) {
if ( is_singular( 'clip' ) ) {
	global $post;
	
	$term = get_the_show_term($post->ID); 

	$related_clips = p2p_type( 'episode_clip' )->get_related( get_queried_object() );
	
	if( $related_clips->have_posts() ){
		
		while ( $related_clips->have_posts() ): $related_clips->the_post();
			$related_clips_ids[] = get_the_ID();
		endwhile;
		
		wp_reset_postdata();

		$args = array(
			'post_type' => 'clip',
			'posts_per_page' => '3',
			'post__in' => $related_clips_ids,
			'post__not_in' => array($post->ID)
		);
		$title = 'More clips from this episode';
	} elseif ( !empty($term) ) {
		
		$args = array(
			'post_type' => 'clip',
			'posts_per_page' => '3',
			'shows' => $term->slug,
			'post__not_in' => array($post->ID)
		);
		$title = 'Related clips from ' . $term->name;
	} else {
		return false;
	}
	
	$content .= tutv_video_block($term, $args, $title);
}
	return $content;		
}
add_filter('tutv_video_sidebar', 'tutv_clip_related_clips', 9);

// [Disabled] Video sidebar widget for related videos from the same genre
function tutv_related_from_this_genre( $content ) {
	global $post;

	if ( is_singular( array( 'clip', 'episodes' ) ) ) {
		 
		$genre = wp_get_object_terms($post->ID, 'genre');
		$genre = $genre[0];
		
		$term = wp_get_object_terms($post->ID, 'shows');
		$term = $term[0]; 

		$post_type = get_post_type();

		if( empty($genre) )
			return;
			
		$posts_from_this_show = get_posts( array(
			//'posts_per_page' => '-1',
			'shows' => $term->slug,
			'post_type' => $post_type
		) );
		$excluded_posts = array( $post->ID );
		
		foreach($posts_from_this_show as $show_post) {
			$excluded_posts[] = $show_post->ID;
		}
		
		$args = array(
			'genre' => $genre->slug,
			'post_type' => $post_type,
			'post__not_in' => $excluded_posts
	  	);	  				
		$genre_name = strtolower( $genre->name );
		$title = "Related $genre_name videos";
		
		$content .= tutv_video_block($genre, $args, $title);
	}
	
	return $content;		
}
//add_filter('tutv_video_sidebar', 'tutv_related_from_this_genre', 11);



// Outputs video widgets content
// 
// $term: A term object (usually a show term)
// $args: a wordpress query array.
// $title: Title text for the video area
function tutv_video_block( $term, $args, $title = '' ) {
	
	if ($args['post_type']) 
		$post_type = get_post_type_object( $args['post_type'] );
	
	if( !$title ) 
		$title = 'Related ' . $post_type->name . ' from ' . $term->name ;
	
	$video_block = new WP_Query($args);
	
	if( $video_block->have_posts() ) {
		
		$content = "<div class='" . $post_type->query_var . "-block video-block'>";
		$content .= "<h3 class='section-header'>$title</h3>";
		while ($video_block->have_posts()) : $video_block->the_post();
			$content .=  "<div class='" . $post_type->query_var . "-item video-item'>";
				$content .=  '<a href="' . get_permalink( get_the_ID() ) . '" rel="bookmark">';
					$content .=  '<span class="video-title">';
						$content .=  get_the_show( get_the_ID() ) . get_the_title(); 
					$content .= '</span>';
					$content .= get_video_thumbnail( get_the_ID() ); 

				$content .= '</a>';
			$content .= '</div>';
		endwhile;
		$content .= '</div>';
		
	} else {
		$content = '<!-- No content to return -->';
	}
	wp_reset_query();
	
	return $content;
}
 