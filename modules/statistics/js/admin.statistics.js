jQuery(document).ready(function(){
	if(swrCurrentStatsTab == 'detailed_login') {
		jQuery('#swrDetailedLoginTbl').jqGrid({ 
			url: swrDetailedLoginDataUrl
		,	datatype: 'json'
		,	autowidth: true
		,	shrinkToFit: true
		,	colNames:[toeLangSwr('ID'), toeLangSwr('User ID'), toeLangSwr('Email'), toeLangSwr('IP'), toeLangSwr('Date')]
		,	colModel:[
				{name: 'id', index: 'id', searchoptions: {sopt: ['eq']}, width: '30', align: 'center'}
			,	{name: 'uid', index: 'uid', searchoptions: {sopt: ['eq']}, width: '30', align: 'center'}
			,	{name: 'email', index: 'email', searchoptions: {sopt: ['eq']}, align: 'center', sortable: false}
			,	{name: 'ip', index: 'ip', searchoptions: {sopt: ['eq']}, align: 'center'}
			,	{name: 'date_created', index: 'date_created', searchoptions: {sopt: ['eq']}, align: 'center'}
			]
		,	rowNum:10
		,	rowList:[10, 20, 30, 1000]
		,	pager: '#swrDetailedLoginTblNav'
		,	sortname: 'id'
		,	viewrecords: true
		,	sortorder: 'desc'
		,	jsonReader: { repeatitems : false, id: '0' }
		,	caption: toeLangSwr('Admins login')
		,	height: '100%' 
		,	emptyrecords: toeLangSwr('You have no data about admin login or now.')
		,	loadComplete: function() {
				if (this.p.reccount === 0) {
					jQuery(this).hide();
					jQuery('#swrClearStats').remove();
					jQuery('#swrDetailedLoginTblEmptyMsg').show();
				} else {
					jQuery(this).show();
					jQuery('#swrDetailedLoginTblEmptyMsg').hide();
				}
			}
		});
	} else if(swrStatRequests && swrStatRequests.graph && swrStatRequests.graph.length && swrStatRequests.graph[0] && swrStatRequests.graph[0].points && swrStatRequests.graph[0].points.length) {
		var plotData = [];
		for(var i = 0; i < swrStatRequests.graph.length; i++) {
			plotData.push([]);
			for(var j = 0; j < swrStatRequests.graph[i]['points'].length; j++) {
				plotData[i].push([swrStatRequests.graph[ i ]['points'][ j ]['date'], parseInt(swrStatRequests.graph[ i ]['points'][ j ]['total_requests'])]);
			}
		}
		jQuery.jqplot('swrStatGraph', plotData, {
			axes: {
				xaxis: {
					label: toeLangSwr('Date')
				,	labelRenderer: jQuery.jqplot.CanvasAxisLabelRenderer
				,	renderer:	jQuery.jqplot.DateAxisRenderer
				,	tickOptions:{formatString:'%b %#d, %Y'},
				}
			,	yaxis: {
					label: toeLangSwr('Visits')
				,	labelRenderer: jQuery.jqplot.CanvasAxisLabelRenderer
				}
			}
		,	highlighter: {
				show: true
			,	sizeAdjust: 7.5
			,	tooltipContentEditor: function(str, seriesIndex, pointIndex, jqPlot) {
					if(swrStatRequests.graph[ seriesIndex ] && swrStatRequests.graph[ seriesIndex ].label) {
						return swrStatRequests.graph[ seriesIndex ].label+ ' '+ str;
					}
					return str;
				}
			}
		,	cursor: {
				show: true
			,	zoom: true
			}
		});
	}
	jQuery('#swrClearStats').click(function(){
		if(confirm('Are you sure want to clear '+ jQuery.trim(jQuery('#containerWrapper .nav-tab.nav-tab-active').html())+ ' statistics?')) {
			jQuery.sendFormSwr({
				btn: this
			,	data: {mod: 'statistics', action: 'clear', tab: jQuery(this).data('tab')}
			,	onSuccess: function(res) {
					if(!res.error) {
						toeReload();
					}
				}
			});
		}
		return false;
	});
});