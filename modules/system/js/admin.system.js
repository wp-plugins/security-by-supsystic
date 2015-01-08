jQuery(document).ready(function(){
	jQuery('#swrPlugUsageStatForm').submit(function(){
		jQuery(this).sendFormSwr({
			btn: jQuery(this).find('button')
		});
		return false;
	});
	jQuery('#swrContactForm').submit(function(){
		jQuery(this).sendFormSwr({
			btn: jQuery(this).find('button')
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#swrContactForm').slideUp( 300 );
					jQuery('#swrContactThankyou').slideDown( 300 );
				}
			}
		});
		return false;
	});
});