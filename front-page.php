<?php 
/*
Template Name: Front Page
*/
?>
<?php
	add_action('wp_print_scripts', 'childtheme_shows_scripts');
	 
	function childtheme_shows_scripts() {
	
	 	wp_enqueue_script('slideshow', get_bloginfo('stylesheet_directory') . '/js/jcarousellite.min.js', array('jquery'), '1.0', false);
	 	wp_enqueue_script('home', get_bloginfo('stylesheet_directory') . '/js/home.js', array('slideshow', 'jquery'), '1.0', false);

	}

?>

<?php get_header() ?>
	<div id="container">
		<div id="content">
		
		<?php thematic_navigation_above(); ?>
		<?php get_sidebar('index-top') ?>
		
		<?php
		
			query_posts( array( 
				'featured' => 'on',
	  			'post_type' => 'any',
	  			'post_status' => 'publish',
	 			'posts_per_page' => 6,
	  			'caller_get_posts'=> 1 )
  			);

		?>

		<div id="front-featured">
		<ul>
		<?php
		
		$i = 0;

		while (have_posts()) : the_post();
		
		?>
			<li id="item-<?php $i++; echo $i; ?>" class="item <?php if( !has_post_thumbnail() ) echo "no-thumbnail"; ?>">

			<a name="item-<?php echo $i; ?>"></a>  
			  <?php
			  $slides[] = $post->ID;
			  
			  the_post_thumbnail('banner');
			  
			 		 
			  ?>
				<div class="description">
			  	<h2>
			  	<a href="<?php the_permalink() ?>" rel="bookmark">
				<?php the_show(); the_title(); ?>
				</a>
				</h2>
				  <?php 
				  
				  global $more;    // Declare global $more (before the loop).
				  $more = 0;       // Set (inside the loop) to display content above the more tag.
				  $headline = get_post_meta(get_the_ID(), 'headline', TRUE);
				  $subheadline = get_post_meta(get_the_ID(), 'subheadline', TRUE);
				  $subheadline2 = get_post_meta(get_the_ID(), 'subheadline2', TRUE);
				  $subheadline3 = get_post_meta(get_the_ID(), 'subheadline3', TRUE);
				  // New section added 06-13-2011 Pwinnick
				  if($headline) {
				  	echo "<h3 id='headline'>" . $headline . "</h3>"; 
				  }
				  the_excerpt();
				  // Switch from content to excerpt 09-22-2011 PWinnick
				  // the_content('<br> Learn More &raquo;');
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
		
		endwhile; 
		
		?>
		</ul>
		<div class="next"> </div>
		
		</div> <!-- #front-featured -->

		<?php if($i > 1): //only show navigation for multiple posts ?>
		<div id="front-navigation">
			<?php 
			$j = 0;
			foreach($slides as $slide) {
			?>
				<div class="item-<?php $j++; echo $j; ?> nav">
					<a href="#item-<?php echo $j; ?>">
					<?php
					$title = get_the_show($slide) . get_the_title($slide);
					if( has_post_thumbnail( $slide ) ) {
						echo get_the_post_thumbnail( $slide, 'banner-thumbnail', array( 'title' => $title ) );
					} else {
						echo "<span class='no-thumbnail'>$title</span>";
					}
					?>
					</a>
				</div>
			<?php
			} //end foreach
			?>
		</div>
		<?php endif; ?>
		
		<!-- Featured Video Section -->
		<div id="featured-video-block">		
		<h3 class="section-header">Featured Videos:</h3>
        <?php
             $featuredVideos = new WP_Query();
             $featuredVideos->query('post_type=any&featured-video=on&posts_per_page=5'); 
          ?>
          <?php while ($featuredVideos->have_posts()) : $featuredVideos->the_post(); ?>
            <div class="featured-videos">
            <a href="<?php the_permalink(); ?>" rel="bookmark"><div class="featured-video-thumbnail"><?php the_post_thumbnail('thumbnail'); ?></div>
            <div class="featured-video-show"><?php the_show(); ?></div>
            <div class="featured-video-episode-title"><?php the_title(); ?></div>
            <?php
            	$the_trt = get_post_meta(get_the_ID(), 'TRT', TRUE);
            	$post_type = get_post_type_object(get_post_type());
            	if($the_trt) {
              		echo "<div class='featured-video-trt'>" . $post_type->labels->singular_name . " (" . $the_trt . ")</div>";
              	}
            ?>
              
            </div></a> 
         <?php endwhile; ?>
         <?php wp_reset_query(); ?>
		</div>
		
		<div id="last-front-block">
	 	  <!-- Watch Live Section -->	
		  <div id="front-block-3" class="front-block">  
		    <div class="front-block news-block"> 

				<?php the_post() ?>
				<?php the_content() ?>
				<?php edit_post_link(__('Edit', 'thematic'),'<span class="edit-link">','</span>') ?>
				
              </div><!-- .front-block .block-->
		    </div> <!--.front-block.news-block-->
          
		  <div id="schedule-block" class="front-block">
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
			} else {
				?>
				<p class="notice">Sorry, there are no showtimes listed for the next few hours.</p>
				<?
			}
			
			?>
			</div><!-- #front-block-3 .front-block .block-->
	
	

			<?php 
	        // calling the widget area 'index-bottom'
	        get_sidebar('index-bottom');
	
	        // create the navigation below the content
	        //thematic_navigation_below(); 
	        ?>
		  </div><!-- #content -->
	    </div><!-- #container -->
	  </div>

<?php thematic_sidebar(); ?>
<?php get_footer(); ?>
