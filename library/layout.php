<?php

// Add favicon to every page
function tutv_favicon() { 
?>
    <link rel="shortcut icon" href="wp-content/themes/tutv/images/favicon.ico" />
	<!-- WP-Minify JS -->
<?php 
}
add_action('wp_head', 'tutv_favicon');
add_action('admin_head', 'tutv_favicon');

// Add conditional body classes
function tutv_body_classes( $classes ) {
	if( is_tax() ) {
		global $taxonomy;
		if( !in_array( $taxonomy, $classes ) )
			$classes[] = "taxonomy " . "taxonomy-".$taxonomy;
	}
	
	global $post;
	if( isset($post->ID) )
		$shows = wp_get_object_terms( $post->ID, 'shows' );
	if( !empty( $shows) ) {
		foreach($shows as $show) {
			$classes[] = 'has-show-' . $show->slug;
		}
		$classes[] = 'has-taxonomy-show';
	}
	
	if( is_singular( array( 'show_notes' ) ) ) {
		$classes[] = 'show-post';
	}
	
	return $classes;
}
add_filter('body_class', 'tutv_body_classes');


// Fix for thematic to add a page title to taxonomy pages
function tutv_taxonomy_pagetitle( $content ) {
	global $post;
	if ( is_tax() ) {
		// find taxonomy name lifted from http://justintadlock.com/archives/2009/06/04/using-custom-taxonomies-to-create-a-movie-database
		$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); 
		$tax = get_taxonomy( get_query_var( 'taxonomy' ) );
		$content = "<h1 class=\"page-title\">$tax->label : <span>$term->name</span></h1>";
	}
	return $content;
}
add_filter('thematic_page_title', 'tutv_taxonomy_pagetitle');

// Remove default Thematic actions
function remove_thematic_actions() {
	remove_action('thematic_header','thematic_access',9);
}
add_action('init','remove_thematic_actions'); 

// Replace thematic access menu with a wordpress menu area named 'header'
function tutv_access() { 
	?>
    	<div id="access">
    		<div class="skip-link"><a href="#content" title="<?php _e('Skip navigation to the content', 'thematic'); ?>"><?php _e('Skip to content', 'thematic'); ?></a></div>
            <?php wp_nav_menu('menu=header&sort_column=menu_order&container_class=menu&menu_class=sf-menu') ?>
        </div><!-- #access -->
	<?php 
}
add_action('thematic_header','tutv_access',9);

// Add sub-page navigation to pages
function tutv_page_submenus( $content ) {
	global $post;
	$output = '';
	
	if( !is_page() ) 
		return $content;
		
	if( $post->post_parent ) {
		$parent = $post->post_parent;
	} else {
		$parent = $post->ID;
	}
	
	if ( empty($parent) )
		return $content; 

	$pages = get_pages( array(
		'child_of' => $parent
	) );
	if ($pages) {
	
		$pageids = array();
		
		foreach ($pages as $page) {
			$pageids[]= $page->ID;
		}
		
		$args = array(
			'title_li' => '',
			'echo' => 0,
			'include' =>  $parent . ',' . implode(",", $pageids)
		);
		$output .= '<ul id="subpage-menu">';
		$output .= wp_list_pages( $args );
		$output .= '</ul>';
	}

	return $output . $content;
}
add_action( 'the_content', 'tutv_page_submenus' );

// Place 'embed' custom field values into the post body
function  tutv_add_embeds() {
	if ( !is_single() )
		return;
	
	$has_embed = false;
		
	foreach( get_post_meta( get_the_ID(), 'embed' ) as $embed ) {
		if( empty( $embed ) )
			continue;
		 
		$has_embed = true;
		
		?>
		<div class="embed">
			<?php echo wp_oembed_get($embed, 'width=600'); ?><br>
		</div>
		<?php 

	} /* foreach */		
	
	if( !$has_embed && get_post_type() ==  'episodes' ) {
		echo the_post_thumbnail( 'large', array( 'class' => 'aligncenter' ) );
	}
}
add_filter('thematic_content', 'tutv_add_embeds');

function tutv_episode_meta($content) {
	global $post;
	
	$episode = get_post_meta($post->ID, 'episode', true);
	if( !$episode )
		return $content;

	$episode_title = '<span class="meta-prep meta-prep-episode-title">';
	
	if( $post->post_type == 'episodes' && !is_numeric($episode) ) {
		$episode_title .= __(' ', 'tutv');
	} elseif( $post->post_type == 'episodes' ) {
		$episode_title .= __('Episode ', 'tutv');
	} else if( $post->post_type == 'clip' ) {
		$episode_title .= __('From episode ', 'tutv');
	} else {
		return $content;
	}
	
	$episode_title .= '</span>';
    $episode_title .= '<span class="episode-title">';
    $episode_title .= $episode;
    $episode_title .= '</span>';
    
    return $content . $episode_title;
	
}
add_filter ('thematic_post_meta_entrydate', 'tutv_episode_meta');

// Change the excerpt length
function tutv_excerpt_length( $length ) {
	return 24;
}
add_filter('excerpt_length', 'tutv_excerpt_length', 999);

// Custom excerpt more text
function custom_excerpt_more( $more ) {
    global $post;
	return '<a href="'. get_permalink($post->ID) . '"> [...]</a>';
}
add_filter( 'excerpt_more', 'custom_excerpt_more' );

// Remove 'read more' jump link
function tutv_remove_more_jump_link( $link ) { 
	$offset = strpos( $link, '#more-' );
	if ( $offset ) {
		$end = strpos( $link, '"',$offset );
	}
	if ( $end ) {
		$link = substr_replace( $link, '', $offset, $end-$offset) ;
	}
	return $link;
}
add_filter('the_content_more_link', 'tutv_remove_more_jump_link');

