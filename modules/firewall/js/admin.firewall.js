jQuery(document).ready(function(){
	jQuery('#swrFirewallSaveBtn').click(function(){
		jQuery('#swrFirewallForm').submit();
		return false;
	});
	jQuery('#swrFirewallForm').submit(function(){
		jQuery(this).sendFormSwr({
			btn: jQuery('#swrFirewallSaveBtn')
		});
		return false;
	});
});
