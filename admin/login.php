<?php
	// Check if install.php is present
	if(is_dir('../install')) {
		header("Location: ../install/install.php");
	} else {
		// Access DB Info
		include('../config.php');

		// Get Settings Data
		include ('../includes/settings.php');
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
		include('../includes/functions.php');

		$msgBox = '';
		$isReset = '';

		// Admin Log In Form
		if (isset($_POST['submit']) && $_POST['submit'] == 'signIn') {
			if($_POST['adminEmail'] == '') {
				$msgBox = alertBox($emailAddressMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['password'] == '') {
				$msgBox = alertBox($emptyPasswordMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else {
				// Check if the Admin account has been activated
				$adminEmail = (isset($_POST['adminEmail'])) ? htmlentities($_POST['adminEmail']) : '';
				$check = $mysqli->query("SELECT isActive FROM admins WHERE adminEmail = '".$adminEmail."'");
				$row = mysqli_fetch_assoc($check);

				// If the account is active - allow the login
				if ($row['isActive'] == '1') {
					if($stmt = $mysqli -> prepare("
											SELECT
												adminId,
												superuser,
												adminRole,
												adminEmail,
												adminFirstName,
												adminLastName
											FROM
												admins
											WHERE
												adminEmail = ? AND
												password = ?
					"))	{
						$stmt -> bind_param("ss",
									$_POST['adminEmail'],
									md5($_POST['password'])
						);
						$stmt -> execute();
						$stmt -> bind_result(
									$adminId,
									$superuser,
									$adminRole,
									$adminEmail,
									$adminFirstName,
									$adminLastName
						);
						$stmt -> fetch();
						$stmt -> close();

						if (!empty($adminId)) {
							session_start();
							$_SESSION["adminId"] = $adminId;
							$_SESSION["superuser"] = $superuser;
							$_SESSION["adminRole"] = $adminRole;
							$_SESSION["adminEmail"] = $adminEmail;
							$_SESSION["adminFirstName"] = $adminFirstName;
							$_SESSION["adminLastName"] = $adminLastName;
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

		// Reset Account Password Form
		if (isset($_POST['submit']) && $_POST['submit'] == 'resetPass') {
			// Set the email address
			$theEmail = (isset($_POST['theEmail'])) ? $mysqli->real_escape_string($_POST['theEmail']) : '';

			// Validation
			if ($_POST['theEmail'] == "") {
				$msgBox = alertBox($emailAddressMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else {
				$query = "SELECT adminEmail FROM admins WHERE adminEmail = ?";
				$stmt = $mysqli->prepare($query);
				$stmt->bind_param("s",$theEmail);
				$stmt->execute();
				$stmt->bind_result($tenantEmail);
				$stmt->store_result();
				$numrows = $stmt->num_rows();

				if ($numrows == 1){
					// Generate a RANDOM MD5 Hash for a password
					$randomPassword=md5(uniqid(rand()));

					// Take the first 8 digits and use them as the password we intend to email the Admin
					$emailPassword=substr($randomPassword, 0, 8);

					// Encrypt $emailPassword in MD5 format for the database
					$newpassword = md5($emailPassword);

					//update password in db
					$updatesql = "UPDATE admins SET password = ? WHERE adminEmail = ?";
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

					$subject = 'Your '.$siteName.' Admin Password has been Reset';

					$message = '<html><body>';
					$message .= '<h3>'.$subject.'</h3>';
					$message .= '<p>Your temporary password is:</p>';
					$message .= '<hr>';
					$message .= '<p>'.$emailPassword.'</p>';
					$message .= '<hr>';
					$message .= '<p>Please take the time to change your password to something you can easily remember. <br />You can change your password on your My Profile page after logging into your Admin/Landlord account. <br />There you can update your password, as well as your account details.</p>';
					$message .= '<p>You can log into your account with your email address and new password <a href="http://'.$installUrl.'admin/">HERE</a></p>';
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
		<title><?php echo $set['siteName'].' &mdash; '.$adminLogin; ?></title>

		<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>

		<link href="../css/bootstrap.css" rel="stylesheet">
		<link href="../css/custom.css" rel="stylesheet">
		<link href="../css/reside.css" rel="stylesheet">
		<link href="../css/font-awesome.min.css" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="../js/html5shiv.js"></script>
			<script src="../js/respond.js"></script>
		<![endif]-->
	</head>

	<body>
		<div class="container">
			<div class="row">
				<div class="col-md-8">
					<div class="logo">
						<a href=""><img alt="<?php echo $set['siteName']; ?>" src="../images/logo.png" /></a>
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
						<li><a data-toggle="modal" href="#resetPassword"><i class="fa fa-unlock"></i> <?php echo $resetPassword; ?></a></li>
					</ul>
				</div>
			</div>

			<div class="content">

				<h3><?php echo $loginWelcomeMsg; ?></h3>
				<p class="lead"><?php echo $loginInstructions; ?></p>

				<?php if ($msgBox) { echo $msgBox; } ?>

				<form action="" method="post" class="padTop">
					<div class="form-group">
						<label for="adminEmail"><?php echo $emailAddressField; ?></label>
						<input type="email" class="form-control" name="adminEmail" id="adminEmail">
					</div>
					<div class="form-group">
						<label for="password"><?php echo $passwordField; ?></label>
						<input type="password" class="form-control" name="password" id="password">
					</div>
					<button type="input" name="submit" value="signIn" class="btn btn-success btn-icon"><i class="fa fa-sign-in"></i> <?php echo $signInBtn; ?></button>
				</form>

				<!-- RESET PASSWORD MODAL -->
				<div class="modal fade" id="resetPassword" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header modal-primary">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
								<h4 class="modal-title"><?php echo $resetPassword; ?></h4>
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

		<script src="../js/jquery.js"></script>
		<script src="../js/bootstrap.js"></script>
		<script src="../js/reside.js"></script>

	</body>
	</html>
<?php } ?>