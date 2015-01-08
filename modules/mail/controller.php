<?php
class mailControllerSwr extends controllerSwr {
	public function testEmail() {
		$res = new responseSwr();
		$email = reqSwr::getVar('test_email', 'post');
		if($this->getModel()->testEmail($email)) {
			$res->addMessage(__('Now check your email inbox / spam folders for test mail.'));
		} else 
			$res->pushError ($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function saveMailTestRes() {
		$res = new responseSwr();
		$result = (int) reqSwr::getVar('result', 'post');
		frameSwr::_()->getModule('options')->getModel()->save('mail_function_work', $result);
		$res->ajaxExec();
	}
	public function saveOptions() {
		$res = new responseSwr();
		$optsModel = frameSwr::_()->getModule('options')->getModel();
		$submitData = reqSwr::get('post');
		if($optsModel->saveGroup($submitData)) {
			$res->addMessage(__('Done', SWR_LANG_CODE));
		} else
			$res->pushError ($optsModel->getErrors());
		$res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			SWR_USERLEVELS => array(
				SWR_ADMIN => array('testEmail', 'saveMailTestRes', 'saveOptions')
			),
		);
	}
}
