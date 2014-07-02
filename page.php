<?php

  // calling the header.php
  get_header();

?>

    <div id="content" class="clearfix">

      <?php

      // calling the widget area 'page-top'
      get_sidebar('page-top');

      the_post();

      ?>

      <div id="page-header" class="grid_12 block clearfix">

        <h1 class="page-title entry-title"><?php echo tutv_get_parent_title(); ?></h1>

        <?php

        echo tutv_page_submenus();

        ?>

      </div> <!-- end #page-header -->

      <div class="entry-main block grid_8 clearfix">

        <div id="post-<?php the_ID(); ?>" <?php post_class() ?>>

            <div class="entry-content clearfix">

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

      </div> <!-- end .entry-main -->

      <div id="sidebar" class="sidebar grid_4 clearfix" role="complementary">

        <?php tutv_sidebar_connect_block(); ?>

      </div> <!-- end #sidebar -->

    </div><!-- #content -->

<?php
  get_footer();
?>
