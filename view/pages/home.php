<div class="row">
	<div class="col-sm-12">
		<h1><?= $page->data()[Page::COL_TITLE] ?></h1>
	</div>
</div>

<?php if($user->isLoggedIn()): ?>

<button class="btn btn-lg btn-primary btn-block" type="submit">Log out</button>

<?php else: ?>

<?php
/*$db = MySQLConn::getInstance();
$results = $db->query("SELECT * FROM page")->fetchAll();
print_r($results[0][Page::COL_TITLE]);*/

// create new user
/*$salt = Hash::salt(32);

$user->insert(array(
	User::COL_USERNAME => 'admin',
	User::COL_PASSWORD => Hash::make('softengine', $salt),
	User::COL_SALT => $salt
	));*/


?>

<form class="form-signin" action="login.php" method="post">
	<h2 class="form-signin-heading">Please sign in</h2>

	<label for="username" class="sr-only">Username</label>
	<input type="text" id="username" class="form-control" placeholder="Username" required autofocus>

	<label for="password" class="sr-only">Password</label>
	<input type="password" id="password" class="form-control" placeholder="Password" required>

	<div class="checkbox">
		<label>
			<input type="checkbox" value="remember-me"> Remember me
		</label>
	</div>

	<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>

</form>

<?php endif; ?>

<!-- Custom page javascript here -->
<script></script>


<!--
<wrapper>
	<container>
-->
		<view></view>
<!--
	</container>
</wrapper>
-->

<!--
shared stuff
<footer></footer>

<script>
</script>
-->
<custom>
</custom>



