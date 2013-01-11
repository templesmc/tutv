<?php
	
	//add_action('wp_print_scripts', 'childtheme_shows_scripts');
	 
	function childtheme_shows_scripts() {
	
	 	wp_enqueue_script('bbq', get_bloginfo('stylesheet_directory') . '/js/jquery.ba-bbq.min.js', array('jquery'), '1.2.1', false);
	 	wp_enqueue_script('show-scripts.js', get_bloginfo('stylesheet_directory') . '/js/show-scripts.js', array('jquery', 'bbq'), '1.0', false);
	 
	}
	
	$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); 
	
    // calling the header.php
    get_header();

    // action hook for placing content above #container
    thematic_abovecontainer();

?>

	<div id="container">
		<div id="content" class="taxonomy-shows">

            <?php 
			
			tutv_show_header( true );
			
            global $query_string;            
            
            $episodes_paged = ( isset( $_GET['episodes_page'] ) ) ? $_GET['episodes_page'] : 1;

            query_posts($query_string . '&post_type=episodes&posts_per_page=8&paged=' . $episodes_paged);
			
			
            // create the navigation above the content
            thematic_navigation_above();
            
		if( have_posts() ) {
			?>		
			<div id="show-episodes" class="show-block"><a name="episodes"></a>
			<?php 
			
            // action hook creating the archive loop
            thematic_archiveloop();
            
            the_shows_nav();
            
			?>
			</div> <!-- end .episode-list -->
			<?php
		}
			
			wp_reset_query();
			            
            $show_notes_paged = ( isset( $_GET['show_notes_page'] ) ) ? $_GET['show_notes_page'] : 1;
            
            query_posts($query_string . '&post_type=show_notes&posts_per_page=3&paged=' . $show_notes_paged);
			
			if( have_posts()) {
			?>
			<div id="show-notes" class="show-block">
				<a name="show-notes"></a>
				<h2 class="section-header">
				<?php the_show(); ?> Blog 
				</h2>
			
			<?php
			
            // action hook creating the archive loop
            thematic_archiveloop();
			
			the_shows_nav();

			?>
			</div> <!-- end .show-notes -->
			<?php
			}
			
			/* <div class="upcoming-showtimes">
			<h2 class="page-title">Watch <?php echo $term->name; ?> on TV</h2>
			<p>
			<a href="http://tv.sites.templetv.net/schedule/?show=<?php echo $term->slug; ?>">Find showtimes now.</a>
			</p>
			</div> */
			
			wp_reset_query();

			$clips_paged = ( isset( $_GET['clip_page'] ) ) ? $_GET['clip_page'] : 1;
			
            query_posts($query_string . '&post_type=clip&posts_per_page=3&paged=' . $clips_paged);
			
			if( have_posts()) {
			?>
			<div id="show-clips" class="show-block"><a name="clip"></a>
			<h2 class="section-header"><?php the_show(); ?> Clips</h2>
			
			<?php
			
            // action hook creating the archive loop
            thematic_archiveloop();
			
			the_shows_nav();

			?>
			</div> <!-- end .show-notes -->
			<?php
			}			
			
			wp_reset_query();

            // create the navigation below the content
            //thematic_navigation_below();

            ?>

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