<?php require_once ('/modal/core/setup.php');?>
<?php if(!isset($_SESSION['role'])) $_SESSION['role'] = 'Guest'; ?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="description" content="The Computer Shop" />
		<meta name="keywords" content="Ukraine International Airlines, UIA, flight, travel, booking" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" type="image/png" href="assets/favicon.png">
		
		<title><?php echo Config::get('site_name') . " | " .  $page->data()->title; ?></title>

		<base href="<?php echo Config::get('base_url'); ?>/">
		
		<!-- Include css -->
		<?php require_once ('/modal/config/css.php'); ?>
		
	</head>
	
	<body>
		<!-- content start -->
		<div id="page-content">

			<!-- top bar start -->
			<div class="top-bar-center">
				<a href="<?php echo Config::get('home'); ?>">
					<img class="logo" src="assets/logo-alt.png"></img>
				</a>
			</div>
			<!-- top bar end -->

			<!-- view start -->
			<div class="row" style="margin-top: 86px">
				<div class="medium-6 medium-centered large-4 large-centered columns">

				<?php
				//getting the view(content) of the page via the slug (page and slug name must be exact match)
				if (Input::get('page')) {
					if ($page->find(Input::get('page'))) {
						if($page->pageCheck($_SESSION['role'],Input::get('page'))){
							require_once('/view/pages/'.Input::get('page').'.php');
						}
						else{
							header('Location: error403');
							exit();
						}
					}
					else {
						header('Location: error404');
						exit();
					}
				}
				else {
					header('Location: home');
					exit();
				}
				?>

				</div>
			</div>
			<!-- view end -->

		</div>
		<!-- content end -->

		<!-- Footer start -->
		<?php require_once('/view/components/footer.php'); ?>
		<!-- Footer end -->

		<!-- Include javascripts -->
		<?php require_once('/modal/config/js.php'); ?>
	</body>


</html>