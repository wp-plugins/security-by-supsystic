<?php
class secure_filesSwr extends moduleSwr {
	public function init() {
		parent::init();
		dispatcherSwr::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		dispatcherSwr::addFilter('optionsDefine', array($this, 'addOptions'));
	}
	public function addAdminTab($tabs) {
		$tabs[ $this->getCode() ] = array(
			'label' => __('Scan file', SWR_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'fa_icon' => 'fa-folder-open-o', 'sort_order' => 20,
		);
		return $tabs;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function addOptions($opts) {
		$opts[ $this->getCode() ] = array(
			'label' => __('Scan Options', SWR_LANG_CODE),
			'opts' => array(
				'last_time_files_change_check' => array('label' => __('UnAuthorize File changes', SWR_LANG_CODE), 'weight' => 60, 'desc' => __('System files and directories contain vital information that should be accessed only by authorized users. This option creates additional protection for system files and catalogues, reducing the risk of hackers accessing them.', SWR_LANG_CODE)),
				'last_time_files_perms_check' => array('label' => __('Files and Folders permissions', SWR_LANG_CODE), 'weight' => 70, 'desc' => __('To give others the access to your files means subjecting your site to any actions, including illegal actions. This option fights the unauthorized access to your files. It searches for folders with 777 access parameter that allows adding foreign files to the folder. You can change the access parameter to 755 or 775 in the display list of such folders.', SWR_LANG_CODE)),
			),
		);
		return $opts;
	}
}

