jQuery(document).ready(function($) {
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
});