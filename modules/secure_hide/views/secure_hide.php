<?php
class secure_hideViewSwr extends viewSwr {
	public function getTabContent() {
		frameSwr::_()->addScript('admin.'. $this->getCode(), $this->getModule()->getModPath(). 'js/admin.'. $this->getCode(). '.js');
		frameSwr::_()->getModule('templates')->loadJqueryUi();
		
		$options = frameSwr::_()->getModule('options')->getCatOpts( $this->getCode() );
		$this->assign('options', $options);
		return parent::getContent('secureHideAdmin');
	}
}
