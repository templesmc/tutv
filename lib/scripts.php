<?php

/**
 * Remove Thematic scripts
 */
// Superfish dropdown scripts
remove_action('wp_head', 'thematic_head_scripts');

/**
 * Enqueue scripts and stylesheets
 *
 * Enqueue stylesheets in the following order:
 * 1. /theme/assets/css/main.decda255.min.css
 *
 * Enqueue scripts in the following order:
 * 1. jquery-1.11.0.min.js via Google CDN
 * 2. /theme/assets/js/vendor/modernizr-2.7.0.min.js
 * 3. /theme/assets/js/main.min.js (in footer)
 * 4. /theme/assets/js/main.js    (in footer)
 */
function tutv_scripts_and_styles() {
  /**
   * The build task in Grunt renames production assets with a hash
   * Read the asset names from assets-manifest.json
   */
  if (WP_ENV === 'development') {
    $assets = array(
      'css'       => '/assets/css/main.css',
      'js_main'   => '/assets/js/scripts.js',
      'js_front'  => '/assets/js/front-page.js',
      'js_shows'  => '/assets/js/shows-page.js',
      'modernizr' => '/assets/vendor/modernizr/modernizr.js',
      'jquery'    => '//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.js'
    );
  } else {
    $get_assets = file_get_contents(get_stylesheet_directory() . '/assets/manifest.json');
    $assets     = json_decode($get_assets, true);
    $assets     = array(
      'css'       => '/assets/css/main.min.css?' . $assets['assets/css/main.min.css']['hash'],
      'js_main'   => '/assets/js/scripts.min.js?' . $assets['assets/js/scripts.min.js']['hash'],
      'js_front'  => '/assets/js/front-page.min.js' . '?' . $assets['assets/js/front-page.min.js']['hash'],
      'js_shows'  => '/assets/js/shows-page.min.js' . '?' . $assets['assets/js/shows-page.min.js']['hash'],
      'modernizr' => '/assets/js/vendor/modernizr.min.js',
      'jquery'    => '//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'
    );
  }

  wp_enqueue_style('tutv-stylesheet', get_stylesheet_directory_uri() . $assets['css'], false, null);

  // jQuery is loaded using the same method from HTML5 Boilerplate:
  // Grab Google CDN's latest jQuery with a protocol relative URL; fallback to local if offline
  // It's kept in the header instead of footer to avoid conflicts with plugins.
  if (!is_admin()) {
    wp_deregister_script('jquery');
    wp_register_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js', array(), null, false);
    add_filter('script_loader_src', 'tutv_jquery_local_fallback', 10, 2);
  }

  if (!is_admin()) {
    // load google web fonts
    // wp_register_style( 'tutv-webfonts', 'http://fonts.googleapis.com/css?family=Lato:300,400,700,900,400italic,700italic,900italic', array(), '', 'all' );
    // wp_enqueue_style( 'tutv-webfonts' );
  }

  if (is_single() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }

  // Modernizr
  wp_register_script('modernizr', get_stylesheet_directory_uri() . '/assets/js/vendor/modernizr.min.js', array(), null, false);
  // Main scripts
  wp_register_script('tutv-scripts', get_stylesheet_directory_uri() . $assets['js_main'], array('jquery'), null, true);
  // Front page scripts
  wp_register_script('tutv-scripts-front-page', get_stylesheet_directory_uri() . $assets['js_front'], array('jquery', 'tutv-scripts'), null, true);
  // Shows page scripts
  wp_register_script('tutv-scripts-shows-page', get_stylesheet_directory_uri() . $assets['js_shows'], array('jquery', 'tutv-scripts'), null, true);

  wp_enqueue_script('modernizr');
  wp_enqueue_script('jquery');
  wp_enqueue_script('tutv-scripts');

  if (is_front_page()) {
    wp_enqueue_script('tutv-scripts-front-page');
  }

  if (is_page_template('page-shows-grid.php')) {
    wp_enqueue_script('tutv-scripts-shows-page');
  }
}
add_action('wp_enqueue_scripts', 'tutv_scripts_and_styles', 100);

// http://wordpress.stackexchange.com/a/12450
function tutv_jquery_local_fallback($src, $handle = null) {
  static $add_jquery_fallback = false;

  if ($add_jquery_fallback) {
    echo '<script>window.jQuery || document.write(\'<script src="' . get_stylesheet_directory_uri() . '/assets/js/vendor/jquery-1.11.0.min.js"><\/script>\')</script>' . "\n";
    $add_jquery_fallback = false;
  }

  if ($handle === 'jquery') {
    $add_jquery_fallback = true;
  }

  return $src;
}
add_action('wp_head', 'tutv_jquery_local_fallback');
