<?php

class MySQLConn {
	private static $_instance = null;
	private $_pdo, $_conn;
	private $_error = false, $_count = 0, $_lastID;


	/**
	* Setup connection
	*/
	private function __construct() {
		$dsn = "mysql:host=".Config::get('db_host');
		$dsn .= ";dbname=".Config::get('db_name');
		$dsn .= ";charset=".Config::get('db_charset');
		$username = Config::get('db_username');
		$password = Config::get('db_password');

		$opt = [
		    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		    PDO::ATTR_PERSISTENT => true
		    //PDO::ATTR_EMULATE_PREPARES   => false
		];

		// TODO: need a better error handler if unable to establish connection
		try {
			$this->_pdo = new PDO($dsn, $username, $password, $opt);
		}
		catch(PDOException $e) {
			// display friendly message here instead
			// log exception in a log file
			die($e->getMessage());
		}
	}


	/**
	* Return an instance of this object
	*
	* @return MySQLConn                  Instance of MySQLConn
	*/
	public static function getInstance() {
		if(!isset(self::$_instance)) {
			self::$_instance = new MySQLConn();
		}

		return self::$_instance;
	}


	/**
	* Determine PDO param type
	*
	* @param object         $value       Value to be binded
	* @return integer                    Value type
	*/
	private function getParamType($value) {
        if(is_int($value))
            return PDO::PARAM_INT;
        elseif(is_bool($value))
            return PDO::PARAM_BOOL;
        elseif(is_null($value))
            return PDO::PARAM_NULL;
        else
            return PDO::PARAM_STR;
	}


	/**
	* Execute SQL query
	*
	* @param string         $query       Parameterized SQL query
	* @param array(assoc)   $fields      Values to be binded (optional)
	* @return MySQLConn                  Instance of MySQLConn
	* @example query("SELECT * FROM table"); query("SELECT * FROM table");
	*
	* NOTE: Pair this function with fetchAll(), fetch(), hasError(),
	* 		count(), lastInsertId()
	*/
	public function query($query, $fields = array()) {
		$this->_error = false;

		if($this->_conn = $this->_pdo->prepare($query)) {
			if(!empty($fields)) {
				foreach($fields as $name => $value) {
					$this->_conn->bindValue(":{$name}", $value, $this->getParamType($value));
				}
			}
			
			if($this->_conn->execute()) {
				$this->_count = $this->_conn->rowCount();
				$this->_lastID = $this->_pdo->lastInsertID();
			}
			else {
				$this->_error = true;
			}
		}

		return $this;
	}


	/**
	* Determine the selected fields
	*
	* @param array(string)  $columns     Columns to be returned
	* @return string                     Selected columns
	*/
	private function selectedFields($columns) {
		$selected = '';

		if(!empty($columns)) {
			$selected = implode(', ', $columns);
		}
		else {
			$selected .= "*";
		}

		return $selected;
	}


	/**
	* Determine if the format is valid for conditional query
	*
	* @param array(string)  $conditions  Conditions to be evaluated
	* @return boolean                    True if valid, else false
	*/
	private function isConditionValid($conditions) {
		if(count($conditions) === 3) {
			if(in_array($conditions[1], array('=', '>', '<', '>=', '<='))) {
				return true;
			}
		}

		return false;
	}


	/**
	* Validate query passed from select() and delete()
	*
	* @param string         $action      Query
	* @param string         $table       Name of table
	* @param array(string)  $where       Conditions (optional)
	* @return MySQLConn/null             Instance of MySQLConn if query is valid, else null
	*/
	private function action($action, $table, array $where, $format = "") {
		if($this->isConditionValid($where)) {
			$field = $where[0];
			$value = $where[2];
			$operator = $where[1];

			if(strlen($format) > 0) {
				$format = ' ' . $format;
			}

			$query = "{$action} {$table} WHERE {$field} {$operator} :{$field}".$format;

			if(!$this->query($query, array($field => $value))->hasError()) {
				return $this;
			}
		}

		return null;
	}


