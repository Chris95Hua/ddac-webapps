<?php

class Validate {
	
	//variables
	private $_passed = false, $_errors = array(), $_db = null;
	
	//establish DB connection
	public function __construct() {
		$this->_db = MySQLConn::getInstance();
	}
	
	//check items based on rules
	public function check($source, $items = array()) {
		foreach($items as $item=>$rules) {
			foreach($rules as $rule => $rule_value) {
				if (is_string($source[$item])) {
					$value = trim($source[$item]);
					$item = escape($item);
				}
				
				if($rule === 'required' && empty($value)) {
					$this->addError("{$item} is required!");
				}
				else if(!empty($value)) {
					switch($rule) {
						case 'min':
							if(strlen($value) < $rule_value) {
								$this->addError("{$item} must contain a minimum of {$rule_value} characters!");
							}
						break;
						case 'max':
							if(strlen($value) > $rule_value) {
								$this->addError("{$item} must contain less {$rule_value} characters!");
							}
						break;
						case 'matches':
							if($value != $source[$rule_value]) {
								$this->addError("{$rule_value} must match {$item}!");
							}
						break;
						case 'unique':
							$check = $this->_db->get($rule_value,array($item, '=', $value));
							if($check->count()) {
								$this->addError("{$item} already exists!");
							}
						break;
						case 'regex':
							if(preg_match("#^[a-z ']+$#i", $value) === 0) {
								$this->addError("{$item} can only contain alphabet(a-z), space and single quotation (')!");
							}
						break;
					}
				}
			}
		}
		
		if(empty($this->_errors)) {
			$this->_passed = true;
		}
		return $this;
	}
	
	//add errors into an array
	private function addError($error) {
		$this->_errors[] = $error;
	}
	
	//return list of errors
	public function errors() {
		return $this->_errors;
	}
	
	//return true if passed the validation
	public function passed() {
		return $this->_passed;
	}

}