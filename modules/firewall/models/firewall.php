<?php
class firewallModelSwr extends modelSwr {
	public function afterOptionsChange($prevOptsModel, $optsModel, $submitData) {
		// x-frame option changed
		$prevXFrame = $prevOptsModel->get('x_frame_enb');
		$currXFrame = $optsModel->get('x_frame_enb');
		if($currXFrame && !$prevXFrame) {	// Pass enabled
			$this->updateHtaccess_x_frame_enb();
		} elseif($prevXFrame && !$currXFrame) {	// Pass disabled
			$this->removeHtaccess_x_frame_enb();
		}
		// protect system files option change
		$prevLockSysFiles = $prevOptsModel->get('lock_system_files_enb');
		$currLockSysFiles = $optsModel->get('lock_system_files_enb');
		if($currLockSysFiles && !$prevLockSysFiles) {	// Pass enabled
			$this->updateHtaccess_lock_system_files_enb();
		} elseif($prevLockSysFiles && !$currLockSysFiles) {	// Pass disabled
			$this->removeHtaccess_lock_system_files_enb();
		}
		// Files listing option change
		$prevOpt = $prevOptsModel->get('disable_directory_browsing');
		$currOpt = $optsModel->get('disable_directory_browsing');
		if($currOpt && !$prevOpt) {	// Pass enabled
			$this->updateHtaccess_disable_directory_browsing();
		} elseif($prevOpt && !$currOpt) {	// Pass disabled
			$this->removeHtaccess_disable_directory_browsing();
		}
		return !$this->haveErrors();
	}
	public function updateHtaccess_x_frame_enb() {
		$xFrameRules = array(
			'<IfModule mod_headers.c>',
			'Header append X-FRAME-OPTIONS "SAMEORIGIN"', 
			'</IfModule>',
		);
		if(!frameSwr::_()->getModule('htaccess')->savePart('x_frame_enb', $xFrameRules)) {
			$this->pushError(frameSwr::_()->getModule('htaccess')->getErrors());
		}
	}
	public function removeHtaccess_x_frame_enb() {
		if(!frameSwr::_()->getModule('htaccess')->removePart('x_frame_enb')) {
			$this->pushError(frameSwr::_()->getModule('htaccess')->getErrors());
		}
	}
	public function updateHtaccess_lock_system_files_enb() {
		$htaccessRules = array(
			'<FilesMatch "(error_log|[rR]eadme|[cC]hangelog|[lL]icense|config|\.[hH][tT][aApP].*)">',
			'Order allow,deny',
			'Deny from all',
			'Satisfy All',
			'</FilesMatch>',
		);
		if(!frameSwr::_()->getModule('htaccess')->savePart('lock_system_files_enb', $htaccessRules)) {
			$this->pushError(frameSwr::_()->getModule('htaccess')->getErrors());
		}
	}
	public function removeHtaccess_lock_system_files_enb() {
		if(!frameSwr::_()->getModule('htaccess')->removePart('lock_system_files_enb')) {
			$this->pushError(frameSwr::_()->getModule('htaccess')->getErrors());
		}
	}
	public function updateHtaccess_disable_directory_browsing() {
		$htaccessRules = array(
			'Options -Indexes',
		);
		if(!frameSwr::_()->getModule('htaccess')->savePart('disable_directory_browsing', $htaccessRules)) {
			$this->pushError(frameSwr::_()->getModule('htaccess')->getErrors());
		}
	}
	public function removeHtaccess_disable_directory_browsing() {
		if(!frameSwr::_()->getModule('htaccess')->removePart('disable_directory_browsing')) {
			$this->pushError(frameSwr::_()->getModule('htaccess')->getErrors());
		}
	}
}
