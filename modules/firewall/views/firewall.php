<?php
class firewallViewSwr extends viewSwr {
	public function getTabContent() {
		frameSwr::_()->addScript('admin.'. $this->getCode(), $this->getModule()->getModPath(). 'js/admin.'. $this->getCode(). '.js');
		frameSwr::_()->getModule('templates')->loadJqueryUi();
		
		$options = frameSwr::_()->getModule('options')->getCatOpts( $this->getCode() );
		$blacklistTab = frameSwr::_()->getModule('options')->getTab('blacklist');
		$blacklistUrl = $blacklistTab['url']. '&search[type]=404';
		$this->assign('options', $options);
		$this->assign('blacklistUrl', $blacklistUrl);
		return parent::getContent('firewallAdmin');
	}
}
