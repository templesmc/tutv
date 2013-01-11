<?php 
/*
Template Name: Schedule Page
*/
?>
<?php

	// set timezone to wordpress timezone
	date_default_timezone_set( get_option('timezone_string') );
			
	//set to today at midnight to aid in displaying whether a date is past or future
	$today_start_time = mktime( 0, 0, 0, date('n'), date('j'), date('Y') );
	
	//set today's date
	$today = date('Y-m-j');
	
	//set the schedule day to display
	if( !isset($_GET['date']) || 'today' == $_GET['date'] || 0 === $_GET['date'] ) {
		$active_date = $today;
		$query_date = 'today';
	} else {
		$query_date = esc_html( $_GET['date'] );
		$active_date = $query_date;
	}
	
	//set the start time of the schedule's display (8:00AM)
	$active_start_time = mktime( 8, 0, 0, (int) substr($active_date, 5, 7), (int) substr($active_date, 8, 10), (int) substr($active_date, 0, 4) );
	
	$args = array(
		'post_type'=>'events',
		'orderby' => 'meta_value',
		'meta_key' => 'date_value',
		'meta_query' => array(
			array(
				'key' => 'date_value',
				'value' => array($active_start_time, $active_start_time + 60 * 60 * 16),
				'type' => 'numeric',
					'compare' => 'BETWEEN'
			)
		),
		'order' => 'ASC',
		'nopaging' => true			
	);
	
	//filter by a show slug if a show query is present
	if( isset( $_GET['show'] ) ) {
		$show = get_term_by('slug', $_GET['show'], 'shows');
		$args['shows'] = $_GET['show'];
		$filter_by_show = true;
	} else {
		$filter_by_show = false;
	}
	
	query_posts($args);
	
    // calling the header.php
    get_header();

    // action hook for placing content above #container
    thematic_abovecontainer();
	
	// output an odd or even class name for a given int
	function class_odd_or_even( $num ) {
		echo ($num%2) ? ' item-odd' : ' item-even';
	}

