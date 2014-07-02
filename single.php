<?php

  // calling the header.php
  get_header();

?>
    <div id="content" class="clearfix">

      <?php

      the_post();

      if (!is_singular( 'post' )) :

      ?>

        <div id="page-header" class="grid_12 block clearfix">

          <?php thematic_navigation_above(); ?>

        </div> <!-- end #page-header -->

      <?php

      endif;

      ?>

      <div id="main-content" class="grid_8 block clearfix">

        <?php

        // calling the widget area 'single-top'
        get_sidebar('single-top');

        // action hook creating the single post
        thematic_singlepost();

        // calling the widget area 'single-insert'
        get_sidebar('single-insert');

        // create the navigation below the content
        if (!has_term( '', 'shows' )) : ?>
        <div id="nav-below" class="navigation">
          <div class="nav-previous"><?php thematic_previous_post_link() ?></div>
          <div class="nav-next"><?php thematic_next_post_link() ?></div>
        </div>
        <?php endif; ?>

        <div id="comments">

          <?php

          // calling the comments template
          thematic_comments_template();

          ?>

        </div> <!-- end comments -->

        <?php

        // calling the widget area 'single-bottom'
        get_sidebar('single-bottom');

        ?>

      </div> <!-- end #main-content -->

      <div id="sidebar" class="sidebar grid_4 clearfix" role="complementary">

        <?php

        tutv_sidebar_connect_block();

        tutv_video_sidebar();

        if (is_singular( 'post' ) && in_category( 'blog' )) :

          tutv_sidebar_featured_buttons();

          tutv_sidebar_featured_videos();

        endif;

        ?>

      </div> <!-- end #sidebar -->

    </div><!-- #content -->

<?php

  // calling footer.php
  get_footer();

?>
