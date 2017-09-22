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
		<link rel="stylesheet" type="text/css" href="css/foundation-icons.css" />

		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>

	<body>
			<!-- Header start -->
			<div id="header">
				<div class="centralise component-padding" style="margin-top: 5px">
					<a href="index.php"><img id="classic-top-logo" src="img/uia-full-logo.png" /></a>
				</div>
			</div>
			<!-- Header end -->

			<?php
			//getting the view(content) of the page via the slug
			require_once(implode(DS, array(VIEW, 'pages', $view.'.php')));
			?>
	</body>

</html>
