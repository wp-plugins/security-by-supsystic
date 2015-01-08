jQuery(document).ready(function(){
	jQuery('#swrMainOccupancy').knob({
		readOnly: true
	});
	// Make it show with animation
	jQuery({someValue: 0}).animate({
		someValue: swrOccupancy.main
	}, {
		duration: 2000,
		easing:'swing',
		step: function() { // called on every step
			// Update the element's text with rounded-up value:
			jQuery('#swrMainOccupancy').val(Math.ceil(this.someValue)+ '%').trigger('change');
		}
	});
});