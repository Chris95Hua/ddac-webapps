<?php
$error = -1;

if(Input::exist()) {
	$error = $user->login(Input::get('email'),Input::get('password'), isset($_POST['remember']));

	if($error == 0) {
		header('Location: dashboard.php?page=main');
		//exit();
	}
}

?>

<!-- Content start -->
<div id="content">

	<!-- View start -->
	<div class="row">

	<?php if($error == 1): ?>
		<div class="alert callout" data-closable>
			<h5>Login Failed</h5>
			<p>Incorrect password.</p>
			<button class="close-button" aria-label="Dismiss alert" type="button" data-close>
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
	<?php elseif($error == 2): ?>
		<div class="alert callout" data-closable>
			<h5>Login Failed</h5>
			<p>Account does not exists. Consider <a href="portal.php?page=signup">sign up</a> for one.</p>
			<button class="close-button" aria-label="Dismiss alert" type="button" data-close>
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
	<?php endif; ?>

		<div class="medium-6 medium-centered large-4 large-centered columns">

			<form method="post" action="" accept-charset="UTF-8">
				<div class="row column">

					<div class="form-border">
						<h3 class="text-center">Sign in</h3>

						<label>Email
							<input id="email" name="email" type="email" placeholder="somebody@example.com" required>
						</label>

						<label>Password
							<input id="password" name="password" type="password" placeholder="password" required>
						</label>

						<input id="remember" name="remember" type="checkbox"><label for="remember">Remember me</label>

						<button type="submit" id="post" class="button expanded">Sign In</button>
						<p class="text-center">Don't have an account? <a href="portal.php?page=signup">Sign up</a>
					</div>

				</div>
			</form>

		</div>
	</div>
	<!-- View end -->

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