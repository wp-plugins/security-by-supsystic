<?php
class secure_hideSwr extends moduleSwr {
	public function init() {
		parent::init();
		dispatcherSwr::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		dispatcherSwr::addFilter('optionsDefine', array($this, 'addOptions'));
		
		add_filter('style_loader_src', array($this, 'changeLinksVersion'));
		add_filter('script_loader_src', array($this, 'changeLinksVersion'));
	}
	public function addAdminTab($tabs) {
		$tabs[ $this->getCode() ] = array(
			'label' => __('Hide Me', SWR_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'fa_icon' => 'fa-eye-slash', 'sort_order' => 50,
		);
		return $tabs;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function addOptions($opts) {
		global $wpdb;
		$opts[ $this->getCode() ] = array(
			'label' => __('Hide Me', SWR_LANG_CODE),
			'opts' => array(
				'change_standard_db_pref_enb' => array('label' => __('Change standard database prefix', SWR_LANG_CODE), 'weight' => 10, 'html' => 'checkboxHiddenVal', 'desc' => __('By changing the standard table prefix in your WordPress database you’ll make the names of your tables unique. The hackers will no longer have the information about the names of your database tables.', SWR_LANG_CODE)),
				'old_db_pref' => array('label' => __('Default database prefix', SWR_LANG_CODE), 'def' => $wpdb->prefix),
				'new_db_pref' => array('label' => __('New database prefix', SWR_LANG_CODE)),
				
				'rand_wp_version_enb' => array('label' => __('Display random WordPress version', SWR_LANG_CODE), 'weight' => 20, 'html' => 'checkboxHiddenVal', 'desc' => __('With this option you&#39;ll easily confuse the hacker. If you have an old WordPress version and you can’t update, upgrading to a newer version will save you from the attacks used for the older versions.', SWR_LANG_CODE)),
				
				'hide_server_info_enb' => array('label' => __('Hide server info', SWR_LANG_CODE), 'weight' => 20, 'html' => 'checkboxHiddenVal',  'htaccessChange' => true, 'desc' => __('You can simply hide server info in some of your server responces, that make hasker confused.', SWR_LANG_CODE)),
			),
		);
		return $opts;
	}
	// @param $html string for example <link rel='$rel' id='$handle-css' $title href='$href' type='text/css' media='$media' />\n
	public function changeLinksVersion($src) {
		if(frameSwr::_()->getModule('options')->get('rand_wp_version_enb')) {
			static $randVersion;
			if(!$randVersion)
				$randVersion = mt_rand(1, 99999);
			$src = str_replace('ver='. get_bloginfo( 'version' ), 'ver='. $randVersion, $src);
		}
		return $src;
	}
}

