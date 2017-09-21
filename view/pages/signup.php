<?php
// insert into database

$error = -1;

if(Input::exist()) {
	$salt = Hash::salt(32);

	//create user
	$account = array(
			'role_id' => '2',
			'full_name' => Input::get('fullName'),
			'birth_date' => DateTime::createFromFormat('d/m/Y', Input::get('birthdate'))->format('Y-m-d'),
			'email' => Input::get('email'),
			'password' => Hash::make(Input::get('password'),$salt),
			'date_joined' => date('Y-m-d H:i:s'),
			'salt' => $salt
		);
					
	try {
		$error = $user->create($account);
	}
	catch(Exception $e) {
		die($e->getMessage());
	}
}
// admin@uia.com
// 123456
?>

<!-- Content start -->
<div id="content">

	<!-- View start -->
	<div class="row">

	<?php if($error == 1): ?>
		<div class="alert callout" data-closable>
			<h5>Account Creation Failed</h5>
			<p>It appears that there is an issue when creating the account.</p>
			<button class="close-button" aria-label="Dismiss alert" type="button" data-close>
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
	<?php elseif($error == 0): ?>
		<div class="success callout" data-closable>
			<h5>Congratulation!</h5>
			<p>Your account has been created successfully. Click <a href="portal.php?page=signin">here</a> to sign in.</p>
			<button class="close-button" aria-label="Dismiss alert" type="button" data-close>
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
	<?php endif; ?>

		<div class="medium-6 medium-centered large-4 large-centered columns">

			<form method="post" action="" accept-charset="UTF-8">
				<div class="row column" style="margin-bottom: 120px;">

					<div class="form-border">
						<h3 class="text-center">Sign up</h3>

						<label>Full Name
							<input id="fullName" name="fullName" type="text" placeholder="John Doe" autocomplete="off" data-max-length='128' required>
						</label>

						<label>Date of Birth
			                <div class="input-group date" >
			                  <span class="input-group-label"><i class="fi-calendar"></i></span>
			                  <input id="birthdate" name="birthdate" class="input-group-field" type="text" placeholder="28/08/1988" required>
			                </div>
						</label>

						<label>Email
							<input id="email" name="email" type="email" placeholder="somebody@example.com" data-max-length='128' required>
						</label>

						<label>Password
							<input id="password" name="password" data-min-length="5" type="password" placeholder="password" data-max-length='16' required>
						</label>

						<label>Retype Password
							<input id="passwordConfirm" name="passwordConfirm" type="password" placeholder="password" data-max-length='16' data-equal-id="password" required>
						</label>

						<button type="submit" id="post" class="button expanded">Sign Up</button>
						<p class="text-center">Already have an account? <a href="portal.php?page=signin">Sign in</a>
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
<script src="js/foundation-datepicker.min.js"></script>
<script src="js/app.js"></script>
<script>
$(function(){
  $('#birthdate').fdatepicker({
  	format: 'dd/mm/yyyy',
  	endDate: new Date()
	});
});

// custom validation
$('[data-equal-id]').bind('input', function() {
    var to_confirm = $(this);
    var to_equal = $('#' + to_confirm.data('equalId'));

    //if(to_confirm.val() != to_equal.val())
    if($('#password').val() == $('#passwordConfirm').val())
        document.getElementById("password").setCustomValidity('');
    else
    	document.getElementById("password").setCustomValidity('Passwords do not match');
});

$('[data-max-length]').bind('input', function() {
    var input_target = $(this);
    var limit = parseInt(input_target.data('maxLength'));
    
    if(input_target.val().length >= limit)
        this.setCustomValidity('This field must be less than ' + limit + ' characters long');
    else
        this.setCustomValidity('');
});

$('[data-min-length]').bind('input', function() {
    var input_target = $(this);
    var limit = parseInt(input_target.data('minLength'));
    
    if(input_target.val().length <= limit)
        this.setCustomValidity('This field must contain more than ' + limit + ' characters');
    else
        this.setCustomValidity('');
});
</script>