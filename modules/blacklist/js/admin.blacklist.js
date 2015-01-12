jQuery(document).ready(function(){
	var typeSelectHtml = jQuery('#swrBlacklistTypeSel').get(0).outerHTML;
	jQuery('#swrBlacklistTypeSel').remove();
	jQuery('#swrBlacklistTbl').jqGrid({ 
		url: swrBlacklistDataUrl
	,	datatype: 'json'
	,	autowidth: true
	,	shrinkToFit: true
	,	colNames:[toeLangSwr('ID'), toeLangSwr('IP'), toeLangSwr('Date'), typeSelectHtml, toeLangSwr('Action')]
	,	colModel:[
			{name: 'id', index: 'id', searchoptions: {sopt: ['eq']}, width: '30', align: 'center'}
		,	{name: 'ip', index: 'ip', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'date_created', index: 'date_created', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'type_label', index: 'type_label', searchoptions: {sopt: ['eq']}, align: 'center', sortable: false}
		,	{name: 'action', index: 'action', sortable: false, search: false, align: 'center'}
		]
	,	postData: {
			search: {
				type: jQuery('#swrBlacklistTypeSel').val()
			,	text_like: jQuery('#swrBlacklistTblSearchTxt').val()
			}
		}
	,	rowNum:10
	,	rowList:[10, 20, 30, 1000]
	,	pager: '#swrBlacklistTblNav'
	,	sortname: 'id'
	,	viewrecords: true
	,	sortorder: 'desc'
	,	jsonReader: { repeatitems : false, id: '0' }
	,	caption: toeLangSwr('Current Blacklist')
	,	height: '100%' 
	,	emptyrecords: toeLangSwr('You have no data in blacklist for now.')
	,	multiselect: true
	,	onSelectRow: function(rowid, e) {
			var selectedRowIds = jQuery('#swrBlacklistTbl').jqGrid ('getGridParam', 'selarrrow')
			,	totalRows = jQuery('#swrBlacklistTbl').getGridParam('reccount')
			,	totalRowsSelected = selectedRowIds.length;
			if(totalRowsSelected) {
				jQuery('#swrBlacklistRemoveGroupBtn').removeAttr('disabled');
				if(totalRowsSelected == totalRows) {
					jQuery('#cb_swrBlacklistTbl').prop('indeterminate', false);
					jQuery('#cb_swrBlacklistTbl').attr('checked', 'checked');
				} else {
					jQuery('#cb_swrBlacklistTbl').prop('indeterminate', true);
				}
			} else {
				jQuery('#swrBlacklistRemoveGroupBtn').attr('disabled', 'disabled');
				jQuery('#cb_swrBlacklistTbl').prop('indeterminate', false);
				jQuery('#cb_swrBlacklistTbl').removeAttr('checked');
			}
			swrCheckUpdate(jQuery(this).find('tr:eq('+rowid+')').find('input[type=checkbox].cbox'));
			swrCheckUpdate('#cb_'+ jQuery(this).attr('id'));
		}
	,	gridComplete: function(a, b, c) {
			jQuery('#swrBlacklistRemoveGroupBtn').attr('disabled', 'disabled');
			jQuery('#cb_swrBlacklistTbl').prop('indeterminate', false);
			jQuery('#cb_swrBlacklistTbl').removeAttr('checked');
			if(jQuery('#swrBlacklistTbl').jqGrid('getGridParam', 'records'))	// If we have at least one row - allow to clear whole list
				jQuery('#swrBlacklistClearBtn').removeAttr('disabled');
			else
				jQuery('#swrBlacklistClearBtn').attr('disabled', 'disabled');
			// Custom checkbox manipulation
			swrInitCustomCheckRadio('#'+ jQuery(this).attr('id') );
			swrCheckUpdate('#cb_'+ jQuery(this).attr('id'));
		}
	,	loadComplete: function() {
			if (this.p.reccount === 0) {
				jQuery(this).hide();
				jQuery('#swrBlacklistTblEmptyMsg').show();
			} else {
				jQuery(this).show();
				jQuery('#swrBlacklistTblEmptyMsg').hide();
			}
		}
	});
	jQuery('#swrBlacklistTblNavShell').append( jQuery('#swrBlacklistTblNav') );
	jQuery('#swrBlacklistTblNav').find('.ui-pg-selbox').insertAfter( jQuery('#swrBlacklistTblNav').find('.ui-paging-info') );
	jQuery('#swrBlacklistTblNav').find('.ui-pg-table td:first').remove();
	jQuery('#swrBlacklistTblSearchTxt').keyup(function(){
		var searchVal = jQuery.trim( jQuery(this).val() );
		if(searchVal && searchVal != '') {
			swrGridDoListSearch({
				text_like: searchVal
			}, 'swrBlacklistTbl');
		}
	});
	swrInitCustomCheckRadio('#swrBlacklistTbl_cb');
	// Custom selects manipulation
	//swrInitCustomSelect('#swrBlacklistTblNav, #swrBlacklistTbl_type_label', true, true);
	
	
	jQuery('#swrBlacklistTblEmptyMsg').insertAfter(jQuery('#swrBlacklistTbl').parent());
	jQuery('#swrBlacklistTbl').jqGrid('navGrid', '#swrBlacklistTblNav', {edit: false, add: false, del: false});
	jQuery('#cb_swrBlacklistTbl').change(function(){
		jQuery(this).attr('checked') 
			? jQuery('#swrBlacklistRemoveGroupBtn').removeAttr('disabled')
			: jQuery('#swrBlacklistRemoveGroupBtn').attr('disabled', 'disabled');
	});
	jQuery('#swrBlacklistRemoveGroupBtn').click(function(){
		var selectedRowIds = jQuery('#swrBlacklistTbl').jqGrid ('getGridParam', 'selarrrow')
		,	listIds = [];
		for(var i in selectedRowIds) {
			var rowData = jQuery('#swrBlacklistTbl').jqGrid('getRowData', selectedRowIds[ i ]);
			listIds.push( rowData.id );
		}
		jQuery.sendFormSwr({
			btn: this
		,	data: {mod: 'blacklist', action: 'removeGroup', listIds: listIds}
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#swrBlacklistTbl').trigger( 'reloadGrid' );
				}
			}
		});
		return false;
	});
	
	swrBlacklistInitAddByIpDialog();
	swrBlacklistInitAddByCountryDialog();
	swrBlacklistInitAddByBrowserDialog();
	jQuery('#swrBlacklistClearBtn').click(function(){
		if(confirm(toeLangSwr('Clear whole blacklist?'))) {
			jQuery.sendFormSwr({
				btn: this
			,	data: {mod: 'blacklist', action: 'clear'}
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#swrBlacklistTbl').trigger( 'reloadGrid' );
					}
				}
			});
		}
		return false;
	});
	// Blocked countries msg check
	swrCheckBlockedCountriesCnt();
	// Blocked browsers msg check
	swrCheckBlockedBrowserCnt();
	jQuery('.chosen').chosen();
});
function swrBlacklistInitAddByBrowserDialog() {
	var $container = jQuery('#swrBlacklistAddByBrowserDlg').dialog({
		modal:    true
	,	autoOpen: false
	,	width: 480
	,	height: 220
	,	buttons:  {
			OK: function() {
				jQuery('#swrBlacklistAddByBrowserForm').submit();
			}
		,	Cancel: function() {
				$container.dialog('close');
			}
		}
	});
	jQuery('.swrBlacklistAddByBrowserBtn').click(function(){
		jQuery('#swrBlacklistAddBrowserMsg').html('');
		$container.dialog('open');
		return false;
	});
	jQuery('#swrBlacklistAddByBrowserForm').submit(function(){
		var canContinue = true;
		/*if(strpos(ips, currentIp) !== false) {
			canContinue = confirm(toeLangSwr('You entered your current IP - to blacklist.'+
				' This mean that your current computer will be blocked right after you will save this form.'+
				' Are you sure want to continue?'));
		}*/
		if(canContinue) {
			jQuery(this).sendFormSwr({
				msgElID: 'swrBlacklistAddBrowserMsg'
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#swrBlacklistTbl').trigger( 'reloadGrid' );
						swrCheckBlockedBrowserCnt();
					}
				}
			});
		}
		return false;
	});
}
function swrBlacklistInitAddByCountryDialog() {
	var $container = jQuery('#swrBlacklistAddByCountryDlg').dialog({
		modal:    true
	,	autoOpen: false
	,	width: 640
	,	height: 410
	,	buttons:  {
			OK: function() {
				jQuery('#swrBlacklistAddByCountryForm').submit();
			}
		,	Cancel: function() {
				$container.dialog('close');
			}
		}
	,	open: function() {
			jQuery('#swrBlacklistAddByCountryForm').find('.chosen-container').width('100%');
		}
	});
	jQuery('.swrBlacklistAddByCountryBtn').click(function(){
		jQuery('#swrBlacklistAddCountryMsg').html('');
		$container.dialog('open');
		return false;
	});
	jQuery('#swrBlacklistAddByCountryForm').submit(function(){
		var canContinue = true;
		/*if(strpos(ips, currentIp) !== false) {
			canContinue = confirm(toeLangSwr('You entered your current IP - to blacklist.'+
				' This mean that your current computer will be blocked right after you will save this form.'+
				' Are you sure want to continue?'));
		}*/
		if(canContinue) {
			jQuery(this).sendFormSwr({
				msgElID: 'swrBlacklistAddCountryMsg'
			,	onSuccess: function(res) {
					if(!res.error) {
						
						jQuery('#swrBlacklistTbl').trigger( 'reloadGrid' );
						swrCheckBlockedCountriesCnt();
					}
				}
			});
		}
		return false;
	});
}
function swrBlacklistInitAddByIpDialog() {
	var $container = jQuery('#swrBlacklistAddByIpDlg').dialog({
		modal:    true
	,	autoOpen: false
	,	width: 460
	,	height: 400
	,	buttons:  {
			OK: function() {
				jQuery('#swrBlacklistAddByIpForm').submit();
			}
		,	Cancel: function() {
				$container.dialog('close');
			}
		}
	});
	jQuery('.swrBlacklistAddByIpBtn').click(function(){
		jQuery('#swrBlacklistAddByIpMsg').html('');
		$container.dialog('open');
		return false;
	});
	jQuery('#swrBlacklistAddByIpForm').submit(function(){
		var ips = jQuery(this).find('[name=ips]').val()
		,	currentIp = jQuery('#swrCurrentIp').html()
		,	canContinue = true;
		if(strpos(ips, currentIp) !== false) {
			canContinue = confirm(toeLangSwr('You entered your current IP - to blacklist.'+
				' This mean that your current computer will be blocked right after you will save this form.'+
				' Are you sure want to continue?'));
		}
		if(canContinue) {
			jQuery(this).sendFormSwr({
				msgElID: 'swrBlacklistAddByIpMsg'
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#swrBlacklistTbl').trigger( 'reloadGrid' );
						jQuery('#swrBlacklistAddByIpForm').find('textarea').val('');
					}
				}
			});
		}
		return false;
	});
}
function swrBlacklistRemoveRow(id, link) {
	var msgEl = jQuery('<span />').insertAfter( link );
	jQuery.sendFormSwr({
		msgElID: msgEl
	,	data: {mod: 'blacklist', action: 'remove', id: id}
	,	onSuccess: function(res) {
			if(!res.error) {
				jQuery('#swrBlacklistTbl').trigger( 'reloadGrid' );
			}
		}
	});
}
function swrCheckBlockedCountriesCnt() {
	var blockedCountriesCnt = jQuery('#swrBlacklistAddByCountryForm input[name="country_ids[]"] option:selected').size();
	if(blockedCountriesCnt) {
		jQuery('#swrBlockedCountriesMsg').show().find('#swrBlockedCountriesCount').html( blockedCountriesCnt );
	} else {
		jQuery('#swrBlockedCountriesMsg').hide();
	}
}
function swrCheckBlockedBrowserCnt() {
	var blockedBrowsersCnt = jQuery('#swrBlacklistAddByBrowserForm input[name="browser_names[]"]:checked').size();
	if(blockedBrowsersCnt) {
		jQuery('#swrBlockedBrowsersMsg').show().find('#swrBlockedBrowsersCount').html( blockedBrowsersCnt );
	} else {
		jQuery('#swrBlockedBrowsersMsg').hide();
	}
}
function swrBlacklistTypeSelChange() {
	swrGridDoListSearch({
		type: jQuery('#swrBlacklistTypeSel').val()
	}, 'swrBlacklistTbl');
}