	/**
	* Insert new record
	*
	* @param string         $table       Name of table
	* @param array(assoc)   $fields      Fields and data to be inserted
	* @return boolean                    True if there's no error, else false
	* @example insert("user", array("name"=>"chris", "age"=>37, "gender"=>"male"));
	*/
	public function insert($table, array $fields) {
		if(!empty($fields)) {
			$keys = array_keys($fields);

			$sql = "INSERT INTO {$table} (".implode(', ', $keys).") VALUES (:".implode(', :', $keys).")";
			return !$this->query($sql, $fields)->hasError();
		}

		return false;
	}


	/**
	* Update existing record with new values
	*
	* @param string         $table       Name of table
	* @param array(assoc)   $fields      Fields and data to be inserted
	* @param array(string)  $where       Conditions
	* @return MySQLConn/null             Instance of MySQLConn if query is valid, else null
	* @example update("salary", array("basic"=>6000, "annual_leave"=>8), array("role", "=", "manager"));
	*/
	public function update($table, array $fields, array $where) {
		if($count = count($fields)) {

			$table .= "SET ";

			foreach($fields as $name=>$value) {
				$count--;
				$table .= "{$name} = :{$name}";
				if($count) {
					$table .= ', ';
				}
			}

			return $this->action("UPDATE", $table, $where);
		}

		return null;
	}


	/**
	* Conditional select
	*
	* @param string         $table       Name of table
	* @param array(string)  $columns     Columns to be returned (use "*" to select everything)
	* @param array(string)  $where       Conditions (optional)
	* @param string  		$format  	 Formatting (optional)
	* @return MySQLConn                  Instance of MySQLConn
	* @example select("user", array("*"), array("status", "=", "active"));
	*
	* NOTE: Pair this function with fetchAll(), fetch(), hasError(),
	* 		count(), lastInsertId()
	*/
	public function select($table, array $columns, $where = array(), $format = "") {
		if(strlen($format) > 0) {
			$format = ' ' . $format;
		}

		if(empty($where)) {
			$query = "SELECT ".$this->selectedFields($columns)." FROM {$table}" . $format;

			return $this->query($query);
		}
		else {
			$query = "SELECT ".$this->selectedFields($columns)." FROM";

			return $this->action($query, $table, $where, $format);
		}
	}


	/**
	* Conditional delete
	*
	* @param string         $table       Name of table
	* @param array(string)  $where       Conditions (optional)
	* @return MySQLConn                  Instance of MySQLConn
	* @example delete("user", array("status", "=", "fired"));
	*
	* NOTE: Pair this function with fetchAll(), fetch(), hasError(),
	* 		count(), lastInsertId()
	*/
	public function delete($table, $where = array()) {
		if(empty($where)) {
			return $this->query("DELETE FROM {$table}");
		}
		else {
			return $this->action('DELETE FROM', $table, $where);
		}
	}


	/**
	* Call stored procedure
	*
	* @param string         $proc        Procedure name
	* @param array(assoc)   $params      Parameters
	* @return MySQLConn                  Instance of MySQLConn
	* @example callSproc("get_user_by_role", array("role" => "manager"));
	*
	* NOTE: Pair this function with hasError(), count()
	*/
	public function callSproc($proc, $params = array()) {
		$paramValues = "";

		if(!empty($params)) {
			$keys = array_keys($params);
			$paramValues = ":".implode(', :', $keys);
		}
		

		$sql = "CALL {$proc}({$paramValues})";

		return $this->query($sql, $params);
	}


	/**
	* Return results
	*
	* @return array(array(assoc))        Selected fields
	*/
	public function fetchAll() {
		$result = $this->_conn->fetchAll();
		return $result;
	}


	/**
	* Return results
	*
	* @return array(assoc)               Selected fields
	*/
	public function fetch() {
		$result = $this->_conn->fetch();
		return $result;
	}


	/**
	* Return state of the query
	*
	* @return boolean                    True if there's no error, else false
	*/
	public function hasError() {
		return $this->_error;
	}


	/**
	* Return number of rows affected by SQL query
	*
	* @return integer                    Number of rows affected by SQL query
	*/
	public function count() {
		return $this->_count;
	}


	/**
	* Return last inserted ID
	*
	* @return integer                    Last inserted ID
	*/
	public function lastInsertId() {
		return $this->_lastID;
	}
}
