<?php
class htaccessSwr extends moduleSwr {
	private $_content = '';
	private $_adminContent = '';
	private $_noFilesUse = false;
	
	public function init() {
		parent::init();
		if(frameSwr::_()->isAdminPlugOptsPage()) {
			add_action('admin_notices', array($this, 'showUnsavedHtaccessErrors'));
		}
	}
	public function savePart($code, $data, $forAdmin = false, $addToBegining = false) {
		$eol = PHP_EOL;
		$content = $this->getContent($forAdmin);
		if(is_array($data))
			$data = implode($eol, $data);
		$tag = $this->_makeTag( $code );
		$addContent = $eol. $tag['begin']. $eol. $data. $eol. $tag['end']. $eol;
		$removedContent = $this->removePart($code, $forAdmin, true);
		if($removedContent !== false)
			$content = $removedContent;
		return $this->putContent(($addToBegining ? $addContent. $content : $content. $addContent), $forAdmin);
	}
	public function getContent($forAdmin = false) {
		if($this->_noFilesUse) {
			if($forAdmin && !empty($this->_adminContent)) {
				return $this->_adminContent;
			} elseif(!$forAdmin && !empty($this->_content)) {
				return $this->_content;
			}
		}
		$path = $this->getPath($forAdmin);
		if(file_exists($path)) {
			return file_get_contents( $path );
		}
		return '';
	}
	public function putContent($content, $forAdmin = false) {
		$content = trim($content);
		if($this->_noFilesUse) {
			if($forAdmin) {
				$this->_adminContent = $content;
			} else {
				$this->_content = $content;
			}
			return true;
		}
		$path = $this->getPath($forAdmin);
		if(!file_exists($path)) {
			$createdFile = @fopen($path, 'w');
			@fclose($createdFile);
		}
		if(is_writable($path)) {
			file_put_contents($path, $content);
			return true;
		} else {
			$this->pushError(sprintf(__('File %s is not writable, put there next content using FTP or other filemanager: <pre class="swrHtaccessPre">%s<br /></pre>', SWR_LANG_CODE), $path, htmlspecialchars($content)));
			return false;
		}
	}
	public function getPath($forAdmin = false) {
		if($forAdmin)
			return ABSPATH. 'wp-admin'. DS. '.htaccess';
		else
			return ABSPATH. '.htaccess';
	}
	public function removePart($code, $forAdmin = false, $notUpdateFile = false) {
		$tag = $this->_makeTag( $code );
		$content = $this->getContent($forAdmin);
		if(strpos($content, $tag['begin']) !== false) {
			$content = trim(preg_replace('/'. $tag['begin']. '.+'. $tag['end']. '/us', '', $content));
			if(!$notUpdateFile) {
				$this->putContent($content, $forAdmin);
			}
			return $content;
		}
		return false;
	}
	private function _makeTag($code) {
		$innerCode = '### SUPSYSTIC SECURE';
		return array(
			'begin' => $innerCode. ' - '. $code. ' - BEGIN',
			'end' => $innerCode. ' - '. $code. ' - END',
		);
	}
	public function showUnsavedHtaccessErrors() {
		if($this->checkUnsavedHtaccess()) {
			$htaccessPageLink = frameSwr::_()->getModule('options')->getTabUrl( $this->getCode() );
			$html = '<div class="error"><p>'.
				sprintf(__('Your have unsaved changes in your .htaccess file. Please check <a href="%s">this page</a>.', SWR_LANG_CODE), $htaccessPageLink)
			.'</p></div>';
			echo $html;
		}
	}
	public function checkUnsavedHtaccess() {
		// REMOVE THIS  !!!!!
		//return true;
		$allOpts = frameSwr::_()->getModule('options')->getAll();
		$htaccessContent = $this->getContent();
		$adminHtaccessContent = $this->getContent(true);
		foreach($allOpts as $catKey => $cData) {
			foreach($cData['opts'] as $optKey => $opt) {
				if($opt['value'] && isset($opt['htaccessChange']) && $opt['htaccessChange']) {
					$tag = $this->_makeTag( $optKey );
					$regExpr = '/'. $tag['begin']. '.+'. $tag['end']. '/us';
					$checkAdminOnly = isset($opt['forAdminHtaccess']) && $opt['forAdminHtaccess'];
					$checkAdmin = isset($opt['forBothHtaccess']) && $opt['forBothHtaccess'];
					$checkFrontend = !$checkAdminOnly;
					if($checkAdminOnly || $checkAdmin) {
						if(!preg_match($regExpr, $adminHtaccessContent)) {
							return true;
						}
					}
					if($checkFrontend) {
						if(!preg_match($regExpr, $htaccessContent)) {
							return true;
						}
					}
				}
			}
		}
		$htpasswdContent = frameSwr::_()->getModule('options')->get('htaccess_passwd_content');
		$htpasswdPath = frameSwr::_()->getModule('secure_login')->getModel()->getHtpasswdFilePath();
		if(!empty($htpasswdContent) && !file_exists($htpasswdPath)) {
			return true;
		}
		return false;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function getRequiredContent() {
		$this->_noFilesUse = true;
		$allOpts = frameSwr::_()->getModule('options')->getAll();
		$this->_content = $this->getContent();
		$this->_adminContent = $this->getContent(true);
		foreach($allOpts as $catKey => $cData) {
			foreach($cData['opts'] as $optKey => $opt) {
				if($opt['value'] && isset($opt['htaccessChange']) && $opt['htaccessChange']) {
					$needChange = true;
					/*$tag = $this->_makeTag( $optKey );
					$regExpr = '/'. $tag['begin']. '.+'. $tag['end']. '/us';
					$checkAdminOnly = isset($opt['forAdminHtaccess']) && $opt['forAdminHtaccess'];
					$checkAdmin = isset($opt['forBothHtaccess']) && $opt['forBothHtaccess'];
					$checkFrontend = !$checkAdminOnly;
					if($checkAdminOnly || $checkAdmin) {
						if(!preg_match($regExpr, $this->_adminContent)) {
							$needChange = true;
						}
					} 
					if($checkFrontend) {
						if(!preg_match($regExpr, $this->_content)) {
							$needChange = true;
						}
					}*/
					if($needChange) {
						$model = frameSwr::_()->getModule( $catKey )->getModel();
						$addDataMethod = 'updateHtaccess_'. $optKey;

						if(method_exists($model, $addDataMethod)) {
							call_user_func(array($model, $addDataMethod));
						}
					}
				}
			}
		}
		$htpasswdContent = frameSwr::_()->getModule('options')->get('htaccess_passwd_content');
		
		$this->_noFilesUse = false;
		return array(
			'content' => $this->_content,
			'adminContent' => $this->_adminContent,
			'htpasswdContent' => $htpasswdContent,
		);
	}
}

