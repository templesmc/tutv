<?php

  // calling the header.php
  get_header();

  // action hook for placing content above #container
  thematic_abovecontainer();

?>

  <div id="container">
    <div id="content" class="taxonomy-genre">

      <?php
      // displays the page title
      thematic_page_title();

      // create the navigation above the content
      thematic_navigation_above();

      //$genre = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
      $genre = get_query_var( 'term' );
      $tax = get_query_var( 'taxonomy' );

      $shows = get_terms( 'shows' );

      $shows_in_genre = array();

      foreach($shows as $show) {
        $show_genre = get_term_meta( $show->term_id, 'genre', true );
        if( $genre == $show_genre )
          $shows_in_genre[] = $show;
      }

      foreach($shows_in_genre as $show) {
        ?>
        <div class="hentry taxonomy-page-item genre-show-item">

        <div class="entry-content">
          <div class="thumbnail alignleft">
          <a href="<?php echo get_show_link( $show ) ?>">
          <?php echo $taxonomy_images_plugin->get_image_html( 'thumb-small', $show->term_taxonomy_id ); ?></a>
          </div>

          <h2 class="entry-title">
          <a href="<?php echo get_show_link( $show ) ?>">
          <?php echo $show->name; ?>
          </a>
          </h2>

          <p><?php echo $show->description;?></p>
          </div>
        </div>
        <?php
      } /* foreach */

      // create the navigation below the content
      thematic_navigation_below();

      ?>

    </div><!-- #content .hfeed -->
  </div><!-- #container -->

<?php

  // action hook for placing content below #container
  thematic_belowcontainer();

  get_sidebar();

  // calling footer.php
  get_footer();

?>
