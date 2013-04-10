<?php 
/*
Template Name: Grid Show Listings
*/
?>
<?php

	add_action('wp_print_scripts', 'childtheme_shows_scripts');
	 
	function childtheme_shows_scripts() {
	
	 	wp_enqueue_script('masonry', get_bloginfo('stylesheet_directory') . '/js/jquery.masonry.min.js', array('jquery'));
	 
	}

    // calling the header.php
    get_header();

    // action hook for placing content above #container
    thematic_abovecontainer();

?>
	<div id="container">
		<div id="content" class="shows-page-grid">

            <?php 			
            the_post();

            // displays the page title
            thematic_page_title();

            rewind_posts();
            
			
            // create the navigation above the content
            //thematic_navigation_above();
			
			$shows_tax = get_taxonomy('shows');
			$shows = get_weighted_terms('shows');
			
			?>
			<h1 class="page-title"><?php echo $shows_tax->labels->name; ?></h1>

			<div id="filtering-nav">
				<h2>Production Type: </h2>
				<ul>
					<?php			
					global $production_types;
	
					foreach( $production_types as $type ) {
						if( '' == $type['slug'] ) 
							continue;
						
						$slug = 'type-' . $type['slug'];
						
						echo "<li><a href='#{$slug}' class='{$slug}'>{$type['name']}</a></li>";
					}
					?>
					<li><a href="#all" class="all">View All</a></li>
				</ul>


				
				<h2>Genre: </h2>
				<ul>
					<?php
					$genres = get_terms('genre');
					foreach( $genres as $genre ) {
						echo "<li><a href='#genre-{$genre->slug}' class='genre-{$genre->slug}'>{$genre->name}</a></li>";
					}
					?>
					<li><a href="#all" class="all">View All</a></li>
				</ul>
			</div><!-- #filtering-nav-container -->
			<div id="wall">
			<?php

			foreach( $shows as $show ) {
				$title = $show->name;
				$desc = $show->description;
				
				// set column width by show title and description length				
				if( strlen($desc) > 250 && strlen($title) > 20 ) {
					$width = 'col3';
				} else if( strlen($desc) > 150 || strlen($title) > 20 ) {
					$width = 'col2';
				} else {
					$width = 'col1';
				}	
				
				// set the show genre
				$genre = get_term_meta( $show->term_id, 'genre', true );
				if( $genre ) {
					$genre = 'genre-' . $genre;
				} else {
					$genre = '';
				}
				
				// set the production type
				$term_production_type = get_term_meta( $show->term_id, 'production_type', true ); 
				if( $term_production_type ) {
					$type = 'type-' . $production_types[$term_production_type]['slug'];
				} else {
					continue;
				}
				
				$show_link = get_term_link( $show, $shows_tax->query_var );
				?>
				<div class="hentry taxonomy-page-item <?php echo $shows_tax->query_var;?>-item <?php echo "$type $width $genre"; ?>">
				
					<div class="entry-content">
						
						<h2 class="entry-title">
	 						<a href="<?php echo $show_link; ?>">
	 						<?php echo $title; ?>
	 						</a>
	 					</h2>
	 											
						<div class="thumbnail alignleft">					
						<a href="<?php echo $show_link; ?>">
						<?php echo $taxonomy_images_plugin->get_image_html( 'thumbnail', $show->term_taxonomy_id ); ?> </a>
						</div>
						
						<div class="description">
	 						<?php echo $desc;?>
	 					</div>
 					 
					</div>
					
				</div>
				<?php
			} /* foreach $shows */
							
            // create the navigation below the content
            thematic_navigation_below();

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