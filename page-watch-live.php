<?php


	function tutv_watch_live_scripts() {
		wp_enqueue_script('watch-live', get_stylesheet_directory_uri() . '/js/watch-live.js', false);
	}

	add_action('wp_enqueue_scripts', 'tutv_watch_live_scripts');

	// calling the header.php
	get_header();

?>

		<div id="content" class="clearfix">

			<?php
		
			// calling the widget area 'page-top'
			get_sidebar('page-top');

			the_post();
		
			?>

			<div id="page-header" class="grid_12 block clearfix">
		
				<h1 class="page-title entry-title"><?php the_title(); ?></h1>

				<?php

				echo tutv_page_submenus();

				?>

			</div> <!-- end #page-header -->

			<div class="entry-main block grid_8 clearfix">
				
				<div id="post-<?php the_ID(); ?>" class="<?php thematic_post_class() ?>">
					
						<div class="entry-content clearfix">

							<?php
							
							the_content();
							
							wp_link_pages("\t\t\t\t\t<div class='page-link'>".__('Pages: ', 'thematic'), "</div>\n", 'number');
							
							edit_post_link(__('Edit', 'thematic'),'<span class="edit-link">','</span>') ?>

						</div>

				</div><!-- .post -->

				<?php
				
				if ( get_post_custom_values('comments') ) 
					thematic_comments_template(); // Add a key/value of "comments" to enable comments on pages!
				
				// calling the widget area 'page-bottom'
				get_sidebar('page-bottom');
				
				?>

			</div> <!-- end .entry-main -->

			<div id="sidebar" class="sidebar grid_4 clearfix" role="complementary">

				<?php tutv_sidebar_connect_block(); ?>

				<?php
				// this doesn't belong here, but i was testing here anyway. will be moved later.
				// tutv_schedule_block('front-block block grid_6 alpha omega clearfix');

				?>

				<!-- Schedule Block -->
				<div id="schedule-block" class="front-block block clearfix">

					<div class="block-header clearfix">
						<h3 class="section-header">Airing on TUTV</h3>
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

						$active_start_time = time(); // this must be active when site is live!
						//$active_start_time = $timestamp; // this will allow easy editing on staging site
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
						p2p_type( 'schedule_event' )->each_connected( $query );
						
						while ( $query->have_posts() ) : $query->the_post();

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

									$output .= '<div class="thumbnail">';
										$output .= '<a href="' . get_permalink($scheduled_page->ID) . '">';
											$output .= get_video_thumbnail($scheduled_page->ID);
										$output .= '</a>';
									$output .= '</div>';
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

			</div> <!-- end #sidebar -->

		</div><!-- #content -->

<?php 
	get_footer();
?>