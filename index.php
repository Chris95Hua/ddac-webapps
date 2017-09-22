<?php
	require_once ('/modal/core/setup.php');

	if($_SESSION['role'] != Config::get('guest')){
		header('Location: dashboard.php?page=main');
	}

	if(Input::exist() && isset($_POST['currency'])) {		
		$_SESSION['currency'] = Input::get('currency');
		$rate = MySQLConn::getInstance()->select("currency", array("rate"), array("code", '=', $_SESSION['currency']), "LIMIT 1")->fetch()['rate'];
	}

	// validate current page exists and user has permission to view it
	$routes = Booking::getInstance()->getTopFlight(8);
	$view = $page->pageCheck();
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="description" content="<?= Config::get('site_name') ?>" />
		<meta name="keywords" content="Ukraine International Airlines, UIA, flight, travel, booking" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" type="image/png" href="assets/favicon.png">

		<title><?= Config::get('site_name')." | ".$page->data()[Page::COL_TITLE] ?></title>

		<!--<base href="<?= BASE_URL ?>/">-->

		<!-- Foundation -->
		<link rel="stylesheet" type="text/css" href="css/app.css" />

		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>

	<body>

		<!-- Header start -->
		<div id="header">
				

	            <div class="top-bar dashboard-top-bar" style="width:100%;z-index:999">
	                <div class="top-bar-title">
	                  <span data-responsive-toggle="responsive-menu" data-hide-for="large">
	                      <button class="menu-icon dark " type="button" data-toggle="offCanvas" ></button>
	                      <strong class="hide-for-large-up">Menu</strong>
	                  </span>
	                </div>
	                
	                <div id="responsive-menu">
	                    <div class="top-bar-left">
							<a href=""><img style="height:2.5rem" src="img/uia-full-logo.png" /></a>
	                    </div>
	                    <form method="post" action="" accept-charset="UTF-8">
	                    <div class="top-bar-right">
						    <ul class="dropdown menu" data-dropdown-menu>
								<li>
									<a href="#"><?php echo $region . "/" . $_SESSION['currency'] ?></a>
									<ul class="menu vertical">
										<li><button name="currency" type="submit" value="USD">USD</button></li>
										<li><button name="currency" type="submit" value="RUB">RUB</button></li>
										<li><button name="currency" type="submit" value="MYR">MYR</button></li>
									</ul>
								</li>
						    </ul>
	                    </div>
	                    </form>
	                </div>
	            </div>

		</div>
		<!-- Header end -->

		<!-- Content start -->
		<div id="content">
			<div class="cover no-scroll component-padding">
				<img style="max-width: 100%; max-height: 100%;" class="hide-for-small-only" src="img/uia_home_<?php echo rand(1, 4); ?>.jpg" />
				<div style="height:240px" class="show-for-small-only"></div>

				<div class="content middle">
					<div class="row fullwidth" style="z-index: 10; position: relative;">
						<div class="large-5 small-12 columns" style="background:rgba(0,0,0,0.6);padding-top:24px;padding-bottom:12px">
							<h3>Your Journey Begins Here</h3>
							<p>Here at Ukraine International Airlines, we strive to
							provide the best experience when you're taking your
							flight with us. Enjoy world-class facilities and service
							when traveling with us.</p>

							<a class="button primary" href="portal.php?page=signup">Sign Up</a>
							<a class="hollow button" style="color: #3f80ea;border:1px solid #3f80ea" href="portal.php?page=signin">Sign In</a>
						</div>
					</div>
				</div>
			</div>



			<div class="row" style="margin-bottom: 80px">
				<div class="large-6 small-12 columns component-padding">
					<h3>Hot Deals</h3>
					<ul class="deal-list">
						<?php

						for ($x = 0; $x < 4; $x++) {
						    $deal = "<li><a href='#''><span class='deal-item'>";
						    $deal .= $routes[$x][Booking::COL_SOURCE] . " to " . $routes[$x][Booking::COL_DESTINATION];
						    $deal .= "</span><span class='deal-price'><span style='font-size:0.9rem'>From</span> ";
						    $deal .= $_SESSION['currency'].($routes[$x][Booking::COL_ECONOMY] * $rate);
						    $deal .= "</span></a></li>";
						    echo $deal;
						} 

						?>
					</ul>
				</div>

				<div class="large-6 small-12 columns component-padding">
					<h3>Featured Routes</h3>
					<ul class="deal-list">
						<?php

						for ($x = 4; $x < 8; $x++) {
						    $deal = "<li><a href='#''><span class='deal-item'>";
						    $deal .= $routes[$x][Booking::COL_SOURCE] . " to " . $routes[$x][Booking::COL_DESTINATION];
						    $deal .= "</span><span class='deal-price'><span style='font-size:0.9rem'>From</span> ";
						    $deal .= $_SESSION['currency'].($routes[$x][Booking::COL_ECONOMY] * $rate);
						    $deal .= "</span></a></li>";
						    echo $deal;
						} 

						?>
					</ul>
				</div>
			</div>

		</div>
		<!-- Content end -->

		<!-- Footer start -->
		<div id="footer" class="classic-footer">
			<?php require_once('/view/components/footer.php'); ?>
		</div>
		<!-- Footer end -->

		<!-- JQuery -->
		<script src="js/jquery.min.js"></script>
		<!-- Foundation -->
		<script src="js/foundation.min.js"></script>
		<script src="js/app.js"></script>
	</body>

</html>
