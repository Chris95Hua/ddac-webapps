<?php
$allUsers = $user->getUsers();

$intake = new Intake();
$allIntake = $intake->getIntakes();
$alertState = 'hidden';

//delete flag: delete user
if(Input::get('del')) {
	//if(Token::check(Input::get('token'))) {
		$del = Input::get('del');
		unset($_POST['del']);
		
		if ($user->find($del)) {			
			$user->delete($del);
			
			//force refresh
			$_SESSION["errors"][] =array('message' => 'User deleted!', 'type' => 'success');
			header('Location: /BC_Webportal/' . Input::get('page'));
			exit();
			
		}
		else {
			$_SESSION["errors"][] =array('message' => 'Deletion failed', 'type' => 'danger');
		}
	//}
}

else if(Input::exist()) {
	//check if token is from this session
	if(Token::check(Input::get('token'))) {
		$validate = new Validate();
		$salt = Hash::salt(32);
		
		//validate input
		$validation = $validate->check($_POST, array(
			'Email'=> array(
				'required' => true,
				'min' =>2,
				'unique'=>'user'
			),
			'Password'=> array(
				'min' =>6,
				'max' =>16
			),
			'Repeat-password'=> array(
				'min' =>6,
				'max' =>16,
				'matches' => 'Password'
			),
			'fName'=> array(
				'regex' => true
			),
			'lName'=> array(
				'regex' => true
			)
		));
	
		//update user
		if(Input::get('view') && $validate->passed()) {
			$account = array(
					'fName'=>Input::get('fName'),
					'lName'=>Input::get('lName'),
					'nationality'=>Input::get('nationality'),
					'email'=>Input::get('Email'),
					'password'=>Hash::make(Input::get('Password'),$salt),
					'salt'=>$salt
				);
				
				if(strlen(Input::get('intake'))>0) {
					$account['intakeID'] = Input::get('intake');
				}
			
			try {
				$user->update(Input::get('view') ,$account);
			}
			catch(Exception $e) {
				die($e->getMessage());
			}
			
			//force refresh
			header('Location: /BC_Webportal/' . Input::get('page'));
		}
		
		//create user
		else if ($validate->passed()) {
			$account = array(
					'role'=>Input::get('role'),
					'fName'=>Input::get('fName'),
					'lName'=>Input::get('lName'),
					'gender'=>Input::get('gender'),
					'nationality'=>Input::get('nationality'),
					'email'=>Input::get('Email'),
					'password'=>Hash::make(Input::get('Password'),$salt),
					'salt'=>$salt
				);
				
				if(strlen(Input::get('intake'))>0) {
					$account['intakeID'] = Input::get('intake');
				}
			
			try {
				$user->create($account);
			}
			catch(Exception $e) {
				die($e->getMessage());
			}
			
			//force refresh
			header('Location: /BC_Webportal/' . Input::get('page'));
		}
		
		//print error messages
		else {
			foreach ($validate->errors() as $error) {
				$_SESSION["errors"][] =array('message' => $error, 'type' => 'warning');
			}
		}

	}
}

$update = false;

//view flag: view user
if(Input::get('view')) {
	//get user based on ID
	if (!$update = $user->find(Input::get('view'))) {
		$_SESSION["errors"][] =array('message' => 'Unable to fetch users', 'type' => 'danger');
	}
}
?>


