<?php

	// calling the header.php
	get_header();

?>

		<div id="content" class="clearfix">

			<div id="page-header" class="grid_12 block clearfix">

				<h1 class="page-title entry-title">Blog</h1>

			</div> <!-- end #page-header -->

			<div class="entry-main grid_8 clearfix">

				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<div id="post-<?php the_ID(); ?>" class="<?php thematic_post_class() ?> block">

						<div class="entry-header">

							<h1 class="h2"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
							<p class="entry-meta">
								<span class="comments-meta"><a href="<?php comments_link(); ?>"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/comments-blip.png" /><?php comments_number( '0 Comments' ); ?></a></span>
								<span class="posted-date"> / posted <time class="updated" datetime="<?php the_time('Y-m-j'); ?>" pubdate><?php the_time('F j, Y'); ?></time></span>
							</p> <!-- end .entry-meta -->

						</div> <!-- end entry header -->

						<div class="entry-content clearfix">
							<?php the_content(); ?>
						</div> <!-- end article section -->

					</div> <!-- end post -->

				<?php endwhile; ?>



				<?php else : ?>

						<div id="post-not-found" class="hentry clearfix">
							<div class="entry-header">
								<h1>Oops, Post Not Found!</h1>
							</div>
							<div class="entry-content">
								<p>Uh Oh. Something is missing. Try double checking things.</p>
							</div>
						</div>

				<?php endif; ?>

			</div> <!-- end .entry-main -->


			<div id="sidebar" class="sidebar grid_4 clearfix" role="complementary">

				<?php tutv_sidebar_connect_block();

				tutv_sidebar_featured_buttons();

				tutv_sidebar_featured_videos(); ?>

			</div> <!-- end #sidebar -->


			<div id="blog-archive-nav" class="blog-archive-nav archive-navigation navigation grid_12 clearfix" role="navigation">

					<?php if(function_exists('wp_pagenavi')) { ?>
						<?php wp_pagenavi(); ?>
					<?php } else { ?>
						<div class="nav-previous"><?php next_posts_link(__('Older  <span class="meta-nav">&rarr;</span>', 'thematic')) ?></div>
						<div class="nav-next"><?php previous_posts_link(__('<span class="meta-nav">&larr;</span>  Newer', 'thematic')) ?></div>
					<?php } ?>

			</div> <!-- end #blog-archive-nav -->

		</div><!-- #content -->

<?php

	// calling the standard sidebar
	//thematic_sidebar();

	// calling footer.php
	get_footer();

?>