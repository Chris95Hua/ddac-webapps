<?php
	require_once ('/modal/core/setup.php');

	// validate current page exists and user has permission to view it
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
				

	            <div class="top-bar dashboard-top-bar">
	                <div class="top-bar-title">
	                  <span data-responsive-toggle="responsive-menu" data-hide-for="large">
	                      <button class="menu-icon dark " type="button" data-toggle="offCanvas" ></button>
	                      <strong class="hide-for-large-up">Menu</strong>
	                  </span>
	                </div>
	                
	                <div id="responsive-menu">
	                    <div class="top-bar-left">
							<a href="home"><img style="height:2.5rem" src="img/uia-full-logo.png" /></a>
	                    </div>
	                    
	                    <div class="top-bar-right">
						    <ul class="dropdown menu" data-dropdown-menu>
								<li>
								<a href="#">Currency/Region</a>
								<ul class="menu vertical">
									<li><a href="#">One</a></li>
									<li><a href="#">Two</a></li>
									<li><a href="#">Three</a></li>
								</ul>
								</li>
								<li><a href="#">Sign In</a></li>
								<li><a href="#">Sign Up</a></li>
						    </ul>
	                    </div>
	                </div>
	            </div>

		</div>
		<!-- Header end -->

		<!-- Content start -->
		<div id="content">
			<div class="cover no-scroll component-padding">
				<img style="max-width: 100%; max-height: 100%;" class="hide-for-small-only" src="http://via.placeholder.com/1920x730" />
				<div style="height:240px" class="show-for-small-only"></div>

				<div class="content middle">
					<div class="row fullwidth" style="z-index: 10; position: relative;">
						<div class="large-5 small-12 columns" style="background:rgba(0,0,0,0.6);padding-top:24px;padding-bottom:12px">
							<label>Email
								<input id="email" name="email" type="email" placeholder="somebody@example.com" required>
							</label>

							<label>Password
								<input id="password" name="password" type="password" placeholder="password" required>
							</label>

							<button type="submit" id="post" class="button expanded">Search</button>
						</div>
					</div>
				</div>
			</div>



			<div class="row" style="margin-bottom: 80px">
				<div class="large-6 small-12 columns component-padding">
					<h3>Hot Deals</h3>
					<ul class="deal-list">
						<li>
							<a href="#">
								<span class="deal-item">asdasd</span>
								<span class="deal-price"><span style="font-size:0.9rem">From</span> RM 399</span>
							</a>
						</li>
						<li>
							<a href="#">
								<span class="deal-item">asdasd</span>
								<span class="deal-price"><span style="font-size:0.9rem">From</span> RM 399</span>
							</a>
						</li>
						<li>
							<a href="#">
								<span class="deal-item">asdasd</span>
								<span class="deal-price"><span style="font-size:0.9rem">From</span> RM 399</span>
							</a>
						</li>
						<li>
							<a href="#">
								<span class="deal-item">asdasd</span>
								<span class="deal-price"><span style="font-size:0.9rem">From</span> RM 399</span>
							</a>
						</li>
					</ul>
				</div>

				<div class="large-6 small-12 columns component-padding">
					<h3>Featured Routes</h3>
					<ul class="deal-list">
						<li>
							<a href="#">
								<span class="deal-item">asdasd</span>
								<span class="deal-price"><span style="font-size:0.9rem">From</span> RM 399</span>
							</a>
						</li>
						<li>
							<a href="#">
								<span class="deal-item">asdasd</span>
								<span class="deal-price"><span style="font-size:0.9rem">From</span> RM 399</span>
							</a>
						</li>
						<li>
							<a href="#">
								<span class="deal-item">asdasd</span>
								<span class="deal-price"><span style="font-size:0.9rem">From</span> RM 399</span>
							</a>
						</li>
						<li>
							<a href="#">
								<span class="deal-item">asdasd</span>
								<span class="deal-price"><span style="font-size:0.9rem">From</span> RM 399</span>
							</a>
						</li>
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
