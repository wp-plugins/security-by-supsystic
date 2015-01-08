jQuery(document).ready(function(){
	jQuery('#swrSecureHideSaveBtn').click(function(){
		jQuery('#swrSecureHideForm').submit();
		return false;
	});
	jQuery('#swrSecureHideForm').submit(function(){
		jQuery(this).sendFormSwr({
			btn: jQuery('#swrSecureHideSaveBtn')
		});
		return false;
	});
});
