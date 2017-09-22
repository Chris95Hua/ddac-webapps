<?php

class User {
	// table field name
	const USER_TABLE = 'user';
	const SESSION_TABLE = 'user_session';
	const COL_USER_ID = 'user_id';
	const COL_NAME = 'full_name';
	const COL_BIRTHDATE = 'birth_date';
	const COL_EMAIL = 'email';
	const COL_PASSWORD = 'password';
	const COL_SALT = 'salt';
	const COL_DATEJOINED = 'date_joined';
	const COL_ROLE_ID = 'role_id';
	const COL_SESSION_ID = 'session_id';
	const COL_HASH = 'hash';

	private $_db, $_data;


	/**
	* Establish database connection
	*/
	public function __construct() {
		$this->_db = MySQLConn::getInstance();
	}


	/**
	* Create and start user session
	*
	* @param string         $email    	 User email
	* @param string         $password    Account password
	* @param boolean        $remember    Store session for a limited time if true
	* @return boolean                    True if success, else false
	*/
	public function login($email, $password, $remember = false) {
		if($this->find($email, true)) {
			if($this->_data[self::COL_PASSWORD] === Hash::make($password, $this->_data[self::COL_SALT])) {
				$_SESSION['ID'] = $this->_data[self::COL_USER_ID];
				$_SESSION['role'] = $this->_data[self::COL_ROLE_ID];
				$_SESSION['name'] = $this->_data[self::COL_NAME];

				if($remember) {
					$hashCheck = $this->_db->select(self::SESSION_TABLE, array(self::COL_USER_ID, '=', $_SESSION['ID']));

					if(!$hashCheck->count()) {
						$hash = Hash::unique();
						$this->_db->insert(self::SESSION_TABLE, array(
							self::COL_USER_ID => $this->_data[self::COL_USER_ID],
							self::COL_HASH => $hash
						));
					}
					else {
						$hash = $hashCheck->fetch()->hash;
					}

					Cookie::put(Config::get('cookie_name'), $hash, Config::get('cookie_expiry'));
				}

				return 0;
			}
			else {
				return 1;
			}
		}

		return 2;
	}


	/**
	* Remove current user session
	*/
	public function logout() {
		$this->_db->delete(self::SESSION_TABLE, array(self::COL_USER_ID,'=',$this->_data[self::COL_USER_ID]));
		$this->_data = null;
		
		Cookie::delete(Config::get('cookie_name'));
	}


	/**
	* Get all users
	*
	* @param boolean        $asc         Order of list, ASC if true (optional)
	* @return array(array(assoc))        List of users
	*/
	public function getAllUsers($asc = true) {
		$table = self::USER_TABLE;
		$column = self::COL_NAME;
		$order = $asc ? "ASC" : "DESC";

		return $this->_db->query("SELECT * FROM {$table} ORDER BY {$column} {$order}")->fetchAll();
	}


	/**
	* Insert new user record
	*
	* @param array(assoc)   $fields      User fields and data
	*/
	public function create($fields = array()) {
		if(!$this->_db->insert(self::USER_TABLE, $fields)) {
			return 1;
			//throw new Exception('There was a problem creating an account')
		}

		return 0;
	}

	/**
	* Update existing user record
	*
	* @param int            $id          User ID
	* @param array(assoc)   $fields      User fields and data
	*/
	public function update($id, $fields = array()) {
		if(!$this->_db->update(self::USER_TABLE, array(self::COL_USER_ID, '=', $id), $fields)) {
			throw new Exception("There was a problem updating the user's details");
		}
	}


	/**
	* Remove user record
	*
	* @param object         $key         User ID/username
	* @return boolean                    True if success, else false
	*/
	public function delete($key) {
		// TODO: prevent user from deleting himself/herself
		if($key != $_SESSION['ID']) {
			$field = (is_numeric($key)) ? self::COL_USER_ID : self::COL_EMAIL;

			$this->_db->delete(self::USER_TABLE, array($field, '=', $key));
			return true;
		}

		return false;
	}


	/**
	* Check if user exists
	*
	* @param object         $key         User ID/username
	* @param boolean        $get         Get data if set to true
	* @return boolean                    True if user exists, else false
	*/
	public function find($key, $get = false) {
		// TODO: use MemcacheD here
		$field = (is_numeric($key)) ? self::COL_USER_ID : self::COL_EMAIL;

		$user = $this->_db->select(self::USER_TABLE, array(), array($field, '=', $key), "LIMIT 1");

		if($user->count()) {
			if($get) {
				$this->_data = $user->fetch();
			}

			return true;
		}

		return false;
	}


	/**
	* Check if user is logged in
	*
	* @return boolean                    True if user is logged in, else false
	*/
	public function isLoggedIn() {
		return (!empty($this->_data));
	}


	/**
	* Get current session's user information
	*
	* @return array(assoc)               User fields and data
	*/
	public function data() {
		return $this->_data;
	}
}
