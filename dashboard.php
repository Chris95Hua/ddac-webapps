<?php
	require_once ('/modal/core/setup.php');
	
	$booking = Booking::getInstance();

	// validate current page exists and user has permission to view it
	$view = $page->pageCheck();
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="description" content="The Computer Shop" />
		<meta name="keywords" content="Ukraine International Airlines, UIA, flight, travel, booking" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" type="image/png" href="assets/favicon.png">
		
		<title><?= Config::get('site_name')." | ".$page->data()[Page::COL_TITLE] ?></title>

		<!--<base href="<?= BASE_URL ?>/">-->

		<!-- Foundation -->
		<link rel="stylesheet" type="text/css" href="css/app.css" />
		<link rel="stylesheet" type="text/css" href="css/foundation-icons.css" />

		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	
	<body style="height:100%; background-color:#f8f8f8">
		<!-- Header start -->
		<div id="header">
				
			<div data-sticky-container>
	            <div class="top-bar dashboard-top-bar" data-sticky data-options="marginTop:0;" style="width:100%;z-index:999">
	                <div class="top-bar-title">
	                  <span data-responsive-toggle="menu" data-hide-for="large">
	                      <button class="menu-icon dark " type="button" data-toggle="offCanvas" ></button>
	                      <strong class="hide-for-large-up">Menu</strong>
	                  </span>

	                  <span data-responsive-toggle="responsive-menu" data-hide-for="large" style="position: absolute; top: 17px; right: 10px; display: none;">
	                    <button class="menu-icon dark" type="button" data-toggle="true"></button>
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
								<li>
									<a href="#">Name</a>
									<ul class="menu vertical">
										<li><a href="#">Logout</a></li>
									</ul>
								</li>
						    </ul>
	                    </div>
	                </div>
	            </div>
			</div>


		</div>
		<!-- Header end -->

		<?php
		//getting the view(content) of the page via the slug
		require_once(implode(DS, array(VIEW, 'pages', $view.'.php')));
		?>
	</body>


</html>