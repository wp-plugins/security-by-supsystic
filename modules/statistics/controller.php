<?php
class statisticsControllerSwr extends controllerSwr {
	public function clear() {
		$res = new responseSwr();
		$tab = reqSwr::getVar('tab', 'post');
		if($this->getModel()->clear( $tab )) {
			// Do nothing for now
		} else
			$res->pushError ($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			SWR_USERLEVELS => array(
				SWR_ADMIN => array('clear')
			),
		);
	}
}
