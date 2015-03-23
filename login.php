<?php
	// Check if install.php is present
	if(is_dir('install')) {
		header("Location: install/install.php");
	} else {
		// Access DB Info
		include('config.php');

		// Get Settings Data
		include ('includes/settings.php');
		$set = mysqli_fetch_assoc($setRes);

		// Set Localization
		$local = $set['localization'];
		switch ($local) {
			case 'en-gb':
				include ('language/en-gb.php');
				break;
			case 'es':
				include ('language/es.php');
				break;
			case 'fr':
				include ('language/fr.php');
				break;
		}

		// Include Functions
		include('includes/functions.php');

		$msgBox = '';
		$isReset = '';

		// Tenant Log In Form
		if (isset($_POST['submit']) && $_POST['submit'] == 'signIn') {
			if($_POST['tenantEmail'] == '') {
				$msgBox = alertBox($emailAddressMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['password'] == '') {
				$msgBox = alertBox($emptyPasswordMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else {
				// Check if the Tenant account has been activated
				$tenantEmail = (isset($_POST['tenantEmail'])) ? htmlentities($_POST['tenantEmail']) : '';
				$check = $mysqli->query("SELECT isActive FROM tenants WHERE tenantEmail = '".$tenantEmail."'");
				$row = mysqli_fetch_assoc($check);

				// If the account is active - allow the login
				if ($row['isActive'] == '1') {
					if($stmt = $mysqli -> prepare("
											SELECT
												tenantId,
												propertyId,
												leaseId,
												tenantEmail,
												tenantFirstName,
												tenantLastName
											FROM
												tenants
											WHERE
												tenantEmail = ? AND
												password = ?
					"))	{
						$stmt -> bind_param("ss",
									$_POST['tenantEmail'],
									md5($_POST['password'])
						);
						$stmt -> execute();
						$stmt -> bind_result(
									$tenantId,
									$propertyId,
									$leaseId,
									$tenantEmail,
									$tenantFirstName,
									$tenantLastName
						);
						$stmt -> fetch();
						$stmt -> close();

						if (!empty($tenantId)) {
							session_start();
							$_SESSION["tenantId"] = $tenantId;
							$_SESSION["propertyId"] = $propertyId;
							$_SESSION["leaseId"] = $leaseId;
							$_SESSION["tenantEmail"] = $tenantEmail;
							$_SESSION["tenantFirstName"] = $tenantFirstName;
							$_SESSION["tenantLastName"] = $tenantLastName;
							header('Location: index.php');
						} else {
							$msgBox = alertBox($loginFailedMsg, "<i class='fa fa-times-circle'></i>", "danger");
						}
					}
				} else if ($row['isActive'] == '0') {
					// If the account is not active, show a message
					$msgBox = alertBox($inactiveAccountMsg, "<i class='fa fa-warning'></i>", "warning");
				} else {
					// No account found
					$msgBox = alertBox($loginFailedMsg, "<i class='fa fa-times-circle'></i>", "danger");
				}
			}
		}

		// Create a New Account Form
		if (isset($_POST['submit']) && $_POST['submit'] == 'createAccount') {
			// User Validations
			if ($_POST['tenantFirstName'] == '') {
				$msgBox = alertBox($firstNameMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else if ($_POST['tenantLastName'] == '') {
				$msgBox = alertBox($lastNameMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else if ($_POST['newEmail'] == '') {
				$msgBox = alertBox($validEmailMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['password1'] == '') {
				$msgBox = alertBox($newPasswordHelper, "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['password1'] != $_POST['password2']) {
				$msgBox = alertBox($passwordMismatchMsg, "<i class='fa fa-times-circle'></i>", "danger");
			// Black Hole Trap to help reduce bot registrations
			} else if($_POST['isEmpty'] != '') {
				$msgBox = alertBox($newAccountErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else {
				// Set some variables
				$dupEmail = '';
				$newEmail = $mysqli->real_escape_string($_POST['newEmail']);
				$tenantFirstName = $mysqli->real_escape_string($_POST['tenantFirstName']);
				$tenantLastName = $mysqli->real_escape_string($_POST['tenantLastName']);

				// Check for Duplicate email
				$check = $mysqli->query("SELECT 'X' FROM tenants WHERE tenantEmail = '".$newEmail."'");
				if ($check->num_rows) {
					$dupEmail = 'true';
				}

				// If duplicates are found
				if ($dupEmail != '') {
					$msgBox = alertBox($dupEmailMsg, "<i class='fa fa-times-circle'></i>", "danger");
				} else {
					// Create the new account
					$hash = md5(rand(0,1000));
					$isActive = '0';
					$today = date("Y-m-d");
					$password = md5($_POST['password1']);

					if (!($stmt = $mysqli->prepare("
										INSERT INTO
											tenants(
												tenantEmail,
												password,
												tenantFirstName,
												tenantLastName,
												createDate,
												hash,
												isActive
											) VALUES (
												?,
												?,
												?,
												?,
												?,
												?,
												?
											)")))  {
					echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
					}							
					if (!$stmt->bind_param('sssssss',
						$newEmail,
						$password,
						$tenantFirstName,
						$tenantLastName,
						$today,
						$hash,
						$isActive
					)) {
					echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
					}

					if (!$stmt->execute()) {
					echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
					}

					// Send out the email in HTML
					$installUrl = $set['installUrl'];
					$siteName = $set['siteName'];
					$businessEmail = $set['businessEmail'];
					$newPass = $mysqli->real_escape_string($_POST['password1']);

					$subject = 'Your '.$siteName.' Account has been created';

					$message = '<html><body>';
					$message .= '<h3>'.$subject.'</h3>';
					$message .= '<p>Your new Account details:</p>';
					$message .= '<hr>';
					$message .= '<p>Username: Your email address<br />Password: '.$newPass.'</p>';
					$message .= '<p>You must activate your account before you will be able to log in.<br /> Please <a href="'.$installUrl.'activate.php?tenantEmail='.$newEmail.'&hash='.$hash.'"> CLICK HERE </a> to activate your account.</p>';
					$message .= '<hr>';
					$message .= '<p>Once you have activated your new account and logged in, please take the time to update your account profile details.</p>';
					$message .= '<p>You can log in to your account <a href="'.$installUrl.'">HERE</a></p>';
					$message .= '<p>Thank you,<br>'.$siteName.'</p>';
					$message .= '</body></html>';

					$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
					$headers .= "Reply-To: ".$businessEmail."\r\n";
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

					if (mail($newEmail, $subject, $message, $headers)) {
						$msgBox = alertBox("Your New Account has been created and an email has been sent.", "<i class='fa fa-check-square-o'></i>", "success");
						// Clear the Form of values
						$_POST['tenantFirstName'] = $_POST['tenantLastName'] = $_POST['tenantEmail'] = '';
					}
					$stmt->close();
				}
			}
		}

		// Reset Account Password Form
		if (isset($_POST['submit']) && $_POST['submit'] == 'resetPass') {
			// Set the email address
			$theEmail = (isset($_POST['theEmail'])) ? $mysqli->real_escape_string($_POST['theEmail']) : '';

			// Validation
			if ($_POST['theEmail'] == "") {
				$msgBox = alertBox($emailAddressMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else {
				$query = "SELECT tenantEmail FROM tenants WHERE tenantEmail = ?";
				$stmt = $mysqli->prepare($query);
				$stmt->bind_param("s",$theEmail);
				$stmt->execute();
				$stmt->bind_result($tenantEmail);
				$stmt->store_result();
				$numrows = $stmt->num_rows();

				if ($numrows == 1){
					// Generate a RANDOM MD5 Hash for a password
					$randomPassword=md5(uniqid(rand()));

					// Take the first 8 digits and use them as the password we intend to email the user
					$emailPassword=substr($randomPassword, 0, 8);

					// Encrypt $emailPassword in MD5 format for the database
					$newpassword = md5($emailPassword);

					//update password in db
					$updatesql = "UPDATE tenants SET password = ? WHERE tenantEmail = ?";
					$update = $mysqli->prepare($updatesql);
					$update->bind_param("ss",
											$newpassword,
											$theEmail
										);
					$update->execute();

					// Send out the email in HTML
					$installUrl = $set['installUrl'];
					$siteName = $set['siteName'];
					$businessEmail = $set['businessEmail'];

					$subject = 'Your '.$siteName.' Password has been Reset';

					$message = '<html><body>';
					$message .= '<h3>'.$subject.'</h3>';
					$message .= '<p>Your temporary password is:</p>';
					$message .= '<hr>';
					$message .= '<p>'.$emailPassword.'</p>';
					$message .= '<hr>';
					$message .= '<p>Please take the time to change your password to something you can easily remember. <br />You can change your password on your My Profile page after logging into your account. <br />There you can update your password, as well as your account details.</p>';
					$message .= '<p>You can log into your account with your email address and new password <a href="'.$installUrl.'">HERE</a></p>';
					$message .= '<p>Thank you,<br />'.$siteName.'</p>';
					$message .= '</body></html>';

					$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
					$headers .= "Reply-To: ".$businessEmail."\r\n";
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

					if (mail($theEmail, $subject, $message, $headers)) {
						$msgBox = alertBox($passwordResetMsg, "<i class='fa fa-check-square-o'></i>", "success");
						$isReset = 'true';
						$stmt->close();
					}
				} else {
					// No account found
					$msgBox = alertBox($accountNotFoundMsg, "<i class='fa fa-warning'></i>", "warning");
				}
			}
		}
	?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php echo $set['siteName'].' &mdash; '.$tenantLogin; ?></title>

		<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>

		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/custom.css" rel="stylesheet">
		<link href="css/reside.css" rel="stylesheet">
		<link href="css/font-awesome.min.css" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="js/html5shiv.js"></script>
			<script src="js/respond.js"></script>
		<![endif]-->
	</head>

	<body>
	<center><img border="0" src="images/bg3.jpg" alt="Manchester"></center><br /><br />
		<div class="container">
			<div class="row">
				<div class="col-md-8">
					<div class="logo">
						<a href=""><img alt="<?php echo $set['siteName']; ?>" src="images/logo.png" /></a>
					</div>
				</div>
				<div class="col-md-4 userInfo">
					<p class="textRight"><?php echo $todayMsg.' '.date('l'). " the " .date('jS \of F, Y'); ?></p>
				</div>
			</div>

			<div class="navbar navbar-default">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>

				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">
						<li><a data-toggle="modal" href="#newAccount"><i class="fa fa-key"></i> <?php echo $newAccountNav; ?></a></li>
						<li><a data-toggle="modal" href="#resetPassword"><i class="fa fa-unlock"></i> <?php echo $resetPasswordNav; ?></a></li>
					</ul>
				</div>
			</div>

			<div class="content">

				<h3><?php echo $loginWelcomeMsg; ?></h3>
				<p class="lead"><?php echo $loginInstructions; ?></p>

				<?php if ($msgBox) { echo $msgBox; } ?>

				<form action="" method="post" class="padTop">
					<div class="form-group">
						<label for="tenantEmail"><?php echo $emailAddressField; ?></label>
						<input type="email" class="form-control" name="tenantEmail" id="tenantEmail">
					</div>
					<div class="form-group">
						<label for="password"><?php echo $passwordField; ?></label>
						<input type="password" class="form-control" name="password" id="password">
					</div>
					<button type="input" name="submit" value="signIn" class="btn btn-success btn-icon"><i class="fa fa-sign-in"></i> <?php echo $signInBtn; ?></button>
				</form>

				<!-- REGISTER NEW ACCOUNT MODAL -->
				<div class="modal fade" id="newAccount" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header modal-info">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
								<h4 class="modal-title"><?php echo $createNewAccountMisc; ?></h4>
							</div>
							<form action="" method="post">
								<div class="modal-body">
									<div class="form-group">
										<label for="tenantFirstName"><?php echo $firstNameField; ?></label>
										<input type="text" class="form-control" name="tenantFirstName" id="tenantFirstName" value="<?php echo isset($_POST['tenantFirstName']) ? $_POST['tenantFirstName'] : ''; ?>">
									</div>
									<div class="form-group">
										<label for="tenantLastName"><?php echo $lastNameField; ?></label>
										<input type="text" class="form-control" name="tenantLastName" id="tenantLastName" value="<?php echo isset($_POST['tenantLastName']) ? $_POST['tenantLastName'] : ''; ?>">
									</div>
									<div class="form-group">
										<label for="newEmail"><?php echo $emailAddressField; ?></label>
										<input type="email" class="form-control" name="newEmail" id="newEmail" value="<?php echo isset($_POST['newEmail']) ? $_POST['newEmail'] : ''; ?>">
										<span class="help-block"><?php echo $validEmailHelper; ?></span>
									</div>
									<div class="form-group">
										<label for="password1"><?php echo $passwordField; ?></label>
										<input type="text" class="form-control" name="password1" id="password1">
										<span class="help-block"><?php echo $newPasswordHelper; ?></span>
									</div>
									<div class="form-group">
										<label for="password2"><?php echo $rpasswordField; ?></label>
										<input type="text" class="form-control" name="password2" id="password2">
										<span class="help-block"><?php echo $rnewPasswordHelper; ?></span>
									</div>
								</div>
								<div class="modal-footer">
									<input name="isEmpty" id="isEmpty" value="" type="hidden">
									<button type="input" name="submit" value="createAccount" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $createAccountBtn; ?></button>
									<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
								</div>
							</form>
						</div>
					</div>
				</div>

				<!-- RESET PASSWORD MODAL -->
				<div class="modal fade" id="resetPassword" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header modal-primary">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
								<h4 class="modal-title"><?php echo $resetPasswordMisc; ?></h4>
							</div>
							<?php if ($isReset == '') { ?>
								<form action="" method="post">
									<div class="modal-body">
										<div class="form-group">
											<label for="theEmail"><?php echo $emailAddressField; ?></label>
											<input type="email" class="form-control" name="theEmail" id="theEmail" value="">
											<span class="help-block"><?php echo $resetPassEmailField; ?></span>
										</div>
									</div>
									<div class="modal-footer">
										<button type="input" name="submit" value="resetPass" class="btn btn-success btn-icon"><i class="fa fa-unlock"></i> <?php echo $resetPasswordBtn; ?></button>
										<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
									</div>
								</form>
							<?php } else { ?>
								<div class="modal-body">
									<p class="lead"><?php echo $passwordResetMisc; ?></p>
									<p><?php echo $passwordResetInstMisc; ?></p>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>

			</div>

			<div class="footer">
				<p class="textCenter">
					&copy; <?php echo date('Y'); ?> <a href="http://rentmediterraneanapartments.com" target="_blank">My-Home Property Management</a>
					<span><i class="fa fa-plus"></i></span>
					Provided by <a href="http://bitcoin.bigmoney.biz" target="_blank">Mars Systems International</a>
				</p>
			</div>

		</div>

		<script src="js/jquery.js"></script>
		<script src="js/bootstrap.js"></script>
		<script src="js/reside.js"></script>

	</body>
	</html>
<?php } ?>