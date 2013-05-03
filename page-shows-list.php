<?php 
/*
Template Name: Show Listings Page
*/
?>
<?php

    // calling the header.php
    get_header();

    // action hook for placing content above #container
    thematic_abovecontainer();

?>
	<div id="container">
		<div id="content" class="shows-page">

            <?php 			
            the_post();

            // displays the page title
            thematic_page_title();

            rewind_posts();

            // create the navigation above the content
            //thematic_navigation_above();
			
			$tax = 'shows';
			
			global $production_types;
												
			$terms = get_terms($tax);
			$tax = get_taxonomy($tax);
			
			?>
			<h1 class="page-title"><?php echo $tax->labels->name; ?></h1>
			<?php
			
			$terms_by_production_type = array();
			
			foreach( $terms as $term ) {
				// manually remove the movies show for the time being
				//if( 'movies' == $term->slug )
				//	continue;
					
				$term_production_type = get_term_meta( $term->term_id, 'production_type', true ); 
				
				if ( $term_production_type )
					$terms_by_production_type[$term_production_type][] = $term;
			}
			uksort($terms_by_production_type, 'order_production_types');
			
			foreach( $terms_by_production_type as $type => $terms) {
				?>
				<h2 class="entry-title show-type"><?php echo $production_types[$type]['name']; ?></h2>
				<?php
				foreach($terms as $term) {
					?>
					<div class="hentry taxonomy-page-item <?php echo $tax->query_var;?>-item">
					
						<div class="entry-content">
						
							<div class="thumbnail alignleft">					
							<a href="<?php echo get_term_link( $term, $tax->query_var ) ?>">
							<?php echo $taxonomy_images_plugin->get_image_html( 'thumb', $term->term_taxonomy_id ); ?> </a>
							</div>
							
							<h2 class="entry-title">
		 						<a href="<?php echo get_term_link( $term, $tax->query_var ) ?>">
		 						<?php echo $term->name; ?>
		 						</a>
		 					</h2>
							
							<div class="description">
		 						<h2 class="entry-title">
		 						<a href="<?php echo get_term_link( $term, $tax->query_var ) ?>">
		 						<?php echo $term->name; ?>
		 						</a>
		 						</h2>			 
							
		 						<?php echo $term->description;?>
		 					</div>
	 					 
						</div>
						
					</div>
				<?php
				} /* foreach */
			} /* foreach */
				
			
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