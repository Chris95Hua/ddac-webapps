<?php

if(Input::exist()) {
	header('Location: dashboard.php?page=booking&flight=' . Input::get('flight'));
}

$airports = $booking->getAirports(false);

?>

<!-- Content start -->
<div id="content" style="height:calc(100% - 56px); ">

	<?php require_once('/view/components/sidebar.php'); ?>

	<div class="off-canvas-content" data-off-canvas-content="true">

		<div class="small-12 small-centered columns">
			<div class="reveal" id="loading" data-close-on-click="false" data-close-on-esc="false" data-reveal>
			  <h2>Hold on...</h2>
			  <p class="lead">We're searching the available dates for the flight.</p>
			</div>

			<form method="post" action="" accept-charset="UTF-8">
					<div class="row column">
						<div class="small-12 columns" style="padding-top: 15px; padding-bottom: 15px">
							<h3><i class="fi-magnifying-glass"></i> Search flight</h3>
						</div>

						<div class="medium-6 medium-centered large-4 large-centered columns">
							<label>Origin
								<select name="origin" id="origin" onchange="repopulateAirport(this, 'destination')">

								</select>
							</label>
						</div>

						<div class="medium-6 medium-centered large-4 large-centered columns">
							<label>Destination
								<select name="destination" id="destination" onchange="repopulateAirport(this, 'origin')">

								</select>
							</label>
						</div>

						<div class="medium-6 medium-centered large-4 large-centered columns">
							<label>Departure
								<select name="flight" id="departure">
								</select>
							</label>
						</div>

						<div class="medium-6 medium-centered large-4 large-centered columns">
							<button type="submit" id="post" class="button expanded">Search</button>
						</div>	

					</div>
			</form>

		</div>
	</div>

</div>
<!-- Content end -->

<!-- JQuery -->
<script src="js/jquery.min.js"></script>
<!-- Foundation -->
<script src="js/foundation.min.js"></script>
<script src="js/app.js"></script>
<script>
var oriCmb = document.getElementById('origin');
var desCmb = document.getElementById('destination');
var airports = <?php echo json_encode($airports); ?>;

populateAirport(oriCmb, desCmb, "-");
populateAirport(desCmb, oriCmb, "-");
populateDates(oriCmb, desCmb);

function populateAirport(sel, ref, option) {
	var exists = false;

	for (var i=0; i<airports.length; i++) {
		if(ref.options.length == 0 || airports[i].iata_code != ref.options[ref.selectedIndex].value) {
			var opt = document.createElement("option");

			if(airports[i].iata_code == option) {
				exists = true;
			}

			opt.value= airports[i].iata_code;
			opt.innerHTML = airports[i].iata_code + ', ' + airports[i].country; // whatever property it has

			// then append it to the select element
			sel.appendChild(opt);
		}
	}

	return exists;
}

function populateDates(origin, destination) {
	var dateCmb = document.getElementById('departure');
	var oriVal = origin.options[origin.selectedIndex].value;
	var desVal = destination.options[destination.selectedIndex].value;

	$('#loading').foundation('open');
	$("#departure").attr("disabled", true);

	 $.ajax({
		dataType: "json",
		type: "GET",
		url: "REST/departure.php",
		data: {"origin":oriVal, "destination":desVal},
		success: function(result)
		{
			for (var i=0; i<result.length; i++) {
				var opt = document.createElement("option");
				opt.value= result[i].flight_id;
				opt.innerHTML = result[i].departure; // whatever property it has

				// then append it to the select element
				dateCmb.appendChild(opt);
			}

			$("#departure").attr("disabled", false);
	 	},
        error: function (request, status, errorThrown) {
            alert(status);
        }
	 });

	 $('#loading').foundation('close');
}

function repopulateAirport(sel, toUpdate){
	var originSelected = (toUpdate != "origin");

	var toUpdateCmb = document.getElementById(toUpdate);
	var dateCmb = document.getElementById('departure');
	var selectedVal = toUpdateCmb.options[toUpdateCmb.selectedIndex].value;

	// clear another combobox
    var i;
    for(i = toUpdateCmb.options.length - 1 ; i >= 0 ; i--)
    {
        toUpdateCmb.remove(i);
    }

    if(populateAirport(toUpdateCmb, sel, selectedVal)) {
    	toUpdateCmb.value = selectedVal;
    }

	// remove and repopulate date
    for(i = dateCmb.options.length - 1 ; i >= 0 ; i--)
    {
        dateCmb.remove(i);
    }

    populateDates(oriCmb, desCmb);
}




$(document).ready(function () {
 
 $('#retrieve-resources').click(function () {
 var displayResources = $('#display-resources');
 
 displayResources.text('Loading data from JSON source...');
 

 
 });
});
</script>