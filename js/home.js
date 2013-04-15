jQuery(window).load(function() {
	jQuery('.flexslider').flexslider({
		animation           : "slide",
		easing              : "linear",
		direction           : "vertical",
		slideshowSpeed      : 10000,
		pauseOnHover        : true,
		controlNav          : true,
		directionNav        : false
		//controlsContainer   : ".flexslider-control-nav"
	});
});