<?php

/**
 * Add a menu area for each show to be displayed in the show header on all
 * content for that show.
 *
 */
function tutv_add_show_specific_menus() {

	$shows = get_terms('shows');

	$menus = array();

	foreach($shows as $show) {
		$menus[$show->slug] = $show->name . ' Menu';
	}

	register_nav_menus( $menus );

}
add_action( 'init', 'tutv_add_show_specific_menus' );

// Custom login logo
function tutv_login_head() {
	?>
	<style>
	body.login #login h1 a {
		background: url('<?php bloginfo('stylesheet_directory'); ?>/images/logo-login.png') no-repeat center top;
		height: 72px;
	}
	</style>
	<?php
}
add_action('login_head', 'tutv_login_head');

// Site login logo link
function tutv_login_headerurl() {
	return get_home_url();
}
add_filter('login_headerurl','tutv_login_headerurl');

/**
 * Allow for 'showtime' and 'show' columns on schedule edit screen
 *
 * Adapted from: http://scribu.net/wordpress/custom-sortable-columns.html
 *
 * @param array $columns Names of columns to display
 */
function tutv_schedule_columns_register( $columns ) {
		unset($columns['date']);
		unset($columns['author']);
		$columns['showtime'] = 'Showtime';
		// $columns['episode'] = 'Episode';
		$columns['show'] = 'Show';

		return $columns;
}
add_filter('manage_edit-events_columns', 'tutv_schedule_columns_register');

/**
 * Allow for 'show' column on episode and clip edit screens
 *
 * Adapted from: http://scribu.net/wordpress/custom-sortable-columns.html
 *
 * @param array $columns Names of columns to display
 */
function tutv_show_columns_register( $columns ) {
		$columns['show'] = 'Show';

		return $columns;
}
add_filter('manage_edit-episodes_columns', 'tutv_show_columns_register');
add_filter('manage_edit-clip_columns', 'tutv_show_columns_register');
add_filter('manage_edit-show_notes_columns', 'tutv_show_columns_register');
add_filter('manage_edit-show_page_columns', 'tutv_show_columns_register');

/**
 * Output all custom columns
 *
 * Adapted from: http://scribu.net/wordpress/custom-sortable-columns.html
 *
 * @param array $column Name of custom column to generate text for
 */
function tutv_columns_display( $column ) {
	global $post;

	switch( $column ) {

		case 'showtime' :

			$showtime = get_post_meta($post->ID, 'date_value', true);
			if ( $showtime ) {
				echo date('F j, Y', $showtime) . ' at ' . date('h:i A', $showtime);
			} else {
				echo '<em>undefined</em>';
			}
			break;

		case 'episode' :

			$episodes =  get_posts( array(
			  'connected_type' => 'schedule_event',
			  'connected_items' => $post,
			  'nopaging' => true,
			  'suppress_filters' => false
  			) );
			if ( empty($episodes) ) {
				echo '<em>undefined</em>';
 		   	} else {
 		   		echo get_the_show($episodes[0]->ID) . get_the_title($episodes[0]->ID);
			}
			break;


		case 'show' :

 		   	$shows = get_the_term_list( $post->ID, 'shows', '', ', ', '' );
 		   	if ( is_string( $shows ) ) {
				echo $shows;
			}  else {
				echo '<em>undefined</em>';
			}
			break;
	}
}
add_action('manage_posts_custom_column', 'tutv_columns_display', 10, 2);

// Register the column as sortable
function tutv_columns_register_sortable( $columns ) {
	$columns['showtime'] = 'showtime';
	// $columns['episode'] = 'episode';
	$columns['show'] = 'show';

	return $columns;
}
add_filter( 'manage_edit-events_sortable_columns', 'tutv_columns_register_sortable' );

function tutv_columns_column_orderby( $vars ) {
	if ( isset( $vars['orderby'] ) && 'showtime' == $vars['orderby'] ) {
		$vars = array_merge( $vars, array(
			'meta_key' => 'date_value',
			'orderby' => 'meta_value_num'
		) );
	}
	if ( isset( $vars['orderby'] ) && 'show' == $vars['orderby'] ) {
		$vars = array_merge( $vars, array(
			'meta_key' => 'date_value',
			'orderby' => 'meta_value_num'
		) );
	}

	return $vars;
}
add_filter( 'request', 'tutv_columns_column_orderby' );

// Allow filtering episodes by show on the admin browse post screen
function restrict_episodes_by_show() {
    global $typenow;
    global $wp_query;

    if( $typenow == 'episodes' ) {

        $tutv_shows = get_terms('shows');

        ?>
	   <select name="shows">

		<option value="" disabled>Select a show</option>

		<?php

	    foreach($tutv_shows as $tutv_show) {
	    	?>
	    	<option value="<?php echo $tutv_show->slug; ?>" <?php if($tutv_show->slug == $_GET['shows']) echo 'selected'; ?> >
	    	<?php echo $tutv_show->name; ?>
	    	</option>
	    	<?php
	    }
		?>
		</select>
	<?php
    }
}
add_action('restrict_manage_posts','restrict_episodes_by_show');