<?php

class Cookie {
	//check if cookie exists
	public static function exist($name) {
		return (isset($_COOKIE[$name])) ? true : false;
	}
	
	//get name of cookie
	public static function get($name) {
		return $_COOKIE[$name];
	}
	
	//put value into cookie
	public static function put($name, $value, $expiry) {
		if(setcookie($name, $value, time() + $expiry, '/')) {
			return true;
		}
		
		return false;
	}
	
	//set empty string for the cookie
	public static function delete($name) {
		self::put($name, '', time() - 1);
	}
}