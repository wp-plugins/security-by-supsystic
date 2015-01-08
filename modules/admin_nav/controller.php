<?php
class admin_navControllerSwr extends controllerSwr {
	public function getPermissions() {
		return array(
			SWR_USERLEVELS => array(
				SWR_ADMIN => array()
			),
		);
	}
}