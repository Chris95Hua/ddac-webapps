<?php
require_once ('../modal/core/setup.php');

$bookHelper = Booking::getInstance();

$offset = 0;
// read GET
if (Input::get('view')) {
	$offset = Input::get('view') - 1;
	$bookings = $bookHelper->getAllUserBookings($_SESSION['ID'], Config::get('max_booking_display'), $offset * Config::get('max_booking_display'));
}
else {
	echo "<div style='padding-top: 120px; padding-bottom: 60px'><h3>Unable to load bookings.</h3></div>";
}

if($bookings) {
	$table = "<table class='component-padding'><thead style='background-color: #eaeaea'><tr>";
	$table .= "<th width='175'>Booking ID</th>";
	$table .= "<th width='325'>Origin</th>";
	$table .= "<th width='325'>Destination</th>";
	$table .= "<th width='250'>Departure</th>";
	$table .= "</tr></thead><tbody>";

	foreach ($bookings as $booking) {
		$bookingID = $booking[Booking::COL_BOOKING_ID];
		$origin = $booking[Booking::COL_SOURCE];
		$destination = $booking[Booking::COL_DESTINATION];
		$departure = $booking[Booking::COL_DEPARTURE];

		$table .= "<tr><td><a href='dashboard.php?page=detail&id={$bookingID}'>{$bookingID}</a></td>";
		$table .= "<td>{$origin}</td>";
		$table .= "<td>{$destination}</td>";
		$table .= "<td>{$departure}</td></tr>";
	}

	$table .= "</tbody></table>";

	echo $table;
/*			
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
*/					
					

}
else {
	echo "<div style='padding-top: 120px; padding-bottom: 60px'><h3>No bookings found.</h3></div>";
}

?>