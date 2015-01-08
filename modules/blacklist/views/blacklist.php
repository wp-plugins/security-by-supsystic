<?php
class blacklistViewSwr extends viewSwr {
	public function getTabContent() {
		frameSwr::_()->getModule('templates')->loadJqGrid();

		frameSwr::_()->addScript('admin.blacklist', $this->getModule()->getModPath(). 'js/admin.blacklist.js');
		frameSwr::_()->addJSVar('admin.blacklist', 'swrBlacklistDataUrl', uriSwr::mod('blacklist', 'getListForTbl', array('reqType' => 'ajax')));
		
		frameSwr::_()->addStyle('admin.'. $this->getCode(), $this->getModule()->getModPath(). 'css/admin.'. $this->getCode(). '.css');
		
		$typesForSelect = array_merge(array(0 => __('All Types', SWR_LANG_CODE)), $this->getModel()->getTypesLabels());
		
		$blockedCounties = $this->getModel()->getBlockedCountryIds();
		$blockedBrowsers = $this->getModel()->getBlockedBrowsersNames();
		
		$search = reqSwr::getVar('search');
		
		$allCountries = frameSwr::_()->getTable('countries')->get('*');
		$countriesForSelect = array();
		foreach($allCountries as $c) {
			$countriesForSelect[ $c['id'] ] = $c['name'];
		}
		$this->assign('currentIp', utilsSwr::getIP());
		$this->assign('typesForSelect', $typesForSelect);
		$this->assign('typeSelected', (!empty($search) && isset($search['type']) && !empty($search['type']) ? $search['type'] : ''));
		
		$this->assign('countryList', $countriesForSelect);
		$this->assign('blockedCounties', $blockedCounties);
		$this->assign('currentCountry', $this->getModel()->getCountryCode());
		
		$this->assign('browsersList', utilsSwr::getBrowsersList());
		$this->assign('blockedBrowsers', $blockedBrowsers);
		$this->assign('currentBrowser', utilsSwr::getBrowser());
		
		return parent::getContent('blacklistAdmin');
	}
	public function getBlockedPage() {
		return parent::getContent('blacklistBlockedPage');
	}
}
