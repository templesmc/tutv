<?php

// Add favicon to every page
function tutv_favicon() { 
?>
    <link rel="shortcut icon" href="wp-content/themes/tutv/images/favicon.ico" />
	<!-- WP-Minify JS -->
<?php 
}
add_action('wp_head', 'tutv_favicon');
add_action('admin_head', 'tutv_favicon');

// Add conditional body classes
function tutv_body_classes( $classes ) {
	if( is_tax() ) {
		global $taxonomy;
		if( !in_array( $taxonomy, $classes ) )
			$classes[] = "taxonomy " . "taxonomy-".$taxonomy;
	}
	
	global $post;
	if( isset($post->ID) )
		$shows = wp_get_object_terms( $post->ID, 'shows' );
	if( !empty( $shows) ) {
		foreach($shows as $show) {
			$classes[] = 'has-show-' . $show->slug;
		}
		$classes[] = 'has-taxonomy-show';
	}
	
	if( is_singular( array( 'show_notes' ) ) ) {
		$classes[] = 'show-post';
	}
	
	// blog post
	if( is_singular( array( 'post' ) ) && in_category( 'blog' ) ) {
		$classes[] = 'blog-post blog';
	}

	// blog archive page (using index.php on page)
	if ( is_home() && !is_front_page() ) {
		$classes[] = 'blog-archive';
	}

	return $classes;
}
add_filter('body_class', 'tutv_body_classes');


// Fix for thematic to add a page title to taxonomy pages
function tutv_taxonomy_pagetitle( $content ) {
	global $post;
	if ( is_tax() ) {
		// find taxonomy name lifted from http://justintadlock.com/archives/2009/06/04/using-custom-taxonomies-to-create-a-movie-database
		$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); 
		$tax = get_taxonomy( get_query_var( 'taxonomy' ) );
		$content = "<h1 class=\"page-title\">$tax->label : <span>$term->name</span></h1>";
	}
	return $content;
}
add_filter('thematic_page_title', 'tutv_taxonomy_pagetitle');

// Remove default Thematic actions
function remove_thematic_actions() {
	remove_action('thematic_header','thematic_access',9);
}
add_action('init','remove_thematic_actions'); 

// Replace thematic access menu with a wordpress menu area named 'header'
function tutv_access() { 
	?>
    	<div id="access">
    		<div class="skip-link"><a href="#content" title="<?php _e('Skip navigation to the content', 'thematic'); ?>"><?php _e('Skip to content', 'thematic'); ?></a></div>
            <?php wp_nav_menu('menu=header&sort_column=menu_order&container_class=menu&menu_class=sf-menu') ?>
        </div><!-- #access -->
	<?php 
}
add_action('thematic_header','tutv_access',9);

// Add sub-page navigation to pages
function tutv_page_submenus( $content ) {
	global $post;
	$output = '';
	
	if( !is_page() ) 
		return $content;
		
	if( $post->post_parent ) {
		$parent = $post->post_parent;
	} else {
		$parent = $post->ID;
	}
	
	if ( empty($parent) )
		return $content; 

	$pages = get_pages( array(
		'child_of' => $parent
	) );
	if ($pages) {
	
		$pageids = array();
		
		foreach ($pages as $page) {
			$pageids[]= $page->ID;
		}
		
		$args = array(
			'title_li' => '',
			'echo' => 0,
			'include' =>  $parent . ',' . implode(",", $pageids),
			'link_before' => '&raquo;  '
		);
		$output .= '<div class="subpage-menu-container page-header-menu-container">';
		$output .= '<ul class="subpage-menu page-header-menu">';
		$output .= wp_list_pages( $args );
		$output .= '</ul>';
		$output .= '</div> <!-- end .subpage-menu-container -->';
	}

	return $output . $content;
}
//add_action( 'the_content', 'tutv_page_submenus' );

// Place 'embed' custom field values into the post body
function  tutv_add_embeds() {
	if ( !is_single() )
		return;
	
	$has_embed = false;
		
	foreach( get_post_meta( get_the_ID(), 'embed' ) as $embed ) {
		if( empty( $embed ) )
			continue;
		 
		$has_embed = true;
		
		?>
		<div class="embed">
			<?php echo wp_oembed_get($embed, 'width=600'); ?><br>
		</div>
		<?php 

	} /* foreach */		
	
	if( !$has_embed && get_post_type() ==  'episodes' ) {
		echo the_post_thumbnail( 'large', array( 'class' => 'aligncenter' ) );
	}
}
add_filter('thematic_content', 'tutv_add_embeds');

