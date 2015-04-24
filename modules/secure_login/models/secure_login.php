<?php
class secure_loginModelSwr extends modelSwr {
	private $_capchaPublicKey = '6LfUotgSAAAAAL4pqsHxE8sx6Cz8o7AEc_JjtROD';
	private $_capchaPrivateKey = '6LfUotgSAAAAACFAM1TMpIsLiQsfDmV-mRNfQg1n';
	private $_capchaVerifyServer = 'www.google.com';
	
	private $_simpleUserIssues = array();
	
	public function getCapchaPublicKey() {
		return $this->_capchaPublicKey;
	}
	public function getCapchaPrivateKey() {
		return $this->_capchaPrivateKey;
	}
	public function getCapchaVerifyServer() {
		return $this->_capchaVerifyServer;
	}
	public function getSimpleUserIssues() {
		if(empty($this->_simpleUserIssues)) {
			$this->_simpleUserIssues = array(
				'simple_login' => array('label' => __('Login is too simple', SWR_LANG_CODE)),
				'pass_same_as_login' => array('label' => __('Password is same as login', SWR_LANG_CODE)),
				'pass_simple' => array('label' => __('Password is really simple', SWR_LANG_CODE)),
			);
		}
		return $this->_simpleUserIssues;
	}
	public function afterOptionsChange($prevOptsModel, $optsModel, $submitData) {
		// htaccess password option changed
		$prevHtaccessPass = $prevOptsModel->get('htaccess_passwd_enable');
		$currHtaccessPass = $optsModel->get('htaccess_passwd_enable');
		if($currHtaccessPass && !$prevHtaccessPass) {	// Pass enabled
			$login = $submitData['htaccess_login'];
			$passwd = $submitData['htaccess_passwd'];
			$saved = false;
			if(empty($login)) {
				$this->pushError(__('Enter htaccess login', SWR_LANG_CODE), 'htaccess_login');
			} elseif(empty($passwd)) {
				$this->pushError(__('Enter htaccess password', SWR_LANG_CODE), 'htaccess_passwd');
			} else {
				$htpasswdContent = $this->generateHtpasswd($login, $passwd);
				if($htpasswdContent) {
					if($this->isHtpasswdDirWritable()) {
						file_put_contents($this->getHtpasswdFilePath(), $htpasswdContent);
						$this->updateHtaccess_htaccess_passwd_enable();
					} else {
						$htaccessPageLink = frameSwr::_()->getModule('options')->getTabUrl('htaccess');
						$optsModel->save('htaccess_passwd_content', $htpasswdContent);
						$this->pushError(sprintf(__('Can not write to htpasswd directory %s, go to <a href="%s">htaccess page</a> to see next steps.', SWR_LANG_CODE), $this->getHtpasswdDir(), $htaccessPageLink));
					}
					$saved = true;
				} else
					$this->pushError(__('Can not generate htpasswd, please try later', SWR_LANG_CODE));
			}
			if(!$saved)
				$optsModel->save('htaccess_passwd_enable', 0);
		} elseif($prevHtaccessPass && !$currHtaccessPass) {	// Pass disabled
			$this->removeHtaccess_htaccess_passwd_enable();
		}
		// hide admin area option changed
		$prevOpt = $prevOptsModel->get('hide_admin_area_enb');
		$currOpt = $optsModel->get('hide_admin_area_enb');
		if($currOpt && !$prevOpt) {	// Hide wp-admin enabled
			$newSlug = $optsModel->get('hide_admin_area_slug');
			if(empty($newSlug)) {
				$this->pushError(__('Enter new wp-admin slug', SWR_LANG_CODE), 'opt_values[hide_admin_area_slug]');
			} elseif(!$this->checkSlug($newSlug)) {
				$this->pushError(__('New wp-admin slug is invalid', SWR_LANG_CODE), 'opt_values[hide_admin_area_slug]');
			} else {
				$this->updateHtaccess_hide_admin_area_enb( $newSlug );
			}
		} elseif($prevOpt && !$currOpt) {	// Hide admin area disabled
			$this->removeHtaccess_hide_admin_area_enb();
		}
		
		return !$this->haveErrors();
	}
	public function checkSlug($newSlug) {
		return preg_match('/^[a-z0-9]+$/i', $newSlug);
	}
	public function updateHtaccess_hide_admin_area_enb($newSlug = '') {
		if(empty($newSlug))
			$newSlug = frameSwr::_()->getModule('options')->get('hide_admin_area_slug');
		$rules = array(
			'<IfModule mod_rewrite.c>',
			'RewriteEngine On',
			'RewriteRule ^'. $newSlug .'/?$ /wp-login.php?cyB759wM7FXd6735Dmsb [R,L]',
			'RewriteCond %{HTTP_COOKIE} !^.*wordpress_logged_in_.*$',
			'RewriteRule ^'. $newSlug .'/?$ /wp-login.php?cyB759wM7FXd6735Dmsb&redirect_to=/wp-admin/ [R,L]',
			'RewriteRule ^'. $newSlug .'/?$ /wp-admin/?cyB759wM7FXd6735Dmsb [R,L]',
			'RewriteCond %{SCRIPT_FILENAME} !^(.*)admin-ajax\.php',
			'RewriteCond %{HTTP_REFERER} !^(.*)/wp-admin',
			'RewriteCond %{HTTP_REFERER} !^(.*)/wp-login\.php',
			'RewriteCond %{HTTP_REFERER} !^(.*)/'.$newSlug,
			'RewriteCond %{QUERY_STRING} !^cyB759wM7FXd6735Dmsb',
			'RewriteCond %{QUERY_STRING} !^action=logout',
			'RewriteCond %{QUERY_STRING} !^action=rp',
			'RewriteCond %{QUERY_STRING} !^action=postpass',
			'RewriteCond %{HTTP_COOKIE} !^.*wordpress_logged_in_.*$',
			'RewriteRule ^.*wp-admin/?|^.*wp-login\.php - [R=404,L]',
			'RewriteCond %{QUERY_STRING} ^loggedout=true',
			'RewriteRule ^.*$ /wp-login.php?cyB759wM7FXd6735Dmsb [R,L]',
			'</IfModule>',
		);
		if(!frameSwr::_()->getModule('htaccess')->savePart('hide_admin_area_enb', $rules, false, true)) {
			$this->pushError(frameSwr::_()->getModule('htaccess')->getErrors());
		}
	}
	public function removeHtaccess_hide_admin_area_enb() {
		if(!frameSwr::_()->getModule('htaccess')->removePart('hide_admin_area_enb')) {
			$this->pushError(frameSwr::_()->getModule('htaccess')->getErrors());
		}
	}
	public function updateHtaccess_htaccess_passwd_enable() {
		$htpasswdPath = $this->getHtpasswdFilePath();
		$authRules = array(
			'AuthType Basic',
			'AuthName "Authentication Required"',
			'AuthUserFile "'. $htpasswdPath. '"',
			'Require valid-user',
			'Satisfy All',
		);
		$authRulesForLoginFile = array_merge(array('<Files wp-login.php>'), $authRules, array('</Files>'));
		$adminAuthRules = array_merge(
			array(
			'<FilesMatch "(.+?\.(?!(ico$|pdf$|flv$|jpg$|jpeg$|mp3$|mpg$|mp4$|mov$|wav$|wmv$|png$|gif$|swf$|css$|js$)).+)">'),
				$authRules,
		array('</FilesMatch>',
			'<Files admin-ajax.php>',
			'Order allow,deny',
			'Allow from all',
			'Satisfy any',
			'</Files>',
			'<Files async-upload.php>',
			'Order allow,deny',
			'Allow from all',
			'Satisfy any',
			'</Files>'));
		if(!frameSwr::_()->getModule('htaccess')->savePart('htaccess_passwd_enable', $adminAuthRules, true)) {
			$this->pushError(frameSwr::_()->getModule('htaccess')->getErrors());
		}
		if(!frameSwr::_()->getModule('htaccess')->savePart('htaccess_passwd_enable', $authRulesForLoginFile)) {
			$this->pushError(frameSwr::_()->getModule('htaccess')->getErrors());
		}
	}
	public function removeHtaccess_htaccess_passwd_enable() {
		if(!frameSwr::_()->getModule('htaccess')->removePart('htaccess_passwd_enable', true)) {
			$this->pushError(frameSwr::_()->getModule('htaccess')->getErrors());
		}
		if(!frameSwr::_()->getModule('htaccess')->removePart('htaccess_passwd_enable')) {
			$this->pushError(frameSwr::_()->getModule('htaccess')->getErrors());
		}
		$htpasswdPath = $this->getHtpasswdFilePath();
		if(utilsSwr::isWritable($htpasswdPath)) {
			unlink($htpasswdPath);
		} elseif(file_exists($htpasswdPath))
			$this->pushError(sprintf(__('Can not remove http password file %s, you can do this using FTP or other server file manager', SWR_LANG_CODE), $htpasswdPath));
		frameSwr::_()->getModule('options')->getModel()->save('htaccess_passwd_content', '');
	}
	public function getHtpasswdDir() {
		return ABSPATH;
	}
	public function getHtpasswdFilePath() {
		return $this->getHtpasswdDir(). '.htpasswd';
	}
	public function isHtpasswdDirWritable() {
		$dir = $this->getHtpasswdDir();
		return utilsSwr::isWritable($dir);
	}
	public function generateHtpasswd($login, $passwd) {
		$createdPasswdRes = wp_remote_post( 'http://www.htaccesstools.com/htpasswd-generator/' , array( 
			'timeout'	=> 30,
			'method'   => 'POST', 
			'body'     => array('username' => $login, 'password' => $passwd, 'submit' => 'Create .htpasswd file')    ) );
		if($createdPasswdRes && !is_wp_error($createdPasswdRes) && isset($createdPasswdRes['body']) && !empty($createdPasswdRes['body'])) {
			if(preg_match('/\<textarea\s+name\=\"code\"\s+class\=\"generated-code\"\>(.+)\<\/textarea\>/us', $createdPasswdRes['body'], $matches)
				&& isset($matches[1])
			) {
				$htpasswdData = trim($matches[1]);
				if(!empty($htpasswdData)) {
					return $htpasswdData;
				}
			}
		}
		return false;
	}
	public function getSimpleAdminsList() {
		$res = array();
		$adminsList = frameSwr::_()->getModule('user')->getAdminsList();
		if(!empty($adminsList)) {
			$siteName = get_bloginfo('name');
			$siteUrl = get_bloginfo('url');
			$siteAddress = str_replace(array('http://', 'https://'), '', $siteUrl);
			$siteServer = $_SERVER['HTTP_HOST'];
			$simpleLogins = array_map('strtolower', array('admin', 'test', $siteName, $siteUrl, $siteAddress, $siteServer));
			if(!function_exists('wp_hash_password'))
				frameSwr::_()->loadPlugins();
			foreach($adminsList as $admin) {
				if(is_super_admin($admin['ID'])) {
					if(in_array(strtolower($admin['user_login']), $simpleLogins)) {
						$this->_addSimpleAdminToRes($admin, 'simple_login', $res);
					}
					if(wp_check_password($admin['user_login'], $admin['user_pass'], $admin['ID'])) {
						$this->_addSimpleAdminToRes($admin, 'pass_same_as_login', $res);
					}
					foreach($simpleLogins as $sl) {
						if(wp_check_password($sl, $admin['user_pass'], $admin['ID'])) {
							$this->_addSimpleAdminToRes($admin, 'pass_simple', $res);
						}
					}
				}
			}
		}
		return $res;
	}
	private function _addSimpleAdminToRes($user, $issue, &$res) {
		if(!isset($res[ $user['ID'] ])) {
			$res[ $user['ID'] ] = array(
				'user' => $user,
				'issues' => array(),
			);
		}
		$res[ $user['ID'] ]['issues'][] = $issue;
	}
}
