<?php

class Config {
	const CACHE_DB_TRUE = 200;
	const CACHE_DB_FALSE = 201;
	const CACHE_DB_AUTO = 202;

	private static $config = array (
		/**
		* Site
		*
		* site_name 		: name of website
		* home 				: home page's slug name
		* guest 			: session value for unregistered user
		* time_format		: date time format to be used
		* write_log			: log actions to usage.log file (in root dir)
		*/
		'site_name' => 'Ukraine International Airlines',
		'home' => 'home',
		'guest' => 3,
		'time_format' => 'd/m/y',
		'write_log' => false,		
		
		// project specific settings
		'max_booking_display' => 5,

		/**
		* Database
		*
		* db_host			: host address
		* db_username		: database username
		* db_password 		: database password
		* db_name 			: name of the database
		* db_charset		: database charset (*1)
		* 
		* note:
		* 1. Recommended to use utf8mb4 for database charset,
		* 	 as php do not support utf8 fully.
		*/
		'db_host' => 'ap-cdbr-azure-southeast-b.cloudapp.net',
		'db_username' => 'b53d1cd3aa407e',
		'db_password' => '3641bc08',
		'db_name' => 'acsm_ffd0fbc07265879',
		'db_charset' => 'utf8mb4',

		/**
		* MemcacheD
		*
		* cache_db			: cache tables data (*1)
		* cache_host		: MemcacheD host address
		* cache_port		: MemcacheD port number
		*
		* 1. Cached page will takes up more memory on server and will likely
		* 	 be using non-synchronized (not up to date) data, but can provide
		*	 users a better browsing experience. If it's set to false, it will
		* 	 reduce the usage of memory but at the cost of higher CPU usage.
		*	 Recommended to set to true if:
		*	 i.	   Server has Memcached installed (see setup.txt in root)
		*	 ii.   Do not have large amount of data (65mb++)
		*	 iii.  The 'page' table will not be modified frequently/at all
		*	 iv.   The server is not scheduled to perform/undergoing maintenance
		*
		* 	 Leaving the setting to auto will let the script to decide based on
		*	 criteria (i) and (ii).
		*/
		'cache_db' => self::CACHE_DB_AUTO,
		'cache_host' => '127.0.0.1',
		'cache_port' => 11211,

		/**
		* Cookies
		*
		* cookie_name		: name of the cookie, for resuming user session
		* cookie_expiry 	: seconds before cookie expire (86400 = a day)
		*/
		'cookie_name' => 'hash',
		'cookie_expiry' => 86400,

		/**
		* File operations
		*
		* file_operation	: enable file operations (*1)
		* shared_folder		: directory for upload/download (from root)
		* new_dir_mode		: file directory mode (ignored on windows) (*2)
		* max_files 		: maximum files can be uploaded at once
		* max_filesize		: maximum file size in bytes allowed for upload
		* accept_file_extensions: accepted file types to be uploaded
		* restricted_files	: files to hide when generating dir tree (no wildcards)
		*
		* note:
		* 1. File operations such as upload, create directory, etc.
		* 2. 0755 = everything for owner, read and execute for others.
		*/
		'file_operation' => false,
		'shared_folder' => 'upload',
		'new_dir_mode' => 0755,
		'max_files' => 50,
		'max_filesize' => 20971520,
		'accept_file_extensions' => array('*'),
		'restricted_files' => array('.htaccess','Thumbs.db')					
	);
	

	/**
	* Initialize and cleanup configs
	*/
	public static function init() {
		// Setup Memcached
		if(self::$config['cache_db'] == self::CACHE_DB_AUTO) {
			self::$config['cache_db'] = self::CACHE_DB_FALSE;

			if(extension_loaded('memcached')) {
			    $memcached = new Memcached;

				if($memcached->connect(self::$config['cache_host'], self::$config['cache_port'])) {
				    self::$config['cache_db'] = self::CACHE_DB_TRUE;
				} 
			}
		}

		// file operations
		if(self::$config['file_operation']) {
			self::$config['encryption_salt'] = $_SERVER['SERVER_NAME'];

			// convert array to regexp
			if(self::$config['accept_file_extensions'] == '*' || in_array('*', self::$config['accept_file_extensions'])){
				self::$config['accept_file_extensions'] = '/\.+/';
			}
			else {
				self::$config['accept_file_extensions'] = '/(\.|\/)('.implode('|', self::$config['accept_file_extensions']).')$/i';
			}
		}

		// strip trailing slash & forward slashes for shared folder
		self::$config['shared_folder'] = ROOT.DS.self::$config['shared_folder'];
		self::$config['shared_folder'] = rtrim(self::$config['shared_folder'], "/\\");
		self::$config['shared_folder'] = str_replace('\\', '/', self::$config['shared_folder']);
	}


	/**
	* Return value
	*/
	public static function get($param) {
		/*
		if (self::$config['file_operation']) {
			self::$config = File::validateConf(self::$config, $param);
		}
		*/
		
		if(isset(self::$config[$param])) {
			return self::$config[$param];
		}
	}

	public static function loadConfig($url) {
		$ini = file_get_contents($url);
		$config = parse_ini_string($ini);
	}
}