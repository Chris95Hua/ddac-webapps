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

		<base href="<?= URL ?>/">

		<!-- Foundation -->
		<link rel="stylesheet" type="text/css" href="css/app.css" />

		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>

	<body>
		<!-- Wrapper start -->
		<div id="page" class="using-classic-footer">

			<!-- Header start -->
			<div id="header">
				<div class="centralise component-padding" style="margin-top: 5px">
					<a href="/EPDA-war/faces/index.xhtml"><img id="classic-top-logo" src="#" /></a>
				</div>
			</div>
			<!-- Header end -->

			<!-- Content start -->
			<div id="content">

			<!-- View start -->
			<?php
			//getting the view(content) of the page via the slug
			require_once(implode(DS, array(VIEW, 'pages', $view.'.php')));
			?>
			<!-- View end -->

			</div>
			<!-- Content end -->
		</div>
		<!-- Wrapper end -->

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
