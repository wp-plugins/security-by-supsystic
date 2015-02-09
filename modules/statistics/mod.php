<?php
class statisticsSwr extends moduleSwr {
	private $_types = array();
	private $_statTabs = array();
	private $_lastStatId = 0;
	public function init() {
		parent::init();
		dispatcherSwr::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		add_action('init', array($this, 'addStat'));
		add_action('wp', array($this, 'check404'), 99);
		//add_filter('wp_authenticate_user', array($this, 'checkSubmitLogin'), 99);
		add_filter('login_redirect', array($this, 'submitLoginFailed'), 99, 3);
	}
	public function addAdminTab($tabs) {
		$tabs[ $this->getCode() ] = array(
			'label' => __('Statistics', SWR_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'fa_icon' => 'fa-bar-chart', 'sort_order' => 70,
		);
		return $tabs;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function addStat() {
		if(!is_admin() && !frameSwr::_()->getModule('user')->isAdmin()) {
			$this->getTypes();
			$currentType = 0;
			if(frameSwr::_()->getModule('pages')->isLogin()) {
				$currentType = $this->_types['login']['id'];
			} else {
				$currentType = $this->_types['normal']['id'];
			}
			$this->_lastStatId = $this->getModel()->insert(array(
				'ip' => utilsSwr::getIP(),
				'type' => $currentType,
				'url' => uriSwr::getFullUrl(),
			));
		}
	}
	public function check404() {
		if($this->_lastStatId && is_404()) {
			$this->getModel()->updateType($this->_lastStatId, $this->_types['404']['id']);
		}
	}
	/*public function checkSubmitLogin($user) {
		if($this->_lastStatId) {
			$currentType = is_wp_error($user) ? $this->_types['login_error']['id'] : $this->_types['login_submit']['id'];
			$this->getModel()->updateType($this->_lastStatId, $currentType);
		}
		return $user;
	}*/
	public function submitLoginFailed($redirect_to, $requested_redirect_to, $user) {
		if($this->_lastStatId) {
			$currentType = is_wp_error($user) ? $this->_types['login_error']['id'] : $this->_types['login_submit']['id'];
			$this->getModel()->updateType($this->_lastStatId, $currentType);
		}
		if($user && !is_wp_error($user) && is_super_admin( $user->ID )) {
			$this->getModel('detailed_login_stat')->insert(array(
				'uid' => $user->ID,
				'ip' => utilsSwr::getIP(),
			));
		}
		return $redirect_to;
	}
	public function getTypes() {
		if(empty($this->_types)) {
			$this->_types = array(
				'normal' => array('id' => 1),
				'404' => array('id' => 2),
				'login' => array('id' => 3),
				'login_submit' => array('id' => 4),
				'login_error' => array('id' => 5),
			);
		}
		return $this->_types;
	}
	public function getStatTabs() {
		if(empty($this->_statTabs)) {
			$statTabUrl = frameSwr::_()->getModule('options')->getTabUrl('statistics');
			$this->_statTabs = array(
				'all' => array('label' => __('All site views', SWR_LANG_CODE)),
				'404' => array('label' => __('404 page', SWR_LANG_CODE)),
				'login' => array('label' => __('Login page', SWR_LANG_CODE)),
				'detailed_login' => array('label' => __('Admins Login', SWR_LANG_CODE)),
			);
			foreach($this->_statTabs as $k => $v) {
				$this->_statTabs[ $k ]['url'] = $statTabUrl. '&stats_tab='. $k;
			}
		}
		return $this->_statTabs;
	}
	public function getTypeId($code) {
		$this->getTypes();
		return isset($this->_types[ $code ]) ? $this->_types[ $code ]['id'] : false;
	}
	public function getCurrentStatTab() {
		$statsTab = reqSwr::getVar('stats_tab', 'get');
		if(empty($statsTab))
			$statsTab = 'all';
		return $statsTab;
	}
}