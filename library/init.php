<?php

// Set timezone to wordpress timezone
date_default_timezone_set( get_option('timezone_string') );

/**
 * Enqueue/register sitewide scripts and stylesheets.
 *
 * This is necessary in order to keep the necessary theme registration info
 * in /style.css and keep the active stylesheet at /style-active.css. It also loads
 * web fonts and other necessaries.
 *
 * @author Chris Montgomery
 * @see http://themble.com/bones/ Bones website
 *
 * @since 2.0.0
 * @version 1.0.0
 */
add_action('wp_enqueue_scripts', 'tutv_scripts_and_styles', 999);
function tutv_scripts_and_styles() {
	if (!is_admin()) {

		// load google web fonts
		// wp_register_style( 'tutv-webfonts', 'http://fonts.googleapis.com/css?family=Lato:300,400,700,900,400italic,700italic,900italic', array(), '', 'all' );
		// wp_enqueue_style( 'tutv-webfonts' );

		// register main stylesheet
		wp_register_style( 'tutv-stylesheet', get_stylesheet_directory_uri() . '/style-active.css', array(), '2.0.14', 'all' );
		wp_enqueue_style( 'tutv-stylesheet' );

	}
}



// Define custom thumbnail sizes
add_theme_support( 'post-thumbnails' );
add_image_size( 'banner', 960, 330, true ); // Slideshow image size
add_image_size( 'banner-thumbnail', 75, 50, true ); // Slideshow thumbnail size
add_image_size( 'thumb-small', 200, 144, true ); // Thumbnail size
add_image_size( 'thumb-large', 540, 400, true ); // Thumbnail size for episode pages without an embed URL
add_image_size( 'thumbnail-square', 200, 200, true ); // square thumbnail size for show page headers

add_theme_support('nav-menus');
add_theme_support( 'automatic-feed-links' );
add_editor_style();

/**
 * Define a list of production types for shows to be categorized by
 *
 * Used with order_production_types(), tutv_edit_shows_meta(), and page-shows.php
 */
$production_types = array(
	'' => array(
		'name' => 'Select a Production Type',
		'order' => 0,
		'slug' => ''
	),
	'original' => array(
		'name' => 'Original Productions',
		'order' => 1,
		'slug' => 'tutv-original'
	),
	'independent' => array(
		'name' => 'Independent Content',
		'order' => 2,
		'slug' => 'independent'
	),
	'university' => array(
		'name' => 'University Events',
		'order' => 3,
		'slug' => 'university-events'
	),
	'syndicated' => array(
		'name' => 'Syndicated',
		'order' => 4,
		'slug' => 'syndicated'
	)
);
