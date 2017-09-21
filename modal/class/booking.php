<?php

class Booking {
	// table field name
	const BOOKING_TABLE = 'booking';
	const AIRPORT_TABLE = 'airport';
	const ROUTE_TABLE = 'route';
	const FLIGHT_TABLE = 'flight';
	const SEAT_TABLE = 'booked_seat';
	const PRICE_TABLE = 'price';
	const COL_USER_ID = 'user_id';
	const COL_BOOKING_ID = 'booking_id';
	const COL_FLIGHT_ID = 'flight_id';
	const COL_DATE_BOOKED = 'date_booked';
	const COL_ROUTE = "route_id";
	const COL_PRICE = "price_id";
	const COL_FIRST = "first_class";
	const COL_ECONOMY = "economy";
	const COL_BUSINESS = "business";
	const COL_DEPARTURE = "departure";
	const COL_SEAT = 'seat_number';
	const COL_IATA = 'iata_code';
	const COL_COUNTRY = 'country';
	const COL_SOURCE = 'source';
	const COL_DESTINATION = 'destination';

	private static $_instance = NULL;
	private $_db, $_data;


	/**
	* Establish database connection
	*/
	protected function __construct() {
		$this->_db = MySQLConn::getInstance();
	}

	public static function getInstance() {
		if(self::$_instance === NULL)
			self::$_instance = new Booking();

		return self::$_instance;
	}

	//get all users booking
	//find booking
	public function getAirports($iataOnly = true, $orderByCountry = true) {
		$iataCol = self::COL_IATA;

		if ($iataOnly) {
			return $this->_db->select(self::AIRPORT_TABLE, array($iataCol), array(), "ORDER BY {$iataCol} ASC")->fetchAll();
		}
		else {
			$orderCol = $orderByCountry ? self::COL_COUNTRY : self::COL_IATA;

			return $this->_db->select(self::AIRPORT_TABLE, array(), array(), "ORDER BY {$orderCol} ASC")->fetchAll();
		}
	}
	// departure, origin, destination

	/**
	* Get booked seats
	*
	* @param boolean        $asc         Order of list, ASC if true (optional)
	* @return array(array(assoc))        List of users
	*/
	public function getFlightBookedSeats($flightID) {
		// WHERE booking.flight_id = 3
		$query = "SELECT " . self::SEAT_TABLE . "." . self::COL_SEAT . " FROM " . self::SEAT_TABLE;
		$query .= " INNER JOIN " . self::BOOKING_TABLE . " ON ";
		$query .= self::SEAT_TABLE . "." . self::COL_BOOKING_ID . "=" . self::BOOKING_TABLE . "." . self::COL_BOOKING_ID;
		$query .= " WHERE " . self::BOOKING_TABLE . "." . self::COL_FLIGHT_ID . "={$flightID}";

		$results = $this->_db->query($query, array($flightID => $flightID))->fetchAll();

		$formatted = array();
		foreach($results as $seat) {
			$formatted[] = self::formatSeat($seat[self::COL_SEAT], 1);
		}

		return $formatted;
	}

	public function formatSeat($string, $num) {
		$length = strlen($string);
		$output[0] = substr($string, 0, $num);
		$output[1] = substr($string, $num, $length );
		return "'" . $output[0] . "_" . $output[1] . "'";
	}

	public function getFlight($flightID) {
		$query = "SELECT " . self::PRICE_TABLE . "." . self::COL_ECONOMY . ", ";
		$query .= self::PRICE_TABLE . "." . self::COL_BUSINESS . ", ";
		$query .= self::PRICE_TABLE . "." . self::COL_FIRST . ", ";
		$query .= self::FLIGHT_TABLE . "." . self::COL_DEPARTURE . ", ";
		$query .= self::ROUTE_TABLE . "." . self::COL_SOURCE . ", ";
		$query .= self::ROUTE_TABLE . "." . self::COL_DESTINATION . " FROM " . self::PRICE_TABLE;
		$query .= " INNER JOIN " . self::FLIGHT_TABLE;
		$query .= " ON " . self::PRICE_TABLE . "." . self::COL_PRICE . "=" . self::FLIGHT_TABLE . "." . self::COL_PRICE;
		$query .= " INNER JOIN " . self::ROUTE_TABLE;
		$query .= " ON " . self::FLIGHT_TABLE . "." . self::COL_ROUTE . "=" . self::ROUTE_TABLE . "." . self::COL_ROUTE;
		$query .= " WHERE " . self::COL_FLIGHT_ID . " = {$flightID}";
		return $this->_db->query($query, array($flightID => $flightID))->fetch();
	}

