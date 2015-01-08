<?php
class blacklistControllerSwr extends controllerSwr {
	protected function _prepareListForTbl($data) {
		if(!empty($data)) {
			foreach($data as $i => $v) {
				$typeLabel = empty( $v['type'] ) ? __('None', SWR_LANG_CODE) : $this->getModel()->getTypeLabelById( $v['type'] );
				$data[ $i ]['type_label'] = $typeLabel;
				$data[ $i ]['action'] = '<button href="#" onclick="swrBlacklistRemoveRow('. $data[ $i ]['id']. ', this); return false;" title="'. __('Remove', SWR_LANG_CODE). '" class="button"><i class="fa fa-fw fa-2x fa-trash-o" style="margin-top: 5px;"></i></button>';
			}
		}
		return $data;
	}
	protected function _prepareTextLikeSearch($val) {
		$query = '(ip LIKE "%'. $val. '%"';
		if(is_numeric($val)) {
			$query .= ' OR id LIKE "%'. (int) $val. '%"';
		}
		$query .= ')';
		return $query;
	}
	protected function _prepareSortOrder($sortOrder) {
		switch($sortOrder) {
			case 'type_label':
				$sortOrder = 'type';
				break;
		}
		return $sortOrder;
	} 
	public function addGroup() {
		$res = new responseSwr();
		if(($addedNum = $this->getModel()->addGroup(reqSwr::getVar('ips', 'post')))) {
			$res->addMessage(sprintf(__('%d IPs added', SWR_LANG_CODE), $addedNum));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function addGroupCountries() {
		$res = new responseSwr();
		if(($addedNum = $this->getModel()->addGroupCountries(reqSwr::getVar('country_ids', 'post'))) !== false) {
			if($addedNum) {
				$res->addMessage(sprintf(__('%d Countries added', SWR_LANG_CODE), $addedNum));
			} else {
				$res->addMessage(__('All Countries was removed from blacklist', SWR_LANG_CODE));
			}
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function addGroupBrowsers() {
		$res = new responseSwr();
		if(($addedNum = $this->getModel()->addGroupBrowsers(reqSwr::getVar('browser_names', 'post'))) !== false) {
			if($addedNum) {
				$res->addMessage(sprintf(__('%d Browsers added', SWR_LANG_CODE), $addedNum));
			} else {
				$res->addMessage(__('All Browsers was removed from blacklist', SWR_LANG_CODE));
			}
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	
	public function remove() {
		$res = new responseSwr();
		if($this->getModel()->remove(reqSwr::getVar('id', 'post'))) {
			$res->addMessage(__('Done', SWR_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			SWR_USERLEVELS => array(
				SWR_ADMIN => array('getListForTbl', 'addGroup', 'remove', 'removeGroup', 'clear', 'addGroupCountries', 'addGroupBrowsers')
			),
		);
	}
}