<?php
	$msgBox = '';

	// Get Documents Folder from Site Settings
	$docUploadPath = $set['tenantDocsPath'];

	// Get the File Uploads Folder from the Site Settings
	$propertyPicsPath = $set['propertyPicsPath'];

	// Get Property Files Folder from Site Settings
	$uploadPath = $set['uploadPath'];

	// Add a New Tenant
    if (isset($_POST['submit']) && $_POST['submit'] == 'New Tenant') {
        // Validation
        if($_POST['tenantEmail'] == "") {
            $msgBox = alertBox($tenantEmailReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['password'] == "") {
            $msgBox = alertBox($tenantPasswordReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password'] != $_POST['password-r']) {
			$msgBox = alertBox($passwordMismatch, "<i class='fa fa-warning'></i>", "danger");
        } else if($_POST['tenantFirstName'] == "") {
            $msgBox = alertBox($tenantFirstNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['tenantLastName'] == "") {
            $msgBox = alertBox($tenantLastNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Set some variables
			$setActive = $mysqli->real_escape_string($_POST['setActive']);
			$dupEmail = '';
			$newEmail = $mysqli->real_escape_string($_POST['tenantEmail']);
			$tenantFirstName = $mysqli->real_escape_string($_POST['tenantFirstName']);
			$tenantLastName = $mysqli->real_escape_string($_POST['tenantLastName']);

			// Set the Tenant Document Directory using the Tenant Name
			// Replace any spaces with an underscore and set to all lowercase
			$docFolderName = $tenantFirstName.'_'.$tenantLastName;
			$tenantDocs = str_replace(' ', '_', $docFolderName);
			$tenantDocsFolder = strtolower($tenantDocs);

			// Check for Duplicate email
			$check = $mysqli->query("SELECT 'X' FROM tenants WHERE tenantEmail = '".$newEmail."'");
			if ($check->num_rows) {
				$dupEmail = 'true';
			}

			// If duplicates are found
			if ($dupEmail != '') {
				$msgBox = alertBox($duplicateEmail, "<i class='fa fa-warning'></i>", "danger");
			} else {
				if ($setActive == '0') {
					// Create the new account & send Activation Email to Tenant
					$hash = md5(rand(0,1000));
					$isActive = '0';
					$today = date("Y-m-d H:i:s");
					$password = md5($_POST['password']);

					$stmt = $mysqli->prepare("
										INSERT INTO
											tenants(
												tenantDocsFolder,
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
												?,
												?
											)");
					$stmt->bind_param('ssssssss',
						$tenantDocsFolder,
						$newEmail,
						$password,
						$tenantFirstName,
						$tenantLastName,
						$today,
						$hash,
						$isActive
					);
					if (!$stmt->execute()) {
					echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
					}

					// Send out the email in HTML
					$installUrl = $set['installUrl'];
					$siteName = $set['siteName'];
					$businessEmail = $set['businessEmail'];
					$newPass = $mysqli->real_escape_string($_POST['password']);

					$subject = 'Your '.$siteName.' Tenant Account has been created';

					$message = '<html><body>';
					$message .= '<h3>'.$subject.'</h3>';
					$message .= '<p>Your new Account details:</p>';
					$message .= '<hr>';
					$message .= '<p>Username: Your email address<br>Password: '.$newPass.'</p>';
					$message .= '<p>You must activate your account before you will be able to log in. Please click (or copy/paste) the following link to activate your account:<br /><br />http://'.$installUrl.'activate.php?tenantEmail='.$newEmail.'&hash='.$hash.'</p>';
					$message .= '<hr>';
					$message .= '<p>Once you have activated your new Tenant account and logged in, please take the time to update your account profile details.</p>';
					$message .= '<p>You can log in to your account at:  http://'.$installUrl.'</p>';
					$message .= '<p>Thank you,<br />'.$siteName.'</p>';
					$message .= '</body></html>';

					$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
					$headers .= "Reply-To: ".$businessEmail."\r\n";
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

					if (mail($newEmail, $subject, $message, $headers)) {
						$msgBox = alertBox($tenantAccountCreated, "<i class='fa fa-check-square-o'></i>", "success");
						// Clear the form of Values
						$_POST['tenantEmail'] = $_POST['password'] = $_POST['tenantFirstName'] = $_POST['tenantLastName'] = '';
					} else {
						$msgBox = alertBox($accountEmailError, "<i class='fa fa-warning'></i>", "danger");
					}
					$stmt->close();
				} else {
					// Create the new account and set it to Active
					$hash = md5(rand(0,1000));
					$isActive = '1';
					$today = date("Y-m-d H:i:s");
					$password = md5($_POST['password']);

					$stmt = $mysqli->prepare("
										INSERT INTO
											tenants(
												tenantDocsFolder,
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
												?,
												?
											)");
					$stmt->bind_param('ssssssss',
						$tenantDocsFolder,
						$newEmail,
						$password,
						$tenantFirstName,
						$tenantLastName,
						$today,
						$hash,
						$isActive
					);
					if (!$stmt->execute()) {
					echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
					}
					$msgBox = alertBox($tenantAccountActivated, "<i class='fa fa-check-square-o'></i>", "success");

					// Clear the form of Values
					$_POST['tenantEmail'] = $_POST['password'] = $_POST['tenantFirstName'] = $_POST['tenantLastName'] = '';
					$stmt->close();
				}
				// Create the Tenant Document Directory
				if (mkdir('../'.$docUploadPath.$tenantDocsFolder, 0755, true)) {
					$newDir = '../'.$docUploadPath.$tenantDocsFolder;
				}
			}
		}
	}

	// Create a New Property
    if (isset($_POST['submit']) && $_POST['submit'] == 'Add Property') {
        // Validation
        if($_POST['propertyName'] == "") {
            $msgBox = alertBox($propertyNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['propertyDesc'] == "") {
            $msgBox = alertBox($propertyDescReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['propertyRate'] == "") {
            $msgBox = alertBox($monthlyRateReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['latePenalty'] == "") {
            $msgBox = alertBox($latePeneltyReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['propertyDeposit'] == "") {
            $msgBox = alertBox($depositAmountReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Set some variables
			$propertyName = $mysqli->real_escape_string($_POST['propertyName']);
			$propertyDesc = htmlentities($_POST['propertyDesc']);
			$propertyRate = $mysqli->real_escape_string($_POST['propertyRate']);
			$latePenalty = $mysqli->real_escape_string($_POST['latePenalty']);
			$propertyDeposit = $mysqli->real_escape_string($_POST['propertyDeposit']);
			$petsAllowed = $mysqli->real_escape_string($_POST['petsAllowed']);

			// Set the Property Pictures Directory using the Property Name
			// Replace any spaces with an underscore and set to all lowercase
			$propertyPics = str_replace(' ', '_', $propertyName);
			$propertyFolder = strtolower($propertyPics);

			$stmt = $mysqli->prepare("
								INSERT INTO
									properties(
										createdBy,
										propertyName,
										propertyDesc,
										propertyRate,
										latePenalty,
										propertyDeposit,
										petsAllowed,
										propertyFolder
									) VALUES (
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?
									)");
			$stmt->bind_param('ssssssss',
				$adminId,
				$propertyName,
				$propertyDesc,
				$propertyRate,
				$latePenalty,
				$propertyDeposit,
				$petsAllowed,
				$propertyFolder
			);
			if (!$stmt->execute()) {
					echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
					}

			// Create the Property Pictures Directory
			if (mkdir('../'.$propertyPicsPath.$propertyFolder, 0755, true)) {
				$newDir = '../'.$propertyPicsPath.$propertyFolder;
			}

			// Create the Property Documents Directory
			if (mkdir('../'.$uploadPath.$propertyFolder, 0755, true)) {
				$newDir = '../'.$uploadPath.$propertyFolder;
				$msgBox = alertBox($newPropertyCreated, "<i class='fa fa-check-square-o'></i>", "success");

				// Clear the form of Values
				$_POST['propertyName'] = $_POST['propertyDesc'] = $_POST['propertyRate'] = $_POST['latePenalty'] = $_POST['propertyDeposit'] = '';
				$stmt->close();
			}
		}
	}

    // Create a New Service Request
    if (isset($_POST['submit']) && $_POST['submit'] == 'Create Request') {
        // Validation
        if($_POST['leaseId'] == "") {
            $msgBox = alertBox($leaseIdReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['requestTitle'] == "") {
            $msgBox = alertBox($requestTitleReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['requestDesc'] == "") {
            $msgBox = alertBox($requestDescReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Set some variables
			$leaseId = $mysqli->real_escape_string($_POST['leaseId']);
			$requestPriority = $mysqli->real_escape_string($_POST['requestPriority']);
			$requestTitle = $mysqli->real_escape_string($_POST['requestTitle']);
			$requestDesc = htmlentities($_POST['requestDesc']);

			// Get Tenant ID
			$query = "SELECT tenantId FROM tenants WHERE leaseId = ".$leaseId;
			$res = mysqli_query($mysqli, $query) or die('Error, retrieving Tenant Data failed. ' . mysqli_error());
			$row = mysqli_fetch_assoc($res);
			$tenantId = $row['tenantId'];
			$today = date("Y-m-d H:i:s");

            $stmt = $mysqli->prepare("
                                INSERT INTO
                                    servicerequests(
                                        tenantId,
                                        leaseId,
                                        adminId,
                                        requestDate,
                                        requestPriority,
										requestTitle,
										requestDesc
                                    ) VALUES (
                                        ?,
                                        ?,
                                        ?,
										?,
										?,
										?,
										?
                                    )");
            $stmt->bind_param('sssssss',
                $tenantId,
				$leaseId,
				$adminId,
				$today,
				$requestPriority,
				$requestTitle,
				$requestDesc
            );
            if (!$stmt->execute()) {
					echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
					}
			$msgBox = alertBox($requestCreated, "<i class='fa fa-check-square-o'></i>", "success");

			// Clear the form of Values
			$_POST['requestTitle'] = $_POST['requestDesc'] = '';
            $stmt->close();
		}
	}

	// Add a New Admin
    if (isset($_POST['submit']) && $_POST['submit'] == 'New Admin') {
        // Validation
        if($_POST['adminEmail'] == "") {
            $msgBox = alertBox($adminEmailReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['password'] == "") {
            $msgBox = alertBox($adminPasswordReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password'] != $_POST['password-r']) {
			$msgBox = alertBox($passwordMismatch, "<i class='fa fa-warning'></i>", "danger");
        } else if($_POST['adminFirstName'] == "") {
            $msgBox = alertBox($adminFirstNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['adminLastName'] == "") {
            $msgBox = alertBox($adminLastNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Set some variables
			$dupEmail = '';
			$isSuperuser = $mysqli->real_escape_string($_POST['superuser']);
			$adminsRole = $mysqli->real_escape_string($_POST['adminRole']);
			$newEmail = $mysqli->real_escape_string($_POST['adminEmail']);
			$adminFirstName = $mysqli->real_escape_string($_POST['adminFirstName']);
			$adminLastName = $mysqli->real_escape_string($_POST['adminLastName']);

			// Check for Duplicate email
			$check = $mysqli->query("SELECT 'X' FROM admins WHERE adminEmail = '".$newEmail."'");
			if ($check->num_rows) {
				$dupEmail = 'true';
			}

			// If duplicates are found
			if ($dupEmail != '') {
				$msgBox = alertBox($duplicateEmail, "<i class='fa fa-warning'></i>", "danger");
			} else {
				// Create the new account and set it to Active
				$isActive = '1';
				$today = date("Y-m-d H:i:s");
				$password = md5($_POST['password']);

				$stmt = $mysqli->prepare("
									INSERT INTO
										admins(
											superuser,
											adminRole,
											adminEmail,
											password,
											adminFirstName,
											adminLastName,
											createDate,
											isActive
										) VALUES (
											?,
											?,
											?,
											?,
											?,
											?,
											?,
											?
										)");
				$stmt->bind_param('ssssssss',
					$isSuperuser,
					$adminsRole,
					$newEmail,
					$password,
					$adminFirstName,
					$adminLastName,
					$today,
					$isActive
				);
				if (!$stmt->execute()) {
					echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
					}
				$msgBox = alertBox($adminAccountCreated, "<i class='fa fa-check-square-o'></i>", "success");

				// Clear the form of Values
				$_POST['adminEmail'] = $_POST['password'] = $_POST['adminFirstName'] = $_POST['adminLastName'] = '';
				$stmt->close();
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
	<title><?php echo $set['siteName'].' &mdash; '.$pageHeadTitle; ?></title>

	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>

	<link href="../css/bootstrap.css" rel="stylesheet">
	<link href="../css/custom.css" rel="stylesheet">
	<link href="../css/extra.css" rel="stylesheet">
	<link href="../css/datepicker.css" rel="stylesheet">
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
				<p class="textRight">
					<?php echo $welcomeMsg.', '.$adminFirstName.' '.$adminLastName; ?> <br />
					<?php echo $todayMsg.' '.date('l'). " the " .date('jS \of F, Y'); ?>
				</p>
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
				<ul class="nav navbar-nav">
					<li><a href="index.php"><?php echo $dashboardNav; ?></a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $tenantsNav; ?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="index.php?action=activeTenants"><?php echo $activeTenantsNav; ?></a></li>
							<li><a href="index.php?action=inactiveTenants"><?php echo $inactiveTenantsNav; ?></a></li>
							<li><a href="index.php?action=archivedTenants"><?php echo $archivedTenantsNav; ?></a></li>
							<?php if ($superuser == 1) { ?>
								<li class="divider"></li>
								<li><a data-toggle="modal" href="#newTenant"><?php echo $newTenantNav; ?></a></li>
							<?php } ?>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $propertiesNav; ?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="index.php?action=activeProperties"><?php echo $activePropertiesNav; ?></a></li>
							<li><a href="index.php?action=archivedProperties"><?php echo $archivedPropertiesNav; ?></a></li>
							<?php if ($superuser == 1) { ?>
								<li class="divider"></li>
								<li><a data-toggle="modal" href="#newProperty"><?php echo $newPropertyNav; ?></a></li>
							<?php } ?>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $propertyLeasesNav; ?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="index.php?action=activeLeases"><?php echo $activeLeasesNav; ?></a></li>
							<li><a href="index.php?action=archivedLeases"><?php echo $archivedLeasesNav; ?></a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $serviceRequestsNav; ?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="index.php?action=activeRequests"><?php echo $openServiceRequestsNav; ?></a></li>
							<li><a href="index.php?action=closedRequests"><?php echo $closedServiceRequestsNav; ?></a></li>
							<li><a href="index.php?action=archivedRequests"><?php echo $archivedServiceRequestsNav; ?></a></li>
							<li class="divider"></li>
							<li><a data-toggle="modal" href="#newRequest"><?php echo $newServiceRequestNav; ?></a></li>
						</ul>
					</li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<?php if ($superuser == 1) { ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $aminsNav; ?> <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="index.php?action=allAdmins"><?php echo $viewAllAdminsNav; ?></a></li>
								<li><a href="index.php?action=myProfile"><?php echo $myProfileNav; ?></a></li>
								<li class="divider"></li>
								<li><a data-toggle="modal" href="#newAdmin"><?php echo $newAdminNav; ?></a></li>
							</ul>
						</li>
					<?php } else { ?>
						<li><a href="index.php?action=myProfile"><?php echo $myProfileNav; ?></a></li>
					<?php } ?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $manageNav; ?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="index.php?action=siteAlerts"><?php echo $siteAlertsNav; ?></a></li>
							<li><a href="index.php?action=reports"><?php echo $reportsNav; ?></a></li>
							<li><a href="index.php?action=siteTemplates"><?php echo $formsNav; ?></a></li>
							<?php if ($superuser == 1) { ?>
								<li class="divider"></li>
								<li><a href="index.php?action=emailAllTenants"><?php echo $emailAllTenantsNav; ?></a></li>
								<li><a href="index.php?action=siteSettings"><?php echo $siteSettingsNav; ?></a></li>
							<?php } ?>
						</ul>
					</li>
					<li><a data-toggle="modal" href="#signOut"><?php echo $signoutBtn; ?> <i class="fa fa-sign-out"></i></a></li>
				</ul>
			</div>
		</div>

		<!-- -- SIGN OUT MODEL -- -->
		<div class="modal fade" id="signOut" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body">
						<p class="lead"><?php echo $adminFirstName.', '.$signoutConf; ?></p>
					</div>
					<div class="modal-footer">
						<a href="index.php?action=logout" class="btn btn-success btn-icon-alt"><?php echo $signoutBtn; ?> <i class="fa fa-sign-out"></i></a>
						<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</div>
			</div>
		</div>

		<!-- ADD A NEW TENANT MODAL -->
		<div class="modal fade" id="newTenant" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header modal-primary">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
						<h4 class="modal-title"><?php echo $newTenantNav; ?></h4>
					</div>
					<form action="" method="post">
						<div class="modal-body">
							<div class="form-group">
								<label for="setActive"><?php echo $setAsActiveField; ?></label>
								<select class="form-control" id="setActive" name="setActive">
									<option value="0"><?php echo $noBtn; ?></option>
									<option value="1"><?php echo $yesBtn; ?></option>
								</select>
								<span class="help-block"><?php echo $activeAccountHelper; ?></span>
							</div>
							<div class="form-group">
								<label for="tenantEmail"><?php echo $tenantsEmailField; ?></label>
								<input type="text" class="form-control" name="tenantEmail" id="tenantEmail" value="<?php echo isset($_POST['tenantEmail']) ? $_POST['tenantEmail'] : ''; ?>" />
								<span class="help-block"><?php echo $tenantsEmailHelper; ?></span>
							</div>
							<div class="form-group">
								<label for="password"><?php echo $passwordField; ?></label>
								<input type="text" class="form-control" name="password" id="password" value="" />
								<span class="help-block"><?php echo $passwordHelper; ?></span>
							</div>
							<div class="form-group">
								<label for="password-r"><?php echo $rpasswordField; ?></label>
								<input type="text" class="form-control" name="password-r" id="password-r" value="" />
								<span class="help-block"><?php echo $repeatPasswordHelper; ?></span>
							</div>
							<div class="form-group">
								<label for="tenantFirstName"><?php echo $tenantsFirstNameField; ?></label>
								<input type="text" class="form-control" name="tenantFirstName" id="tenantFirstName" value="<?php echo isset($_POST['tenantFirstName']) ? $_POST['tenantFirstName'] : ''; ?>" />
							</div>
							<div class="form-group">
								<label for="tenantLastName"><?php echo $tenantsLastNameField; ?></label>
								<input type="text" class="form-control" name="tenantLastName" id="tenantLastName" value="<?php echo isset($_POST['tenantLastName']) ? $_POST['tenantLastName'] : ''; ?>" />
							</div>
						</div>
						<div class="modal-footer">
							<button type="input" name="submit" value="New Tenant" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $addTenantBtn; ?></button>
							<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- ADD A NEW PROPERTY MODAL -->
		<div class="modal fade" id="newProperty" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header modal-info">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
						<h4 class="modal-title"><?php echo $newPropertyNav; ?></h4>
					</div>
					<form action="" method="post">
						<div class="modal-body">
							<div class="form-group">
								<label for="propertyName"><?php echo $propertyNameField; ?></label>
								<input type="text" class="form-control" name="propertyName" id="propertyName" value="<?php echo isset($_POST['propertyName']) ? $_POST['propertyName'] : ''; ?>">
								<span class="help-block"><?php echo $propertyNameHelper; ?></span>
							</div>
							<div class="form-group">
								<label for="propertyDesc"><?php echo $propertyDescField; ?></label>
								<textarea class="form-control" name="propertyDesc" id="propertyDesc" rows="2"><?php echo isset($_POST['propertyDesc']) ? $_POST['propertyDesc'] : ''; ?></textarea>
								<span class="help-block"><?php echo $propertyDescHelper.' '.$htmlNotAllowed; ?></span>
							</div>
							<div class="form-group">
								<label for="propertyRate"><?php echo $propertyRateField; ?></label>
								<input type="text" class="form-control" name="propertyRate" id="propertyRate" value="<?php echo isset($_POST['propertyRate']) ? $_POST['propertyRate'] : ''; ?>">
								<span class="help-block"><?php echo $numberOnlyHelper; ?></span>
							</div>
							<div class="form-group">
								<label for="latePenalty"><?php echo $latePeneltyField; ?></label>
								<input type="text" class="form-control" name="latePenalty" id="latePenalty" value="<?php echo isset($_POST['latePenalty']) ? $_POST['latePenalty'] : ''; ?>">
								<span class="help-block"><?php echo $latePeneltyHelper; ?></span>
							</div>
							<div class="form-group">
								<label for="propertyDeposit"><?php echo $propertyDepositField; ?></label>
								<input type="text" class="form-control" name="propertyDeposit" id="propertyDeposit" value="<?php echo isset($_POST['propertyDeposit']) ? $_POST['propertyDeposit'] : ''; ?>">
								<span class="help-block"><?php echo $numberOnlyHelper; ?></span>
							</div>
							<div class="form-group">
								<label for="petsAllowed"><?php echo $petsAllowedField; ?></label>
								<select class="form-control" id="petsAllowed" name="petsAllowed">
									<option value="0"><?php echo $noBtn; ?></option>
									<option value="1"><?php echo $yesBtn; ?></option>
								</select>
							</div>
						</div>
						<div class="modal-footer">
							<button type="input" name="submit" value="Add Property" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $addPropertyBtn; ?></button>
							<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- ADD A NEW SERVICE REQUEST MODAL -->
		<div class="modal fade" id="newRequest" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header modal-warning">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
						<h4 class="modal-title"><?php echo $newServiceRequestNav; ?></h4>
					</div>
					<form action="" method="post">
						<div class="modal-body">
							<div class="form-group">
								<label for="leaseId"><?php echo $propertyField; ?></label>
								<select class="form-control" id="leaseId" name="leaseId">
									<?php
										// Get the Property List
										$sqlStmt = "SELECT
														properties.propertyName,
														leases.leaseId
													FROM
														properties
														LEFT JOIN leases ON properties.propertyId = leases.propertyId
													WHERE
														properties.isArchived != 1 AND
														leases.isClosed = 0";
										$results = mysqli_query($mysqli, $sqlStmt) or die(mysqli_error());
									?>
									<option value="">...</option>
									<?php while ($row = mysqli_fetch_assoc($results)) { ?>
										<option value="<?php echo $row['leaseId']; ?>"><?php echo clean($row['propertyName']); ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group">
								<label for="requestPriority"><?php echo $servicePriorityField; ?></label>
								<select class="form-control" id="requestPriority" name="requestPriority">
									<option value="0" selected><?php echo $normalSelect; ?></option>
									<option value="1"><?php echo $importantSelect; ?></option>
									<option value="2"><?php echo $urgenSelect; ?></option>
								</select>
								<span class="help-block"><?php echo $servicePriorityHelper; ?></span>
							</div>
							<div class="form-group">
								<label for="requestTitle"><?php echo $serviceRequestField; ?></label>
								<input type="text" class="form-control" name="requestTitle" id="requestTitle" value="<?php echo isset($_POST['requestTitle']) ? $_POST['requestTitle'] : ''; ?>">
								<span class="help-block"><?php echo $serviceTitleHelper; ?></span>
							</div>
							<div class="form-group">
								<label for="requestDesc"><?php echo $serviceRequestDesc; ?></label>
								<textarea class="form-control" name="requestDesc" id="requestDesc" rows="4"><?php echo isset($_POST['requestDesc']) ? $_POST['requestDesc'] : ''; ?></textarea>
								<span class="help-block"><?php echo $beDescriptiveHelper.' '.$htmlNotAllowed; ?></span>
							</div>
						</div>
						<div class="modal-footer">
							<button type="input" name="submit" value="Create Request" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
							<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- ADD A NEW ADMIN/LANDLORD MODAL -->
		<div class="modal fade" id="newAdmin" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header modal-danger">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
						<h4 class="modal-title"><?php echo $newAdminNav; ?></h4>
					</div>
					<form action="" method="post">
						<div class="modal-body">
							<p class="lead"><?php echo $adminsSetActive; ?></p>
							<div class="form-group padTop">
								<label for="superuser"><?php echo $adminAccountType; ?></label>
								<select class="form-control" id="superuser" name="superuser">
									<option value="0" selected><?php echo $superuserNo; ?></option>
									<option value="1"><?php echo $superuserYes; ?></option>
								</select>
								<span class="help-block"><?php echo $superuserHelper; ?></span>
							</div>
							<div class="form-group">
								<label for="adminRole"><?php echo $adminRoleField; ?></label>
								<select class="form-control" id="adminRole" name="adminRole">
									<option value="0"><?php echo $adminRoleAdmin; ?></option>
									<option value="1" selected><?php echo $adminRoleLandlord; ?></option>
								</select>
								<span class="help-block"><?php echo $selectRoleHelper; ?></span>
							</div>
							<div class="form-group">
								<label for="adminEmail"><?php echo $adminsEmailField; ?></label>
								<input type="text" class="form-control" name="adminEmail" id="adminEmail" value="<?php echo isset($_POST['adminEmail']) ? $_POST['adminEmail'] : ''; ?>" />
								<span class="help-block"><?php echo $adminsEmailHelper; ?></span>
							</div>
							<div class="form-group">
								<label for="password"><?php echo $passwordField; ?></label>
								<input type="text" class="form-control" name="password" id="password" value="" />
								<span class="help-block"><?php echo $passwordHelper; ?></span>
							</div>
							<div class="form-group">
								<label for="password-r"><?php echo $rpasswordField; ?></label>
								<input type="text" class="form-control" name="password-r" id="password-r" value="" />
								<span class="help-block"><?php echo $repeatPasswordHelper; ?></span>
							</div>
							<div class="form-group">
								<label for="adminFirstName"><?php echo $adminsFirstNameField; ?></label>
								<input type="text" class="form-control" name="adminFirstName" id="adminFirstName" value="<?php echo isset($_POST['adminFirstName']) ? $_POST['adminFirstName'] : ''; ?>" />
							</div>
							<div class="form-group">
								<label for="adminLastName"><?php echo $adminsLastNameField; ?></label>
								<input type="text" class="form-control" name="adminLastName" id="adminLastName" value="<?php echo isset($_POST['adminLastName']) ? $_POST['adminLastName'] : ''; ?>" />
							</div>
						</div>
						<div class="modal-footer">
							<button type="input" name="submit" value="New Admin" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $addAdminBtn; ?></button>
							<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div class="content">