<?php
class secure_loginViewSwr extends viewSwr {
	public function getTabContent() {
		frameSwr::_()->addScript('admin.'. $this->getCode(), $this->getModule()->getModPath(). 'js/admin.'. $this->getCode(). '.js');
		frameSwr::_()->getModule('templates')->loadJqueryUi();

		$options = frameSwr::_()->getModule('options')->getCatOpts( $this->getCode() );
		$blacklistTab = frameSwr::_()->getModule('options')->getTab('blacklist');
		$blacklistUrl = $blacklistTab['url']. '&search[type]=login';
		$simpleUsersIssues = array();
		$simpleAdminsList = $this->getModel()->getSimpleAdminsList();
		if(!empty($simpleAdminsList)) {
			$simpleUsersIssues = $this->getModel()->getSimpleUserIssues();
		}
		$this->assign('options', $options);
		$this->assign('breadcrumbs', frameSwr::_()->getModule('admin_nav')->getView()->getBreadcrumbs());
		$this->assign('blacklistUrl', $blacklistUrl);
		$this->assign('currentIp', utilsSwr::getIP());
		$this->assign('simpleAdmins', $simpleAdminsList);
		$this->assign('simpleUsersIssues', $simpleUsersIssues);
		return parent::getContent('secureLoginAdmin');
	}
	public function getCapchaOnLogin() {
		$this->assign('publicKey', $this->getModel()->getCapchaPublicKey());
		return parent::getContent('secureLoginCapcha');
	}
}
