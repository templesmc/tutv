<?php 
/*
Template Name: Front Page
*/
?>
<?php
	add_action('wp_print_scripts', 'tutv_home_scripts');
	 
	function tutv_home_scripts() {
	
		wp_enqueue_script('flexslider', get_bloginfo('stylesheet_directory') . '/js/jquery.flexslider-min.js', array('jquery'), '1.0', false);
		wp_enqueue_script('home', get_bloginfo('stylesheet_directory') . '/js/home.js', array('jquery', 'flexslider'), '2.0', false);

	}

?>

<?php get_header() ?>

		<div id="content" class="clearfix">

			<div id="masthead" class="grid_12">

				<div id="tutv-network-info">

					<p class="cable"><span class="cable-provider">Comcast</span> <span class="cable-channel-number">50</span></p>
					<p class="cable"><span class="cable-provider">Verizon</span> <span class="cable-channel-number">45</span></p>
					<p class="location">in <em class="city">Philadelphia</em></p>

				</div> <!-- end #tutv-network-info -->

				<div id="masthead-social-media">

					<p>Follow Us</p>
					
					<?php tutv_social_media_icons(); ?>

				</div> <!-- end #masthead-social-media -->
			
			</div> <!-- end #masthead -->

			<div id="front-featured" class="grid_12 flexslider-container clearfix">

				<div class="flexslider">
				
					<ul class="slides">

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

								<?php
								/*
								Some problematic names of variables and ids to follow, as addressed in _home.scss .
								These naming problems extend into the database and likely can't be changed without significant investment.

									Postponed. -montchr, 2013.03.14
								*/
								?>

								<h2><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_show(); the_title(); ?></a></h2>

								<?php 
								
								global $more;    // Declare global $more (before the loop).
								$more = 0;       // Set (inside the loop) to display content above the more tag.
								$headline = get_post_meta(get_the_ID(), 'headline', TRUE);
								$subheadline = get_post_meta(get_the_ID(), 'subheadline', TRUE);
								$subheadline2 = get_post_meta(get_the_ID(), 'subheadline2', TRUE);
								$subheadline3 = get_post_meta(get_the_ID(), 'subheadline3', TRUE);

								if($headline) {
									echo "<h3 class='headline'>" . $headline . "</h3>"; 
								}

								the_excerpt();

								if($subheadline) {
									echo "<div id='subheadline'>" . $subheadline . "</div>"; 
								}
								if($subheadline2) {
									echo "<div id='subheadline2'>" . $subheadline2 . "</div>"; 
								}
								/*
								// Nah, just... don't. There's already too much going on.
								if($subheadline3) {
									echo "<div id='subheadline3'>" . $subheadline3 . "</div>"; 
								}
								*/
								
								?>

								</div>
						</li>
						
					<?php 
					
					endwhile; // end the featured post loop
					
					?>
					</ul> <!-- end ul.slides -->

				</div> <!-- end .flexslider -->
			
			</div> <!-- #front-featured -->
			
			<!-- Recent Blog Posts Block -->
			<div id="recent-blog-posts-block" class="front-block block grid_6">

				<div class="block-header">
					<h3 class="section-header">Recent Blog Posts</h3>
					<p><a class='header' href="<?php echo home_url('/blog'); ?>">view more &rarr;</a></p>
				</div>

				<div class="block-inner">

					<?php

					// Sets args for the two recent blog posts in the query
					$args = array(
						'post_type'        => 'post', // posts of post type post...
						'category_name'    => 'blog', // ...in blog category...
						'tax_query'        => array( // ...is NOT featured...
							array( // this is not a clear way of querying. can it be improved? query above uses deprecated arg ({tax})
								'taxonomy'         => 'featured',
								'field'            => 'slug',
								'terms'            => 'on',
								'operator'         => 'NOT IN'
							)
						),
						'posts_per_page'   => 3 // ...and get only two.
					);

					// This is the query
					$recent_blog_posts = new WP_Query($args);

					// The Loop
					if ($recent_blog_posts->have_posts()) :
						while ($recent_blog_posts->have_posts()) :
							$recent_blog_posts->the_post();
					?>

							<div id="post-<?php the_ID(); ?>" <?php post_class('article clearfix'); ?> role="article">

								<?php setup_postdata($recent_blog_posts); ?>
								
								<div class="featured-image-container">
									<?php the_post_thumbnail('thumb'); ?>
								</div> <!-- end .featured-image-container -->

								<div class="entry-header">
									<h4 class="h3"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
								</div> <!-- end .entry-header -->

								<div class="entry-content">
									<?php
									/* If the Advanced Excerpt plugin was updated,
									maybe it would be possible to control its output
									without having to completely ignore the custom excerpt.
									But for now, I will make due with adding a read more link
									right here in the template. See below.

									Just keep in mind that this plugin,
									as of this writing (2013.04.10),
									the Advanced Excerpt plugin
									hasn't been updated since 2011 (v4.1.1).
									Be careful of future core WordPress updates. */
									$args = array(
										'length'      => 20,
										'use_words'   => 1,
										'finish_word' => 1,
										'read_more'   => 'Read More &rarr;',
										'add_link'    => 1
									);

									//the_excerpt();
									the_advanced_excerpt($args);
									?>
									<span class="read-more"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">Read&nbsp;More&nbsp;&rarr;</a></span>
								</div> <!-- end .entry-content -->

							</div><!-- .post -->

					<?php
						// Close the loop
						endwhile; 
					endif;

					wp_reset_postdata();

					?>


				</div> <!-- end .block-inner -->

			</div> <!-- end #recent-blog-posts-block -->
			


			<!-- Schedule and Featured Buttons Block Container -->
			<div id="schedule-meta-block-container" class="grid_6">



				<!-- Schedule Block -->
				<div id="schedule-block" class="front-block block grid_6 alpha omega clearfix">

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

						//$active_start_time = time(); // this must be active when site is live!
						$active_start_time = $timestamp; // this will allow easy editing on staging site
						$args = array(
							'post_type'=>'events',
							'meta_key' => 'date_value',
							'orderby' => 'meta_value',
							'meta_compare' => '>=',
							//search for shows starting up to 3 hours ago
							'meta_value' => $active_start_time - 60 * 60 * 3,
							//'meta_value' => $timestamp,
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

				<?php //tutv_schedule_block('front-block block grid_6 alpha omega clearfix'); ?>

				<!-- Featured Buttons Section -->
				<div id="featured-buttons-section" class="grid_6 alpha omega">

					<!-- Watch Live Section -->
					<div id="watch-live-button" class="front-featured-button grid_6 alpha omega">
						<a href="/watch-live" title="Watch TUTV Live">
							<h3 id="watch-live-button-title" class="front-featured-button-title"><em>watch</em> TUTV <em>live</em></h3>
						</a><!-- end #watch-live-button -->
					</div>

					<div id="about-tutv-button" class="front-featured-button grid_3 alpha">
						<a href="/about" title="About TUTV">
							<h3 id="about-tutv-button-title" class="front-featured-button-title"><em>about</em> TUTV</h3>
						</a><!-- end #about-tutv-button -->
					</div>

					<div id="submit-a-video-button" class="front-featured-button grid_3 omega">
						<a href="/submissions" title="Submit A Video">
							<h3 id="submit-a-video-button-title" class="front-featured-button-title"><em>submit</em> A VIDEO</h3>
						</a><!-- end #submit-a-video-button -->
					</div>

					<div id="read-the-blog-button" class="front-featured-button grid_6 alpha omega">
						<a href="/blog" title="Read The Blog">
							<h3 id="read-the-blog-button-title" class="front-featured-button-title"><em>read</em> the blog</h3>
						</a><!-- end #read-the-blog-button -->
					</div>

				</div><!-- end #featured-buttons-section -->

			</div><!-- end #schedule-meta-block-container -->

			<!-- Featured Video Section -->
			<div class="featured-video-section grid_12 clearfix">

				<h3 class="section-header">Featured Videos</h3>

				<?php
				// set class to alpha/omega depending on position in 4 column layout
				// http://wordpress.org/support/topic/adding-different-styling-every-3rd-post
				$style_classes = array('alpha', '', '', 'omega');
				$styles_count = count($style_classes);
				$style_index = 0;

				$featuredVideos = new WP_Query();
				$featuredVideos->query('post_type=any&featured-video=on&posts_per_page=4');
				?>

				<?php while ( $featuredVideos->have_posts() ) : $featuredVideos->the_post();

					// this is the second part of the operation that determines first or last class based on column divisions. see above.
					$k = $style_classes[$style_index++ % $styles_count]; ?>

					<div class="featured-video block grid_3 <?php echo $k; ?>">

						<a href="<?php the_permalink(); ?>" rel="bookmark">

							<div class="thumbnail"><?php the_post_thumbnail('thumb-small'); ?></div>

							<?php
							// if the show is defined, echo it with a colon suffix
							if ( function_exists('the_show')) { echo the_show( '<span class="featured-video-show">', '</span>: ') . ''; }
							?>
							<span class="featured-video-episode-title"><?php the_title(); ?></span>

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
			</div><!-- end #featured-video-section -->

			<?php 
			// calling the widget area 'index-bottom'
			get_sidebar('index-bottom');
			?>

		</div><!-- #content -->

<?php //thematic_sidebar(); ?>

<?php get_footer(); ?>
