<?php


  get_header();

?>

    <div id="content">

      <div id="main" class="grid_8 clearfix" role="main">

        <div id="page-header" class="block clearfix">

          <h1 class="archive-title search-title">Search Results for: <span class="query-string">&ldquo;<?php echo esc_attr(get_search_query()); ?>&rdquo;</span></h1>

        </div> <!-- end #page-header -->

        <?php if (have_posts()) :

          thematic_searchloop();

        else : ?>

          <div id="post-0" class="post noresults block">
            <h1 class="entry-title"><?php _e('Nothing Found', 'thematic') ?></h1>
            <div class="entry-content">
              <p><?php _e('Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'thematic') ?></p>
            </div>
            <form id="noresults-searchform" method="get" action="<?php bloginfo('home') ?>">
              <div>
                <input id="noresults-s" name="s" type="text" value="<?php echo esc_html(stripslashes($_GET['s']), true); ?>" size="40" />
                <input id="noresults-searchsubmit" name="searchsubmit" type="submit" value="<?php _e('Find', 'thematic') ?>" />
              </div>
            </form>
          </div><!-- .post -->

        <?php endif; ?>

      </div> <!-- end #main -->

      <div id="sidebar" class="sidebar grid_4 clearfix" role="complementary">

        <?php tutv_sidebar_connect_block();

        tutv_sidebar_featured_buttons();

        tutv_sidebar_featured_videos(); ?>

      </div> <!-- end #sidebar -->

    </div><!-- #content -->

<?php

  // calling the standard sidebar
  //thematic_sidebar();

  // calling footer.php
  get_footer();

?>
