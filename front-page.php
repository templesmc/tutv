<?php 
/*
Template Name: Front Page
*/
?>
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
		
		<?php thematic_navigation_above(); ?>
		<?php get_sidebar('index-top') ?>

			<div id="masthead" class="grid_12 alpha omega">
				<!-- stuff goes here -->
			</div>

			<div id="front-featured" class="grid_12 alpha omega">
				<ul>
					<?php

					query_posts( array( 
						'featured' => 'on',
						'post_type' => 'any',
						'post_status' => 'publish',
						'posts_per_page' => 6,
						'caller_get_posts'=> 1 )
					);
					
					$i = 0;

					while (have_posts()) : the_post();
					
					?>

					<li id="item-<?php $i++; echo $i; ?>" class="item<?php if( !has_post_thumbnail() ) echo ' no-thumbnail'; ?>">

						<a name="item-<?php echo $i; ?>"></a>  

						<?php

						$slides[] = $post->ID;
						
						the_post_thumbnail('banner');
						
						?>

						<div class="description">

							<!--
							Some problematic names of variables and ids to follow, as addressed in _home.scss .
							These naming problems extend into the database and likely can't be changed without significant investment.

								Postponed. -montchr, 2013.03.14
							-->
							<h2><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_show(); the_title(); ?></a></h2>

							<?php 
							
							global $more;    // Declare global $more (before the loop).
							$more = 0;       // Set (inside the loop) to display content above the more tag.
							$headline = get_post_meta(get_the_ID(), 'headline', TRUE);
							$subheadline = get_post_meta(get_the_ID(), 'subheadline', TRUE);
							$subheadline2 = get_post_meta(get_the_ID(), 'subheadline2', TRUE);
							$subheadline3 = get_post_meta(get_the_ID(), 'subheadline3', TRUE);

							if($headline) {
								echo "<h3 id='headline'>" . $headline . "</h3>"; 
							}

							the_excerpt();

							if($subheadline) {
								echo "<div id='subheadline'>" . $subheadline . "</div>"; 
							}
							if($subheadline2) {
								echo "<div id='subheadline2'>" . $subheadline2 . "</div>"; 
							}
							if($subheadline3) {
								echo "<div id='subheadline3'>" . $subheadline3 . "</div>"; 
							}
							
							?>

							</div>
					</li>
					
				<?php 
				
				endwhile; // end the featured post loop
				
				?>
				</ul>

				<div class="next"> </div>
			
			</div> <!-- #front-featured -->
			
			<!-- Recent Blog Posts Block -->
			<div id="recent-blog-posts-block" class="grid_6 alpha">

				<!-- stuff goes here! -->

			</div> <!-- end #recent-blog-posts-block -->
			
			<!-- Schedule and Site Meta Block -->
			<div id="schedule-meta-block" class="grid_6 omega">

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
						
						$output = "<div id='post-" . get_the_ID() . "' class='hentry show-{$term->slug} grid_6 alpha omega";
							if( $date_value < $active_start_time ) {
								$output .= ' active';
							}
							$output .= "'>";

							$output .= "<div class='entry-info grid_4 omega'>";

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
								
							$output .= '</div><!-- .entry-info -->';

							$output .= '<div class="entry-content grid_2 alpha omega">';

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

				<!-- Site Meta Block -->
				<div id="featured-buttons-block" class="grid_6 alpha omega">

					<!-- Watch Live Section -->	
					<a href="/watch-live" id="watch-live-button" class="front-featured-button grid_6 alpha omega" title="Watch TUTV Live">
						<h3 id="watch-live-button-title" class="front-featured-button-title"><em>watch</em> TUTV <em>live</em></h3>
					</a><!-- end #watch-live-button -->

					<a href="/about" id="about-tutv-button" class="front-featured-button grid_3 alpha" title="About TUTV">
						<h3 id="about-tutv-button-title" class="front-featured-button-title"><em>about</em> TUTV</h3>
					</a><!-- end #about-tutv-button -->

					<a href="/submissions" id="submit-a-video-button" class="front-featured-button grid_3 omega" title="Submit A Video">
						<h3 id="submit-a-video-button-title" class="front-featured-button-title"><em>submit</em> A VIDEO</h3>
					</a><!-- end #submit-a-video-button -->

					<a href="/blog" id="read-the-blog-button" class="front-featured-button grid_6 alpha omega" title="Read The Blog">
						<h3 id="read-the-blog-button-title" class="front-featured-button-title"><em>read</em> the blog</h3>
					</a><!-- end #read-the-blog-button -->

				</div><!-- end #featured-buttons-block -->

			</div><!-- end #schedule-meta-block -->

			<!-- Featured Video Section -->
			<div id="featured-video-block" class="grid_12 alpha omega">

				<h3 class="section-header">Featured Videos:</h3>

				<?php
				// set class to alpha/omega depending on position in 4 column layout
				// http://wordpress.org/support/topic/adding-different-styling-every-3rd-post
				$style_classes = array('alpha', '', '', 'omega');
				$styles_count = count($style_classes);
				$style_index = 0;

				$featuredVideos = new WP_Query();
				$featuredVideos->query('post_type=any&featured-video=on&posts_per_page=5');
				?>

				<?php while ( $featuredVideos->have_posts() ) : $featuredVideos->the_post();

					// this is the second part of the operation that determines first or last class based on column divisions. see above.
					$k = $style_classes[$style_index++ % $styles_count]; ?>

					<div class="featured-video grid_3 <?php echo $k; ?>">

						<a href="<?php the_permalink(); ?>" rel="bookmark">

							<div class="featured-video-thumbnail"><?php the_post_thumbnail('thumbnail'); ?></div>
							<div class="featured-video-show"><?php the_show(); ?></div>
							<div class="featured-video-episode-title"><?php the_title(); ?></div>

							<?php
							/*
							//We're not using the TRT. But it might be useful for future designs. Keeping it for now. -montchr, 2013.03.14
							$the_trt = get_post_meta(get_the_ID(), 'TRT', TRUE);
							$post_type = get_post_type_object(get_post_type());
							if($the_trt) {
								echo "<div class='featured-video-trt'>" . $post_type->labels->singular_name . " (" . $the_trt . ")</div>";
							}
							*/ ?>

						</a>
					</div><!-- end .featured-video -->
				<?php
				endwhile;
				wp_reset_query();
				?>
			</div><!-- end #featured-video-block -->

			<?php 
			// calling the widget area 'index-bottom'
			get_sidebar('index-bottom');
			?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php thematic_sidebar(); ?>
<?php get_footer(); ?>
