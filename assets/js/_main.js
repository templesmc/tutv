/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can
 * always reference jQuery with $, even when in .noConflict() mode.
 *
 * Google CDN, Latest jQuery
 * To use the default WordPress version of jQuery, go to lib/config.php and
 * remove or comment out: add_theme_support('jquery-cdn');
 * ======================================================================== */

(function($) {

// Use this variable to set up the common and page specific functions. If you
// rename this variable, you will also need to rename the namespace below.
var TUTV = {
  // All pages
  common: {
    init: function() {

      $("ul.sf-menu").superfish({
          delay:       400,                               // delay on mouseout
          animation:   {opacity:'show',height:'show'},    // fade-in and slide-down animation
          speed:       'fast',                            // faster animation speed
          autoArrows:  false,                             // disable generation of arrow mark-up
          dropShadows: false                              // disable drop shadows
      });
    }
  },
  // Home page
  home: {
    init: function() {

      $('.flexslider').flexslider({
        animation           : "slide",
        easing              : "linear",
        direction           : "vertical",
        slideshowSpeed      : 6000,
        pauseOnHover        : true,
        controlNav          : true,
        directionNav        : false
      });

      // Schedule browser
      $("#schedule-block .hentry").mouseover( function(){
        $("#schedule-block .hentry.active").removeClass("active");
        $(this).addClass("active");
      });

      // TextFill - for schedule block titles
      //
      // Note that this was originally called outside of `$(document).ready`
      $(".schedule-item").textfill({
        maxFontPixels: 14,
        debug: false,
        innerTag: ".entry-info-inner",
        explicitHeight: 30
        //explicitWidth: 276
      });
    }
  },
  // About us page, note the change from about-us to about_us.
  slug_shows: {
    init: function() {
      var $wall = $('.wall') ;

      $wall.isotope();

      $('#filtering-nav-container a').click(function(){
        var selector = $(this).attr('data-filter');
        $wall.isotope({ filter: selector });
        return false;
      });
    }
  },
  slug_watch_live: {
    init: function() {

      $("#schedule-block .hentry").mouseover( function(){
        $("#schedule-block .hentry.active").removeClass("active");
        $(this).addClass("active");
      });

    }
  }
};

// The routing fires all common scripts, followed by the page specific scripts.
// Add additional events for more control over timing e.g. a finalize event
var UTIL = {
  fire: function(func, funcname, args) {
    var namespace = TUTV;
    funcname = (funcname === undefined) ? 'init' : funcname;
    if (func !== '' && namespace[func] && typeof namespace[func][funcname] === 'function') {
      namespace[func][funcname](args);
    }
  },
  loadEvents: function() {
    UTIL.fire('common');

    $.each(document.body.className.replace(/-/g, '_').split(/\s+/),function(i,classnm) {
      UTIL.fire(classnm);
    });
  }
};

$(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.
