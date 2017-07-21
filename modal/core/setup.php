<?php
// constants
// BASE_URL 	: base URL
// DS 			: OS specific directory separator
// ROOT 		: project root
// MODAL 		: modal directory
// VIEW 		: view directory
// CONTROLLER	: controller directory
if(!defined('BASE_URL')) define('URL', 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
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
Config::init();
 
// Load basic classes
$page = new Page();
$user = new User();
 
// Auto login if cookie was found
if(Cookie::exist(Config::get('cookie_name')) && !isset($_SESSION['ID'])) {
	$hash = Cookie::get(Config::get('cookie_name'));
	$hashCheck = MySQLConn::getInstance()->get('user_session', array('hash','=',$hash));

	if($hashCheck->count()) {
		$user = new User($hashCheck->first()->userID);
		
		$user->login();
	}
}

if(!isset($_SESSION['role'])) $_SESSION['role'] = Config::get('guest');