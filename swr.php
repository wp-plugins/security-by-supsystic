<?php
/**
 * Plugin Name: Security by Supsystic
 * Plugin URI: http://supsystic.com
 * Description: Secure website and defence from all attacks with Security by Supsyctic. Firewall, Login Security, Hide WordPress, Blacklist and more functions
 * Version: 1.0.3
 * Author: supsystic.com
 * Author URI: http://supsystic.com
 **/
	/**
	 * Base config constants and functions
	 */
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'config.php');
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'functions.php');
	/**
	 * Connect all required core classes
	 */
    importClassSwr('dbSwr');
    importClassSwr('installerSwr');
    importClassSwr('baseObjectSwr');
    importClassSwr('moduleSwr');
    importClassSwr('modelSwr');
    importClassSwr('viewSwr');
    importClassSwr('controllerSwr');
    importClassSwr('helperSwr');
    importClassSwr('dispatcherSwr');
    importClassSwr('fieldSwr');
    importClassSwr('tableSwr');
    importClassSwr('frameSwr');
    importClassSwr('langSwr');
    importClassSwr('reqSwr');
    importClassSwr('uriSwr');
    importClassSwr('htmlSwr');
    importClassSwr('responseSwr');
    importClassSwr('fieldAdapterSwr');
    importClassSwr('validatorSwr');
    importClassSwr('errorsSwr');
    importClassSwr('utilsSwr');
    importClassSwr('modInstallerSwr');
    importClassSwr('wpUpdater');
	importClassSwr('toeWorswressWidgetSwr');
	importClassSwr('installerDbUpdaterSwr');
	importClassSwr('dateSwr');
	/**
	 * Check plugin version - maybe we need to update database, and check global errors in request
	 */
    installerSwr::update();
    errorsSwr::init();
    /**
	 * Start application
	 */
    frameSwr::_()->parseRoute();
    frameSwr::_()->init();
    frameSwr::_()->exec();
