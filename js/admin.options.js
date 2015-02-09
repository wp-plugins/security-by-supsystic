var swrAdminFormChanged = [];
window.onbeforeunload = function(){
	// If there are at lease one unsaved form - show message for confirnation for page leave
	if(swrAdminFormChanged.length)
		return 'Some changes were not-saved. Are you sure you want to leave?';
};
jQuery(document).ready(function(){
	if(typeof(swrActiveTab) != 'undefined' && swrActiveTab != 'main_page' && jQuery('#toplevel_page_security-wp-supsystic').hasClass('wp-has-current-submenu')) {
		var subMenus = jQuery('#toplevel_page_security-wp-supsystic').find('.wp-submenu li');
		subMenus.removeClass('current').each(function(){
			if(jQuery(this).find('a[href*="&tab='+ swrActiveTab+ '"]').size()) {
				jQuery(this).addClass('current');
			}
		});
	}
	
	// Timeout - is to count only user changes, because some changes can be done auto when form is loaded
	setTimeout(function() {
		// If some changes was made in those forms and they were not saved - show message for confirnation before page reload
		var formsPreventLeave = [];
		if(formsPreventLeave && formsPreventLeave.length) {
			jQuery('#'+ formsPreventLeave.join(', #')).find('input,select').change(function(){
				var formId = jQuery(this).parents('form:first').attr('id');
				changeAdminFormSwr(formId);
			});
			jQuery('#'+ formsPreventLeave.join(', #')).find('input[type=text],textarea').keyup(function(){
				var formId = jQuery(this).parents('form:first').attr('id');
				changeAdminFormSwr(formId);
			});
			jQuery('#'+ formsPreventLeave.join(', #')).submit(function(){
				adminFormSavedSwr( jQuery(this).attr('id') );
			});
		}
	}, 1000);
	if(jQuery('.swrInputsWithDescrForm').size()) {
		jQuery('.swrInputsWithDescrForm').find('input[type=checkbox][data-optkey]').change(function(){
			var optKey = jQuery(this).data('optkey')
			,	descShell = jQuery('#swrFormOptDetails_'+ optKey);
			if(descShell.size()) {
				if(jQuery(this).attr('checked')) {
					descShell.slideDown( 300 );
				} else {
					descShell.slideUp( 300 );
				}
			}
		}).trigger('change');
	}
	// Tooltipster init
	jQuery('.supsystic-tooltip').tooltipster({
		contentAsHTML: true
	,	interactive: true
	,	speed: 250
	,	delay: 0
	,	animation: 'swing'
	,	position: 'top-left'
	,	maxWidth: 450
	});

	swrInitStickyItem();
	swrInitCustomCheckRadio();
	//swrInitCustomSelect();
	// If there are only one panel for whole page - let's make it's height equals to navigation sidebar height
	if(jQuery('.supsystic-item.supsystic-panel').size() == 1) {
		var fullPanelHeight = jQuery('.supsystic-navigation:first').height() - (jQuery('.supsystic-item.supsystic-panel').offset().top - jQuery('.supsystic-navigation:first').offset().top);
		jQuery('.supsystic-item.supsystic-panel').css({
			'min-height': fullPanelHeight
		}).attr('data-dev-hint', 'height is calculated in admin.options.js');
	}
});
function changeAdminFormSwr(formId) {
	if(jQuery.inArray(formId, swrAdminFormChanged) == -1)
		swrAdminFormChanged.push(formId);
}
function adminFormSavedSwr(formId) {
	if(swrAdminFormChanged.length) {
		for(var i in swrAdminFormChanged) {
			if(swrAdminFormChanged[i] == formId) {
				swrAdminFormChanged.pop(i);
			}
		}
	}
}
function checkAdminFormSaved() {
	if(swrAdminFormChanged.length) {
		if(!confirm(toeLangSwr('Some changes were not-saved. Are you sure you want to leave?'))) {
			return false;
		}
		swrAdminFormChanged = [];	// Clear unsaved forms array - if user wanted to do this
	}
	return true;
}
function isAdminFormChanged(formId) {
	if(swrAdminFormChanged.length) {
		for(var i in swrAdminFormChanged) {
			if(swrAdminFormChanged[i] == formId) {
				return true;
			}
		}
	}
	return false;
}
/*Some items should be always on users screen*/
function swrInitStickyItem() {
	jQuery(window).scroll(function(){
		var stickiItemsSelectors = ['.ui-jqgrid-hdiv', '.supsystic-sticky']
		,	elementsUsePaddingNext = ['.ui-jqgrid-hdiv']	// For example - if we stick row - then all other should not offest to top after we will place element as fixed
		,	wpTollbarHeight = 32
		,	wndScrollTop = jQuery(window).scrollTop() + wpTollbarHeight
		,	footer = jQuery('.swrAdminFooterShell')
		,	footerHeight = footer && footer.size() ? footer.height() : 0
		,	docHeight = jQuery(document).height();
		for(var i = 0; i < stickiItemsSelectors.length; i++) {
			var element = jQuery(stickiItemsSelectors[ i ]);
			if(element && element.size()) {
				var scrollMinPos = element.offset().top
				,	prevScrollMinPos = parseInt(element.data('scrollMinPos'))
				,	useNextElementPadding = toeInArray(stickiItemsSelectors[ i ], elementsUsePaddingNext) !== -1;
				if(wndScrollTop > scrollMinPos && !element.hasClass('supsystic-sticky-active')) {
					element.addClass('supsystic-sticky-active').data('scrollMinPos', scrollMinPos).css({
						'top': wpTollbarHeight
					});
					if(useNextElementPadding) {
						element.addClass('supsystic-sticky-active-bordered');
						var nextElement = element.next();
						if(nextElement && nextElement.size()) {
							nextElement.data('prevPaddingTop', nextElement.css('padding-top'));
							nextElement.css({
								'padding-top': element.height()
							});
						}
					}
				} else if(!isNaN(prevScrollMinPos) && wndScrollTop <= prevScrollMinPos) {
					element.removeClass('supsystic-sticky-active').data('scrollMinPos', 0).css({
						'top': 0
					});
					if(useNextElementPadding) {
						element.removeClass('supsystic-sticky-active-bordered');
						var nextElement = element.next();
						if(nextElement && nextElement.size()) {
							var nextPrevPaddingTop = parseInt(nextElement.data('prevPaddingTop'));
							if(isNaN(nextPrevPaddingTop))
								nextPrevPaddingTop = 0;
							nextElement.css({
								'padding-top': nextPrevPaddingTop
							});
						}
					}
				} else {
					if(element.hasClass('supsystic-sticky-active') && footerHeight) {
						var elementHeight = element.height()
						,	heightCorrection = 32
						,	topDiff = docHeight - footerHeight - (wndScrollTop + elementHeight + heightCorrection);
						//console.log(topDiff);
						if(topDiff < 0) {
							//console.log(topDiff, elementTop + topDiff);
							element.css({
								'top': wpTollbarHeight + topDiff
							});
						} else {
							element.css({
								'top': wpTollbarHeight
							});
						}
					}
				}
			}
		}
	});
}
function swrInitCustomCheckRadio(selector) {
	if(!selector)
		selector = document;
	jQuery(selector).find('input').iCheck('destroy').iCheck({
		checkboxClass: 'icheckbox_minimal'
	,	radioClass: 'iradio_minimal'
	}).on('ifChanged', function(e){
		// for checkboxHiddenVal type, see class htmlSwr
		jQuery(this).trigger('change');
		if(jQuery(this).hasClass('cbox')) {
			var parentRow = jQuery(this).parents('.jqgrow:first');
			if(parentRow && parentRow.size()) {
				jQuery(this).parents('td:first').trigger('click');
			} else {
				var checkId = jQuery(this).attr('id');
				if(checkId && checkId != '' && strpos(checkId, 'cb_') === 0) {
					var parentTblId = str_replace(checkId, 'cb_', '');
					if(parentTblId && parentTblId != '' && jQuery('#'+ parentTblId).size()) {
						jQuery('#'+ parentTblId).find('input[type=checkbox]').iCheck('update');
					}
				}
			}
		}
	}).on('ifClicked', function(e){
		jQuery(this).trigger('click');
	});
}
function swrCheckUpdate(checkbox) {
	jQuery(checkbox).iCheck('update');
}
function swrCheckUpdateArea(selector) {
	jQuery(selector).find('input[type=checkbox]').iCheck('update');
}
/*function swrInitCustomSelect(selector, force, checkTblHeaders) {
	if(!selector)
		selector = document;
	var selectsForCustomize = jQuery(selector).find('select');
	if(!force) {
		selectsForCustomize = selectsForCustomize.not('.supsystic-no-customize')
	}
	if(checkTblHeaders) {
		selectsForCustomize.each(function(){
			jQuery(this).data('originalOffsetTop', jQuery(this).offset().top);
			jQuery(this).data('originalOffsetLeft', jQuery(this).offset().left);
		});
	}
	selectsForCustomize.chosen({
		disable_search_threshold: 10
	});
	if(checkTblHeaders) {
		selectsForCustomize.each(function(){
			jQuery(this).next('.chosen-container').css({
				'position': 'absolute'
			,	'top': jQuery(this).data('originalOffsetTop')
			,	'left': jQuery(this).data('originalOffsetLeft')
			});
		});
	}
}*/