// Show excerpt on show taxonomy page
function tutv_conditional_excerpt( $display_type ) {
	if( is_tax( 'shows' ) ){
		$display_type = 'excerpt';
	}
	return $display_type;
}
add_filter('thematic_content', 'tutv_conditional_excerpt', 11);

// Selectively add post thumbnails
function tutv_add_post_thumbnail( $content ) {
	if( is_archive( array('episodes', 'clip') ) ) {
		?>
		<a href="<?php the_permalink(); ?>">
		<?php the_video_thumbnail( get_the_ID() ); ?>
		</a>
		<?php
	}
	echo $content;
}
add_filter('thematic_content', 'tutv_add_post_thumbnail');

// Add a span ('page-title-prefix') to the page header for easy css manipulation
// 
// Archives in thematic have a prefix such as 'Category Archives:' that is not always
// desired. This filter adds a span to these prefixes that allows us the remove them with css. 
function tutv_page_title($content) {
	$content = preg_replace('/">/', '"><span class="page-title-prefix">', $content);
	$content = preg_replace("/<span>/", "</span><span>", $content);
	return $content;
}
add_filter('thematic_page_title', 'tutv_page_title');


// Output custom fields for a given post to users with editor or administrator roles
function tutv_meta_footer( $content ) {
	$post_type = get_post_type();
	if( is_singular( array( 'clip', 'episodes' ) ) && current_user_can( 'editor' ) ) {
		
		?>
		<div class="post-metadata">
			<h2 class="section-header">Metadata (normal users will not see this box)</h2>
			<?php the_meta(); ?>
		</div>
		<?php	
	}
	echo $content;
}
add_filter ('thematic_postfooter', 'tutv_meta_footer', 3);
	
// Output the page title of the 'posts page'
//
// The page you select to be your 'posts page' will not display a page title like 
// every other page or post as if it were still the homepage.
function tutv_posts_page_title() {
	if( is_home() ){
		
		$post_obj = get_queried_object();
		echo "<h1 class='entry-title'>{$post_obj->post_title}</h1>";
	}
}
add_filter('thematic_navigation_above', 'tutv_posts_page_title');

// For show-based content, add a header with the show title and background 
//
// string $force: Override to force display
function tutv_show_header( $force = false ) {
	global $post;
	
	if ( is_singular( array( 'episodes', 'show_notes', 'clip', 'show_page' ) ) || $force ) {		
		$term = get_the_show_term( $post->ID );
		if ( empty( $term ) || has_no_show( $term ) ) 
			return;
		
		$show_background = get_show_background($term->slug);
		
		
		if( !empty($show_background) ) {
			?>
			<style>body { background: url("<?php echo  $show_background; ?>") 50% 0 no-repeat; }</style>
			<?php
		}
		?>
		<div id="show-header">
		
			<?php			
			if ($term->name) {
			?>
				<h1 id="show-name">
					<a href="<?php echo get_show_link($term) ?>"><?php echo $term->name; ?></a>
				</h1>
			<?php
			}
			wp_nav_menu( array(
				'fallback_cb'     => '',
				'theme_location'  => $term->slug,
				'container_class' => 'menu-container',
				'menu_class' => 'sf-menu'
			) );

			?>
			<div id="show-description">

				<p>
				<?php
				if ($term->description) {
					 echo $term->description;
				}
				?>
				</p>
			</div>
		</div> <!-- #show-header -->
		<?php
	}
}
add_filter('thematic_navigation_above', 'tutv_show_header', 1);

// Changes Footer Text
function childtheme_footer($thm_footertext) {
	$thm_footertext = 'Copyright &copy; ' . date("Y") . ' TUTV - Temple University Television. All rights reserved.';
	return $thm_footertext;
}
add_filter('thematic_footertext','childtheme_footer');

/**
 * Add widget area to header for search box.
 *
 * @author Chris Montgomery <mont.chr@gmail.com>
 *
 */
function tutv_header_search() {
	echo '<div id="header-search">';
		get_search_form();
	echo '</div> <!-- end #header-search -->';
}
add_action('thematic_header', 'tutv_header_search', 8);

/**
 * Add widget area to footer for search box.
 *
 * @author Chris Montgomery <mont.chr@gmail.com>
 *
 */
function tutv_footer() {
	echo '<div id="footer-social-media">';
		tutv_social_media_icons();
	echo '</div> <!-- end #footer-social-media -->';
	echo '<div id="footer-search">';
		get_search_form();
	echo '</div> <!-- end #footer-search -->';
}
add_action('thematic_footer', 'tutv_footer', 8);

/**
 * Returns list of social media icons.
 * 
 * @author Chris Montgomery <mont.chr@gmail.com>
 * @since 2.0.0
 *
 */
function tutv_social_media_icons(
	$fb_url = 'https://www.facebook.com/TempleTV',
	$twitter_url = 'https://twitter.com/templetv',
	$rss_url = 'feed://templetv.net/feed'
	) {
	?>
	<ul class="social-media-icons">
		<li id="fb-icon" class="social-media-icon">
			<a href="<?php echo $fb_url; ?>" title="Like us on Facebook">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/social-icons/icon-fb.png" title="Facebook" alt="Facebook" />
			</a>
		</li>
		<li id="twitter-icon" class="social-media-icon">
			<a href="<?php echo $twitter_url; ?>" title="Follow us on Twitter">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/social-icons/icon-twitter.png" title="Twitter" alt="Twitter" />
			</a>
		</li>
		<li id="rss-icon" class="social-media-icon">
			<a href="<?php echo $rss_url; ?>" title="Subscribe to our latest updates">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/social-icons/icon-rss.png" title="RSS" alt="RSS" />
			</a>
		</li>
	</ul> <!-- end .social-media-icons -->
	<?php
}
