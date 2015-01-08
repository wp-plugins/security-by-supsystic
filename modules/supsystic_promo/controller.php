<?php
class supsystic_promoControllerSwr extends controllerSwr {
    public function welcomePageSaveInfo() {
		$res = new responseSwr();
		installerSwr::setUsed();
		if($this->getModel()->welcomePageSaveInfo(reqSwr::get('get'))) {
			$res->addMessage(__('Information was saved. Thank you!', SWR_LANG_CODE));
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		$originalPage = reqSwr::getVar('original_page');
		$http = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
		if(strpos($originalPage, $http. $_SERVER['HTTP_HOST']) !== 0) {
			$originalPage = '';
		}
		redirect($originalPage);
	}
	/**
	 * @see controller::getPermissions();
	 */
	public function getPermissions() {
		return array(
			SWR_USERLEVELS => array(
				SWR_ADMIN => array('welcomePageSaveInfo')
			),
		);
	}
}