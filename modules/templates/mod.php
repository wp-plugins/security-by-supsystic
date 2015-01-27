<?php
class templatesSwr extends moduleSwr {
    protected $_styles = array();
    public function init() {
        if (is_admin() && ($isAdminPlugOptsPage = frameSwr::_()->isAdminPlugOptsPage())) {
			$this->loadCoreJs();
			$this->loadAdminCoreJs();
			$this->loadCoreCss();
			$this->loadAdminCoreCss();
			$this->loadChosenSelects();
			frameSwr::_()->addScript('adminOptionsSwr', SWR_JS_PATH. 'admin.options.js', array(), false, true);
			//add_action('admin_enqueue_scripts', array($this, 'loadMediaScripts'));
		}
        parent::init();
    }
	public function loadMediaScripts() {
		wp_enqueue_media();
	}
	public function loadAdminCoreJs() {
		frameSwr::_()->addScript('jquery-ui-dialog');
		frameSwr::_()->addScript('tooltipster', SWR_JS_PATH. 'jquery.tooltipster.min.js');
		frameSwr::_()->addScript('icheck', SWR_JS_PATH. 'icheck.min.js');
	}
	public function loadCoreJs() {
		frameSwr::_()->addScript('jquery');

		frameSwr::_()->addScript('commonSwr', SWR_JS_PATH. 'common.js');
		frameSwr::_()->addScript('coreSwr', SWR_JS_PATH. 'core.js');

		$ajaxurl = admin_url('admin-ajax.php');
		$jsData = array(
			'siteUrl'					=> SWR_SITE_URL,
			'imgPath'					=> SWR_IMG_PATH,
			'cssPath'					=> SWR_CSS_PATH,
			'loader'					=> SWR_LOADER_IMG, 
			'close'						=> SWR_IMG_PATH. 'cross.gif', 
			'ajaxurl'					=> $ajaxurl,
			'options'					=> frameSwr::_()->getModule('options')->getAllowedPublicOptions(),
			'SWR_CODE'					=> SWR_CODE,
			'ball_loader'				=> SWR_IMG_PATH. 'ajax-loader-ball.gif',
			'ok_icon'					=> SWR_IMG_PATH. 'ok-icon.png',
		);
		$jsData['allCheckRegPlugs']	= modInstallerSwr::getCheckRegPlugs();

		$jsData = dispatcherSwr::applyFilters('jsInitVariables', $jsData);
		frameSwr::_()->addJSVar('coreSwr', 'SWR_DATA', $jsData);
	}
	public function loadAdminCoreCss() {
		$this->_addStylesArr(array(
			'dashicons'			=> array('for' => 'admin'),
			'tooltipster'		=> array('path' => SWR_CSS_PATH. 'tooltipster.css', 'for' => 'admin'),
			'icheck'			=> array('path' => SWR_CSS_PATH. 'jquery.icheck.css', 'for' => 'admin'),
		));
		$this->loadFontAwesome();
	}
	public function loadCoreCss() {
		$this->_addStylesArr(array(
			'styleSwr'			=> array('path' => SWR_CSS_PATH. 'style.css', 'for' => 'admin'), 
			'supsystic-uiSwr'	=> array('path' => SWR_CSS_PATH. 'supsystic-ui.css', 'for' => 'admin'), 
			'bootstrap-alerts'	=> array('path' => SWR_CSS_PATH. 'bootstrap-alerts.css', 'for' => 'admin'),
		));
	}
	private function _addStylesArr( $addStyles ) {
		foreach($addStyles as $s => $sInfo) {
			if(!empty($sInfo['path'])) {
				frameSwr::_()->addStyle($s, $sInfo['path']);
			} else {
				frameSwr::_()->addStyle($s);
			}
		}
	}
	public function loadJqueryUi() {
		frameSwr::_()->addStyle('jquery-ui', SWR_CSS_PATH. 'jquery-ui.min.css');
		frameSwr::_()->addStyle('jquery-ui.structure', SWR_CSS_PATH. 'jquery-ui.structure.min.css');
		frameSwr::_()->addStyle('jquery-ui.theme', SWR_CSS_PATH. 'jquery-ui.theme.min.css');
	}
	public function loadJqGrid() {
		$this->loadJqueryUi();
		frameSwr::_()->addScript('jq-grid', SWR_JS_PATH. 'jquery.jqGrid.min.js');
		frameSwr::_()->addStyle('jq-grid', SWR_CSS_PATH. 'ui.jqgrid.css');
		$langToLoad = strlen(SWR_WPLANG) > 2 ? substr(SWR_WPLANG, 0, 2) : SWR_WPLANG;
		if(!file_exists(SWR_JS_DIR. 'i18n'. DS. 'grid.locale-'. $langToLoad. '.js')) {
			$langToLoad = 'en';
		}
		frameSwr::_()->addScript('jq-grid-lang', SWR_JS_PATH. 'i18n/grid.locale-'. $langToLoad. '.js');
	}
	public function loadFontAwesome() {
		frameSwr::_()->addStyle('font-awesomeSwr', SWR_CSS_PATH. 'font-awesome.css');
	}
	public function loadChosenSelects() {
		frameSwr::_()->addStyle('jquery.chosen', SWR_CSS_PATH. 'chosen.min.css');
		frameSwr::_()->addScript('jquery.chosen', SWR_JS_PATH. 'chosen.jquery.min.js');
	}
	public function loadJqplot() {
		$jqplotDir = 'jqplot/';
		
		frameSwr::_()->addStyle('jquery.jqplot', SWR_CSS_PATH. 'jquery.jqplot.min.css');
		
		frameSwr::_()->addScript('jplot', SWR_JS_PATH. $jqplotDir. 'jquery.jqplot.min.js');
		frameSwr::_()->addScript('jqplot.canvasAxisLabelRenderer', SWR_JS_PATH. $jqplotDir. 'jqplot.canvasAxisLabelRenderer.min.js');
		frameSwr::_()->addScript('jqplot.canvasTextRenderer', SWR_JS_PATH. $jqplotDir. 'jqplot.canvasTextRenderer.min.js');
		frameSwr::_()->addScript('jqplot.dateAxisRenderer', SWR_JS_PATH. $jqplotDir. 'jqplot.dateAxisRenderer.min.js');
		frameSwr::_()->addScript('jqplot.canvasAxisTickRenderer', SWR_JS_PATH. $jqplotDir. 'jqplot.canvasAxisTickRenderer.min.js');
		frameSwr::_()->addScript('jqplot.highlighter', SWR_JS_PATH. $jqplotDir. 'jqplot.highlighter.min.js');
		frameSwr::_()->addScript('jqplot.cursor', SWR_JS_PATH. $jqplotDir. 'jqplot.cursor.min.js');
		frameSwr::_()->addScript('jqplot.barRenderer', SWR_JS_PATH. $jqplotDir. 'jqplot.barRenderer.min.js');
		frameSwr::_()->addScript('jqplot.categoryAxisRenderer', SWR_JS_PATH. $jqplotDir. 'jqplot.categoryAxisRenderer.min.js');
		frameSwr::_()->addScript('jqplot.pointLabels', SWR_JS_PATH. $jqplotDir. 'jqplot.pointLabels.min.js');
	}
}
