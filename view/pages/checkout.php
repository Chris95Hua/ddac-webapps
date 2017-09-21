<?php

$error = -1;

if(isset($_SESSION['flight']) && isset($_SESSION['seats'])) {
	$flight = $booking->getFlight($_SESSION['flight']);
}
else {
	header('Location: dashboard.php?page=search');
}

if(Input::exist() && isset($_POST['confirm'])) {
	$bookDate = date('Y-m-d H:i:s');

	$newBooking = array(
			Booking::COL_USER_ID => $_SESSION['ID'],
			Booking::COL_FLIGHT_ID => $_SESSION['flight'],
			Booking::COL_DATE_BOOKED => $bookDate
		);

	$newID = $booking->createBooking($newBooking);

	if($newID > 0) {
		$bookedSeats = array();

	    foreach($_SESSION['seats'] as $seat => $class) { 
	    	$bookedSeats[] = $newID.",'{$seat}'";
	    }

		$booking->addBookingSeats($bookedSeats);

		unset($_SESSION['seats']);
		unset($_SESSION['flight']);

		header('Location: dashboard.php?page=main');
	}
	else {
		$error = 1;
	}
}

?>

<!-- Content start -->
<div id="content">

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
					<span>Create</span>
				</a>
			</li>
		</ul>
	</div>

	<div class="off-canvas-content" data-off-canvas-content="true" style="height: 100%; overflow-y: auto;">
		<div class="row" style="padding-top: 15px">

		<?php if($error == 1): ?>
			<div class="small-12 columns">
				<div class="alert callout" data-closable>
					<h5>Booking Failed</h5>
					<p>It appears that there is an issue when creating the booking. Please try again later.</p>
					<button class="close-button" aria-label="Dismiss alert" type="button" data-close>
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			</div>
		<?php endif; ?>

			<div class="small-12 medium-8 columns">
				<div class="callout">
					<h4 class="float-left">
						<i class="fi-info"></i>
						 Booking Summary
					</h4>

					<div class="row">
						<div class="small-12 columns component-padding" id"selSeat">
							<ul class="vertical-detail">
								<li><span>Origin</span><?php echo $flight[Booking::COL_SOURCE]; ?></li>
								<li><span>Destination</span><?php echo $flight[Booking::COL_DESTINATION]; ?></li>
								<li><span>Departure</span><?php echo $flight[Booking::COL_DEPARTURE]; ?></li>
								<li><span>Booked Seats</span>
								<?php
									$price = 0;
								    foreach($_SESSION['seats'] as $seat => $class) { 
								    	if($class == 'f') {
								    		$price += $flight[BOOKING::COL_FIRST];
								    		echo "<div class='seatCharts-seat seatCharts-cell first-class available'>{$seat}</div>";
								    	}
								    	else if($class == 'b') {
								    		$price += $flight[BOOKING::COL_BUSINESS];
								    		echo "<div class='seatCharts-seat seatCharts-cell business-class available'>{$seat}</div>";
								    	}
								    	else {
								    		$price += $flight[BOOKING::COL_ECONOMY];
								    		echo "<div class='seatCharts-seat seatCharts-cell economy-class available'>{$seat}</div>";
								    	}
								    }
								?>
								</li>
							</ul>
						</div>
					</div>
					
					<div class="row columns">
						<h4 class="float-right strong">Total: <strong>RM<?php echo $price; ?></strong></h4>
					</div>
				</div>
			</div>

			<div class="small-12 medium-4 columns" style="padding: 0; height: 100%; overflow-y: auto">

				<form method="post" action="" accept-charset="UTF-8">
					<div class="row columns" style="padding: 0px;">
						<div class="row columns" style="padding: 15px;">
							<h3><i class="fi-credit-card"></i> Checkout</h3>
						</div>

                        <div class="small-12 columns">
                            <label>Name on Card
                                <input type="text" name="cardName" placeholder="John Doe" autocomplete="off" required/>
                            </label>
                        </div>

						<div class="small-12 columns">
							<label>Card Number
								<input type="text" id="card" name="card" placeholder="0123456789" autocomplete="off" required/>
							</label>
						</div>

						<div class="small-12 columns">
							<label>Expiry Date and Security No.
								<div class="row">

									<div class="small-4 columns">
										<select id="exp-month" name="exp-month">
											<option value="1">Jan</option>
											<option value="2">Feb</option>
											<option value="3">Mar</option>
											<option value="4">Apr</option>
											<option value="5">May</option>
											<option value="6">Jun</option>
											<option value="7">Jul</option>
											<option value="8">Aug</option>
											<option value="9">Sep</option>
											<option value="10">Oct</option>
											<option value="11">Nov</option>
											<option value="12">Dec</option>
										</select>
									</div>

									<div class="small-4 columns">
										<select id="exp-year" name="exp-year">
											<option value="2017">2017</option>
											<option value="2018">2018</option>
											<option value="2019">2019</option>
											<option value="2020">2020</option>
											<option value="2021">2021</option>
											<option value="2022">2022</option>
											<option value="2023">2023</option>
											<option value="2024">2024</option>
											<option value="2025">2025</option>
											<option value="2026">2026</option>
											<option value="2027">2027</option>
											<option value="2028">2028</option>
											<option value="2016">2029</option>
										</select>
									</div>

									<div class="small-4 columns">
										<input type="number" id="cvv" name="cvv" min="001" max="999" autocomplete="off" required/>
									</div>
								</div>
							</label>
						</div>

						<div class="small-12 columns">
							<button type="submit" id="post" name="confirm" class="button expanded">Checkout</button>
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