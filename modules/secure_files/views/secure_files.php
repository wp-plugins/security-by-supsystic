<?php
class secure_filesViewSwr extends viewSwr {
	public function getTabContent() {
		frameSwr::_()->getModule('templates')->loadJqGrid();
		
		frameSwr::_()->addScript('progressmeter', SWR_JS_PATH. 'progressmeter.js');
		frameSwr::_()->addStyle('progressmeter', SWR_CSS_PATH. 'progressmeter.css');
		$fileScanPortions = array(
			'change' => 500,
			'perms' => 500,
		);
		frameSwr::_()->addScript('admin.'. $this->getCode(), $this->getModule()->getModPath(). 'js/admin.'. $this->getCode(). '.js');
		frameSwr::_()->addJSVar('admin.'. $this->getCode(), 'swrFilesScanPortions', $fileScanPortions);
		frameSwr::_()->addJSVar('admin.'. $this->getCode(), 'swrFilesIssuesDataUrl', uriSwr::mod('secure_files', 'getListForTbl', array('reqType' => 'ajax')));
		
		frameSwr::_()->addStyle('admin.'. $this->getCode(), $this->getModule()->getModPath(). 'css/admin.'. $this->getCode(). '.css');
		
		$options = frameSwr::_()->getModule('options')->getCatOpts( $this->getCode() );
		$lastTimeChnageIssuesCount = $this->getModel()->getModifiedLastErrorsCount();
		$lastTimePermsIssuesCount = $this->getModel()->getPermsLastErrorsCount();
		$alerts = array(
			'last_time_files_change_check' => array(),
			'last_time_files_perms_check' => array(),
		);
		if(empty($options['last_time_files_change_check']['value'])) {
			$alerts['last_time_files_change_check'] = array(
				'desc' => __('Start scan now - to be able track file changes in future', SWR_LANG_CODE), 
				'alert_class' => 'warning');
		} elseif(!empty($lastTimeChnageIssuesCount)) {
			$alerts['last_time_files_change_check'] = array(
				'desc' => sprintf(__('During last scan we found %d issues', SWR_LANG_CODE), $lastTimeChnageIssuesCount), 
				'alert_class' => 'warning');
		}
		if(empty($options['last_time_files_perms_check']['value'])) {
			$alerts['last_time_files_perms_check'] = array(
				'desc' => __('Start scan now - to check your files and folders permissions issues', SWR_LANG_CODE), 
				'alert_class' => 'warning');
		} elseif(!empty($lastTimePermsIssuesCount)) {
			$alerts['last_time_files_perms_check'] = array(
				'desc' => sprintf(__('During last scan we found %d issues', SWR_LANG_CODE), $lastTimePermsIssuesCount), 
				'alert_class' => 'danger');
		}
		$typesForSelect = array(0 => __('All Types', SWR_LANG_CODE));
		$types = $this->getModel()->getIssueTypes();
		foreach($types as $tKey => $t) {
			$typesForSelect[ $t['id'] ] = $t['label'];
		}
		
		$this->assign('options', $options);
		$this->assign('alerts', $alerts);
		$this->assign('typesForSelect', $typesForSelect);
		return parent::getContent('secureFilesAdmin');
	}
}
