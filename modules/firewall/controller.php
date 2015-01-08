<?php
class firewallControllerSwr extends controllerSwr {
	public function saveOptions() {
		$res = new responseSwr();
		$optsModel = frameSwr::_()->getModule('options')->getModel();
		$prevOptsModel = clone($optsModel);
		$submitData = reqSwr::get('post');
		if($optsModel->saveGroup($submitData)) {
			if($this->getModel()->afterOptionsChange($prevOptsModel, $optsModel, $submitData)) {
				$res->addMessage(__('Done', SWR_LANG_CODE));
			} else
				$res->pushError ($this->getModel()->getErrors());
		} else
			$res->pushError ($optsModel->getErrors());
		$res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			SWR_USERLEVELS => array(
				SWR_ADMIN => array('saveOptions')
			),
		);
	}
}

