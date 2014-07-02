<?php
/*
Template Name: Vimeo slideshow
*/
?>
<?php

  add_action('wp_print_scripts', 'childtheme_shows_scripts');

  function childtheme_shows_scripts() {

    wp_enqueue_script('vimeo-slideshow', get_bloginfo('stylesheet_directory') . '/assets/js/vimeo-slideshow.js', array('jquery'));

  }

  // calling the header.php
  get_header();

?>

  <div id="container">
    <div id="content">

      <?php

      // calling the widget area 'page-top'
      get_sidebar('page-top');

      the_post();

      ?>

      <div id="post-<?php the_ID(); ?>" <?php post_class() ?>>

        <?php

        // creating the post header
        thematic_postheader();

        ?>

        <div class="entry-content">

           <?php

          the_content();

          wp_link_pages("\t\t\t\t\t<div class='page-link'>".__('Pages: ', 'thematic'), "</div>\n", 'number');

          edit_post_link(__('Edit', 'thematic'),'<span class="edit-link">','</span>') ?>

        </div>
      </div><!-- .post -->

    <?php

    if ( get_post_custom_values('comments') )
      thematic_comments_template(); // Add a key/value of "comments" to enable comments on pages!

    // calling the widget area 'page-bottom'
    get_sidebar('page-bottom');

    ?>

    </div><!-- #content -->
  </div><!-- #container -->

<?php

  // action hook for placing content below #container
  thematic_belowcontainer();

  // calling the standard sidebar
  get_sidebar();

  // calling footer.php
  get_footer();

?>
