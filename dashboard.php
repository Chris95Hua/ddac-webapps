<?php
	require_once ('/modal/core/setup.php');
	
	if(Input::exist() && isset($_POST['currency'])) {		
		$_SESSION['currency'] = Input::get('currency');
		$rate = MySQLConn::getInstance()->select("currency", array("rate"), array("code", '=', $_SESSION['currency']), "LIMIT 1")->fetch()['rate'];
	}

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
							<a href="index.php"><img style="height:2.5rem" src="img/uia-full-logo.png" /></a>
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
								<li>
									<a href="#"><?php echo $_SESSION['name']; ?></a>
									<ul class="menu vertical">
										<li><a href="controller/logout.php">Logout</a></li>
									</ul>
								</li>
						    </ul>
	                    </div>
	                    </form>
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