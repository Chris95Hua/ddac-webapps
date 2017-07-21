<?php
// user not logged in
if(!$user->isLoggedIn()) {
	// check if login inputs are provided
	if(Input::exist()) {
		
		$remember = (Input::get('remember') === 'on') ? true : false;
		
		if($user->login(Input::get('email'),Input::get('password'), $remember)) {
			header('Location: '.Config::get('home'));
			exit();
		}
		else{
			$_SESSION["errors"][] =array('message' => 'Email or Password incorrect', 'type' => 'danger');
		}
	}
}
// user is logged in, get message count
else
{
	$mess = new Message();
	$messCount = $mess->countUnread();
}
?>

<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container">
		<!-- site name -->
		<a class="navbar-brand" href="<?php echo Config::get('home'); ?>"><img src="img/logo.svg" alt="<?php echo Config::get('site_name'); ?>" height="45px"/></a>
		
		<!-- mobile control start -->
		<div class="navbar-header">
		  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bc-navbar"">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		  </button>
		</div>
		<!-- mobile control end -->
		
		<!-- navbar items start -->
		<div class="collapse navbar-collapse" id="bc-navbar">
			<!-- navbar pages start -->
			<ul class="nav navbar-nav">
				<?php
					$role = ($user->isLoggedIn()) ? $_SESSION['role'] : 'Public';
					$pages = $page->getPages($role);
					
					$menu = array();
					$dropdown = array();
					
					if ($pages) :
						foreach($pages as $result):
							if($page->load($result)):
								if(!empty($page->loadData()->dropdown)): 
									//store drop down
									$dropdown[$page->loadData()->dropdown][] = '<li><a href="' . $page->loadData()->slug . '">' . $page->loadData()->title . '</a></li>';		
									
								else:
									
									//store normal link
									$m1 = '<li';
									$active = (Input::get("page")==$page->loadData()->slug) ? ' class="active"' : '';
									$m2 = '><a href="' . $page->loadData()->slug . '">';
									$name = $page->loadData()->title;
									$badge = ($user->isLoggedIn() && !empty($messCount) && $page->loadData()->slug == "message") ? '<span class="badge pull-right">' . $messCount . '</span>' : '';
									$m3 = '</a></li>';
									
									echo $m1 . $active . $m2 . $name . $badge . $m3; 
								
								endif;
							endif;
						endforeach;
						
						foreach ($dropdown as $dropName => $value):
							echo '<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">' . $dropName . '<span class="caret"></span></a><ul class="dropdown-menu" role="menu">';
							foreach($value as $title):
								echo $title;
							endforeach;
							echo '</ul></li>';
						endforeach;
		
					endif;
				?>
			
			</ul>
			<!-- navbar pages end -->
			
			<!-- log in form start -->
			<?php if($user->isLoggedIn()): ?>
			
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown">
						<a class="dropdown-toggle" href="#" data-toggle="dropdown"><?php echo $user->data()->fName . " " . $user->data()->lName; ?><span class="caret"></span></a>
						  <ul class="dropdown-menu" role="menu">
							<li><a href="logout.php">Logout</a></li>
						  </ul>
					</li>
				</ul>
				
			<?php else: ?>
			
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown">
						<a class="dropdown-toggle highlight" href="#" data-toggle="dropdown">Sign In <span class="caret"></span></a>
						<div class="dropdown-menu form-group-xs" style="padding: 15px; padding-bottom: 0px; width:300px;">
							<form method="post" action="" accept-charset="UTF-8">
								<input class="form-control" style="margin-bottom: 15px;" type="text" placeholder="Email" id="email" name="email" required >
								<input class="form-control" style="margin-bottom: 10px;" type="password" placeholder="Password" id="password" name="password" required >
								<input class="checkbox" style="float: left; margin-right: 10px;" type="checkbox" name="remember" id="remember">
								<label class="string" style="font-family:sans-serif; font-weight:normal; margin-bottom:10px">Remember me</label>
								<input class="btn btn-primary btn-block" type="submit" id="sign-in" value="Sign In">
								<br>
							</form>
						</div>
					</li>
				</ul>
				
			<?php endif; ?>
			<!--log in form end -->
			
		</div>
		<!-- navbar items end -->
	</div>
</nav>