jQuery(document).ready( function($) {
	var duration = 10000,
	transition_speed = 'slow',
	counter = 1,
	num_slides = $("#slideshow .slide").length,
	interrupted = false,
	previous;
	function SplitID (element) {
		return element.attr('id').split('-').pop();
	}
	$.fn.transition = function() {
		if (interrupted) {
			return;
		}
		
		previous = counter - 1;
		
		if (previous < 1) {
			previous = num_slides;
		}
		
		$('div#slide-' + previous).fadeOut(transition_speed, function() {
			$('div.nav').each(function(){$(this).removeClass('nav-active');});
			$('div#nav-' + counter).addClass('nav-active');
			$('div#slide-' + counter).fadeIn(transition_speed);
			
			counter++;
			
			if (counter > num_slides) {
				counter = 1;
			}
			setTimeout('$.fn.transition();', duration);
		});
	};
	$('div.nav').click(function() {
		interrupted = true;
		if( !$(this).hasClass('nav-active') ) {
			$('div.slide').fadeOut(transition_speed);
			$('div.nav').removeClass('nav-active');
			
			$('div#slide-' + SplitID($(this))).fadeIn(transition_speed);
			$(this).addClass('nav-active');
		}
	});
	
	$.fn.transition();

});