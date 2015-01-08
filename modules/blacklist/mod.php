<?php
class blacklistSwr extends moduleSwr {
	public function init() {
		parent::init();
		$this->checkCurrentIpBlock();	// If current IP is in blacklist - it will be blocked
		dispatcherSwr::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
	}
	public function addAdminTab($tabs) {
		$tabs[ $this->getCode() ] = array(
			'label' => __('Blacklist', SWR_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'fa_icon' => 'fa-ban', 'sort_order' => 40,
		);
		return $tabs;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function checkCurrentIpBlock() {
		$ipInBlackList = $this->getModel()->checkIp( utilsSwr::getIP() );
		if($ipInBlackList) {
			echo $this->getView()->getBlockedPage();
			exit();
		}
	}
}

