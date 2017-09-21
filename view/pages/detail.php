<?php

$error = -1;

if(Input::get('booking')) {
	// find route/booking stuff
}
else {
	header('Location: dashboard.php?page=main');
}

?>

<!-- Content start -->
<div id="content" style="height:calc(100% - 56px); ">

	<div class="off-canvas position-left reveal-for-large dashboard-off-canvas is-transition-push" id="offCanvas" data-off-canvas="true" aria-hidden="false">
		<ul class="menu vertical">
			<li class="active">
				<a href="#">
					<i class="fi-results"></i>
					<span>Booking</span>
				</a>
			</li>
			<li>
				<a href="#">
					<i class="fi-plus"></i>
					<span>New</span>
				</a>
			</li>
		</ul>
	</div>

	<div class="off-canvas-content" data-off-canvas-content="true">
		<div class="full-width" style="height:100%">
			<!-- check destination + date, select seats, pay -->
			<div class="small-12 medium-8 columns" style="padding: 0; height: 100%; overflow-y: auto">
				<div style="width:555px;margin:0 auto;position:relative">
					<div id="seat-map">
						<div class="front-indicator">Front</div>
					</div>
					<div class="booking-details">
						<h2>Legend</h2>
						<div id="legend"></div>
					</div>
				</div>

			</div>

			<div class="small-12 medium-4 columns" style="padding: 0; height: 100%; overflow-y: auto">

				<form method="post" action="" accept-charset="UTF-8">
					<div class="row columns" style="padding: 0px;">
						<div class="row columns" style="padding: 15px;">
							<h3><i class="fi-plus"></i> Booking Details</h3>
						</div>

                        <ul class="accordion" data-accordion="true" data-multi-expand="true" data-allow-all-closed="true">
                            <li class="accordion-item is-active" data-accordion-item="true">
                              <a href="#" class="accordion-title">Booking info</a>
                              <div class="accordion-content" data-tab-content="true">

                                  <div class="row">
                                      <div class="small-12 columns">
                                          <ul class="vertical-detail">
                                              <li><span>Origin</span>[origin]</li>
                                              <li><span>Destination</span>[destination]</li>
                                              <li><span>Departure</span>[datetime]</li>
                                          </ul>
                                      </div>
                                  </div>

                              </div>
                            </li>

                            <li class="accordion-item is-active" data-accordion-item="true">
                              <a href="#" class="accordion-title">Seats (<span id="counter">0</span>)</a>
                              <div class="accordion-content" data-tab-content="true">

                                  <div class="row">
                                      <div class="small-12 columns">
										<ul id="selected-seats"></ul>
										<h5 style="margin-bottom:0px">Total: <b>$<span id="total">0</span></b></h5>

                                      </div>
                                  </div>

                              </div>
                            </li>
                        </ul>

						<div class="small-12 columns">
							<button type="submit" id="post" class="button expanded" disabled>Confirm</button>
						</div>	

					</div>
				</form>

			</div>

		</div>
	</div>

</div>
<!-- Content end -->

<link rel="stylesheet" type="text/css" href="css/jquery.seat-charts.css" />
<!-- JQuery -->
<script src="js/jquery.min.js"></script>
<!-- Foundation -->
<script src="js/foundation.min.js"></script>
<script src="js/app.js"></script>
<script src="js/foundation-datepicker.min.js"></script>
<script src="js/jquery.autocomplete.min.js"></script>
<script src="js/jquery.seat-charts.min.js"></script>
<script>

// seats
var firstSeatLabel = 1;

$(document).ready(function() {
	var $cart = $('#selected-seats'),
		$counter = $('#counter'),
		$total = $('#total'),
		sc = $('#seat-map').seatCharts({
		map: [
			'fff_fff',
			'fff_fff',
			'eee_eee',
			'eee_eee',
			'eee___',
			'eee_eee',
			'eee_eee',
			'eee_eee',
			'eeee',
		],
		seats: {
			f: {
				price   : 100,
				classes : 'first-class',
				category: 'First Class'
			},
			e: {
				price   : 40,
				classes : 'economy-class',
				category: 'Economy Class'
			}					
		
		},
		naming : {
			top : false,
			getLabel : function (character, row, column) {
				return firstSeatLabel++;
			},
		},
		legend : {
			node : $('#legend'),
		    items : [
				[ 'f', 'available',   'First Class' ],
				[ 'e', 'available',   'Economy Class'],
				[ 'f', 'unavailable', 'Already Booked']
		    ]					
		},
		click: function () {
			if (this.status() == 'available') {
				//let's create a new <li> which we'll add to the cart items
				$('<li>'+this.data().category+' Seat #'+this.settings.label+': <b>$'+this.data().price+'</b> <a href="javascript:void(0)" class="cancel-cart-item">[cancel]</a></li>')
					.attr('id', 'cart-item-'+this.settings.id)
					.data('seatId', this.settings.id)
					.appendTo($cart);
					
				var selTotal = sc.find('selected').length+1;
				$counter.text(selTotal);
				$("#post").attr("disabled", (selTotal == 0));

				$total.text(recalculateTotal(sc)+this.data().price);
				
				return 'selected';
			} else if (this.status() == 'selected') {
				//update the counter
				var selTotal = sc.find('selected').length-1;
				$counter.text(selTotal);
				$("#post").attr("disabled", (selTotal == 0));

				//and total
				$total.text(recalculateTotal(sc)-this.data().price);
			
				//remove the item from our cart
				$('#cart-item-'+this.settings.id).remove();
			
				//seat has been vacated
				return 'available';
			} else if (this.status() == 'unavailable') {
				//seat has been already booked
				return 'unavailable';
			} else {
				return this.style();
			}			
		}
	});

	//this will handle "[cancel]" link clicks
	$('#selected-seats').on('click', '.cancel-cart-item', function () {
		//let's just trigger Click event on the appropriate seat, so we don't have to repeat the logic here
		sc.get($(this).parents('li:first').data('seatId')).click();
	});

	//let's pretend some seats have already been booked
	sc.get(['1_2', '4_1', '7_1', '7_2']).status('unavailable');

});

function recalculateTotal(sc) {
	var total = 0;
	var count = 0;

	//basically find every selected seat and sum its price
	sc.find('selected').each(function () {
		total += this.data().price;
		count++;
	});

	return total;
}

</script>