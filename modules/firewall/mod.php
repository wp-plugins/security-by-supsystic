<?php
class firewallSwr extends moduleSwr {
	public function init() {
		parent::init();
		dispatcherSwr::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		dispatcherSwr::addFilter('optionsDefine', array($this, 'addOptions'));
		add_action('wp', array($this, 'check404'));
		// Comments spam
		add_action('comment_form', array($this, 'displayCommentSecretField'));
		add_action('pre_comment_on_post', array($this, 'checkCommentsSpam'));
	}
	public function addAdminTab($tabs) {
		$tabs[ $this->getCode() ] = array(
			'label' => __('Firewall', SWR_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'fa_icon' => 'fa-bolt', 'sort_order' => 30,
		);
		return $tabs;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function addOptions($opts) {
		$opts[ $this->getCode() ] = array(
			'label' => __('Firewall', SWR_LANG_CODE),
			'opts' => array(
				'404_black_list_detection' => array('label' => __('404 Black list detection', SWR_LANG_CODE), 'weight' => 70, 'html' => 'checkboxHiddenVal', 'desc' => __('The plugin registers any suspicious activity and based on the analysis can prevent attempts of unauthorized access. Often 404 errors may indicate that your site is being probed by an attacker. They may also indicate that someone used an incorrect link to your site. Based on the data you can analyze the reason of 404 errors and take necessary actions: prevent the unauthorized access or write to the owner of the site with the incorrect link.', SWR_LANG_CODE)),

				'404_bld_attempts' => array('label' => __('404 Attempts', SWR_LANG_CODE), 'def' => 10),
				'404_bld_stop_time' => array('label' => __('404 Stop time', SWR_LANG_CODE), 'def' => 1),
				'404_bld_attempts_data' => array('label' => __('404 Attempts data', SWR_LANG_CODE), 'def' => array()),
				
				'x_frame_enb' => array('label' => __('Disalow user site on other domains in iframe', SWR_LANG_CODE), 'weight' => 60, 'html' => 'checkboxHiddenVal', 'htaccessChange' => true, 'desc' => __('The option that protects your site from the so called Clickjacking attack<br /><br />Clickjacking attack means that the hacker uses various layers, usually transparent, to force the user into involuntarily clicking on a button the user didn’t want to click on in the first place. Such button may lead to a malicious or fishing site. The Clickjacking usually happens when a user is clicking in the top level of the page. Similar techniques are being used to steal the user’s information, for example, the password to a bank account, when he is typing it into a field on the page. Hackers use complex iframes and carefully crafted text boxes to collect the keystrokes information form an unsuspecting user.', SWR_LANG_CODE)),
				'lock_system_files_enb' => array('label' => __('Protect system files', SWR_LANG_CODE), 'weight' => 30, 'html' => 'checkboxHiddenVal', 'htaccessChange' => true, 'desc' => __('System files and directories contain vital information to which access should be restricted to authorized users. This option provides additional protection for the system files and directories, reducing the chance of an attacker to gain access to them.', SWR_LANG_CODE)),
				'reduce_comment_spam' => array('label' => __('Reduce comment spam', SWR_LANG_CODE), 'weight' => 65, 'html' => 'checkboxHiddenVal', 'desc' => __('Disable the comments feature or mark it as spam if there’s no referrer or if there’s no identification.', SWR_LANG_CODE)),
				'disable_directory_browsing' => array('label' => __('Disable directory browsing', SWR_LANG_CODE), 'weight' => 65, 'html' => 'checkboxHiddenVal', 'htaccessChange' => true, 'desc' => __('', SWR_LANG_CODE)),
			),
		);
		return $opts;
	}
	public function check404() {
		if(frameSwr::_()->getModule('options')->get('404_black_list_detection') && is_404() && !frameSwr::_()->getModule('user')->isAdmin()) {
			$lockoutAttempts = frameSwr::_()->getModule('options')->get('404_bld_attempts_data');
			if(!$lockoutAttempts)
				$lockoutAttempts = array();
			$ip = utilsSwr::getIP();
			$time = time();
			$blocked = false;
			$stopTime = (int) frameSwr::_()->getModule('options')->get('404_bld_stop_time');
			if(!isset($lockoutAttempts[ $ip ])) {
				$lockoutAttempts[ $ip ] = array(
					'attempts' => 1,
					'last_try' => $time,
				);
			} else {
				if(!$stopTime || ($time - $lockoutAttempts[ $ip ]['last_try']) <= $stopTime * 60) {
					$lockoutAttempts[ $ip ]['attempts']++;
				}
				$lockoutAttempts[ $ip ]['last_try'] = $time;
			}
			// Block IP
			if($lockoutAttempts[ $ip ]['attempts'] >= frameSwr::_()->getModule('options')->get('404_bld_attempts')) {
				frameSwr::_()->getModule('blacklist')->getModel()->save(array(
					'ip' => $ip, 
					'type' => '404'));
				$notifyEmail = frameSwr::_()->getModule('options')->get('notify_email');
				if(!empty($notifyEmail)) {
					frameSwr::_()->getModule('mail')->send(
							$notifyEmail, 
							__('404 page blocked IP', SWR_LANG_CODE), 
							sprintf(__('On your site %s IP %s was added to blacklist as from it we detected too much requests - %d requests for %s minutes.', SWR_LANG_CODE), SWR_SITE_URL, $ip, $lockoutAttempts[ $ip ]['attempts'], $stopTime));
				}
				unset($lockoutAttempts[ $ip ]);
				$blocked = true;
			}
			frameSwr::_()->getModule('options')->getModel()->save('404_bld_attempts_data', $lockoutAttempts);
			if($blocked) {	// If blocked - just redirect to show block page
				redirect(uriSwr::getFullUrl());
			}
		}
	}
	public function displayCommentSecretField($postId) {
		if(frameSwr::_()->getModule('options')->get('reduce_comment_spam')) {
			$browser = utilsSwr::getUserBrowserString();
			if(empty($browser))
				return;
			$secret = $this->generateCommentSecretKeyVal($postId, $browser);
			echo htmlSwr::hidden($secret['key'], array('value' => $secret['val']));
		}
	}
	public function generateCommentSecretKeyVal($postId, $browser) {
		$keyVal = str_split(md5($browser. $postId. AUTH_KEY), 16);
		$pref = '_swr_';
		return array(
			'key' => $pref. $keyVal[0],
			'val' => $pref. $keyVal[1],
		);
	}
	public function checkCommentsSpam() {
		if(frameSwr::_()->getModule('options')->get('reduce_comment_spam')) {
			$browser = utilsSwr::getUserBrowserString();
			if(empty($browser) || empty($_SERVER['HTTP_REFERER'])) {
				wp_die(__('You must use browser in order to post comments', SWR_LANG_CODE));
			}
			$comment_post_ID = isset($_POST['comment_post_ID']) ? (int) $_POST['comment_post_ID'] : 0;
			$secret = $this->generateCommentSecretKeyVal($comment_post_ID, $browser);
			$secretValSubmited = reqSwr::getVar($secret['key'], 'post');
			if(empty($secretValSubmited) || $secretValSubmited != $secret['val']) {
				wp_die(__('You are trying to post comment - not from this post?', SWR_LANG_CODE));
			}
		}
	}
}

