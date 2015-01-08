jQuery(document).ready(function(){
	jQuery('#swrMailTestForm').submit(function(){
		jQuery(this).sendFormSwr({
			btn: jQuery(this).find('button:first')
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#swrMailTestForm').slideUp( 300 );
					jQuery('#swrMailTestResShell').slideDown( 300 );
				}
			}
		});
		return false;
	});
	jQuery('.swrMailTestResBtn').click(function(){
		var result = parseInt(jQuery(this).data('res'));
		jQuery.sendFormSwr({
			btn: this
		,	data: {mod: 'mail', action: 'saveMailTestRes', result: result}
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#swrMailTestResShell').slideUp( 300 );
					jQuery('#'+ (result ? 'swrMailTestResSuccess' : 'swrMailTestResFail')).slideDown( 300 );
				}
			}
		});
		return false;
	});
	jQuery('#swrMailSettingsForm').submit(function(){
		jQuery(this).sendFormSwr({
			btn: jQuery(this).find('button:first')
		});
		return false; 
	});
});