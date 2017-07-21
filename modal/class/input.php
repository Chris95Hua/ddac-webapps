<?php

class Input {
	//check if item/data is submitted
	public static function exist($type = 'post') {
		switch($type) {
			case 'post':
				return (!empty($_POST)) ? true : false;
				break;
			case 'get':
				return (!empty($_GET)) ? true : false;
				break;
			default:
				return false;
				break;
		}
	}
	
	//retrieve item/data
	public static function get($item) {
		if(isset($_POST[$item])) {
			return $_POST[$item];
		}
		elseif (isset($_GET[$item])) {
			return $_GET[$item];
		}
		
		return '';
	}	
}