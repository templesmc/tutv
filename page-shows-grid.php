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

?>

	<div id="content" class="shows-page-grid hfeed clearfix">

		<?php
		
		// create the navigation above the content
		//thematic_navigation_above();
		
		$shows_tax = get_taxonomy('shows');
		$shows = get_weighted_terms('shows');
		
		?>

		<div id="page-header" class="block grid_12 clearfix">

			<h1 class="page-title"><?php echo $shows_tax->labels->name; ?></h1>

		</div> <!-- end #page-header -->

		<div id="filtering-nav-container" class="block grid_12 clearfix">

			<div id="production-type-filters" class="filtering-nav grid_6 alpha clearfix">

				<h2>Production Type</h2>

				<ul>
					<?php			
					global $production_types;
	
					foreach( $production_types as $type ) {
						if( '' == $type['slug'] ) {
							continue;
						}
						
						$slug = 'type-' . $type['slug'];
						
						echo "<li><a href='#{$slug}' class='{$slug} btn-production-type btn-small btn'>{$type['name']}</a></li>";
					}
					?>
					<li><a href="#all" class="all btn-primary btn-small btn">View All</a></li> <!-- there should only be one "view all" button... somewhere -->
				</ul>

			</div> <!-- #production-type-filters -->

			<div id="genre-filters" class="filtering-nav grid_6 omega clearfix">
			
				<h2>Genre</h2>

				<ul>
					<?php
					$genres = get_terms('genre');
					foreach( $genres as $genre ) {
						echo "<li><a href='#genre-{$genre->slug}' class='btn-genre-{$genre->slug} btn-genre btn-small btn'>{$genre->name}</a></li>";
					}
					?>
					<li><a href="#all" class="all btn-primary btn-small btn">View All</a></li> <!-- there should only be one "view all" button... somewhere -->
				</ul>

			</div> <!-- end #genre-filters -->

		</div><!-- #filtering-nav-container -->

		<div id="shows-wall" class="wall grid_12 clearfix">

		<?php

		foreach( $shows as $show ) {
			$title = $show->name;
			$desc = $show->description;
			
			/*
			nice idea, but badly implemented. saving code as a legacy.
			// Set column width by show title and description length				
			if( strlen($desc) > 250 && strlen($title) > 20 ) {
				$width = 'col3';
			} else if( strlen($desc) > 150 || strlen($title) > 20 ) {
				$width = 'col2';
			} else {
				$width = 'col1';
			}
			*/
			$width = ''; // turn this off if bringing back the above code
			
			// Set the show genre
			$genre = get_term_meta( $show->term_id, 'genre', true );
			if( $genre ) {
				$genre = 'genre-' . $genre;
			} else {
				$genre = '';
			}
			
			// Set the production type
			$term_production_type = get_term_meta( $show->term_id, 'production_type', true ); 
			if( $term_production_type ) {
				$type = 'type-' . $production_types[$term_production_type]['slug'];
			} else {
				continue;
			}
			
			$show_link = get_term_link( $show, $shows_tax->query_var );
			?>

			<div class="hentry shows-page-item taxonomy-page-item <?php echo $shows_tax->query_var;?>-item <?php echo "$type $width $genre"; ?> block grid_6 clearfix">
			
				<div class="entry-header">

					<h2 class="entry-title">
						<a href="<?php echo $show_link; ?>">
							<?php echo $title; ?>
						</a>
					</h2>

				</div> <!-- end .entry-header -->

				<div class="entry-content">
											
					<div class="thumbnail alignleft">					
						<a href="<?php echo $show_link; ?>">
							<?php echo $taxonomy_images_plugin->get_image_html( 'thumbnail', $show->term_taxonomy_id ); ?>
						</a>
					</div>
					
					<div class="description">
						<p><?php echo $desc;?></p>
					</div>
				 
				</div> <!-- end .entry-content -->
				
			</div> <!-- end .hentry.shows-page-item -->

			<?php
		} /* end foreach $shows */
		?>

		</div> <!-- end #shows-wall -->

		<?php
						
		// create the navigation below the content
		// thematic_navigation_below();

		?>

	</div><!-- #content .hfeed -->

<?php 

get_footer();

?>