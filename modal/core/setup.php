<?php
// constants
// BASE_URL 	: base URL
// DS 			: OS specific directory separator
// ROOT 		: project root
// MODAL 		: modal directory
// VIEW 		: view directory
// CONTROLLER	: controller directory
if(!defined('BASE_URL')) define('BASE_URL', '://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
if(!defined('ROOT')) define('ROOT', realpath(__DIR__.'/../..'));
if(!defined('MODAL')) define('MODAL', ROOT.DS.'modal');
if(!defined('VIEW')) define('VIEW', ROOT.DS.'view');
if(!defined('CONTROLLER')) define('CONTROLLER', ROOT.DS.'controller');


// error_reporting(0);		// Turn error reporting off
session_start();		// Turn on session
ob_start();				// Turn on output buffering

// Load settings
require_once(implode(DS, array(MODAL, 'config', 'config.php')));

// Load escaper
require_once(implode(DS, array(MODAL, 'function', 'sanitise.php')));

// Load classes
spl_autoload_register(function($class) {
	require_once(implode(DS, array(MODAL, 'class', $class.'.php')));
});

// setup configs
//Config::init();
if(!isset($_SESSION['config'])){
	$_SESSION['config'] = true;
	Config::loadConfig("https://uiaconfig.blob.core.windows.net/config/uia_config.ini");
}
 
// Load basic classes
$page = Page::getInstance();
$user = new User();

// region settings
$region = "Asia";
if(!isset($_SESSION['currency'])){
	$_SESSION['currency'] = "MYR";
}
if(!isset($rate)) {
	$rate = MySQLConn::getInstance()->select("currency", array("rate"), array("code", '=', $_SESSION['currency']), "LIMIT 1")->fetch()['rate'];
}
 
// Auto login if cookie was found
if(Cookie::exist(Config::get('cookie_name')) && !isset($_SESSION['ID'])) {
	$hash = Cookie::get(Config::get('cookie_name'));
	$hashCheck = MySQLConn::getInstance()->select(User::SESSION_TABLE, array(), array(User::COL_HASH, '=', $hash), "LIMIT 1")->fetch();

	if($hashCheck) {
		$account = MySQLConn::getInstance()->select(User::USER_TABLE, array(), array(User::COL_USER_ID, '=', $hashCheck[User::COL_USER_ID]), "LIMIT 1")->fetch();

		if($account) {
			$_SESSION['ID'] = $account[User::COL_USER_ID];
			$_SESSION['role'] = $account[User::COL_ROLE_ID];
			$_SESSION['name'] = $account[User::COL_NAME];
			header('Location: dashboard.php?page=main');
		}
		else {
			MySQLConn::getInstance()->delete(User::SESSION_TABLE, array(User::COL_HASH, '=', $hash));
		}
	}
}

if(!isset($_SESSION['role'])) $_SESSION['role'] = Config::get('guest');