<?php

class Hash {
	//hash string
	public static function make($string, $salt = '') {
		return hash('sha256',$string.$salt);
	}
	
	//randomly generate salt value based on salt size given
	public static function salt($length) {
		return mcrypt_create_iv($length);
	}
	
	//create unique hash
	public static function unique() {
		return self::make(uniqid());
	}
}