function tutv_episode_meta($content) {
	global $post;
	
	$episode = get_post_meta($post->ID, 'episode', true);
	if( !$episode )
		return $content;

	$episode_title = '<span class="meta-prep meta-prep-episode-title">';
	
	if( $post->post_type == 'episodes' && !is_numeric($episode) ) {
		$episode_title .= __(' ', 'tutv');
	} elseif( $post->post_type == 'episodes' ) {
		$episode_title .= __('Episode ', 'tutv');
	} else if( $post->post_type == 'clip' ) {
		$episode_title .= __('From episode ', 'tutv');
	} else {
		return $content;
	}
	
	$episode_title .= '</span>';
    $episode_title .= '<span class="episode-title">';
    $episode_title .= $episode;
    $episode_title .= '</span>';
    
    return $content . $episode_title;
	
}
add_filter ('thematic_post_meta_entrydate', 'tutv_episode_meta');

// Show excerpt on show taxonomy page
function tutv_conditional_excerpt( $display_type ) {
	if( is_tax( 'shows' ) ){
		$display_type = 'excerpt';
	}
	return $display_type;
}
add_filter('thematic_content', 'tutv_conditional_excerpt', 11);

// Selectively add post thumbnails
function tutv_add_post_thumbnail( $content ) {
	if( is_archive( array('episodes', 'clip') ) ) {
		?>
		<a href="<?php the_permalink(); ?>">
		<?php the_video_thumbnail( get_the_ID() ); ?>
		</a>
		<?php
	}
	echo $content;
}
add_filter('thematic_content', 'tutv_add_post_thumbnail');

// Add a span ('page-title-prefix') to the page header for easy css manipulation
// 
// Archives in thematic have a prefix such as 'Category Archives:' that is not always
// desired. This filter adds a span to these prefixes that allows us the remove them with css. 
function tutv_page_title($content) {
	$content = preg_replace('/">/', '"><span class="page-title-prefix">', $content);
	$content = preg_replace("/<span>/", "</span><span>", $content);
	return $content;
}
add_filter('thematic_page_title', 'tutv_page_title');


// Output custom fields for a given post to users with editor or administrator roles
function tutv_meta_footer( $content ) {
	$post_type = get_post_type();
	if( is_singular( array( 'clip', 'episodes' ) ) && current_user_can( 'editor' ) ) {
		
		?>
		<div class="post-metadata">
			<h2 class="section-header">Metadata (normal users will not see this box)</h2>
			<?php the_meta(); ?>
		</div>
		<?php	
	}
	echo $content;
}
//add_filter ('thematic_postfooter', 'tutv_meta_footer', 3);
	
// Output the page title of the 'posts page'
//
// The page you select to be your 'posts page' will not display a page title like 
// every other page or post as if it were still the homepage.
function tutv_posts_page_title() {
	if( is_home() ){
		
		$post_obj = get_queried_object();
		echo "<h1 class='entry-title'>{$post_obj->post_title}</h1>";
	}
}
add_filter('thematic_navigation_above', 'tutv_posts_page_title');

// For show-based content, add a header with the show title and background 
//
// string $force: Override to force display
function tutv_show_header( $force = false ) {
	global $post;
	
	if ( is_singular( array( 'episodes', 'show_notes', 'clip', 'show_page' ) ) || $force ) {		
		$term = get_the_show_term( $post->ID );
		if ( empty( $term ) || has_no_show( $term ) ) 
			return;
		?>

		<div id="show-thumbnail" class="grid_3 alpha">

			<?php // print_r($term); ?>

			<?php echo get_posts_show_thumbnail($post->ID, 'thumbnail-square'); ?>

		</div> <!-- end #show-thumbnail -->

		<div id="show-info-container" class="grid_9 omega">

			<div id="show-header" class="clearfix">

				<?php			
				if ($term->name) {
				?>
					<h1 id="show-name">
						<a href="<?php echo get_show_link($term) ?>"><?php echo $term->name; ?></a>
					</h1>
				<?php
				}

				wp_nav_menu( array(
					'fallback_cb'     => '',
					'theme_location'  => $term->slug,
					'container_class' => 'show-menu-container page-header-menu-container',
					'menu_class'      => 'show-menu page-header-menu',
					'link_before'     => '&raquo;  '
				) );

				?>

			</div> <!-- #show-header -->

			<div id="show-description">
				<p>
				<?php
				if ($term->description) {
					 echo $term->description;
				}
				?>
				</p>
			</div>
		</div> <!-- end #show-info-container -->
		<?php
	}
}
add_filter('thematic_navigation_above', 'tutv_show_header', 1);

// Changes Footer Text
function childtheme_footer($thm_footertext) {
	$thm_footertext = 'Copyright &copy; ' . date("Y") . ' TUTV - Temple University Television. All rights reserved.';
	return $thm_footertext;
}
add_filter('thematic_footertext','childtheme_footer');

