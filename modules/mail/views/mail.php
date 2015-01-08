<?php
class mailViewSwr extends viewSwr {
	public function getTabContent() {
		frameSwr::_()->getModule('templates')->loadJqueryUi();
		frameSwr::_()->addScript('admin.'. $this->getCode(), $this->getModule()->getModPath(). 'js/admin.'. $this->getCode(). '.js');
		
		$this->assign('options', frameSwr::_()->getModule('options')->getCatOpts( $this->getCode() ));
		$this->assign('testEmail', frameSwr::_()->getModule('options')->get('notify_email'));
		return parent::getContent('mailAdmin');
	}
}
