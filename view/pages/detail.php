<?php

$error = -1;

if(Input::get('id')) {
	// find route/booking stuff
	$flight = $booking->getUserBooking(Input::get('id'));
	$seats = $booking->getBookedSeats(Input::get('id'));

}
else {
	header('Location: dashboard.php?page=main');
}

?>

<!-- Content start -->
<div id="content" style="height:calc(100% - 56px); ">

	<?php require_once('/view/components/sidebar.php'); ?>

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

			<div class="small-12 medium-4 columns" style="padding: 0; height: 100%; overflow-y: auto;">

				<div class="row columns" style="padding: 0px;">
					<div class="row columns" style="padding: 15px;">
						<h3><i class="fi-plus"></i> Booking Details</h3>
					</div>

                  <div class="row columns">
                      <div class="small-12 columns">
                          <ul class="vertical-detail">
                              <li><span>Origin</span><?php echo $flight[Booking::COL_SOURCE]; ?></li>
                              <li><span>Destination</span><?php echo $flight[Booking::COL_DESTINATION]; ?></li>
                              <li><span>Departure</span><?php echo $flight[Booking::COL_DEPARTURE]; ?></li>
                              <li><span>Total (<?php echo $_SESSION['currency'] ?>)</span><p id="total">0</p></li>
                          </ul>
                      </div>
                  </div>

				</div>

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
<script src="js/jquery.seat-charts-static.min.js"></script>
<script>

// seats
var firstSeatLabel = 1;
var sc;
$(document).ready(function() {
	var $cart = $('#selected-seats'),
		$counter = $('#counter'),
		$total = $('#total'),
		$sel = $('#selSeat');
	sc = $('#seat-map').seatCharts({
		map: [
			'fff_fff',
			'fff_fff',
			'bbb_bbb',
			'eee_eee',
			'eee_eee',
			'eee___',
			'eee_eee',
			'eee_eee',
			'eee_eee',
			'eee_eee',
		],
		seats: {
			f: {
				price   : <?php echo $flight[BOOKING::COL_FIRST] * $rate; ?>,
				classes : 'first-class',
				category: 'First Class',
				type	: 'f'
			},
			b: {
				price   : <?php echo $flight[BOOKING::COL_BUSINESS] * $rate; ?>,
				classes : 'business-class',
				category: 'Business Class',
				type	: 'b'
			},
			e: {
				price   : <?php echo $flight[BOOKING::COL_ECONOMY] * $rate; ?>,
				classes : 'economy-class',
				category: 'Economy Class',
				type	: 'e'
			}					
		
		},
		naming : {
			top : false,
			rows: ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
			getLabel : function (character, row, column) {
				return row + column;
			},
		},
		legend : {
			node : $('#legend'),
		    items : [
				[ 'f', 'available',   'First Class'],
				[ 'b', 'available',   'Business Class'],
				[ 'e', 'available',   'Economy Class'],
				[ 'f', 'selected', 'Booked Seats']
		    ]					
		},
		click: function () {
			if (this.status() == 'available') {
				//let's create a new <li> which we'll add to the cart items
				console.log(this.data().type);
				$('<li>'+this.data().category+' Seat #<input type="checkbox" name="seats[]" style="display: none" value="'+this.settings.label+':'+this.data().type+'" checked/>'+this.settings.label+': <b>$'+this.data().price+'</b> <a href="javascript:void(0)" class="cancel-cart-item">[cancel]</a></li>')
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
	sc.get([<?php echo implode(", ",$seats); ?>]).status('selected');

	//console.log(sc.get('E_6').data().type);
	$total.text(recalculateTotal(sc));
});

function recalculateTotal(sc) {
	var total = 0;

	//basically find every selected seat and sum its price
	sc.find('selected').each(function () {
		total += this.data().price;
	});

	return total;
}

</script>