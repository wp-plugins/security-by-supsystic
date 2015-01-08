<?php
class systemViewSwr extends viewSwr {
	public function getTabContent() {
		global $wpdb;
		frameSwr::_()->addScript('admin.'. $this->getCode(), $this->getModule()->getModPath(). 'js/admin.'. $this->getCode(). '.js');
		frameSwr::_()->getModule('templates')->loadJqueryUi();
		
		$systemInfo = array(
            'Operating System' => array('value' => PHP_OS),
            'PHP Version' => array('value' => PHP_VERSION),
            'Server Software' => array('value' => $_SERVER['SERVER_SOFTWARE']),
            'MySQL Version' => array('value' => isset($wpdb->use_mysqli) && $wpdb->use_mysqli ? mysqli_get_server_info($wpdb->dbh) : mysql_get_server_info()),
			'PHP Zip support' => array('value' => function_exists('gzopen') ? 'Yes' : 'No'),
			'PHP Zlib support' => array('value' => class_exists('ZipArchive') ? 'Yes' : 'No'),
            'PHP Safe Mode' => array('value' => ini_get('safe_mode') ? 'Yes' : 'No', 'error' => ini_get('safe_mode')),
            'PHP Allow URL Fopen' => array('value' => ini_get('allow_url_fopen') ? 'Yes' : 'No'),
            'PHP Memory Limit' => array('value' => ini_get('memory_limit')),
            'PHP Max Post Size' => array('value' => ini_get('post_max_size')),
            'PHP Max Upload Filesize' => array('value' => ini_get('upload_max_filesize')),
            'PHP Max Script Execute Time' => array('value' => ini_get('max_execution_time')),
            'PHP XML Support' => array('value' => extension_loaded('libxml') ? 'Yes' : 'No', 'error' => !extension_loaded('libxml')),
            'PHP CURL Support' => array('value' => extension_loaded('curl') ? 'Yes' : 'No', 'error' => !extension_loaded('curl')),
			'PHP EXIF Support' => array('value' => extension_loaded('exif') ? 'Yes' : 'No'),
        );
		if($systemInfo['PHP EXIF Support']['value'] == 'Yes') {
			$systemInfo['PHP EXIF Version'] = array('value' => phpversion('exif'));
		}
		$this->assign('systemInfo', $systemInfo);
		
		$this->assign('sendStatistic', frameSwr::_()->getModule('options')->get('send_stats'));
		$this->assign('mailContent', frameSwr::_()->getModule('mail')->getTabContent());
		$this->assign('htaccessContent', frameSwr::_()->getModule('htaccess')->getTabContent());
		
		$this->assign('contactFormFields', $this->getModule()->getContactFormFields());
		return parent::getContent('systemAdmin');
	}
}
