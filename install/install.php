<?php
    /*
     * Function to show an Alert type Message Box
     *
     * @param string $message   The Alert Message
     * @param string $icon      The Font Awesome Icon
     * @param string $type      The CSS style to apply
     * @return string           The Alert Box
     */
    function alertBox($message, $icon = "", $type = "") {
        return "<div class=\"alertMsg $type\"><span>$icon</span> $message <a class=\"alert-close\" href=\"#\">x</a></div>";
    }

	$step = '1';
	$file = false;
	$msgBox = '';

	// Get the install URL
	$siteURL = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$installURL = str_replace("install/install.php", "", $siteURL);

    if(isset($_POST['submit']) && $_POST['submit'] == 'On to Step 2') {
        // Validation
        if($_POST['dbhost'] == '') {
			$msgBox = alertBox("Please enter in your Host name. This is usually 'localhost'.", "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['dbuser'] == '') {
			$msgBox = alertBox("Please enter the username for the database.", "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['dbpass'] == '') {
			$msgBox = alertBox("Please enter your database password.", "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['dbname'] == '') {
			$msgBox = alertBox("Please enter the database name.", "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$dbhost = htmlspecialchars($_POST['dbhost'], ENT_COMPAT,'ISO-8859-1', true);
			$dbuser = htmlspecialchars($_POST['dbuser'], ENT_COMPAT,'ISO-8859-1', true);
			$dbpass = htmlspecialchars($_POST['dbpass'], ENT_COMPAT,'ISO-8859-1', true);
			$dbname = htmlspecialchars($_POST['dbname'], ENT_COMPAT,'ISO-8859-1', true);

            $str ="<?php
error_reporting(0);

$"."dbhost = '".$dbhost."';
$"."dbuser = '".$dbuser."';
$"."dbpass = '".$dbpass."';
$"."dbname = '".$dbname."';

".file_get_contents('config.txt')."
?>";
            if (!file_put_contents('../config.php', $str)) {
                $no_perm = true;
            }
        }
    }

    if (is_file('../config.php')) {
		include ('../config.php');

        if (!$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname)) {
            $step = '1';
            $file = true;
        } else {
			if (mysqli_connect_errno()) {
                $step = '1';
            } else {
				$sql = file_get_contents('install.sql');
				if (!$sql){
					die ('Error opening file');
				}
				mysqli_multi_query($mysqli, $sql);
				$step = '2';
			}
		}

		if(isset($_POST['submit']) && $_POST['submit'] == 'Complete Install') {
			include ('../config.php');

			// Settings Validations
			if($_POST['installUrl'] == "") {
				$msgBox = alertBox("Please enter your Installation URL (include the trailing slash).", "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['siteName'] == "") {
				$msgBox = alertBox("Please enter a Site Name.", "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['businessName'] == "") {
				$msgBox = alertBox("Please enter the the name of your Business.", "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['businessAddress'] == "") {
				$msgBox = alertBox("Please enter the your Address or the Address of your Business.", "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['businessEmail'] == "") {
				$msgBox = alertBox("Please enter your Business Email.", "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['businessPhone'] == "") {
				$msgBox = alertBox("Please enter your Business Phone.", "<i class='fa fa-times-circle'></i>", "danger");
			}  else if($_POST['fileTypesAllowed'] == "") {
				$msgBox = alertBox("Please enter the File Type Extensions allowed to be uploaded.", "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['avatarTypes'] == "") {
				$msgBox = alertBox("Please enter the Avatar File Type Extensions allowed to be uploaded.", "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['propertyPicTypes'] == "") {
				$msgBox = alertBox("Please enter the Property Picture File Type Extensions allowed to be uploaded.", "<i class='fa fa-times-circle'></i>", "danger");
			}
			// Admin Account Validations
			else if($_POST['adminEmail'] == '') {
				$msgBox = alertBox("Please enter a valid email for the Primary Admin. Email addresses are used as your account login.", "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['password'] == '') {
				$msgBox = alertBox("Please enter a password for the Primary Admin's Account.", "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['r-password'] == '') {
				$msgBox = alertBox("Please re-enter the password for the Primary Admin's Account.", "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['password'] != $_POST['r-password']) {
				$msgBox = alertBox("The password for the Primary Admin's Account does not match.", "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['adminFirstName'] == '') {
				$msgBox = alertBox("Please enter the Primary Admin's First Name.", "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['adminLastName'] == '') {
				$msgBox = alertBox("Please enter the Primary Admin's Last Name.", "<i class='fa fa-times-circle'></i>", "danger");
			} else {
				$installUrl = $mysqli->real_escape_string($_POST['installUrl']);
				$siteName = $mysqli->real_escape_string($_POST['siteName']);
				$businessName = $mysqli->real_escape_string($_POST['businessName']);
				$businessAddress = htmlentities($_POST['businessAddress']);
				$businessEmail = $mysqli->real_escape_string($_POST['businessEmail']);
				$businessPhone = $mysqli->real_escape_string($_POST['businessPhone']);
				$fileTypesAllowed = $mysqli->real_escape_string($_POST['fileTypesAllowed']);
				$avatarTypes = $mysqli->real_escape_string($_POST['avatarTypes']);
				$propertyPicTypes = $mysqli->real_escape_string($_POST['propertyPicTypes']);
				$adminEmail = $mysqli->real_escape_string($_POST['adminEmail']);
				$password = md5($_POST['password']);
				$adminFirstName = $mysqli->real_escape_string($_POST['adminFirstName']);
				$adminLastName = $mysqli->real_escape_string($_POST['adminLastName']);
				$today = date("Y-m-d H:i:s");
				
				// Add data to the siteSettings Table
				$stmt = $mysqli->prepare("
									INSERT INTO
										sitesettings(
											installUrl,
											siteName,
											businessName,
											businessAddress,
											businessEmail,
											businessPhone,
											fileTypesAllowed,
											avatarTypes,
											propertyPicTypes
										) VALUES (
											?,
											?,
											?,
											?,
											?,
											?,
											?,
											?,
											?
										)");
				$stmt->bind_param('sssssssss',
					$installUrl,
					$siteName,
					$businessName,
					$businessAddress,
					$businessEmail,
					$businessPhone,
					$fileTypesAllowed,
					$avatarTypes,
					$propertyPicTypes
				);
				$stmt->execute();
				$stmt->close();

				// Add the new Admin Account
				$stmt = $mysqli->prepare("
									INSERT INTO
										admins(
											superuser,
											adminRole,
											adminEmail,
											password,
                                            adminFirstName,
											adminLastName,
											createDate
										) VALUES (
											1,
											0,
											?,
											?,
											?,
											?,
											?
										)");
				$stmt->bind_param('sssss',
									$adminEmail,
									$password,
									$adminFirstName,
									$adminLastName,
									$today
				);
				$stmt->execute();
				$stmt->close();

                if (is_file('../config.php')) {
					include ('../config.php');

                    // Get Settings Data
                    $settingsql  = "SELECT installUrl, siteName, businessEmail FROM sitesettings";
                    $settingres = mysqli_query($mysqli, $settingsql) or die('Error, retrieving Settings failed. ' . mysqli_error());
                    $set = mysqli_fetch_assoc($settingres);

                    // Get Admin Data
                    $adminsql  = "SELECT adminEmail, adminFirstName, adminLastName FROM admins";
                    $adminres = mysqli_query($mysqli, $adminsql) or die('Error, retrieving Admin failed. ' . mysqli_error());
                    $admin = mysqli_fetch_assoc($adminres);

                    //Email out a confirmation
                    $siteName = $set['siteName'];
                    $businessEmail = $set['businessEmail'];
                    $installUrl = $set['installUrl'];
                    $adminEmail = $admin['adminEmail'];

                    $bodyText = "Congratulations, Reside V2 has been successfully installed.

Your Admin Account details:
-------------------------------------
Login: ".$adminEmail."
Password: The password you set up during Installation


For security reasons and to stop any possible re-installations please,
DELETE or RENAME the \"install\" folder, otherwise you will not be able
to log in as Administrator.

You can log in to your Admin account at ".$installUrl."admin
after the install folder has been taken care of.

If you lose or forget your password, you can use the \"Reset Account Password\"
link located at ".$installUrl."admin/login.php

Tenants can also have their password reset by using the \"Reset Password\"
link located at ".$installUrl."login.php

Thank you,
".$siteName."

This email was automatically generated.";

                    $subject = 'Reside Installation Successful';
                    $emailBody = $bodyText;

                    $mail = mail($adminEmail, $subject, $emailBody,
                    "From: ".$siteName." <".$businessEmail.">\r\n"
                    ."Reply-To: ".$businessEmail."\r\n"
                    ."X-Mailer: PHP/" . phpversion());
                }

				$step = '3';
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
	<title>My-Home Software Installation</title>

	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>

	<link href="../css/bootstrap.css" rel="stylesheet">
	<link href="../css/custom.css" rel="stylesheet">
	<link href="../css/extra.css" rel="stylesheet">
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
						<a href=""><img alt="" src="../images/logo.png" /></a>
					</div>
				</div>
				<div class="col-md-4 userInfo">
					<p class="textRight"><?php echo $todayMsg.' '.date('l'). " the " .date('jS \of F, Y'); ?></p>
				</div>
			</div>

			<div class="content">
				<?php if ($step == '1') { ?>

				<h3 class="primary">My-Home Setup &amp; Installation</h3>
				<p class="lead">Installing My-Home is easy. Three steps and less then 5 minutes. Ready?</p>

				<?php if ($msgBox) { echo $msgBox; } ?>

				<h3 class="info padTop">Step 1 <i class="icon-long-arrow-right"></i> Configure Database</h3>
				<p class="lead">Please type in your database information.</p>

				<?php if (isset($no_perm)) { ?>

				<script type="text/javascript">
					function select_all(obj) {
						var text_val = eval(obj);
						text_val.focus();
						text_val.select();
					}
				</script>
				<p class="lead">
					You haven't the permissions to create a new file. Please manually create a file named <strong>config.php</strong> in the root
					directory and copy the text from the box below.<br />
					Once it's created, <a href="install.php">refresh this page</a>.
				</p>
				<textarea name="configStr" id="configStr" onClick="select_all(this);" cols="58" rows="6"><?php echo $str; ?></textarea>

				<?php } elseif (!$file) { ?>
					<form action="" method="post" class="padTop">
						<div class="form-group">
							<label for="dbhost">Host Name</label>
							<input type="text" class="form-control" name="dbhost" value="localhost">
							<span class="help-block">Usually 'localhost'. Check with your Host Provider.</span>
						</div>
						<div class="form-group">
							<label for="dbuser">Database Username</label>
							<input type="text" class="form-control" name="dbuser" value="<?php echo isset($_POST['dbuser']) ? $_POST['dbuser'] : '' ?>">
							<span class="help-block">The User allowed to connect to the Database.</span>
						</div>
						<div class="form-group">
							<label for="dbpass">Database Password</label>
							<input type="text" class="form-control" name="dbpass" value="<?php echo isset($_POST['dbpass']) ? $_POST['dbpass'] : '' ?>">
							<span class="help-block">The Password for the above User.</span>
						</div>
						<div class="form-group">
							<label for="dbname">Database Name</label>
							<input type="text" class="form-control" name="dbname" value="<?php echo isset($_POST['dbname']) ? $_POST['dbname'] : '' ?>">
							<span class="help-block">The Database Name.</span>
						</div>
						<button type="input" name="submit" value="On to Step 2" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> On to Step 2</button>
					</form>
				<?php } else { ?>
					<div class="alertMsg danger">
						<i class='fa fa-times-circle'></i> Your database information is incorrect. Please delete the generated <strong>config.php</strong> file and then <a href="install.php">refresh this page</a>.
					</div>
				<?php } ?>

				<?php
				} else if ($step == '2') {

					include('../config.php');
					$isSetup = '';

					// Check for Data
					if ($result = $mysqli->query("SELECT * FROM sitesettings LIMIT 1")) {
						if ($obj = $result->fetch_object()) {
							$isSetup = 'true';
						}
						$result->close();
					}

					if($isSetup == '') {
				?>

					<h3 class="primary">Step 2 <i class="fa fa-long-arrow-right"></i> My-Home Settings &amp; Admin Account</h3>

					<?php if ($msgBox) { echo $msgBox; } ?>

					<div class="alertMsg success">
						<i class='fa fa-check-square-o'></i> Your database has been correctly configured.
					</div>

					<form action="" method="post" class="padTop">
						<h4 class="warning">Site Settings</h4>
						<p class="lead">Now please take a few minutes and complete the information below in order to finish installing My-Home.</p>
						
						<div class="form-group padTop">
							<label for="installUrl">Installation URL</label>
							<input type="text" class="form-control" name="installUrl" value="<?php echo $installURL; ?>">
							<span class="help-block">Used in Notification emails &amp; Uploads. Must include the trailing slash. Change the default value if it is not correct.</span>
						</div>
						<div class="form-group">
							<label for="siteName">Site Name</label>
							<input type="text" class="form-control" name="siteName" value="<?php echo isset($_POST['siteName']) ? $_POST['siteName'] : ''; ?>">
							<span class="help-block">ie. My-Home (Appears at the top of the browser, the header logo, in the footer and in other headings throughout the site).</span>
						</div>
						<div class="form-group">
							<label for="businessName">Business Name</label>
							<input type="text" class="form-control" name="businessName" value="<?php echo isset($_POST['businessName']) ? $_POST['businessName'] : ''; ?>">
							<span class="help-block"></span>
						</div>
						<div class="form-group">
							<label for="businessAddress">Business Address</label>
							<textarea class="form-control" name="businessAddress" rows="3"><?php echo isset($_POST['businessAddress']) ? $_POST['businessAddress'] : ''; ?></textarea>
							<span class="help-block">Your Address or the Address of your Business.</span>
						</div>
						<div class="settingsNote highlight"></div>
						<div class="form-group">
							<label for="businessEmail">Office/Support Email</label>
							<input type="text" class="form-control" name="businessEmail" id="businessEmail" value="<?php echo isset($_POST['businessEmail']) ? $_POST['businessEmail'] : ''; ?>">
							<span class="help-block">Used in email notifications as the "from/reply to" email address.</span>
						</div>
						<div class="form-group">
							<label for="businessPhone">Office/Support Phone</label>
							<input type="text" class="form-control" name="businessPhone" id="businessPhone" value="<?php echo isset($_POST['businessPhone']) ? $_POST['businessPhone'] : ''; ?>">
							<span class="help-block">Phone number that Tenant's can use to contact you.</span>
						</div>
						<div class="form-group">
							<label for="fileTypesAllowed">Upload File Types Allowed</label>
							<input type="text" class="form-control" name="fileTypesAllowed" value="gif,jpg,jpeg,png,tiff,tif,zip,rar,pdf,doc,docx,txt,xls,csv">
							<span class="help-block">The file types you allow to be uploaded, NO spaces & each separated by a comma (Format: xls,pdf,doc,docx).</span>
						</div>
						<div class="form-group">
							<label for="avatarTypes">Avatar File Types Allowed</label>
							<input type="text" class="form-control" name="avatarTypes" value="jpg,jpeg,png">
							<span class="help-block">The file types you allow to be uploaded, NO spaces & each separated by a comma (Format: jpg,jpeg,png).</span>
						</div>
						<div class="form-group">
							<label for="propertyPicTypes">Property Pictures File Types Allowed</label>
							<input type="text" class="form-control" name="propertyPicTypes" value="jpg,jpeg,png">
							<span class="help-block">The file types you allow to be uploaded, NO spaces & each separated by a comma (Format: jpg,jpeg,png).</span>
						</div>

						<hr />

						<h4 class="danger">Primary Admin Account</h4>
						<p class="lead">Finally, set up the Primary Admin Account.</p>
						
						<div class="adminNote highlight"></div>
						<div class="form-group padTop">
							<label for="adminEmail">Admin's Email Address</label>
							<input type="text" class="form-control" name="adminEmail" id="adminEmail" value="<?php echo isset($_POST['adminEmail']) ? $_POST['adminEmail'] : ''; ?>">
							<span class="help-block">Your email address is also used for your Account log In.</span>
						</div>
						<div class="form-group">
							<label for="password">Administrator's Password</label>
							<input type="text" class="form-control" name="password" value="">
							<span class="help-block">Type a Password for your Account.</span>
						</div>
						<div class="form-group">
							<label for="r-password">Re-type Administrator's Password</label>
							<input type="text" class="form-control" name="r-password" value="">
							<span class="help-block">Please type your desired Password again. Passwords MUST Match.</span>
						</div>
						<div class="form-group">
							<label for="adminFirstName">Administrator's First Name</label>
							<input type="text" class="form-control" name="adminFirstName" value="<?php echo isset($_POST['adminFirstName']) ? $_POST['adminFirstName'] : ''; ?>">
						</div>
						<div class="form-group">
							<label for="adminLastName">Administrator's Last Name</label>
							<input type="text" class="form-control" name="adminLastName" value="<?php echo isset($_POST['adminLastName']) ? $_POST['adminLastName'] : ''; ?>">
						</div>
						<button type="input" name="submit" value="Complete Install" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> Complete Install</button>
					</form>
					<?php } else { ?>
						<h3 class="primary">Step 3 <i class="fa fa-long-arrow-right"></i> Ready to get Started?</h3>
						<div class="alertMsg info">
							<i class='fa fa-info-circle'></i> Whoops! Looks like the <strong>"install"</strong> folder is still there!
						</div>
						<p class="lead">
							For security reasons and to stop any possible re-installations please, <strong>DELETE or RENAME</strong> the "install" folder,<br />
							otherwise you will not be able to log in as Administrator.
						</p>
						<div class="alertMsg warning">
							<i class="fa fa-warning"></i> Please <strong>DELETE or RENAME</strong> the "install" folder.
						</div>
						<a href="../admin/login.php" class="btn btn-large btn-info"><i class="fa fa-sign-in"></i> Administrator Log In</a>
					<?php } ?>


				<?php } else { ?>

					<h3 class="primary">Step 3 <i class="fa fa-long-arrow-right"></i> Ready to get Started?</h3>
					<div class="alertMsg success">
						<i class='fa fa-check-square-o'></i> My-Home was successfully installed.
					</div>
					<p class="lead">
						For security reasons and to stop any possible re-installations please, <strong>DELETE or RENAME</strong> the "install" folder,<br />
						otherwise you will not be able to log in as Administrator.
						<br />
						A confirmation email has been sent to the email address you supplied for the Primary Administrator.
					</p>
					<div class="alertMsg warning">
						<i class="fa fa-warning"></i> You MUST <strong>DELETE or RENAME</strong> the "install" folder.
					</div>
					<a href="../admin/login.php" class="btn btn-large btn-info"><i class="fa fa-sign-in"></i> Administrator Log In</a>
				<?php } ?>
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
	<script src="../js/extra.js"></script>
	<script src="install.js"></script>

</body>
</html>