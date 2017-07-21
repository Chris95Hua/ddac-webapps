<?php

class Token {
	// generate unique token
	public static function generate() {
		return $_SESSION['token'] = md5(uniqid());
	}

	// compare token
	public static function check($token) {
		if(isset($_SESSION['token']) && $token === $_SESSION['token']) {
			unset($_SESSION['token']);

			return true;
		}
		
		return false;
	}
}