?>

	<div id="container">
		<div id="content">
			
			<div id="schedule">
            <?php 
			
            // displays the page title
            ?>
            <h1 class="entry-title">Schedule</h1>
            
            <?php
            if( $filter_by_show ) {
            	?>
            	<p class="schedule-filter">Showing upcoming showtimes for 
	            <select onchange="javascript:location.href = this.options[this.selectedIndex].value;">
	            	<option value="" disabled>Select a show</option>
		            <?php 
		 
		            $tutv_shows = get_terms('shows');
		            foreach($tutv_shows as $tutv_show) {
		            	?>
		            	<option value="?show=<?php echo $tutv_show->slug; ?>" <?php if($tutv_show->slug == $_GET['show']) echo 'selected'; ?> >
		            	<?php echo $tutv_show->name; ?>
		            	</option>
		            	<?php
		            }
		            
					 ?>
				
	            </select>
				. View all showtimes for <a href="?date=today">today</a> instead.</p>
            	<?php
            } else {
           		?>
	            <ul id="schedule_nav">
	            	<?php
	            	// if the selected date is in the past add an active tab to display it
	            	if( $active_start_time < $today_start_time ) {
	            	?>
	            		<li class="active"><a href="?date=<?php echo $query_date; ?>">Past Day</a></li>
	            	<?php
	            	}
	            	?>
	            
	            	<li class="<?php if($today == $active_date) echo 'active'; ?>"><a href="?date=today">Today</a></li>
					<?php
					$i = 1;
					$date = time();
					while($i < 7) {
						// add one day to the current time
						$date += 60 * 60 * 24;
						$i++;
						
						$schedule_day = date('Y-m-j', $date);
						?>
						
						<li class="<?php if($active_date == $schedule_day) echo 'active'; ?>">
							<a href="?date=<?php echo $schedule_day; ?>"><?php echo date('l', $date); ?></a>
						</li>
						
						<?php
					}
	            	// if the selected date is over a week in the future add an active tab to display it
	            	if( $active_start_time > $today_start_time + (7 * 60 * 60 * 24) ) {
	            	?>
	            		<li class="active"><a href="?date=<?php echo $query_date; ?>">Future Day</a></li>
	            	<?php
	            	}
	            	?>
	            
	            </ul><!-- #schedule_nav -->
            <?php
            }// endif $filter_by_show
            
			// unset active day
            $active_date = -1;
            $today = getdate();
            
            
            global $wp_query;
            p2p_type( 'schedule_event' )->each_connected( $wp_query );
            
            if( !have_posts() ) {
				?>
				<p class="notice">Sorry, there are no showtimes listed for <strong>
				<?php
				if( $filter_by_show ) {
					echo ($show->name) ? $show->name : esc_html( $_GET['show'] );
				} else {
					echo date('l F j, Y', $active_start_time );
				}
				?>
				</strong>.</p>
				<?php
			}
            
            while ( have_posts() ) : the_post();
			
				//get the episode associated with this schedule item
				$episodes = $post->connected;
  				
  				//if there are connected episodes, set the first one to display
				if( $episodes ) {
					$scheduled_page = $episodes[0] ;
				} else {
					continue;
				}				
				
				$date_value = get_post_meta(get_the_ID(), 'date_value', TRUE);
				$formatted_time = '';
				
				if( $date_value ) {
					$formatted_time .= "<span class='start'>";
					$formatted_time .= date('h:i A', $date_value);
					$formatted_time .= "</span>";
					$current_day = date('w', $date_value);
					$current_day_name = date('l', $date_value);
					$current_date_name = date('F j, Y', $date_value);
				} else {
					continue;
				}
			
				// only show uncoming shows from the selected day (in the next 16 hours)
				if ( !$filter_by_show && $date_value > $active_start_time + 60 * 60 * 16 - 1 ) {
					continue;
				}
				// only show schedule results filtered by show for the next week
				if( $filter_by_show && $date_value > $active_start_time + (7 * 60 * 60 * 24) ) {
					continue;
				}
			
				// to facilitate display of multiple days displayed on one page
				if( $current_day != $active_date ) {			
					$active_date = $current_day;
					
					if( $today['yday'] == date('z', $date_value) ) {
						$schedule_class = 'today';
					} else {
						$schedule_class = '';
					}
					?>
									
					<h2 class="<?php echo $schedule_class; ?> schedule-date">
						<span class="schedule-day-name"><?php echo $current_day_name; ?></span>, <span class="schedule-date-name"><?php echo $current_date_name; ?></span>
					</h2>
					
					<?php
					//reset index of scheduled shows for new day
					$schedule_index = 0;
				}
				
				$terms = wp_get_object_terms($post->ID, 'shows');
				$term = $terms[0];
				?>
				<div id="post-<?php the_ID() ?>" class="<?php thematic_post_class(); class_odd_or_even( $schedule_index++ ); echo ' show-' . $term->slug; ?>">
					<div class="scheduled-time"><?php echo $formatted_time; ?></div>

					<h3 class="entry-title">
						<?php
						if( $term ) {
							?>
							<a href="<?php echo get_show_link($term); ?>">
								<?php echo get_the_show($scheduled_page->ID); ?>
							</a>
							<?php 
						} 
						?>
						<a href="<?php echo get_permalink($scheduled_page->ID);?>">
							<?php echo get_the_title($scheduled_page->ID); ?>
						</a>
					</h3>
				
					<?php 
					$post = get_post($scheduled_page->ID); 
					setup_postdata($post);
					$excerpt = get_the_excerpt();
					?>
					<div class="entry-content">
						<a href="<?php echo get_permalink($scheduled_page->ID); ?>">
							<?php the_video_thumbnail($scheduled_page->ID); ?>
						</a>
						<?php if( !empty($excerpt) ) echo $excerpt; ?>
					</div>
				</div><!-- .post -->
				<?php endwhile; ?>
			</div><!-- #schedule -->
		</div><!-- #content .hfeed -->
	</div><!-- #container -->

<?php 

    // action hook for placing content below #container
    thematic_belowcontainer();

    // calling the standard sidebar 
    thematic_sidebar();
    
    // calling footer.php
    get_footer();

?>