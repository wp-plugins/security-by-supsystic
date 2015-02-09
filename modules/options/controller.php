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
	public function getPermissions() {
		return array(
			SWR_USERLEVELS => array(
				SWR_ADMIN => array('saveGroup')
			),
		);
	}
}

