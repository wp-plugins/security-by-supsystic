<?php
    global $wpdb;
    if (!defined('WPLANG') || WPLANG == '') {
        define('SWR_WPLANG', 'en_GB');
    } else {
        define('SWR_WPLANG', WPLANG);
    }
    if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

    define('SWR_PLUG_NAME', basename(dirname(__FILE__)));
    define('SWR_DIR', WP_PLUGIN_DIR. DS. SWR_PLUG_NAME. DS);
    define('SWR_TPL_DIR', SWR_DIR. 'tpl'. DS);
    define('SWR_CLASSES_DIR', SWR_DIR. 'classes'. DS);
    define('SWR_TABLES_DIR', SWR_CLASSES_DIR. 'tables'. DS);
	define('SWR_HELPERS_DIR', SWR_CLASSES_DIR. 'helpers'. DS);
    define('SWR_LANG_DIR', SWR_DIR. 'lang'. DS);
    define('SWR_IMG_DIR', SWR_DIR. 'img'. DS);
    define('SWR_TEMPLATES_DIR', SWR_DIR. 'templates'. DS);
    define('SWR_MODULES_DIR', SWR_DIR. 'modules'. DS);
    define('SWR_FILES_DIR', SWR_DIR. 'files'. DS);
    define('SWR_ADMIN_DIR', ABSPATH. 'wp-admin'. DS);

    define('SWR_SITE_URL', get_bloginfo('wpurl'). '/');
    define('SWR_JS_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/js/');
    define('SWR_CSS_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/css/');
    define('SWR_IMG_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/img/');
    define('SWR_MODULES_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/modules/');
    define('SWR_TEMPLATES_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/templates/');
    define('SWR_JS_DIR', SWR_DIR. 'js/');

    define('SWR_URL', SWR_SITE_URL);

    define('SWR_LOADER_IMG', SWR_IMG_PATH. 'loading-cube.gif');
	define('SWR_TIME_FORMAT', 'H:i:s');
    define('SWR_DATE_DL', '/');
    define('SWR_DATE_FORMAT', 'm/d/Y');
    define('SWR_DATE_FORMAT_HIS', 'm/d/Y ('. SWR_TIME_FORMAT. ')');
    define('SWR_DATE_FORMAT_JS', 'mm/dd/yy');
    define('SWR_DATE_FORMAT_CONVERT', '%m/%d/%Y');
    define('SWR_WPDB_PREF', $wpdb->prefix);
    define('SWR_DB_PREF', 'swr_');
    define('SWR_MAIN_FILE', 'swr.php');

    define('SWR_DEFAULT', 'default');
    define('SWR_CURRENT', 'current');
	
	define('SWR_EOL', "\n");    
    
    define('SWR_PLUGIN_INSTALLED', true);
    define('SWR_VERSION', '1.0.4');
    define('SWR_USER', 'user');
    
    define('SWR_CLASS_PREFIX', 'swrc');     
    define('SWR_FREE_VERSION', false);
    
    define('SWR_SUCCESS', 'Success');
    define('SWR_FAILED', 'Failed');
	define('SWR_ERRORS', 'swrErrors');
	
	define('SWR_ADMIN',	'admin');
	define('SWR_LOGGED','logged');
	define('SWR_GUEST',	'guest');
	
	define('SWR_ALL',		'all');
	
	define('SWR_METHODS',		'methods');
	define('SWR_USERLEVELS',	'userlevels');
	/**
	 * Framework instance code, unused for now
	 */
	define('SWR_CODE', 'swr');

	define('SWR_LANG_CODE', 'swr_lng');
	/**
	 * Plugin name
	 */
	define('SWR_WP_PLUGIN_NAME', 'Security by Supsystic');
	/**
	 * Custom defined for plugin
	 */

