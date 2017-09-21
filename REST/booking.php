<?php
require_once ('../modal/core/setup.php');

$bookHelper = Booking::getInstance();

// read GET
if (Input::get('view')) {

}
else {
	echo "<div style='padding-top: 120px; padding-bottom: 60px'><h3>No bookings found.</h3></div>";
}

// echo result
echo $_SESSION['ID'];
if(0) {
	
}
else {
	//(Integer clientId, Integer resultCount, Integer offset

	$table = "<table class='component-padding'><thead style='background-color: #eaeaea'><tr>";
	$table .= "<th width='175'>Booking ID</th>";
	$table .= "<th width='325'>Origin</th>";
	$table .= "<th width='325'>Destination</th>";
	$table .= "<th width='250'>DateTime</th>";
	$table .= "<th>Status</th>";
	$table .= "</tr></thead><tbody>";



	$table .= "</tbody></table>";

/*			
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
*/					
					
	/*
           
            for (CargoDetails cargo: mBookings) {
                content.append("<tr><td><a href='details.xhtml?id=").append(cargo.getCargoId()).append("'>");
                content.append(cargo.getCargoId()).append("</a></td>");
                
                content.append("<td>").append(cargo.getOrigin()).append("</td>");
                content.append("<td>").append(cargo.getDestination()).append("</td>");
                content.append("<td>").append(cargo.getFormattedDeadline("dd-MM-yyyy")).append("</td>");
                
                String formatStyle;
                
                switch(cargo.getStatus()) {
                    case 1:
                        formatStyle = "<i class='fi-compass' style='color: yellow'></i>";
                        break;
                    case 2:
                        formatStyle = "<i class='fi-check' style='color: greenyellow'></i>";
                        break;
                    default:
                        formatStyle = "<i class='fi-x-circle' style='color: red'></i>";
                        break;
                }
                
                content.append("<td>").append(formatStyle).append("</td>").append("</tr>");    
            }
	*/
}

?>