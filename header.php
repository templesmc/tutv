<!doctype html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>" />
  <!-- Google Chrome Frame for IE -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title><?php echo tutv_title(); ?></title>

  <link rel="profile" href="http://gmpg.org/xfn/11" />
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

  <!-- icons & favicons (for more: http://www.jonathantneal.com/blog/understand-the-favicon/) -->
  <!-- <link rel="apple-touch-icon" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/apple-icon-touch.png"> -->
  <link rel="icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico">
  <!--[if IE]>
    <link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico">
  <![endif]-->

  <?php wp_head(); ?>

  <!-- rss link -->
  <link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo('name'); ?> Feed" href="<?php echo esc_url(get_feed_link()); ?>">

  <!--[if (gte IE 6)&(lte IE 8)]>
    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/vendor/selectivizr.min.js"></script>
    <noscript><link rel="stylesheet" href="[fallback css]" /></noscript>
  <![endif]-->

  <script>
    // Paul Irish's FOUT fix: hide page until fonts loaded (or for setTimout value)
    // http://paulirish.com/2009/fighting-the-font-face-fout/
    (function(){
      // if firefox 3.5+, hide content till load (or 3 seconds) to prevent FOUT
      var d = document, e = d.documentElement, s = d.createElement('style');
      if (e.style.MozTransform === ''){ // gecko 1.9.1 inference
      s.textContent = 'body{visibility:hidden}';
      var r = document.getElementsByTagName('script')[0];
      r.parentNode.insertBefore(s, r);
      function f(){ s.parentNode && s.parentNode.removeChild(s); }
      addEventListener('load',f,false);
      setTimeout(f,3000);
      }
    })();
  </script>

</head>

<body class="<?php body_class() ?>">

<div id="wrapper" class="hfeed clearfix">

  <div id="header" class="clearfix">

    <div id="header-inner" class="container_12 clearfix">

      <div id="branding">
        <div id="blog-title"><span><a href="<?php bloginfo('url') ?>/" title="<?php bloginfo('name') ?>" rel="home"><?php bloginfo('name') ?></a></span></div>
      </div>

      <div class="header-search">
        <?php get_search_form(); ?>
      </div>

      <div id="access">
        <div class="skip-link"><a href="#content" title="<?php _e('Skip navigation to the content', 'thematic'); ?>"><?php _e('Skip to content', 'thematic'); ?></a></div>
        <?php wp_nav_menu('menu=header&sort_column=menu_order&container_class=menu&menu_class=sf-menu') ?>
      </div><!-- #access -->

    </div> <!-- end #header-inner -->

  </div><!-- #header-->

  <?php

  // action hook for placing content below the theme header
  thematic_belowheader();

  ?>

  <div id="main" class="clearfix">

    <?php thematic_abovecontainer(); ?>

    <div id="container" class="container_12 clearfix">
