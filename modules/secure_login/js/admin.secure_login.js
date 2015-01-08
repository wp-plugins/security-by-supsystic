jQuery(document).ready(function(){
	jQuery('#swrSecureLoginSaveBtn').click(function(){
		jQuery('#swrSecureLoginForm').submit();
		return false;
	});
	jQuery('#swrSecureLoginForm').submit(function(){
		jQuery(this).sendFormSwr({
			btn: jQuery('#swrSecureLoginSaveBtn')
		});
		return false;
	});
	var adminIpDialog = jQuery('#swrAdminIpLoginDialog').dialog({
		autoOpen: false
	,	height: 490
	,	width: 480
	,	modal: true
	,	buttons: {
			Save: {
				text: toeLangSwr('Save')
			,	click: function(event) {
					jQuery.sendFormSwr({
						btn: event.target
					,	data: {mod: 'secure_login', action: 'saveAdminLoginIpsList', admin_ip_login_list: jQuery('#swrAdminLoginIpListTxt').val()}
					,	onSuccess: function(res) {
							swrCheckAdminLoginIpError();
						}
					});
				}
			}
		,	Cancel: {
				text: toeLangSwr('Cancel')
			,	click: function() {
					adminIpDialog.dialog('close');
				}
			}
		}
	});
	// Add font awesome icon to save btn - not only pretty, but it also will show us loading indicator on save action
	var saveBtn = adminIpDialog.parents('.ui-dialog:first').find('.ui-dialog-buttonpane .ui-button:contains("'+ toeLangSwr('Save')+ '")');
	saveBtn.prepend('<i class="fa fa-fw fa-save" style="padding: 0.6em 0"></i>')
	.find('.ui-button-text').css({
		'float': 'right'
	});
	
	jQuery('#swrAdminIpLoginShowListBtn').click(function(){
		adminIpDialog.dialog('open');
		return false;
	});
	swrCheckAdminLoginIpError();
	jQuery('#opt_valuesadmin_ip_login_enb_check').change(function(){
		swrCheckAdminLoginIpError();
	});
});
function swrCheckAdminLoginIpError() {
	jQuery('#swrAdminIpLoginCurrentError, #swrAdminIpLoginEmptyError').hide();
	if(jQuery('#opt_valuesadmin_ip_login_enb_check').attr('checked')) {
		var ipsList = jQuery.trim( jQuery('#swrAdminLoginIpListTxt').val() );
		if(!ipsList || ipsList == '') {
			jQuery('#swrAdminIpLoginEmptyError').show();
		} else {
			var currentIp = jQuery.trim(jQuery('#swrCurrentIp').html())
			,	currentIpFoundInList = false
			,	ipsList = ipsList.split("\n");
			for(var i in ipsList) {
				if(jQuery.trim(ipsList[ i ]) == currentIp) {
					currentIpFoundInList = true;
					break;
				}
			}
			if(!currentIpFoundInList) {
				jQuery('#swrAdminIpLoginCurrentError').show();
			}
		}
	}
}
