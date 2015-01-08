jQuery(document).ready(function(){
	// Files changes scan start btn click
	jQuery('#swrFileScanChangeBtn').click(function(){
		jQuery('#swrFileScanChangeProgressRow').slideDown(300, function(){
			jQuery('#swrFileScanChangeProgressLine')
				.progressInitialize()
				.attr('data-loading', toeLangSwr('Gathering information...'))
				.progressSet( 0.1, true );
			jQuery('#swrFileScanChangeBtn').find('.swrBtnInnerTxt').html( jQuery('#swrFileScanChangeBtn').data('loadtext') );
			jQuery.sendFormSwr({
				btn: jQuery('#swrFileScanChangeBtn')
			,	data: {mod: 'secure_files', action: 'getFilesList', only_files: 1, scan_type: 'modified'}
			,	onSuccess: function(res) {
					if(!res.error) {
						swrFilesChangeScan(res.data.files);
					}
				}
			});
		});
		return false;
	});
	// Files perms scan start btn click
	jQuery('#swrFileScanPermsBtn').click(function(){
		jQuery('#swrFileScanPermsProgressRow').slideDown(300, function(){
			jQuery('#swrFileScanPermsProgressLine')
				.progressInitialize()
				.attr('data-loading', toeLangSwr('Gathering information...'))
				.progressSet( 0.1, true );
			jQuery('#swrFileScanPermsBtn').html( jQuery('#swrFileScanPermsBtn').data('loadtext') );
			jQuery.sendFormSwr({
				btn: jQuery('#swrFileScanPermsBtn')
			,	data: {mod: 'secure_files', action: 'getFilesList', scan_type: 'perms'}
			,	onSuccess: function(res) {
					if(!res.error) {
						swrFilesPermsScan(res.data.files);
					}
				}
			});
		});
		return false;
	});
	var typeSelectHtml = jQuery('#swrFilesIssuesTypeSel').get(0).outerHTML;
	jQuery('#swrFilesIssuesTypeSel').remove();
	// Files issues jqFrid table
	jQuery('#swrFilesIssuesTbl').jqGrid({ 
		url: swrFilesIssuesDataUrl
	,	datatype: 'json'
	,	autowidth: true
	,	shrinkToFit: true
	,	colNames:[toeLangSwr('ID'), toeLangSwr('Name'), toeLangSwr('Path'), toeLangSwr('Modification time'), typeSelectHtml]
	,	colModel:[
			{name: 'id', index: 'id', searchoptions: {sopt: ['eq']}, width: '30', align: 'center'}
		,	{name: 'filename', index: 'filename', searchoptions: {sopt: ['eq', 'like']}, align: 'center'}
		,	{name: 'filepath', index: 'filepath', searchoptions: {sopt: ['eq', 'like']}, width: '400', align: 'center'}
		,	{name: 'last_time_modified_date', index: 'last_time_modified_date', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'type_label', index: 'type_label', searchoptions: {sopt: ['eq']}, align: 'center', sortable: false}
		]
	,	postData: {
			search: {
				type: jQuery('#swrFilesIssuesTypeSel').val()
			,	text_like: jQuery('#swrFilesTblSearchTxt').val()
			}
		}
	,	rowNum:10
	,	rowList:[10, 20, 30, 1000]
	,	pager: '#swrFilesIssuesTblNav'
	,	sortname: 'id'
	,	viewrecords: true
	,	sortorder: 'desc'
	,	jsonReader: { repeatitems : false, id: '0' }
	,	caption: toeLangSwr('Current Blacklist')
	,	height: '100%' 
	,	emptyrecords: toeLangSwr('You have no file issues for now.')
	,	multiselect: true
	,	onSelectRow: function(rowid, e) {
			swrCheckUpdate(jQuery(this).find('tr:eq('+rowid+')').find('input[type=checkbox].cbox'));
			var selectedRowIds = jQuery('#swrFilesIssuesTbl').jqGrid ('getGridParam', 'selarrrow')
			,	totalRows = jQuery('#swrFilesIssuesTbl').getGridParam('reccount')
			,	totalRowsSelected = selectedRowIds.length;
			if(totalRowsSelected) {
				jQuery('#swrFilesIssuesRemoveGroupBtn').removeAttr('disabled');
				if(totalRowsSelected == totalRows) {
					jQuery('#cb_swrFilesIssuesTbl').prop('indeterminate', false);
					jQuery('#cb_swrFilesIssuesTbl').attr('checked', 'checked');
				} else {
					jQuery('#cb_swrFilesIssuesTbl').prop('indeterminate', true);
				}
			} else {
				jQuery('#swrFilesIssuesRemoveGroupBtn').attr('disabled', 'disabled');
				jQuery('#cb_swrFilesIssuesTbl').prop('indeterminate', false);
				jQuery('#cb_swrFilesIssuesTbl').removeAttr('checked');
			}
		}
	,	gridComplete: function(a, b, c) {
			jQuery('#swrFilesIssuesRemoveGroupBtn').attr('disabled', 'disabled');
			jQuery('#cb_swrFilesIssuesTbl').prop('indeterminate', false);
			jQuery('#cb_swrFilesIssuesTbl').removeAttr('checked');
			if(jQuery('#swrFilesIssuesTbl').jqGrid('getGridParam', 'records'))	// If we have at least one row - allow to clear whole list
				jQuery('#swrFilesIssuesClearBtn').removeAttr('disabled');
			else
				jQuery('#swrFilesIssuesClearBtn').attr('disabled', 'disabled');
			// Custom checkbox manipulation
			swrInitCustomCheckRadio('#'+ jQuery(this).attr('id') );
			swrCheckUpdate('#cb_'+ jQuery(this).attr('id'));
		}
	,	loadComplete: function() {
			if (this.p.reccount === 0) {
				jQuery(this).hide();
				jQuery('#swrFilesIssuesTblEmptyMsg').show();
			} else {
				jQuery(this).show();
				jQuery('#swrFilesIssuesTblEmptyMsg').hide();
			}
		}
	});
	jQuery('#swrFilesIssuesTblNavShell').append( jQuery('#swrFilesIssuesTblNav') );
	jQuery('#swrFilesIssuesTblNav').find('.ui-pg-selbox').insertAfter( jQuery('#swrFilesIssuesTblNav').find('.ui-paging-info') );
	jQuery('#swrFilesIssuesTblNav').find('.ui-pg-table td:first').remove();
	jQuery('#swrFilesTblSearchTxt').keyup(function(){
		var searchVal = jQuery.trim( jQuery(this).val() );
		if(searchVal && searchVal != '') {
			swrGridDoListSearch({
				text_like: searchVal
			}, 'swrFilesIssuesTbl');
		}
	});
	swrInitCustomCheckRadio('#swrFilesIssuesTbl_cb');

	jQuery('#swrFilesIssuesTblEmptyMsg').insertAfter(jQuery('#swrFilesIssuesTbl').parent());
	jQuery('#cb_swrFilesIssuesTbl').change(function(){
		jQuery(this).attr('checked') 
			? jQuery('#swrFilesIssuesRemoveGroupBtn').removeAttr('disabled')
			: jQuery('#swrFilesIssuesRemoveGroupBtn').attr('disabled', 'disabled');
	});
	jQuery('#swrFilesIssuesRemoveGroupBtn').click(function(){
		var selectedRowIds = jQuery('#swrFilesIssuesTbl').jqGrid ('getGridParam', 'selarrrow')
		,	listIds = [];
		for(var i in selectedRowIds) {
			var rowData = jQuery('#swrFilesIssuesTbl').jqGrid('getRowData', selectedRowIds[ i ]);
			listIds.push( rowData.id );
		}
		jQuery.sendFormSwr({
			btn: this
		,	data: {mod: 'secure_files', action: 'removeGroup', listIds: listIds}
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#swrFilesIssuesTbl').trigger( 'reloadGrid' );
				}
			}
		});
		return false;
	});
	jQuery('#swrFilesIssuesClearBtn').click(function(){
		if(confirm(toeLangSwr('Clear whole Files Issues test results?'))) {
			jQuery.sendFormSwr({
				btn: this
			,	data: {mod: 'secure_files', action: 'clear'}
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#swrFilesIssuesTbl').trigger( 'reloadGrid' );
						jQuery('<div>'+ toeLangSwr('Don\'t forget to run scan one more time!')+ '</div>').dialog({
							width: '400'
						});
					}
				}
			});
		}
		return false;
	});
});
// Files change scan functions
function swrFilesChangeScan(allFiles, startFrom) {
	if(!startFrom)
		startFrom = 0;
	startFrom = parseInt(startFrom);
	swrFilesScanPortions.change = parseInt(swrFilesScanPortions.change);
	if(startFrom >= allFiles.length) {
		swrFilesChangeScanEnd();
		return;
	}
	var files = []
	,	percent = 0;

	for(var i = startFrom; i < startFrom + swrFilesScanPortions.change; i++) {
		if(typeof(allFiles[i]) !== 'undefined') {
			files.push( allFiles[i] );
		}
	}
	percent = Math.ceil(startFrom * 100  / allFiles.length);
	
	jQuery('#swrFileScanChangeProgressLine').attr('data-loading', percent+ '%').progressSet( percent ? percent : 0.1 );
	jQuery.sendFormSwr({
		data: {mod: 'secure_files', action: 'checkFilesChange', files: files}
	,	btn: jQuery('#swrFileScanChangeBtn')
	,	onSuccess: function(res) {
			if(!res.error) {
				swrFilesChangeScan(allFiles, startFrom + swrFilesScanPortions.change);
			} else {
				jQuery('#swrFileScanChangeProgressRow').slideUp(300);
			}
		}
	});
}
function swrFilesChangeScanEnd() {
	jQuery.sendFormSwr({
		data: {mod: 'secure_files', action: 'checkFilesChangeEnd'}
	,	btn: jQuery('#swrFileScanChangeBtn')
	,	onSuccess: function(res) {
			if(!res.error) {
				jQuery('#swrFileScanChangeProgressLine').attr('data-loading', toeLangSwr('Done'));
				window.location.reload();
			}
		}
	});
}
// Files perms scan functions
function swrFilesPermsScan(allFiles, startFrom) {
	if(!startFrom)
		startFrom = 0;
	startFrom = parseInt(startFrom);
	swrFilesScanPortions.perms = parseInt(swrFilesScanPortions.perms);
	if(startFrom >= allFiles.length) {
		swrFilesPermsScanEnd();
		return;
	}
	var files = []
	,	percent = 0;

	for(var i = startFrom; i < startFrom + swrFilesScanPortions.perms; i++) {
		if(typeof(allFiles[i]) !== 'undefined') {
			files.push( allFiles[i] );
		}
	}
	percent = Math.ceil(startFrom * 100  / allFiles.length);
	
	jQuery('#swrFileScanPermsProgressLine').attr('data-loading', percent+ '%').progressSet( percent ? percent : 0.1 );
	jQuery.sendFormSwr({
		data: {mod: 'secure_files', action: 'checkFilesPerms', files: files}
	,	btn: jQuery('#swrFileScanPermsBtn')
	,	onSuccess: function(res) {
			if(!res.error) {
				swrFilesPermsScan(allFiles, startFrom + swrFilesScanPortions.perms);
			} else {
				jQuery('#swrFileScanChangeProgressRow').slideUp(300);
			}
		}
	});
}
function swrFilesPermsScanEnd() {
	jQuery.sendFormSwr({
		data: {mod: 'secure_files', action: 'checkFilesPermsEnd'}
	,	btn: jQuery('#swrFileScanPermsBtn')
	,	onSuccess: function(res) {
			if(!res.error) {
				jQuery('#swrFileScanPermsProgressLine').attr('data-loading', toeLangSwr('Done'));
				window.location.reload();
			}
		}
	});
}
function swrFilesIssueTypeSelChange() {
	swrGridDoListSearch({
		type: jQuery('#swrFilesIssuesTypeSel').val()
	}, 'swrFilesIssuesTbl');
}