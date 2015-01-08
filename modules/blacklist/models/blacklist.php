<?php
class blacklistModelSwr extends modelSwr {
	private $_types = array();
	private $_typesLabelsById = array();
	public function __construct() {
		$this->_setTbl('blacklist');
	}
	public function save($d = array()) {
		$d['ip'] = isset($d['ip']) ? trim($d['ip']) : '';
		$d['type'] = isset($d['type']) ? trim($d['type']) : '';
		if(isset($d['ip'])) {
			$this->getTypes();
			$typeId = (int) (isset($this->_types[ $d['type'] ]) ? $this->_types[ $d['type'] ]['id'] : $d['type']);
			$insertData = array(
				'ip' => $d['ip'],
				'type' => $typeId,
			);
			if(frameSwr::_()->getTable('blacklist')->insert($insertData)) {
				return true;
			} else
				$this->pushError(__('Database error detected', SWR_LANG_CODE));
		} else
			$this->pushError(__('Empty IP', SWR_LANG_CODE));
		return false;
	}
	public function getList() {
		return $this->getFromTbl();
	}
	public function getByIp($ip) {
		return $this->getList(array('ip' => $ip));
	}
	public function getListForCountries() {
		return $this->getFromTbl(array('tbl' => 'blacklist_countries'));
	}
	public function getBlockedCountryIds() {
		$res = array();
		$countries = $this->getListForCountries();
		if(!empty($countries)) {
			foreach($countries as $c) {
				$res[] = $c['country_id'];
			}
		}
		return $res;
	}
	public function checkIp($ip) {
		$ipBlocked = (int) frameSwr::_()->getTable('blacklist')->get('COUNT(*) AS total', array('ip' => $ip), '', 'one');
		if(!$ipBlocked) {
			$ipBlocked = $this->checkCountryByIp( $ip );
			if(!$ipBlocked) {
				$ipBlocked = $this->checkBrowser();
			}
		}
		return $ipBlocked;
	}
	public function getCountryCode( $ip = false ) {
		static $sxGeo;
		if(!$sxGeo) {
			importClassSwr('SxGeo', SWR_HELPERS_DIR. 'SxGeo.php');
			$sxGeo = new SxGeo(SWR_FILES_DIR. 'SxGeo.dat');
		}
		if(!$ip)
			$ip = utilsSwr::getIP ();
		return $sxGeo->getCountry($ip);
	}
	public function checkCountryByIp($ip) {
		$countryBlocked = (int) $this->getCount(array('tbl' => 'blacklist_countries'));
		if($countryBlocked) {
			$countryBlocked = false;
			$countryCode = $this->getCountryCode($ip);
			if(!empty($countryCode)) {
				$countryBlocked = (int) dbSwr::get('SELECT COUNT(*) AS total FROM @__blacklist_countries 
					INNER JOIN @__countries ON @__countries.id = @__blacklist_countries.country_id
					WHERE @__countries.iso_code_2 = "'. $countryCode. '"', 'one');
			}
		}
		return $countryBlocked;
	}
	public function checkBrowser() {
		$browserBlocked = (int) $this->getCount(array('tbl' => 'blacklist_browsers'));
		if($browserBlocked) {
			$currentBrowser = utilsSwr::getBrowser();
			$browserBlocked = (int) $this
					->setWhere(array('browser_name' => $currentBrowser['name']))
					->getCount(array('tbl' => 'blacklist_browsers'));
		}
		return $browserBlocked;
	}
	public function getTypes() {
		if(empty($this->_types)) {
			$this->_types = array(
				'login' => array('label' => __('Login', SWR_LANG_CODE), 'id' => 1),
				'404' => array('label' => __('404 page brute force', SWR_LANG_CODE), 'id' => 2),
			);
			
		}
		return $this->_types;
	}
	public function getTypeLabelById($id) {
		$this->getTypesLabels();
		return isset($this->_typesLabelsById[ $id ]) ? $this->_typesLabelsById[ $id ] : false;
	}
	public function getTypesLabels() {
		$this->getTypes();
		if(empty($this->_typesLabelsById)) {
			foreach($this->_types as $t) {
				$this->_typesLabelsById[ $t['id'] ] = $t['label'];
			}
		}
		return $this->_typesLabelsById;
	}
	public function addGroup($ips) {
		if(!empty($ips)) {
			if(!is_array($ips)) {
				$ips = array_map('trim', explode(PHP_EOL, $ips));
			}
			$values = array();
			$invalidIps = array();
			foreach($ips as $ip) {
				if(empty($ip)) continue;
				if(strlen($ip) > 16) {
					$invalidIps[] = $ip;
					continue;
				}
				$values[] = '("'. $ip. '")';
			}
			if(!empty($values) && empty($invalidIps)) {
				if(dbSwr::query('INSERT INTO @__'. $this->_tbl. ' (ip) VALUES '. implode(',', $values))) {
					return count($values);
				} else
					$this->pushError (__('Database error detected', SWR_LANG_CODE));
			} else {
				if(count($invalidIps)) {
					$this->pushError(sprintf(__('IPs list contains invalid values: %s', SWR_LANG_CODE), implode(', ', $invalidIps)));
				} else
				$this->pushError(__('Empty IPs list provided', SWR_LANG_CODE));
			}
		} else
			$this->pushError(__('Empty IPs list provided', SWR_LANG_CODE));
		return false;
	}
	public function remove($id) {
		$id = (int) $id;
		if($id) {
			if(frameSwr::_()->getTable( $this->_tbl )->delete(array('id' => $id))) {
				return true;
			} else
				$this->pushError (__('Database error detected', SWR_LANG_CODE));
		} else
			$this->pushError(__('Invalid ID', SWR_LANG_CODE));
		return false;
	}
	public function addGroupCountries($countryIds) {
		// Clear all prev. countries
		frameSwr::_()->getTable('blacklist_countries')->delete();
		if(!empty($countryIds)) {
			if(!is_array($countryIds))
				$countryIds = array( $countryIds );
			$countryIds = array_map('intval', $countryIds);
			if(!dbSwr::query('INSERT INTO @__blacklist_countries (country_id) VALUES ('. implode('),(', $countryIds). ')')) {
				$this->pushError(__('Database error detected', SWR_LANG_CODE));
				return false;
			}
			return count($countryIds);
		}
		return 0;	// No one were added - just cleared country list - this is not error, this is ok
	}
	public function addGroupBrowsers($browserNames) {
		// Clear all prev. countries
		frameSwr::_()->getTable('blacklist_browsers')->delete();
		if(!empty($browserNames)) {
			if(!is_array($browserNames))
				$browserNames = array( $browserNames );
			if(!dbSwr::query('INSERT INTO @__blacklist_browsers (browser_name) VALUES ("'. implode('"),("', $browserNames). '")')) {
				$this->pushError(__('Database error detected', SWR_LANG_CODE));
				return false;
			}
			return count($browserNames);
		}
		return 0;	// No one were added - just cleared browsers list - this is not error, this is ok
	}
	public function getListForBrowsers() {
		return $this->getFromTbl(array('tbl' => 'blacklist_browsers'));
	}
	public function getBlockedBrowsersNames() {
		$res = array();
		$browsers = $this->getListForBrowsers();
		if(!empty($browsers)) {
			foreach($browsers as $b) {
				$res[] = $b['browser_name'];
			}
		}
		return $res;
	}
}
