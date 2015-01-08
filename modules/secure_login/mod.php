<?php
class secure_loginSwr extends moduleSwr {
	public function init() {
		parent::init();
		dispatcherSwr::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		dispatcherSwr::addFilter('optionsDefine', array($this, 'addOptions'));
		add_action('login_form', array($this, 'showCapchaOnLogin'));
		add_filter('wp_authenticate_user', array($this, 'checkLoginCapcha'), 99);
		add_filter('wp_authenticate_user', array($this, 'checkAdminIpLogin'), 99);
		add_action('wp_login_failed', array($this, 'addInvalidLoginTry'));
		add_action('user_profile_update_errors', array($this, 'checkPasswordStrength'));
		$this->checkAdminPasswordsChange();	// see option admin_pass_change_enb
		add_action('admin_notices', array($this, 'checkChangePassMsg'));
		add_action('user_profile_update_errors', array($this, 'resetRemoveChangePassMsg'), 99, 3);
		add_filter('login_errors', array($this, 'checkLoginErrorDisable'));
		add_action('plugins_loaded', array($this, 'checkLoginPageRestrict'), 1);
	}
	public function addAdminTab($tabs) {
		$tabs[ $this->getCode() ] = array(
			'label' => __('Login Security', SWR_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'fa_icon' => 'fa-key', 'sort_order' => 10,
		);
		return $tabs;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function addOptions($opts) {
		$opts[ $this->getCode() ] = array(
			'label' => __('Login Security', SWR_LANG_CODE),
			'opts' => array(
				'capcha_on_login' => array('label' => __('Capcha on login', SWR_LANG_CODE), 'weight' => 70, 'html' => 'checkboxHiddenVal', 'desc' => __('CAPTCHA is one of the most effective means against automatic password selection, the so called broot force. Of course, CAPTCHA creates a certain inconvenience for a user that needs to write it down correctly. That’s why we use the verified reCapcha from Google that is complicated for bots and simple for humans.', SWR_LANG_CODE)),
				
				'htaccess_passwd_enable' => array('label' => __('Anti BrootForce second password', SWR_LANG_CODE), 'weight' => 65, 'html' => 'checkboxHiddenVal', 'htaccessChange' => true, 'forBothHtaccess' => true, 'desc' => __('A simple and effective tool that allows you to reduce the probability of guessing a password brute force and at the same time protect the load associated with such an attack.', SWR_LANG_CODE)),
				'htaccess_passwd_content' => array('label' => __('Htaccess password content', SWR_LANG_CODE)),
				
				'login_lockout' => array('label' => __('Login lockout', SWR_LANG_CODE), 'weight' => 60, 'html' => 'checkboxHiddenVal', 'desc' => __('The most common way to hack a site is by selecting the username/password through the login form. To prevent such hacking attack, select the automatic add-on of an IP that was used to pick the username/password for your site to black list, in case such IP has generated several unsuccessful login attempts.', SWR_LANG_CODE)),
				//+++
				'login_lockout_attempts' => array('label' => __('Attempts', SWR_LANG_CODE), 'def' => 3),
				'login_lockout_stop_time' => array('label' => __('Stop time', SWR_LANG_CODE), 'def' => 5),
				'login_lockout_attempts_data' => array('label' => __('Attempts Array', SWR_LANG_CODE), 'def' => array()),
				// enb == enable
				'passwd_min_length_enb' => array('label' => __('Minimal password length', SWR_LANG_CODE), 'weight' => 20, 'html' => 'checkboxHiddenVal', 'desc' => __('A password is the key to your website. By using a combination of letters, numbers and symbols in your password you increase the safety of your account. The default length of a WordPress password is 7 symbols but you can increase this number with the help of this option.', SWR_LANG_CODE)),
				//+++
				'passwd_min_length' => array('label' => __('Min pass length symbols', SWR_LANG_CODE), 'def' => 7),
				
				'admin_ip_login_enb' => array('label' => __('Admin IP login protection', SWR_LANG_CODE), 'weight' => 40, 'html' => 'checkboxHiddenVal', 'desc' => __('Attaching the login permission to any IP is one of the most effective means of protection. However, such protection may cause some inconvenience. Thus, by activating this option you yourself won’t be able to login from another IP.', SWR_LANG_CODE)),
				//+++
				'admin_ip_login_list' => array('label' => __('Admin IP list', SWR_LANG_CODE), 'def' => ''),
				// enb == enable
				'admin_pass_change_enb' => array('label' => __('Regular admin change passwords', SWR_LANG_CODE), 'weight' => 40, 'html' => 'checkboxHiddenVal', 'desc' => __('', SWR_LANG_CODE)),
				//+++
				'admin_pass_change_freq' => array('label' => __('Admin pass change freq', SWR_LANG_CODE), 'def' => '30'),
				'admin_pass_change_auto' => array('label' => __('Do it auto', SWR_LANG_CODE), 'def' => '0'),
				'admin_pass_change_last_check' => array('label' => __('Do it auto', SWR_LANG_CODE), 'def' => '0'),
				// enb == enable
				'hide_login_errors_enb' => array('label' => __('Hide login error messages', SWR_LANG_CODE), 'weight' => 20, 'html' => 'checkboxHiddenVal', 'desc' => __('Will not display errors on login form if login was incorrect.', SWR_LANG_CODE)),
				// enb == enable
				'hide_login_page_enb' => array('label' => __('Hide login page', SWR_LANG_CODE), 'weight' => 30, 'html' => 'checkboxHiddenVal', 'htaccessChange' => true, 'desc' => __('The attacker will not know the address of the page to log in to your site - this will reduce the risk of breaking of site.', SWR_LANG_CODE)),
				'hide_login_page_slug' => array('label' => __('New login slug', SWR_LANG_CODE), 'def' => ''),
			),
		);
		return $opts;
	}
	public function showCapchaOnLogin() {
		if(frameSwr::_()->getModule('options')->get('capcha_on_login')) {
			frameSwr::_()->getModule('templates')->loadFontAwesome();
			echo $this->getView()->getCapchaOnLogin();
		}
	}
	public function checkLoginCapcha($user) {
		if(frameSwr::_()->getModule('options')->get('capcha_on_login') && !is_wp_error($user)) {
			if(!$this->recaptchaCheckAnswer($this->getModel()->getCapchaPrivateKey(), 
				$_SERVER['REMOTE_ADDR'], 
				reqSwr::getVar('recaptcha_challenge_field', 'post'),
				reqSwr::getVar('recaptcha_response_field', 'post'))
			) {
				$user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid words from capcha.', SWR_LANG_CODE));
			}
		}
		return $user;
	}
	public function checkAdminIpLogin($user) {
		if(frameSwr::_()->getModule('options')->get('admin_ip_login_enb') 
			&& !is_wp_error($user) 
			&& is_super_admin( $user->ID )
		) {
			$ipListStr = frameSwr::_()->getModule('options')->get('admin_ip_login_list');
			if($ipListStr) {
				$ipListArr = array_map('trim', explode(SWR_EOL, $ipListStr));
				$currIp = utilsSwr::getIP();
				if(!in_array($currIp, $ipListArr)) {
					$user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: You can not login from this IP.', SWR_LANG_CODE));
				}
			}
		}
		return $user;
	}
	public function recaptchaCheckAnswer ($privkey, $remoteip, $challenge, $response, $extra_params = array()) {
		if ($privkey == null || $privkey == '') {
			return false;
		}
		if ($remoteip == null || $remoteip == '') {
			return false;
		}
		//discard spam submissions
		if ($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0) {
			return false;
		}
		$response = $this->_recaptchaHttpPost($this->getModel()->getCapchaVerifyServer(), '/recaptcha/api/verify',
			array (
				'privatekey' => $privkey,
				'remoteip' => $remoteip,
				'challenge' => $challenge,
				'response' => $response,
			) + $extra_params
		);
		if(empty($response))	// Empty answer from server - just let it go
			return true;
		$answers = explode ("\n", $response);
		if (trim ($answers [0]) == 'true') {
			return true;
		}
		return false;
	}
	private function _recaptchaHttpPost($host, $path, $data, $port = 80) {
			$req = $this->_recaptchaQsencode ($data);
			$eol = "\r\n";
			$reqData = array(
				"POST $path HTTP/1.0",
				"Host: $host",
				"Content-Type: application/x-www-form-urlencoded",
				"Content-Length: " . strlen($req),
				"User-Agent: reCAPTCHA/PHP",
			);
			$response = '';
			// Usual sock request
			// FIX ===
			if( false === ( $fs = @fsockopen($host, $port, $errno, $errstr, 20) ) ) {
				$httpRequest = implode($eol, $reqData). $eol.$eol. $req;
				fwrite($fs, $httpRequest);
				while ( !feof($fs) )
					$response .= fgets($fs, 1160); // One TCP-IP packet
				fclose($fs);
				$response = explode($eol. $eol, $response, 2);
			} else {	// But if this will not work - try to make wp remove request
				$requestUrl = 'http://'. $host. $path;
				$headers = implode($eol, $reqData);

				$request = new WP_Http;
				$wpRemoteReq = $request->request( $requestUrl , array( 'method' => 'POST', 'body' => $req, 'headers' => $headers ) );
				if($wpRemoteReq && isset($wpRemoteReq['body']) && !empty($wpRemoteReq['body'])) {
					$response = $wpRemoteReq['body'];
				}
			}
			return $response;
	}
	private function _recaptchaQsencode ($data) {
		$req = "";
		foreach ( $data as $key => $value )
			$req .= $key . '=' . urlencode( stripslashes($value) ) . '&';

		// Cut the last '&'
		$req = substr($req, 0, strlen($req)-1);
		return $req;
	}
	public function addInvalidLoginTry() {
		if(frameSwr::_()->getModule('options')->get('login_lockout')) {
			$lockoutAttempts = frameSwr::_()->getModule('options')->get('login_lockout_attempts_data');
			if(!$lockoutAttempts)
				$lockoutAttempts = array();
			$ip = utilsSwr::getIP();
			$time = time();
			$blocked = false;
			if(!isset($lockoutAttempts[ $ip ])) {
				$lockoutAttempts[ $ip ] = array(
					'attempts' => 1,
					'last_try' => $time,
				);
			} else {
				$stopTime = (int) frameSwr::_()->getModule('options')->get('login_lockout_stop_time');
				if(!$stopTime || ($time - $lockoutAttempts[ $ip ]['last_try']) <= $stopTime * 60) {
					$lockoutAttempts[ $ip ]['attempts']++;
				}
				$lockoutAttempts[ $ip ]['last_try'] = $time;
			}
			// Block IP
			if($lockoutAttempts[ $ip ]['attempts'] >= frameSwr::_()->getModule('options')->get('login_lockout_attempts')) {
				frameSwr::_()->getModule('blacklist')->getModel()->save(array(
					'ip' => $ip, 
					'type' => 'login'));
				$notifyEmail = frameSwr::_()->getModule('options')->get('notify_email');
				if(!empty($notifyEmail)) {
					frameSwr::_()->getModule('mail')->send(
							$notifyEmail, 
							__('Login form blocked IP', SWR_LANG_CODE), 
							sprintf(__('On your site %s login form was added IP %s to blacklist after it tried to login %d times.', SWR_LANG_CODE), SWR_SITE_URL, $ip, $lockoutAttempts[ $ip ]['attempts']));
				}
				unset($lockoutAttempts[ $ip ]);
				$blocked = true;
			}
			frameSwr::_()->getModule('options')->getModel()->save('login_lockout_attempts_data', $lockoutAttempts);
			if($blocked) {	// If blocked - just redirect to show block page
				redirect(uriSwr::getFullUrl());
			}
		}
	}
	public function checkPasswordStrength($errors) {
		if(frameSwr::_()->getModule('options')->get('passwd_min_length_enb')) {
			$pass1 = reqSwr::getVar('pass1', 'post');
			$minLength = (int) frameSwr::_()->getModule('options')->get('passwd_min_length');
			if(!empty($pass1) && $minLength && strlen($pass1) < $minLength) {
				$errors->add('weak-password', sprintf(__('Password should be at least %d symbols', SWR_LANG_CODE), $minLength), array( 'form-field' => 'pass1' ));
			}
		}
	}
	public function checkAdminPasswordsChange() {
		if(frameSwr::_()->getModule('options')->get('admin_pass_change_enb')) {
			$time = time();
			$lastCheck = (int) frameSwr::_()->getModule('options')->get('admin_pass_change_last_check');
			if(!$lastCheck) {
				$lastCheck = $time;
				frameSwr::_()->getModule('options')->getModel()->save('admin_pass_change_last_check', $time);
			}
			$checkDays = (int) frameSwr::_()->getModule('options')->get('admin_pass_change_freq');
			if($checkDays && ($time - $lastCheck) >= $checkDays * 3600 * 24) {
				// We need this to trigger after pluggable functions will be loaded
				add_action('plugins_loaded', array($this, 'makeAdminsPasswordChange'));
			}
		}
	}
	public function makeAdminsPasswordChange() {
		$adminsList = frameSwr::_()->getModule('user')->getAdminsList();
		$time = time();
		if(!empty($adminsList)) {
			$autoChange = frameSwr::_()->getModule('options')->get('admin_pass_change_auto');
			foreach($adminsList as $admin) {
				if($autoChange && isset($admin['user_email']) && !empty($admin['user_email'])) {
					$newPass = $this->generateNewPass($admin);
					if($newPass) {
						$this->sendNewPass($admin, $newPass);
					}
				} else
					update_user_meta($admin['ID'], SWR_CODE. '_pass_change_require', $time);
			}
		}
		frameSwr::_()->getModule('options')->getModel()->save('admin_pass_change_last_check', $time);
	}
	public function generateNewPass($user) {
		if(!function_exists('wp_generate_password'))
			frameSwr::_()->loadPlugins();
		$newPass = wp_generate_password(mt_rand(12, 16));
		if(!empty($newPass)) {
			wp_set_password($newPass, $user['ID']);
			return $newPass;
		}
		return false;
	}
	public function sendNewPass($user, $newPass) {
		frameSwr::_()->getModule('mail')->send(
			$user['user_email'], 
			__('Password changed', SWR_LANG_CODE), 
			sprintf(__('Password on site %s for your admin account was changed due expiration date. Your new password is:<br /> %s', SWR_LANG_CODE), SWR_SITE_URL, $newPass));
	}
	public function checkChangePassMsg() {
		if(frameSwr::_()->getModule('options')->get('admin_pass_change_enb')) {
			$passChangeRequire = get_user_meta(get_current_user_id(), SWR_CODE. '_pass_change_require');
			if($passChangeRequire) {
				$profileLink = get_edit_user_link();
				$html = '<div class="update-nag">'.
						sprintf(__('Your password has expired. Go to your profile <a href="%s">%s</a> and change it.', SWR_LANG_CODE), $profileLink, $profileLink)
						.'</div>';
				echo $html;
			}
		}
	}
	public function resetRemoveChangePassMsg($errors, $update, $user) {
		if(!$errors->get_error_codes()
			&& $update
			&& $user->ID == get_current_user_id()
			&& frameSwr::_()->getModule('options')->get('admin_pass_change_enb')
			&& get_user_meta($user->ID, SWR_CODE. '_pass_change_require')
		) {
			$oldUserData = WP_User::get_data_by('id', $user->ID);
			if($oldUserData 
				&& !is_wp_error($oldUserData) 
				&& !wp_check_password($user->user_pass, $oldUserData->user_pass, $user->ID)	// Password should not be the same
			) {
				delete_user_meta($user->ID, SWR_CODE. '_pass_change_require');
			}
		}
	}
	public function checkLoginErrorDisable($errors) {
		if(frameSwr::_()->getModule('options')->get('hide_login_errors_enb')) {
			$errors = null;
		}
		return $errors;
	}
	public function checkLoginPageRestrict() {
		
	}
}

