jQuery(document).ready(function($) {
	$("#schedule-block .hentry").mouseover( function(){
		$("#schedule-block .hentry.active").removeClass("active");
		$(this).addClass("active");
	});
});