/**
 * Add search box to header via thematic_header hook.
 *
 * @author Chris Montgomery <mont.chr@gmail.com>
 *
 */
function tutv_header_search() {
	echo '<div id="header-search">';
		get_search_form();
	echo '</div> <!-- end #header-search -->';
}
add_action('thematic_header', 'tutv_header_search', 8);

/**
 * Add social media icons and search box to footer via thematic_footer hook.
 *
 * @author Chris Montgomery <mont.chr@gmail.com>
 *
 */
function tutv_footer() {
	echo '<div id="footer-social-media">';
		tutv_social_media_icons();
	echo '</div> <!-- end #footer-social-media -->';
	echo '<div id="footer-search">';
		get_search_form();
	echo '</div> <!-- end #footer-search -->';
}
add_action('thematic_footer', 'tutv_footer', 8);

/**
 * Returns list of social media icons.
 * 
 * @author Chris Montgomery <mont.chr@gmail.com>
 * @since 2.0.0
 *
 * @param array $icon The icon(s) to return.
 * @param string $fb_url The TUTV Facebook page URL
 * @param string $twitter_url The TUTV Twitter profile URL
 * @param string $rss_url The templetv.net RSS feed with feed:// protocol
 *
 */
function tutv_social_media_icons(
	$icon,
	$fb_url = 'https://www.facebook.com/TempleTV',
	$twitter_url = 'https://twitter.com/templetv',
	$rss_url = 'feed://templetv.net/feed'
) {
	
	if ( empty($icon) ) { ?>
		<ul class="social-media-icons">
			<li id="fb-icon" class="social-media-icon">
				<a href="<?php echo $fb_url; ?>" title="Like us on Facebook">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/social-icons/icon-fb.png" title="Facebook" alt="Facebook" />
				</a>
			</li>
			<li id="twitter-icon" class="social-media-icon">
				<a href="<?php echo $twitter_url; ?>" title="Follow us on Twitter">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/social-icons/icon-twitter.png" title="Twitter" alt="Twitter" />
				</a>
			</li>
			<li id="rss-icon" class="social-media-icon">
				<a href="<?php echo $rss_url; ?>" title="Subscribe to our latest updates">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/social-icons/icon-rss.png" title="RSS" alt="RSS" />
				</a>
			</li>
		</ul> <!-- end .social-media-icons -->
	<?php } // endif 

	$output = '';

	if ( $icon == 'facebook' || in_array('facebook', $icon) ) {
		$output .= '<div id="fb-icon" class="social-media-icon">';
			$output .= '<a href="' . $fb_url . '" title="Like us on Facebook">';
				$output .= '<img src="' . get_stylesheet_directory_uri() . '/images/social-icons/icon-fb.png" title="Facebook" alt="Facebook" />';
			$output .= '</a>';
		$output .= '</div>';
	}

	if ( $icon == 'twitter' || in_array('twitter', $icon) ) {
		$output .= '<div id="twitter-icon" class="social-media-icon">';
			$output .= '<a href="' . $twitter_url . '" title="Follow us on Twitter">';
				$output .= '<img src="' . get_stylesheet_directory_uri() . '/images/social-icons/icon-twitter.png" title="Twitter" alt="Twitter" />';
			$output .= '</a>';
		$output .= '</div>';
	}

	if ( $icon == 'rss' || in_array('rss', $icon) ) {
		$output .= '<div id="rss-icon" class="social-media-icon">';
			$output .= '<a href="' . $rss_url . '" title="Subscribe to our latest updates">';
				$output .= '<img src="' . get_stylesheet_directory_uri() . '/images/social-icons/icon-rss.png" title="RSS" alt="RSS" />';
			$output .= '</a>';
		$output .= '</div>';
	}

	if ( $icon == 'contact' || in_array('contact', $icon) ) {
		$output .= '<div id="contact-icon" class="social-media-icon">';
			$output .= '<a href="' . home_url('/contact') . '" title="Questions? Contact us">';
				$output .= '<img src="' . get_stylesheet_directory_uri() . '/images/social-icons/icon-contact.png" title="Questions? Contact us" alt="Questions? Contact us" />';
			$output .= '</a>';
		$output .= '</div>';
	}

	echo $output;
}

/**
 * Schedule Block
 *
 * In use on Front Page and in sidebar.
 *
 * @author Chris Montgomery
 * @author Sam Marguiles
 * @since 2.0.0
 * @version 1.0.0
 *
 * @param string $classes A space-separated list of class attributes for schedule block container
 * @param string $header_title The name of the block
 */
