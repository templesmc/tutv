<?php 
/*
Template Name: Taxonomy Page ('taxonomy' custom field)
*/
?>
<?php

    // calling the header.php
    get_header();

    // action hook for placing content above #container
    thematic_abovecontainer();

?>

	<div id="container">
		<div id="content">

            <?php 			
            the_post();

            // displays the page title
            thematic_page_title();

            rewind_posts();

            // create the navigation above the content
            thematic_navigation_above();
			
			$tax = get_post_meta(get_the_ID(), 'taxonomy', true);
			
			if ($tax) {
				$terms = get_terms($tax);
				$tax = get_taxonomy($tax);
				
				?>
				<h1 class="entry-title"><?php echo $tax->labels->name; ?></h1>
				<?php
				
				foreach($terms as $term) {
				?>
				<div class="hentry taxonomy-page-item <?php echo $tax->query_var;?>-item">
				
				<div class="entry-content">
				<div class="thumbnail alignleft">
				<a href="<?php echo get_term_link( $term, $tax->query_var ) ?>">
				<?php echo $taxonomy_images_plugin->get_image_html( 'thumb-small', $term->term_taxonomy_id ); ?>
				</a>
				</div>
				
				<h2 class="entry-title"><a href="<?php echo get_term_link( $term, $tax->query_var ) ?>"><?php echo $term->name; ?></a></h2>
				<p><?php echo $term->description;?></p>
				</div>
				</div>
				<?php
				}
				
			} else {
				echo '<strong>Error: please enter a taxonomy custom field (eg. "shows")</strong>';
			}
			
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