<!-- Wrapper start -->
<div id="wrapper">
	
	<!-- Content start -->
	<div class="container">	
	
		<?php echo "<h1>" . $page->data()->title . "</h1>"; ?>
		
			<!-- User container start -->
			<div class="col-sm-4">
				<div class="panel panel-default">
					<ul class="list-group" style="height:495px;overflow-y:auto;width:100%;">
						
						
						<?php foreach($allUsers as $indUser): ?>
						
							<li class="list-group-item" href="#">
								<h4 class="list-group-item-heading"><a href="<?php echo Input::get('page'); ?>/view/<?php echo $indUser['ID']; ?>"><?php echo $indUser['fName'] . ' ' . $indUser['lName']; ?></a>
								
								<?php if($_SESSION['ID'] != $indUser['ID']): ?>
								
									<a class="close" href="<?php echo Input::get('page'); ?>/del/<?php echo $indUser['ID']; ?>" aria-label="Close">
										<span>&times;</span>
									</a>
								</h4>
								
								<?php endif; ?>
								
								<p class="list-group-item-text" style="font-size: 1.6rem;"><?php echo $indUser['role']; ?></p>
							</li>
							
						<?php endforeach; ?>
						
					</ul>
					<div class="panel-footer">
						<a class="btn btn-default" href="<?php echo Input::get('page'); ?>">
							<h4 class="list-group-item-heading"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> New user</h4>
						</a>
					</div>
				</div>
			</div>
			<!-- User container end -->
			

			
			<!-- User content start -->
			<div class="col-sm-8">
			<div class="panel panel-default">
				<form action="" method="post">
				<div class="panel-heading">
					<div class="form-group">
						<label for="Email1">Email address</label>
						<input type="email" class="form-control" name="Email" id="Email" placeholder="Enter email" value="<?php if($update) echo $user->data()->email; ?>" required>
					</div>
					
					<div class="form-group">
						<label for="fName">First name</label>
						<input type="text" class="form-control" name="fName" id="fName" placeholder="Enter first name" value="<?php if($update) echo $user->data()->fName; ?>" required>
					</div>
					
					<div class="form-group">
						<label for="lName">Last name</label>
						<input type="text" class="form-control" name="lName" id="lName" placeholder="Enter last name" value="<?php if($update) echo $user->data()->lName; ?>" required>
					</div>
					
					<div class="form-group">
						<div style="float: left">
							<label for="gender">Gender</label>
							<label class="radio-inline">
								<input type="radio" name="gender" id="male" value="Male" <?php if($update && $user->data()->gender == 'Male') echo 'checked="checked"'; ?> required <?php if($update) echo 'disabled'; ?>> Male
							</label>
							<label class="radio-inline">
								<input type="radio" name="gender" id="female" value="Female" <?php if($update && $user->data()->gender == 'Female') echo 'checked="checked"'; ?> <?php if($update) echo 'disabled'; ?>> Female
							</label>
						</div>
						
						<div style="float: right">
							<label for="role">Role</label>
							<label class="radio-inline">
								<input type="radio" name="role" id="admin" value="Admin" <?php if($update && $user->data()->role == 'Admin') echo 'checked="checked"'; ?> required <?php if($update) echo 'disabled'; ?>> Admin
							</label>
							<label class="radio-inline">
								<input type="radio" name="role" id="lecturer" value="Lecturer" <?php if($update && $user->data()->role == 'Lecturer') echo 'checked="checked"'; ?> <?php if($update) echo 'disabled'; ?>> Lecturer
							</label>
							<label class="radio-inline">
								<input type="radio" name="role" id="student" value="Student" <?php if($update && $user->data()->role == 'Student') echo 'checked="checked"'; ?> <?php if($update) echo 'disabled'; ?>> Student
							</label>
						</div>					
					</div>
					
					<div class="form-group" style="clear: both;">
						<div style="float: left">
							<label for="intake">Intake</label>
							<select class="selectpicker form-control" name="intake" title='Select intake' id="intake" data-size="5" data-live-search="true" <?php if($update && $user->data()->role != 'Student') echo 'disabled'; ?>>
								<option value="" data-subtext="Non-student">N/A</option>
								
								<?php foreach($allIntake as $indIntake): ?>
							
								<option value="<?php echo $indIntake['ID']; ?>" data-subtext="<?php echo $indIntake['faculty']; ?>"><?php echo $indIntake['ID']; ?></option>

								<?php endforeach; ?>
								
							</select>
						</div>
						
						<div style="float: right">
							<label for="nationality">Nationality</label>
							<select class="selectpicker form-control" name="nationality" title='Select nationality' id="nationality">
								<option value="Malaysian">Malaysian</option>
								<option value="Other Asian">Other Asian</option>
								<option value="American">American</option>
								<option value="European">European</option>
								<option value="Oceanian">Oceanian</option>
							</select>
						</div>
					</div>
					
					<div class="form-group" style="clear: both;">
						<label for="Password">Password</label>
						<input type="password" class="form-control" name="Password" id="Password" placeholder="Password" required>
					</div>
					
					<div class="form-group">
						<label for="Repeat-password">Repeat password</label>
						<input type="password" class="form-control" name="Repeat-password" id="Repeat-password" placeholder="Repeat your password" required>
					</div>
				  </div>
				  
				  <!-- !IMPORTANT: Token to prevent CSRF -->
				  <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
				  <!-- !IMPORTANT: Token to prevent CSRF -->
				  
				  <div class="panel-footer">
					<button type="submit" id="post" class="btn btn-primary pull-right" onclick="validate()"><?php echo (Input::get('view') and $update) ? 'Update' : 'Add'; ?></button>
					<div class="clearfix"></div>
				  </div>
				  
				</form>
			</div>
			<!-- User content end -->
			
		</div>
	</div>
	</div>
	<!-- Content end -->
</div>
<!-- Wrapper end -->

<!-- Include javascripts -->
<?php require_once('/modal/config/js.php'); ?>
<!-- Bootstrap-select -->
<script type="text/javascript" src="javascripts/bootstrap-select.min.js"></script>

<script>
function validate(){
		if (document.getElementById("admin").checked || document.getElementById("lecturer").checked){
			document.getElementById("intake").value = "";
		}
	}
	$('.selectpicker').selectpicker();
	
	<?php
	if($update) {
		echo "$('#intake').selectpicker('val', '" . $user->data()->intakeID . "');";
		echo "$('#nationality').selectpicker('val', '" . $user->data()->nationality . "');";
	}
	?>
</script>