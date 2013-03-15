<?php
	add_action('wp_print_scripts', 'childtheme_shows_scripts');
	 
	function childtheme_shows_scripts() {
	
		wp_enqueue_script('slideshow', get_bloginfo('stylesheet_directory') . '/js/jcarousellite.min.js', array('jquery'), '1.0', false);
		wp_enqueue_script('home', get_bloginfo('stylesheet_directory') . '/js/home-ck.js', array('slideshow', 'jquery'), '1.0', false);

	}

?>

<?php get_header() ?>

	<div id="container">

		<div id="content">

				<!-- Schedule Block -->
				<div id="schedule-block" class="grid_6 alpha omega">

					<h3 class="section-header"><a class='header' href="/schedule">Programing Schedule:</a></h3>

					<?php
					$active_start_time = time();
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
					query_posts($args);
					$final_output = '';
					$num_items = 0;
					
					global $wp_query;
					p2p_type( 'schedule_event' )->each_connected( $wp_query );
					
					while ( have_posts() ) : the_post();

						//get the episode associated with this schedule item
						$episodes = $post->connected;
									
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
						
						$output = "<div id='post-" . get_the_ID() . "' class='hentry show-{$term->slug}";
							if( $date_value < $active_start_time ) {
								$output .= ' active';
							}
							$output .= "'>";

							$output .= "<div class='scheduled-time'>$formatted_time</div>";

							$output .= "<h4 class='entry-title'>";

								if( $term ) {
									$output .= "<a href='" . get_show_link($term) . "'>";
										$output .= get_the_show($scheduled_page->ID);
									$output .= '</a>';
								}
								$output .= "<a href='" . get_permalink($scheduled_page->ID) . "'>";
									$output .= get_the_title($scheduled_page->ID);
								$output .= '</a>';
							
							$output .= '</h4>';

							$post = get_post($scheduled_page->ID); 
							setup_postdata($post);
							
							$output .= '<div class="entry-content">';

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

				</div><!-- end #schedule-block -->

		</div><!-- #content -->
	</div><!-- #container -->

<?php thematic_sidebar(); ?>
<?php get_footer(); ?>
