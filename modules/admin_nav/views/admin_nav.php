<?php
class admin_navViewSwr extends viewSwr {
	public function getBreadcrumbs() {
		$this->assign('breadcrumbsList', $this->getModule()->getBreadcrumbsList());
		return parent::getContent('adminNavBreadcrumbs');
	}
}
