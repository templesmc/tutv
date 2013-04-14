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
function tutv_sidebar_connect_block() { ?>
	<div class="connect-block block">
		<div class="facebook">
			<?php tutv_social_media_icons('facebook'); ?>
			<a class="social-media-link">Like <em>Temple TV</em> on Facebook</a>
		</div>
		<div class="twitter">
			<?php tutv_social_media_icons('twitter'); ?>
			<a class="social-media-link">Follow <em>@TempleTV</em> on Twitter</a>
		</div>
		<div class="questions">
			<?php tutv_social_media_icons('contact'); ?>
			<a class="contact-link">Questions? <em>Contact us</em></a>
		</div>
	</div> <!-- end .connect-block -->
<?php
}