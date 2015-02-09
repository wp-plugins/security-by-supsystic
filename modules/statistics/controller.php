<?php
class statisticsControllerSwr extends controllerSwr {
	private $_loadModelName = '';
	public function clear() {
		$res = new responseSwr();
		$tab = reqSwr::getVar('tab', 'post');
		if($this->getModel()->clear( $tab )) {
			// Do nothing for now
		} else
			$res->pushError ($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	protected function _prepareListForTbl($data) {
		if($this->_loadModelName == 'detailed_login_stat') {
			if(!empty($data)) {
				$users = array();
				foreach($data as $i => $v) {
					if(!isset($users[ $v['uid'] ])) {
						$users[ $v['uid'] ] = get_userdata( $v['uid'] );
					}
					$data[ $i ]['email'] = $users[ $v['uid'] ]->user_email;
				}
			}
		}
		return $data;
	}
	protected function _prepareSortOrder($sortOrder) {
		if($this->_loadModelName == 'detailed_login_stat') {
			switch($sortOrder) {
				case 'email':
					$sortOrder = 'uid';
					break;
			}
		}
		return $sortOrder;
	}
	public function getListForTblDetailedLogin() {
		$this->_loadModelName = 'detailed_login_stat';
		parent::getListForTbl();
	}
	public function getModel($name = '') {
		return parent::getModel($name ? $name : $this->_loadModelName);
	}
	public function getPermissions() {
		return array(
			SWR_USERLEVELS => array(
				SWR_ADMIN => array('clear', 'getListForTblDetailedLogin')
			),
		);
	}
}
