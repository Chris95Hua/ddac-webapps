<?php
	require_once ('../modal/core/setup.php');

	// URL example: http://localhost/REST/api/?page=3&more=yes
	// where "api" = this file name
	// anything after "/?" will be [key]=[value], separated by "&"

	/*
	========================= READ ME =========================
	Config file (/modal/config):
		Config file contains server and db info, where this api
		will read from
	*/

	// create and load stuff here
	// note: see modal/class/mysqlconn.php if need to connect to mysql db
	// remember to modify db settings in config.php


	// Setup and get connection instance:
	// ========================================================
	// private _db, _data;
	// $this->_db = MySQLConn::getInstance();




	// $where argument and helper functions
	// ========================================================
	// '$where' array argument (for use with update, select, delete)
	// --------------------------------------------------------
	// array([field name], [operation], [value])
	// valid operation: "=", ">", "<", ">=", "<="
	// See "update", "select", and "delete" operation for examples
	// 
	//
	// Helper function (for use with insert, select, delete)
	// --------------------------------------------------------
	// fetchAll() 				array(array(assoc)) 
	//     -- return multi rows of selected fields in array of associated array eg: ("column" => value)
 	//     -- example: $allUsers = $this->_db->select(..)->fetchAll();
	//				   echo $allUsers[0]["column"]; // or foreach them
	//
	// fetch()					array(assoc)
	//     -- return single row of selected fields in array of associated array eg: ("column" => value)
 	//     -- example: $findUser = $this->_db->select(..)->fetch();
	//				   echo $findUser["column"];
	//
	// hasError()				boolean
	//	   -- return boolean to indicate if the previous operation has error
 	//     -- example: if($this->_db->delete(..)->hasError()) { $echo "error"; }
	//
	// count()					int
	//	   -- return number of rows affected by previous operation/query
	//
	// lastInsertId()			int
	//	   -- return id of last inserted row





	// Insert
	// ========================================================
	// insert($table, array $fields)
	//
	// Note: Pair this function with lastInsertId()
	//
	// Example: $this->_db->insert("user", array("name"=>"chris", "age"=>37, "gender"=>"male"));


	// Update
	// ========================================================
	// update($table, array $fields, array $where)
	//
	// Example: $this->_db->update("salary", array("basic"=>6000, "annual_leave"=>8), array("role", "=", "manager"));


	// Select
	// ========================================================
	// select($table, array $columns, $where = array())
	//
	// Note: Use "*" to select everything, or write the column names in "$columns" array
   	//       The "$where" argument is optional, leaving it blank will select without condition
	//       Pair this function with fetchAll(), fetch(), hasError(), count()
	//
	// Example: $this->_db->select("user", array("name", "birth_date"), array("status", "=", "active"));
	//          $this->_db->select("user", array("*"));


	// Delete
	// ========================================================
	// delete($table, $where = array())
	//
	// Note: the "$where" argument is optional, leaving it blank will delete everything
	//       Pair this function with hasError(), count()
	//
	// Example: $this->_db->delete("user", array("status", "=", "fired"));
	//          $this->_db->delete("user");

	// General purpose query function
	// ========================================================
	// query($query, $fields = array())
	//
	// Note: the "$fields" argument is optional, only pass arguments if there is a need to (eg: insert)
	//       Pair this function with fetchAll(), fetch(), hasError(), count(), lastInsertId()
	//
	// Example: $this->_db->delete->query("SELECT * FROM user");
	//          $this->_db->delete->query("SELECT * FROM user WHERE name=:name", array(":name" => "Jason"));

$dates = Booking::getInstance();


// Simple GET request
$arr;

// read GET
if (Input::get('origin') && Input::get('destination')) {
	// perform task
	$id = $dates->getRoute(Input::get('origin'), Input::get('destination'));
	$arr = $dates->getFlightDates($id['route_id']);
}
else {
	$arr = array('error' => "No known parameter received");
}

// echo result
header('Content-type: application/json');
echo json_encode($arr);


/*
// Handling more request

switch ($_SERVER['REQUEST_METHOD']) {
  case 'PUT':
    parse_str(file_get_contents("php://input"),$post_vars);
    echo $post_vars["name"];  
    break;
  case 'POST':
    echo $_POST["name"];
    break;
  case 'GET':
    echo $_GET["name"];
    break;
  case 'HEAD':
  	// HEAD can be get using GET
    echo $_GET["name"];
    break;
  case 'DELETE':
    parse_str(file_get_contents("php://input"),$post_vars);
    echo $post_vars["name"];
    break;
  case 'OPTIONS':
    // TODO: handle options
    break;
  default:
    // handle error here
    break;
}

// Token generation & checking
// put it in a hidden input tag perhaps?
<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">

// when sending it back to php, do:
if(Token::check(Input::get('token'))) {
	// stuff
}
*/

?>