function tutv_schedule_block($classes = 'block clearfix', $header_title = 'Airing on TUTV') { ?>
	<div id="schedule-block" class="<?php echo $classes; ?>">
		<div class="block-header clearfix">
			<h3 class="section-header"><?php echo $header_title; ?></h3>
			<p><a class='header' href="<?php echo home_url('/schedule'); ?>">view more &rarr;</a></p>
		</div>
		<div class="block-inner accordion clearfix">
			<?php
			/*
			I may be wrong,
			but the way these posts are called seems pretty cumbersome.
			It should be cleaned up.
			Besides being nearly unreadable,
			it doesn't loop in the standard way,
			but instead adds each post to the $output variable.

			Also, the posts_per_page query var
			doesn't ACTUALLY control how many are displayed -
			it only limits the MAX number QUERIED
			(but of course turning this off would make for a very slow query).
			The number displayed is regulated by
			the $num_items elseif on line 338.

			WHy search for shows starting up to three hours in the past
			and then limit the results to only shows in the next four hours?


			-montchr, 2013.04.09
			*/

			$timestamp = strtotime('2012-04-11 08:00:00');
			//echo $timestamp;

			//$active_start_time = time(); // this must be active when site is live!
			$active_start_time = $timestamp; // this will allow easy editing on staging site
			$args = array(
				'post_type'=>'events',
				'meta_key' => 'date_value',
				'orderby' => 'meta_value',
				'meta_compare' => '>=',
				//search for shows starting up to 3 hours ago
				'meta_value' => $active_start_time - 60 * 60 * 3,
				'order' => 'ASC',
				'posts_per_page' => '10',
			);
			//query_posts($args);

			$query = new WP_Query($args);

			$final_output = '';
			$num_items = 0;
			
			//global $wp_query;
			p2p_type( 'schedule_event' )->each_connected( $query, array(), 'episode' );
			
			while ( $query->have_posts() ) : $query->the_post();

				echo 'post';

				//get the episode associated with this schedule item
				$episodes = $post->connected;

				print_r($episodes);
							
					//if there are connected episodes, set the first one to display
					if( $episodes ) {
						$scheduled_page = $episodes[0] ;
					} else {
						continue;
					}

				$date_value = get_post_meta(get_the_ID(), 'date_value', true);
				
					if( $date_value ) {
						$formatted_time = "<span class='start'>";
						$formatted_time .= date('h:i A', $date_value);
						$formatted_time .= "</span>";
					} else {
						continue;
					}

					// only show uncoming shows starting in the next 4 fours
					if ( $date_value > $active_start_time + 60 * 60 * 8 ) {
						continue;
					}

				
				$terms = wp_get_object_terms($scheduled_page->ID, 'shows');
				$term = $terms[0];
				
				$output = "<div id='post-" . get_the_ID() . "' class='schedule-item post hentry show-{$term->slug}";
					if( $date_value < $active_start_time ) {
						$output .= ' active';
					}
					$output .= "'>";

					$output .= "<div class='entry-info accordion-header'>";

						$output .= "<div class='scheduled-time'>$formatted_time</div>";

						$output .= "<h4 class='entry-title'>";

							if( $term ) {
								$output .= '<span class="show-name">';
									$output .= "<a href='" . get_show_link($term) . "'>";
										$output .= get_the_show($scheduled_page->ID);
									$output .= '</a>';
								$output .= '</span>';
							}
							$output .= '<span class="episode-title">';
								$output .= "<a href='" . get_permalink($scheduled_page->ID) . "'>";
									$output .= get_the_title($scheduled_page->ID);
								$output .= '</a>';
							$output .= '</span>';
						
						$output .= '</h4>';

						$post = get_post($scheduled_page->ID); 
						setup_postdata($post);
						
					$output .= '</div><!-- .entry-info -->';

					$output .= '<div class="entry-content accordion-content">';

						$output .= '<a href="' . get_permalink($scheduled_page->ID) . '">';
							$output .= get_video_thumbnail($scheduled_page->ID);
						$output .= '</a>';
						$output .= get_the_excerpt();
					
					$output .= '</div><!-- .entry-content -->';
				$output .= '</div><!-- .post -->';
				
				// if the selected showtime is in the past, replace all previously queued showtimes
				// with the most recent showtime
				if ( $date_value < $active_start_time ) {
					$final_output = $output;
					$num_items = 1;
				} else if ($num_items < 5 ) { //limit the number of items to 5
					$final_output .= $output;
					$num_items++;
				} else {
					break;
				}
			
			endwhile;
			
			if ( $final_output ) {
				echo $final_output;
			} else { ?>
				<p class="notice">Sorry, there are no showtimes listed for the next few hours.</p>
			<?php } ?>
		</div> <!-- end .block-inner -->
	</div><!-- end #schedule-block -->
<?php
} // don't remove this bracket!