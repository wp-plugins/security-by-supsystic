<?php
class secure_filesModelSwr extends modelSwr {
	private $_issueTypes = array(
		'modified' => array('id' => 1),
		'global_perms' => array('id' => 2),
	);
	private $_locationTypes = array(
		'core' => array('id' => 0),
		'plugins' => array('id' => 1),
		'themes' => array('id' => 2),
		'uploads' => array('id' => 3),
	);
	private $_maxFileSize = 0;
	public function __construct() {
		$this->_setTbl('files_issues');
		$this->_maxFileSize = 30 * 1024 * 1024;	// 30MB
		foreach($this->_locationTypes as $code => $type) {
			$this->_locationTypes[ $code ]['code'] = $code;
		}
		foreach($this->_issueTypes as $code => $type) {
			$this->_issueTypes[ $code ]['code'] = $code;
		}
		$this->_issueTypes['modified']['label'] = __('UnAuthorize changes', SWR_LANG_CODE);
		$this->_issueTypes['global_perms']['label'] = __('777 Permissions', SWR_LANG_CODE);
		
	}
	public function getIssueLabelById( $id ) {
		static $issuesById = array();
		if(!$issuesById) {
			foreach($this->_issueTypes as $key => $data) {
				$issuesById[ $data['id'] ] = $data;
			}
		}
		return isset($issuesById[ $id ]) ? $issuesById[ $id ]['label'] : '';
	}
	public function startNewModifiedScan() {
		frameSwr::_()->getTable('files_issues')->update(
			array('last_scan' => 0), 
			array('type' => $this->_issueTypes['modified']['id']));
	}
	public function getFilesList($directory = false, $params = array()) {
		$onlyFiles = isset($params['only_files']) ? (int) $params['only_files'] : 0;
		if(!$directory)
			$directory = ABSPATH;
		$files = glob(realpath($directory). '/*');
		$res = array();
		if(!empty($files)) {
			$diffRootPathWithSufix = array();
			$wpRootPath = ABSPATH;
			if(in_array(substr($wpRootPath, - 1), array('/', DS))) {
				$rootPathWithoutSuff = substr($wpRootPath, 0, - 1);
				$diffRootPathWithSufix[] = $rootPathWithoutSuff. '/';
				$diffRootPathWithSufix[] = $rootPathWithoutSuff. DS;
			} else {
				$diffRootPathWithSufix[] = $wpRootPath;
			}
			foreach($files as $file) {
				$isDir = false;
				if(is_dir($file)) {
					$subfiles = $this->getFilesList( $file, $params );
					if($subfiles && is_array($subfiles)) {
						$res = array_merge($res, $subfiles);
					}
					$isDir = true;
				}
				if(!$onlyFiles || !$isDir) {
					$filePath = trim(str_replace($diffRootPathWithSufix, '', $file));
					if(!empty($filePath))
						$res[] = $filePath;
				}
			}
		}
		return $res;
	}
	public function checkFilesChange($d = array()) {
		$files = isset($d['files']) ? $d['files'] : array();
		if(!empty($files)) {
			$files = array_map('stripslashes', $files);
			$wpVersion = get_bloginfo('version');
			$filesPathMd5 = array();
			foreach($files as $i => $filepath) {
				$filesPathMd5[ $i ] = md5($filepath, true);
			}
			$savedFiles = $this->getListByPathMd5( $filesPathMd5 );
			$insertData = $updateData = $issuedFiles = array();
			$pluginsVersionsData = get_option('_site_transient_update_plugins');
			if(!empty($pluginsVersionsData) && isset($pluginsVersionsData->checked) && !empty($pluginsVersionsData->checked)) {
				$pluginsVersionsData = $pluginsVersionsData->checked;
			} else {
				$pluginsVersionsData = array();
			}
			foreach($files as $i => $filepath) {
				if(empty($filepath)) continue;
				$fullPath = ABSPATH. DS. $filepath;
				$fileSize = filesize($fullPath);
				if($fileSize > $this->_maxFileSize) continue;	// Avoid break execution if really large file found
				$filePathMd5 = $filesPathMd5[ $i ];
				$fileMd5 = md5_file($fullPath, true);
				$locationType = $this->getLocationTypeForFile($filepath);
				$version = false;
				if($locationType['code'] == 'plugins') {
					$version = $this->_extractVertionFor($filepath, $pluginsVersionsData);
				}
				if(!$version)
					$version = $wpVersion;
				if(isset($savedFiles[ $filePathMd5 ])) {
					if($savedFiles[ $filePathMd5 ]['md5'] != $fileMd5) {
						if($savedFiles[ $filePathMd5 ]['version'] == $version) {
							$issuedFiles[] = '("'. $this->_md5ToDb($filePathMd5). '", "'
								. $this->_md5ToDb($filepath). '", "'
								. $this->_md5ToDb(basename($filepath)). '", "'
								. filemtime($fullPath). '", "'
								. $this->_issueTypes['modified']['id']. '", "'
								. $locationType['id']. '")';
						}
						$updateData[] = array(
							'values' => array('md5' => $this->_md5ToDb($fileMd5), 'version' => $version), 
							'where' => array('md5' => $this->_md5ToDb($savedFiles[ $filePathMd5 ]['md5'])));
					}
				} else {
					$insertData[] = '("'. $this->_md5ToDb($filePathMd5). '", "'
							. $this->_md5ToDb($filepath). '", "'
							. $this->_md5ToDb($fileMd5). '", "'
							. $this->_md5ToDb($fileMd5). '", "'
							. $version. '")';
				}
			}
			if(!empty($insertData)) {
				if(!dbSwr::query('INSERT INTO `@__files_snapshot` (`filepathMd5`, `filepath`, `md5`, `md5_old`, `version`) VALUES '. implode(',', $insertData))) {
					$this->pushError (__('Error insert data to database', SWR_LANG_CODE));
					return false;
				}
			}
			if(!empty($updateData)) {
				foreach($updateData as $updateD) {
					if(!frameSwr::_()->getTable('files_snapshot')->update($updateD['values'], $updateD['where'])) {
						$this->pushError (__('Error update data in database', SWR_LANG_CODE));
						return false;
					}
				}
			}
			if(!empty($issuedFiles)) {
				if(!dbSwr::query('INSERT INTO `@__files_issues` (`filepathMd5`, `filepath`, `filename`, `last_time_modified`, `type`, `location_type`) VALUES '. implode(',', $issuedFiles))) {
					$this->pushError (__('Error issued insert data to database', SWR_LANG_CODE));
					return false;
				}
			}
			return true;
		} else
			$this->pushError (__('Empty files set', SWR_LANG_CODE));
		return false;
	}
	private function _extractVertionFor($filepath, $versionsArr) {
		$pathPlugName = explode('plugins', $filepath);
		if(isset($pathPlugName[1]) && !empty($pathPlugName[1])) {
			$pathPlugName[1] = substr($pathPlugName[1], 1);
			$plugDir = explode(DS, $pathPlugName[1]);
			$plugDir = $plugDir[0];
			foreach($versionsArr as $key => $val) {
				if(strpos($key, $plugDir) === 0) {
					return $val;
				}
			}
		}
		return false;
	}
	public function getListByPathMd5($filePathMd5) {
		$res = array();
		$filePathMd5 = array_map(array($this, '_md5ToDb'), $filePathMd5);
		$data = frameSwr::_()->getTable('files_snapshot')->get('*', array('additionalCondition' => 'filepathMd5 IN ("'. implode('","', $filePathMd5). '")'));
		if(!empty($data)) {
			foreach($data as $d) {
				$res[ $d['filepathMd5'] ] = $d;
			}
		}
		return $res;
	}
	private function _md5ToDb($md5) {
		return mysql_real_escape_string( $md5 );
	}
	public function getLocationTypeForFile($filepath) {
		$typeKey = '';
		if(strpos($filepath, 'wp-content'. DS. 'plugins') !== false) {
			$typeKey = 'plugins';
		} elseif(strpos($filepath, 'wp-content'. DS. 'themes') !== false) {
			$typeKey = 'themes';
		} elseif(strpos($filepath, 'wp-content'. DS. 'uploads') !== false) {
			$typeKey = 'uploads';
		} else {
			$typeKey = 'core';
		}
		return $this->_locationTypes[ $typeKey ];
	}
	public function getModifiedLastErrorsCount() {
		return $this->addWhere(array('last_scan' => 1))
				->addWhere(array('type' => $this->_issueTypes['modified']['id']))
				->getCount(array('tbl' => 'files_issues'));
	}
	public function getPermsLastErrorsCount() {
		return $this->addWhere(array('last_scan' => 1))
				->addWhere(array('type' => $this->_issueTypes['global_perms']['id']))
				->getCount(array('tbl' => 'files_issues'));
	}
	public function checkFilesPerms($d = array()) {
		$files = isset($d['files']) ? $d['files'] : array();
		if(!empty($files)) {
			$files = array_map('stripslashes', $files);
			$issuedFiles = array();
			foreach($files as $i => $filepath) {
				if(empty($filepath)) continue;
				$fullPath = ABSPATH. DS. $filepath;
				$perms = fileperms($fullPath) & 0777;
				if($perms == 0777) {
					$locationType = $this->getLocationTypeForFile($filepath);
					$filePathMd5 = md5( $filepath );
					$issuedFiles[] = '("'. $this->_md5ToDb($filePathMd5). '", "'
						. $this->_md5ToDb($filepath). '", "'
						. $this->_md5ToDb(basename($filepath)). '", "'
						. filemtime($fullPath). '", "'
						. $this->_issueTypes['global_perms']['id']. '", "'
						. $locationType['id']. '")';
				}
			}
			if(!empty($issuedFiles)) {
				if(!dbSwr::query('INSERT INTO `@__files_issues` (`filepathMd5`, `filepath`, `filename`, `last_time_modified`, `type`, `location_type`) VALUES '. implode(',', $issuedFiles))) {
					$this->pushError (__('Error issued insert data to database', SWR_LANG_CODE));
					return false;
				}
			}
			return true;
		} else
			$this->pushError (__('Empty files set', SWR_LANG_CODE));
		return false;
	}
	public function getIssueTypes() {
		return $this->_issueTypes;
	}
}
