<?php
class optionsControllerSwr extends controllerSwr {
	public function saveGroup() {
		$res = new responseSwr();
		if($this->getModel()->saveGroup(reqSwr::get('post'))) {
			$res->addMessage(__('Done', SWR_LANG_CODE));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function activatePlugin() {
		$res = new responseSwr();
		if($this->getModel('modules')->activatePlugin(reqSwr::get('post'))) {
			$res->addMessage(__('Plugin was activated', SWR_LANG_CODE));
		} else {
			$res->pushError($this->getModel('modules')->getErrors());
		}
		return $res->ajaxExec();
	}
	public function activateUpdate() {
		$res = new responseSwr();
		if($this->getModel('modules')->activateUpdate(reqSwr::get('post'))) {
			$res->addMessage(__('Very good! Now plugin will be updated.', SWR_LANG_CODE));
		} else {
			$res->pushError($this->getModel('modules')->getErrors());
		}
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			SWR_USERLEVELS => array(
				SWR_ADMIN => array('saveGroup', 'activatePlugin', 'activateUpdate')
			),
		);
	}
}

