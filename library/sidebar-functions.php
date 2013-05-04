<?php

/**
 * Social media block
 *
 * Creates a social media block for the sidebar.
 *
 * @author Chris Montgomery
 * @since 2.0.0
 * @version 1.0.0
 */
function tutv_sidebar_connect_block() {
	if( !is_singular( array( 'episodes', 'events', 'clip' ) ) && !is_page('watch-live') ) { ?>
		<div class="connect-block block">
			<div class="facebook social-connection">
				<?php tutv_social_media_icons('facebook'); ?>
				<a href="https://www.facebook.com/TempleTV" title="Like us on Facebook" class="social-media-link social-connection-link">Like <em>Temple TV</em> on Facebook</a>
			</div>
			<div class="twitter social-connection">
				<?php tutv_social_media_icons('twitter'); ?>
				<a href="https://twitter.com/templetv" title="Follow us on Twitter" class="social-media-link social-connection-link">Follow <em>@TempleTV</em> on Twitter</a>
			</div>
			<div class="questions social-connection">
				<?php tutv_social_media_icons('contact'); ?>
				<a href="feed://templetv.net/feed" title="Subscribe to our latest updates" class="contact-link social-connection-link">Questions? <em>Contact us</em></a>
			</div>
		</div> <!-- end .connect-block -->
	<?php
	} // endif
}

/**
 * Featured buttons blocks for sidebar.
 *
 * @author Chris Montgomery
 * @since 2.0.0
 * @version 1.0.0
 */
function tutv_sidebar_featured_buttons() { ?>
	<div class="sidebar-featured-buttons-section">
		<a href="<?php echo home_url('/about'); ?>" id="sidebar-about-tutv-button" class="sidebar-featured-button block" title="About TUTV">
			<h3><em>about</em> TUTV</h3>
		</a><!-- end #about-tutv-button -->
		<?php
		/*
		// disabled because no such blog exists!
		<a href="<?php echo home_url('/blog'); ?>" id="sidebar-temple-update-blog-button" class="sidebar-featured-button block" title="Temple Update Blog">
			<h3><em>temple update</em> blog</h3>
		</a><!-- end #read-the-blog-button -->
		*/
		?>
		<a href="<?php echo home_url('/watch-live'); ?>" id="sidebar-watch-live-button" class="sidebar-featured-button block" title="Watch Live">
			<h3>watch <em>live</em></h3>
		</a><!-- end #watch-live-button -->
	</div><!-- end #featured-buttons-section -->
<?php
}

/**
 * Featured Videos sidebar
 *
 * N.B. This will only work well with two columns, as the a/o style class would need to change with the new param.
 *
 * @author Chris Montgomery
 * @author Sam Marguiles
 * @since 2.0.0
 * @version 1.0.0
 *
 * @param string $grid_class 960gs class for block widths 
 */
function tutv_sidebar_featured_videos($grid_class = 'grid_2') { ?>
	<div class="featured-video-section">
		<h3 class="section-header">Featured Videos</h3>
		<?php
		// set class to alpha/omega depending on position in 2 column layout
		// http://wordpress.org/support/topic/adding-different-styling-every-3rd-post
		$style_classes = array('alpha', 'omega');
		$styles_count = count($style_classes);
		$style_index = 0;

		$featuredVideos = new WP_Query();
		$featuredVideos->query('post_type=any&featured-video=on&posts_per_page=6');

		while ( $featuredVideos->have_posts() ) : $featuredVideos->the_post();

			// this is the second part of the operation that determines first or last class based on column divisions. see above.
			$k = $style_classes[$style_index++ % $styles_count]; ?>
			<div class="featured-video video-item block <?php echo $grid_class . ' ' . $k; ?> clearfix">
				<a href="<?php the_permalink(); ?>" rel="bookmark">
					<div class="thumbnail"><?php the_post_thumbnail('thumb'); ?></div>
					<?php
					// if the show is defined, echo it with a colon suffix
					if ( function_exists('the_show')) { echo the_show( '<span class="featured-video-show">', '</span>: ') . ''; }
					?>
					<span class="featured-video-episode-title"><?php the_title(); ?></span>
				</a>
			</div><!-- end .featured-video -->
		<?php
		endwhile;
		wp_reset_query();
		?>
	</div><!-- end .featured-video-section -->
<?php
} // don't remove this bracket!
