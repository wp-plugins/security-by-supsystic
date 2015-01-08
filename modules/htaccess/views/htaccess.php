<?php
class htaccessViewSwr extends viewSwr {
	public function getTabContent() {
		frameSwr::_()->getModule('templates')->loadJqueryUi();
		
		$haveUnsavedChanges = $this->getModule()->checkUnsavedHtaccess();
		if($haveUnsavedChanges) {
			$this->assign('requiredHtaccess', $this->getModule()->getRequiredContent());
			$this->assign('path', $this->getModule()->getPath());
			$this->assign('adminPath', $this->getModule()->getPath(true));
			$this->assign('htpasswdPath', frameSwr::_()->getModule('secure_login')->getModel()->getHtpasswdFilePath());
		}
		
		$this->assign('haveUnsavedChanges', $haveUnsavedChanges);
		return parent::getContent('htaccessAdmin');
	}
}
