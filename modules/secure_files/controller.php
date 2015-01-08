<?php
class secure_filesControllerSwr extends controllerSwr {
	public function getFilesList() {
		@set_time_limit(0);
		$res = new responseSwr();
		if(($files = $this->getModel()->getFilesList(false, reqSwr::get('post')))) {
			clearstatcache();	// Clear php file info cache
			$scanType = reqSwr::getVar('scan_type');
			if($scanType == 'modified') {
				$this->getModel()->startNewModifiedScan();
			}
			$res->addData('files', $files);
		} else
			$res->pushError(__('Can\'t find any file', SWR_LANG_CODE));
		$res->ajaxExec();
	}
	public function checkFilesChange() {
		@set_time_limit(0);
		$res = new responseSwr();
		if($this->getModel()->checkFilesChange(reqSwr::get('post'))) {
			// Do nothing for now
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function checkFilesChangeEnd() {
		$res = new responseSwr();
		frameSwr::_()->getModule('options')->getModel()->save('last_time_files_change_check', time());
		$res->ajaxExec();
	}
	public function checkFilesPerms() {
		@set_time_limit(0);
		$res = new responseSwr();
		if($this->getModel()->checkFilesPerms(reqSwr::get('post'))) {
			// Do nothing for now
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function checkFilesPermsEnd() {
		$res = new responseSwr();
		frameSwr::_()->getModule('options')->getModel()->save('last_time_files_perms_check', time());
		$res->ajaxExec();
	}
	protected function _prepareListForTbl($data) {
		if(!empty($data)) {
			$model = $this->getModel();
			foreach($data as $i => $v) {
				$data[ $i ]['last_time_modified_date'] = dateSwr::_( $v['last_time_modified'] );
				$data[ $i ]['type_label'] = $model->getIssueLabelById( $v['type'] );
			}
		}
		return $data;
	}
	protected function _prepareSearchField($searchField) {
		return $searchField;
	}
	protected function _prepareTextLikeSearch($val) {
		$query = '(filepath LIKE "%'. $val. '%"';
		if(is_numeric($val)) {
			$query .= ' OR id LIKE "%'. (int) $val. '%"';
		}
		$query .= ')';
		return $query;
	}
	protected function _prepareSortOrder($orderBy) {
		switch($orderBy) {
			case 'last_time_modified_date':
				$orderBy = 'last_time_modified';
				break;
			case 'type_label':
				$orderBy = 'type';
				break;
		}
		return $orderBy;
	}
	public function getPermissions() {
		return array(
			SWR_USERLEVELS => array(
				SWR_ADMIN => array('getListForTbl', 'getFilesList', 
					'checkFilesChange', 'checkFilesChangeEnd', 
					'checkFilesPerms', 'checkFilesPermsEnd',
					'removeGroup', 'clear')
			),
		);
	}
}

