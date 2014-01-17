		</div><!-- #container -->

	</div><!-- #main -->

	<?php

	// action hook for placing content above the footer
	thematic_abovefooter();

	?>

	<div id="footer">

		<div id="footer-inner" class="container_12">

			<div id="footer-social-media">
				<?php tutv_social_media_icons(); ?>
			</div> <!-- end #footer-social-media -->

			<div id="footer-search">
				<?php get_search_form(); ?>
			</div> <!-- end #footer-search -->

			<div id="footer-links">
				<ul>
					<li class="about-link"><a href="<?php echo home_url('/about'); ?>">About</a></li>
					<li class="blog-link"><a href="<?php echo home_url('/blog'); ?>">Blog</a></li>
				</ul>
			</div> <!-- end #footer-links -->

			<div id="siteinfo">
				<p><?php echo 'Copyright &copy; ' . date("Y") . ' TUTV – Temple University Television. All rights reserved.'; ?></p>
			</div> <!-- end #siteinfo -->

		</div> <!-- end #footer-inner -->

	</div><!-- #footer -->

	<?php

	// actio hook for placing content below the footer
	//thematic_belowfooter();

	?>

</div><!-- #wrapper .hfeed -->

<?php

wp_footer();

?>

</body>
</html>
