<?php

	// calling the header.php
	get_header();

?>
		<div id="content" class="clearfix">

			<?php 
			
			the_post(); ?>

			<div id="page-header" class="grid_12 block clearfix">
			
				<?php thematic_navigation_above(); ?>

			</div> <!-- end #page-header -->

			<div id="main-content" class="grid_8 block clearfix">

				<?php

				// calling the widget area 'single-top'
				get_sidebar('single-top');

				// action hook creating the single post
				thematic_singlepost();
				
				// calling the widget area 'single-insert'
				get_sidebar('single-insert');

				// create the navigation below the content
				thematic_navigation_below();

				// calling the comments template
				thematic_comments_template();

				// calling the widget area 'single-bottom'
				get_sidebar('single-bottom');
				
				?>

			</div> <!-- end #main-content -->

			<div id="sidebar" class="sidebar grid_4 clearfix" role="complementary">

				<?php

				tutv_sidebar_connect_block();

				tutv_video_sidebar();

				?>

			</div> <!-- end #sidebar -->

		</div><!-- #content -->

<?php
	
	// calling footer.php
	get_footer();

?>