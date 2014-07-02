<?php

//add_action('wp_print_scripts', 'tutv_shows_scripts');

function tutv_shows_scripts() {

  // make sure that scripts are enqueuing properly after changing the location of the default stylesheet - montchr
  wp_enqueue_script('bbq', get_bloginfo('stylesheet_directory') . '/assets/js/jquery.ba-bbq.min.js', array('jquery'), '1.2.1', false);
  wp_enqueue_script('show-scripts.js', get_bloginfo('stylesheet_directory') . '/assets/js/show-scripts.js', array('jquery', 'bbq'), '1.0', false);

}

$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

// calling the header.php
get_header();

?>

  <div id="content" class="single-show-page taxonomy-shows hfeed">

    <div id="page-header" class="block grid_12 clearfix">

      <?php tutv_show_header( true ); ?>

    </div> <!-- end #page-header -->

    <?php

    // set class to alpha/omega depending on position in 4 column layout
    // http://wordpress.org/support/topic/adding-different-styling-every-3rd-post
    $style_classes = array('alpha', '', 'omega');
    $styles_count = count($style_classes);
    $style_index = 0;

    global $query_string;

    /* EPISODES PAGING */
    $episodes_paged = ( isset( $_GET['episodes_page'] ) ) ? $_GET['episodes_page'] : 1;
    query_posts($query_string . '&post_type=episodes&posts_per_page=9&paged=' . $episodes_paged);

    if( have_posts() ) { ?>

      <div id="show-episodes" class="show-block grid_12 clearfix">

        <a name="episodes"></a>
        <?php

        while ( have_posts() ) : the_post();

        // this is the second part of the operation that determines first or last class based on column divisions. see above.
        $k = $style_classes[$style_index++ % $styles_count];

        $post_classes = array(
          'block',
          'grid_4',
          'clearfix',
          $k
        );

        ?>

        <div id="post-<?php the_ID() ?>" <?php post_class($post_classes) ?>>

          <div class="entry-header">

            <h3 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>

            <?php echo thematic_postheader_postmeta(); ?>

          </div> <!-- end .entry-header -->

          <div class="thumbnail">
            <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_video_thumbnail( $post->ID ); ?></a>
          </div> <!-- end .entry-image -->

          <div class="entry-content">

            <?php

            $args = array(
              'length'      => 10,
              'use_words'     => 1,
              'finish_sentence' => 1,
              'read_more'     => '&rarr;',
              'add_link'    => 1
            );

            the_advanced_excerpt( $args ); ?>

          </div> <!-- end .entry-content -->

        </div><!-- .post -->

        <?php endwhile;

      the_shows_nav(); ?>

      </div> <!-- end #show-episodes -->

    <?php
    } // endif have_posts()

    wp_reset_query();

/*

Disabled feature. Post type is still active but not used since 2011.
-montchr, 2013.04.13

/*    /* SHOW NOTES PAGING */
/*    $show_notes_paged = ( isset( $_GET['show_notes_page'] ) ) ? $_GET['show_notes_page'] : 1;
/*    query_posts($query_string . '&post_type=show_notes&posts_per_page=3&paged=' . $show_notes_paged);
/*
/*    if( have_posts()) { ?>
/*
/*      <div id="show-notes" class="show-block">
/*
/*        <a name="show-notes"></a>
/*
/*        <h2 class="section-header">
/*        <?php the_show(); ?> Blog
/*        </h2>
/*
/*      <?php
/*      // action hook creating the archive loop
/*      thematic_archiveloop();
/*
/*      the_shows_nav();
/*      ?>
/*      </div> <!-- end .show-notes -->
/*
/*    <?php
/*    } // endif have_posts()
/*
/*    // this could be great!!
/*
/*    /* <div class="upcoming-showtimes">
/*    <h2 class="page-title">Watch <?php echo $term->name; ?> on TV</h2>
/*    <p>
/*    <a href="http://tv.sites.templetv.net/schedule/?show=<?php echo $term->slug; ?>">Find showtimes now.</a>
/*    </p>
/*    </div> */
/*
/*    wp_reset_query();

*/


    /* CLIPS PAGING */

    // set class to alpha/omega depending on position in 4 column layout
    // http://wordpress.org/support/topic/adding-different-styling-every-3rd-post
    $style_classes = array('alpha', '', '', 'omega');
    $styles_count = count($style_classes);
    $style_index = 0;

    $clips_paged = ( isset( $_GET['clip_page'] ) ) ? $_GET['clip_page'] : 1;
    query_posts($query_string . '&post_type=clip&posts_per_page=4&paged=' . $clips_paged);

    if( have_posts()) { ?>

      <div id="show-clips" class="show-block grid_12 clearfix">

        <a name="clip"></a>

        <h3 class="section-header"><?php the_show(); ?> Clips</h3>

        <?php

        while ( have_posts() ) : the_post();

        // this is the second part of the operation that determines first or last class based on column divisions. see above.
        $k = $style_classes[$style_index++ % $styles_count];

        $post_classes = array(
          'block',
          'grid_3',
          'clearfix',
          $k
        );

        ?>

        <div id="post-<?php the_ID() ?>" <?php post_class($post_classes); ?>>

          <div class="thumbnail">
            <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_video_thumbnail( $post->ID ); ?></a>
          </div> <!-- end .entry-image -->

          <div class="entry-header">
            <h3 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
            <?php echo thematic_postheader_postmeta(); ?>
          </div> <!-- end .entry-header -->

        </div><!-- .post -->

        <?php endwhile;

        the_shows_nav(); ?>

      </div> <!-- end .show-clips -->
    <?php
    } // endif have_posts

    wp_reset_query();
    ?>

  </div><!-- #content .hfeed -->

<?php

  get_footer();

?>
