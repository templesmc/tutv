jQuery(document).ready(function($){
    // Setup slideshow
    if( $("#front-featured-navigation .item-1").length ) {
        autoscroll = 10000;
    } else {
        autoscroll = false;
    }
    $("#front-featured").jCarouselLite({
        btnNext: "#front-featured .next",
        speed: 600,
        visible: 1,
        vertical: true,
        easing: "swing",
        btnGo:["#front-featured-navigation .item-1", "#front-featured-navigation .item-2","#front-featured-navigation .item-3", "#front-featured-navigation .item-4", "#front-featured-navigation .item-5", "#front-featured-navigation .item-6"],
        afterEnd: function(a) {
            $( "#front-featured-navigation .nav" ).removeClass("active");
            $( "#front-featured-navigation .nav." + a.attr("id") ).addClass("active");;
        }
    });
    $( "#front-featured-navigation .nav:first-child" ).addClass("active");
    
    if ( autoscroll ) {
        var x = setInterval("jQuery('#front-featured .next').click();", autoscroll);
    }
    $( "#front-featured-navigation .nav").click(function(){
        clearInterval(x);
    });
    // Schedule browser
    $("#schedule-block .hentry").mouseover( function(){
    	$("#schedule-block .hentry.active").removeClass("active");
    	$(this).addClass("active");
    });

});