	// get flight route id, list of booked seats, and price
	// getRoute + getFlight + getBookedSeats
	public function getRoute($source, $destination) {
		$query = "SELECT * FROM " . self::ROUTE_TABLE . " WHERE " . self::COL_SOURCE . " = '{$source}' AND " . self::COL_DESTINATION . " = '{$destination}'";
		return $this->_db->query($query, array($source => $source, $destination => $destination))->fetch();
	}

	public function getFlightDates($routeID) {
		$dates = array();
		return $this->_db->query("SELECT " . self::COL_FLIGHT_ID . "," . self::COL_DEPARTURE . " FROM " . self::FLIGHT_TABLE . " WHERE " . self::COL_ROUTE . " = {$routeID} AND " . self::COL_DEPARTURE . " >= CURDATE()")->fetchAll();
	}

	/**
	* Get all users
	*
	* @param boolean        $asc         Order of list, ASC if true (optional)
	* @return array(array(assoc))        List of users
	*/

/*

SELECT flight.flight_id,
	   booking.booking_id,
       booking.date_booked,
       route.source,
       route.destination,
       flight.departure
       FROM booking
       INNER JOIN flight ON booking.flight_id = flight.flight_id
       INNER JOIN booked_seat ON booking.booking_id = booked_seat.booking_id
       INNER JOIN price ON flight.price_id = price.price_id
       INNER JOIN route ON flight.route_id = route.route_id
       INNER JOIN airport AS src ON route.source = src.iata_code
       INNER JOIN airport As des ON route.destination = des.iata_code
       WHERE booking.user_id = 1 GROUP BY booking.booking_id ORDER BY booking.date_booked DESC; 

*/


	public function getAllUserBookings($userID, $returnCount, $offset) {

	}

/*

SELECT price.economy,
		price.business,
        price.first_class,
        flight.departure,
        route.source,
        route.destination
FROM booking
INNER JOIN flight ON booking.flight_id = flight.flight_id
INNER JOIN price ON price.price_id = flight.price_id
INNER JOIN route ON route.route_id = flight.route_id WHERE booking.booking_id = 26

*/

	public function getUserBooking($bookingID) {

	}

	public function getBookedSeats($bookingID) {

	}


	/**
	* Create new booking
	*
	* @param array(assoc)   $fields      Booking fields and data
	*/
	public function createBooking($fields = array()) {
		if(!$this->_db->insert(self::BOOKING_TABLE, $fields)) {
			return 1;
		}

		$sql = "SELECT " . self::COL_BOOKING_ID . " FROM " . self::BOOKING_TABLE;
		$sql .= " WHERE " . self::COL_USER_ID . "=" . $fields[Booking::COL_USER_ID] . " AND ";
		$sql .= self::COL_FLIGHT_ID . "=" . $fields[Booking::COL_FLIGHT_ID] . " AND ";
		$sql .= self::COL_DATE_BOOKED . "='" . $fields[Booking::COL_DATE_BOOKED] . "'";

		$fields = array($fields[Booking::COL_USER_ID], $fields[Booking::COL_FLIGHT_ID], $fields[Booking::COL_DATE_BOOKED]);

		return $this->_db->query($sql, $fields)->fetch()[self::COL_BOOKING_ID];
	}

	public function addBookingSeats($values = array()) {
		//INSERT INTO MyTable ( Column1, Column2 ) VALUES ( Value1, Value2 ), ( Value1, Value2 )
		$sql = "INSERT INTO " . self::SEAT_TABLE . " (" . self::COL_BOOKING_ID . "," . self::COL_SEAT;
		$sql .= ") VALUES (".implode('),(', $values).")";
	
		echo $sql;

		return !$this->_db->query($sql, $values)->hasError();
	}

	/**
	* Update existing booking
	*
	* @param int            $id          Booking ID
	* @param array(assoc)   $fields      Booking fields and data
	*/
	public function update($id, $fields = array()) {
		if(!$this->_db->update(self::BOOKING_TABLE, array(self::COL_BOOKING_ID, '=', $id), $fields)) {
			throw new Exception("There was a problem updating the booking's details");
		}
	}


	/**
	* Remove booking
	*
	* @param object         $id          Booking ID
	* @return boolean                    True if success, else false
	*/
	public function delete($id) {
		$this->_db->delete(self::BOOKING_TABLE, array(self::COL_BOOKING_ID, '=', $id));
	}


	/**
	* Check if user exists
	*
	* @param object         $id          Booking ID
	* @param boolean        $get         Get data if set to true
	* @return boolean                    True if user exists, else false
	*/
	//////////////////////
	public function find($id, $get = false) {
		$user = $this->_db->select(self::BOOKING_TABLE, array(), array(self::COL_BOOKING_ID, '=', $id));

		if($user->count()) {
			if($get) {
				$this->_data = $user->fetchAll();
			}

			return true;
		}

		return false;
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
