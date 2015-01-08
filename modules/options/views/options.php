<?php
class optionsViewSwr extends viewSwr {
	private $_news = array();
	public function getNewFeatures() {
		$res = array();
		$readmePath = SWR_DIR. 'readme.txt';
		if(file_exists($readmePath)) {
			$readmeContent = @file_get_contents($readmePath);
			if(!empty($readmeContent)) {
				$matchedData = '';
				if(preg_match('/= '. SWR_VERSION. ' =(.+)=.+=/isU', $readmeContent, $matches)) {
					$matchedData = $matches[1];
				} elseif(preg_match('/= '. SWR_VERSION. ' =(.+)/is', $readmeContent, $matches)) {
					$matchedData = $matches[1];
				}
				$matchedData = trim($matchedData);
				if(!empty($matchedData)) {
					$res = array_map('trim', explode("\n", $matchedData));
				}
			}
		}
		return $res;
	}
    public function getAdminPage() {
		$tabs = $this->getModule()->getTabs();
		$activeTab = $this->getModule()->getActiveTab();
		$content = 'No tab content found - ERROR';
		if(isset($tabs[ $activeTab ]) && isset($tabs[ $activeTab ]['callback'])) {
			frameSwr::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('tab.'. $activeTab);
			$content = call_user_func($tabs[ $activeTab ]['callback']);
		}
		frameSwr::_()->addJSVar('adminOptionsSwr', 'swrActiveTab', $activeTab);
		$this->assign('tabs', $tabs);
		$this->assign('activeTab', $activeTab);
		$this->assign('content', $content);
		$this->assign('mainUrl', $this->getModule()->getTabUrl());
		$this->assign('breadcrumbs', frameSwr::_()->getModule('admin_nav')->getView()->getBreadcrumbs());
		
        parent::display('optionsAdminPage');
    }
	public function sortOptsSet($a, $b) {
		if($a['weight'] > $b['weight'])
			return -1;
		if($a['weight'] < $b['weight'])
			return 1;
		return 0;
	}
	public function getTabContent() {
		$allOptions = $this->getModule()->getAll();
		$optsDisplayOnMainPage = array();
		$occupancy = array(
			'main' => 0,
		);
		$mainOccupancy = 0;
		$catsOccupancies = array();
		foreach($allOptions as $cKey => $cData) {
			$currentSet = array();
			$totalCatWeight = 0;
			$availableCatWeight = 0;
			foreach($cData['opts'] as $oKey => $oData) {
				if(isset($oData['weight'])) {
					if(empty($oData['value'])) {
						$currentSet[ $oKey ] = $oData;
					} else {
						$availableCatWeight += $oData['weight'];
					}
					$totalCatWeight += $oData['weight'];
				}
			}
			if(!empty($currentSet)) {
				usort($currentSet, array($this, 'sortOptsSet'));
				$optsDisplayOnMainPage[ $cKey ] = $cData;	// Just to copy all category data
				$optsDisplayOnMainPage[ $cKey ]['tab_url'] = $this->getModule()->getTabUrl($cKey);	// As category key - should be same as options module code
				$optsDisplayOnMainPage[ $cKey ]['opts'] = $currentSet;	// Replace default options - to required
			}
			if($totalCatWeight)
				$catsOccupancies[ $cKey ] = $availableCatWeight * 100 / $totalCatWeight;
		}
		if($catsOccupancies) {
			$occupancy['main'] = array_sum( $catsOccupancies ) / count( $catsOccupancies );
		}
		frameSwr::_()->addScript('jquery.knob', SWR_JS_PATH. 'jquery.knob.min.js');
		frameSwr::_()->addScript('admin.mainoptions', $this->getModule()->getModPath(). 'js/admin.mainoptions.js');
		frameSwr::_()->addJSVar('admin.mainoptions', 'swrOccupancy', $occupancy);

		$this->assign('occupancy', $occupancy);
		$this->assign('optsDisplayOnMainPage', $optsDisplayOnMainPage);
		$this->assign('notifyLevels', array('danger' => 50, 'warning' => 30, 'info' => 0));
		return parent::getContent('optionsAdminMain');
	}
	public function serverSettings() {
		$this->assign('systemInfo', array(
            'Operating System' => array('value' => PHP_OS),
            'PHP Version' => array('value' => PHP_VERSION),
            'Server Software' => array('value' => $_SERVER['SERVER_SOFTWARE']),
            'MySQL' => array('value' => mysql_get_server_info()),
            'PHP Safe Mode' => array('value' => ini_get('safe_mode') ? 'Yes' : 'No', 'error' => ini_get('safe_mode')),
            'PHP Allow URL Fopen' => array('value' => ini_get('allow_url_fopen') ? 'Yes' : 'No'),
            'PHP Memory Limit' => array('value' => ini_get('memory_limit')),
            'PHP Max Post Size' => array('value' => ini_get('post_max_size')),
            'PHP Max Upload Filesize' => array('value' => ini_get('upload_max_filesize')),
            'PHP Max Script Execute Time' => array('value' => ini_get('max_execution_time')),
            'PHP EXIF Support' => array('value' => extension_loaded('exif') ? 'Yes' : 'No'),
            'PHP EXIF Version' => array('value' => phpversion('exif')),
            'PHP XML Support' => array('value' => extension_loaded('libxml') ? 'Yes' : 'No', 'error' => !extension_loaded('libxml')),
            'PHP CURL Support' => array('value' => extension_loaded('curl') ? 'Yes' : 'No', 'error' => !extension_loaded('curl')),
        ));
		return parent::display('_serverSettings');
	}
}
