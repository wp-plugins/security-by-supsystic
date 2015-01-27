<?php
class optionsSwr extends moduleSwr {
	private $_tabs = array();
	private $_options = array();
	private $_optionsToCategoires = array();	// For faster search
	
	public function init() {
		dispatcherSwr::addAction('afterModulesInit', array($this, 'initAllOptValues'));
	}
	public function initAllOptValues() {
		// Just to make sure - that we loaded all default options values
		$this->getAll();
	}
    /**
     * This method provides fast access to options model method get
     * @see optionsModel::get($d)
     */
    public function get($code) {
        return $this->getModel()->get($code);
    }
	/**
     * This method provides fast access to options model method get
     * @see optionsModel::get($d)
     */
	public function isEmpty($code) {
		return $this->getModel()->isEmpty($code);
	}
	public function getAllowedPublicOptions() {
		// empty for now
		return array();
	}
	public function getAdminPage() {
		if(installerSwr::isUsed()) {
			return $this->getView()->getAdminPage();
		} else {
			return frameSwr::_()->getModule('supsystic_promo')->showWelcomePage();
		}
	}
	public function getTabs() {
		if(empty($this->_tabs)) {
			$this->_tabs = dispatcherSwr::applyFilters('mainAdminTabs', array(
				'main_page' => array('label' => __('Main Page', SWR_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'wp_icon' => 'dashicons-shield-alt', 'sort_order' => 0), 
			));
			foreach($this->_tabs as $tabKey => $tab) {
				if(!isset($this->_tabs[ $tabKey ]['url'])) {
					$this->_tabs[ $tabKey ]['url'] = $this->getTabUrl( $tabKey );
				}
			}
			uasort($this->_tabs, array($this, 'sortTabsClb'));
		}
		return $this->_tabs;
	}
	public function sortTabsClb($a, $b) {
		if(isset($a['sort_order']) && isset($b['sort_order'])) {
			if($a['sort_order'] > $b['sort_order'])
				return 1;
			if($a['sort_order'] < $b['sort_order'])
				return -1;
		}
		return 0;
	}
	public function getTab($tabKey) {
		$this->getTabs();
		return isset($this->_tabs[ $tabKey ]) ? $this->_tabs[ $tabKey ] : false;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function getActiveTab() {
		$reqTab = reqSwr::getVar('tab');
		return empty($reqTab) ? 'main_page' : $reqTab;
	}
	public function getTabUrl($tab = '') {
		static $mainUrl;
		if(empty($mainUrl)) {
			$mainUrl = frameSwr::_()->getModule('adminmenu')->getMainLink();
		}
		if(!empty($tab)) {
			switch($tab) {
				case 'htaccess':
					$tab = 'system#swrHtaccessBlock';
					break;
			}
		}
		return empty($tab) ? $mainUrl : $mainUrl. '&tab='. $tab;
	}
	public function getAll() {
		if(empty($this->_options)) {
			$this->_options = dispatcherSwr::applyFilters('optionsDefine', array(
				'general' => array(
					'label' => __('General', SWR_LANG_CODE),
					'opts' => array(
						'send_stats' => array('label' => __('Send usage statistics', SWR_LANG_CODE), 'desc' => '', 'def' => '1', 'html' => 'checkboxHiddenVal'),
					),
				),
			));
			foreach($this->_options as $catKey => $cData) {
				uasort( $this->_options[ $catKey ]['opts'], array($this, 'sortOptsClb') );
			}
			
			// Not used for now
			/*foreach($this->_options as $catKey => $cData) {
				foreach($cData['opts'] as $optKey => $opt) {
					$this->_optionsToCategoires[ $optKey ] = $catKey;
				}
			}*/
			$this->getModel()->fillInValues( $this->_options );
		}
		return $this->_options;
	}
	public function sortOptsClb($a, $b) {
		if(isset($a['weight']) && !isset($b['weight'])) {
			return -1;
		} elseif(!isset($a['weight']) && isset($b['weight'])) {
			return 1;
		} else {
			if($a['weight'] > $b['weight']) {
				return -1;
			} elseif($a['weight'] < $b['weight']) {
				return 1;
			}
		}
		return 0;
	}
	public function getFullCat($cat) {
		$this->getAll();
		return isset($this->_options[ $cat ]) ? $this->_options[ $cat ] : false;
	}
	public function getCatOpts($cat) {
		$opts = $this->getFullCat($cat);
		return $opts ? $opts['opts'] : false;
	}
}

