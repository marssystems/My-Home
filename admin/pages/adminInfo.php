<?php
	$adminId = $_GET['adminId'];
	$jsFile = 'adminInfo';

	// Get Avatar Folder from Site Settings
	$avatarDir = $set['avatarFolder'];

	// Remove Avatar Image
    if (isset($_POST['submit']) && $_POST['submit'] == 'removeAvatar') {
		// Get the Admin's avatar url
		$sql = "SELECT adminAvatar FROM admins WHERE adminId = ".$adminId;
		$result = mysqli_query($mysqli, $sql) or die(mysqli_error());
		$r = mysqli_fetch_assoc($result);
		$avatarName = $r['adminAvatar'];

		$filePath = '../'.$avatarDir.$avatarName;
		// Delete the Admin's image from the server
		if (file_exists($filePath)) {
			unlink($filePath);

			// Update the Admin record
			$adminAvatar = 'adminDefault.png';
			$stmt = $mysqli->prepare("
								UPDATE
									admins
								SET
									adminAvatar = ?
								WHERE
									adminId = ?
			");
			$stmt->bind_param('ss',
							   $adminAvatar,
							   $adminId
			);
			$stmt->execute();
			$msgBox = alertBox($adminAvatarRemovedMsg, "<i class='fa fa-check-square-o-sign'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($adminAvatarRemoveErrorMsg, "<i class='icon-warning-sign'></i>", "warning");
		}
	}

	// Update Account Type
	if (isset($_POST['submit']) && $_POST['submit'] == 'editType') {
		$superuser = $mysqli->real_escape_string($_POST['superuser']);
		$adminRole = $mysqli->real_escape_string($_POST['adminRole']);

		$stmt = $mysqli->prepare("
							UPDATE
								admins
							SET
								superuser = ?,
								adminRole = ?
							WHERE
								adminId = ?
		");
		$stmt->bind_param('sss',
						   $superuser,
						   $adminRole,
						   $adminId
		);
		$stmt->execute();
		$msgBox = alertBox($adminAccountUpdatedMsg, "<i class='fa fa-check-square-o-sign'></i>", "success");
		$stmt->close();
	}

	// Update Personal Info
	if (isset($_POST['submit']) && $_POST['submit'] == 'updateInfo') {
		// Validation
        if($_POST['adminFirstName'] == "") {
            $msgBox = alertBox($firstNameReqVal, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['adminLastName'] == "") {
            $msgBox = alertBox($lastNameReqVal, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$adminFirstName = $mysqli->real_escape_string($_POST['adminFirstName']);
			$adminLastName = $mysqli->real_escape_string($_POST['adminLastName']);

			// Encrypt sensitive data
			$adminAddress = encryptIt($_POST['adminAddress']);
			$adminPhone = encryptIt($_POST['adminPhone']);
			$adminAltPhone = encryptIt($_POST['adminAltPhone']);

			$stmt = $mysqli->prepare("
								UPDATE
									admins
								SET
									adminFirstName = ?,
									adminLastName = ?,
									adminAddress = ?,
									adminPhone = ?,
									adminAltPhone = ?
								WHERE
									adminId = ?
			");
			$stmt->bind_param('ssssss',
								$adminFirstName,
								$adminLastName,
								$adminAddress,
								$adminPhone,
								$adminAltPhone,
								$adminId
			);
			$stmt->execute();
			$msgBox = alertBox($adminPersonalInfoUpdatedMsg, "<i class='fa fa-check-square-o-sign'></i>", "success");
			$stmt->close();
		}
	}

	// Update Account Email
	if (isset($_POST['submit']) && $_POST['submit'] == 'updateEmail') {
		if($_POST['adminEmail'] == "") {
            $msgBox = alertBox($adminEmailReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$adminEmail = $mysqli->real_escape_string($_POST['adminEmail']);

			$stmt = $mysqli->prepare("
								UPDATE
									admins
								SET
									adminEmail = ?
								WHERE
									adminId = ?
			");
			$stmt->bind_param('ss',
							   $adminEmail,
							   $adminId
			);
			$stmt->execute();
			$msgBox = alertBox($adminEmailUpdatedMsg, "<i class='fa fa-check-square-o-sign'></i>", "success");
			$stmt->close();
		}
	}

	// Update Password
	if (isset($_POST['submit']) && $_POST['submit'] == 'updatePass') {
		if($_POST['password'] == '') {
			$msgBox = alertBox($adminNewPasswordReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password_r'] == '') {
			$msgBox = alertBox($adminRetypePassReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password'] != $_POST['password_r']) {
            $msgBox = alertBox($newPasswordsNoMatchMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			if(isset($_POST['password']) && $_POST['password'] != "") {
				$password = md5($_POST['password']);
			} else {
				$password = $_POST['passwordOld'];
			}

			$stmt = $mysqli->prepare("
								UPDATE
									admins
								SET
									password = ?
								WHERE
									adminId = ?
			");
			$stmt->bind_param('ss',
							   $password,
							   $adminId
			);
			$stmt->execute();
			$msgBox = alertBox($adminsPasswordSavedMsg, "<i class='fa fa-check-square-o-sign'></i>", "success");
			$stmt->close();
		}
	}

	// Update Account Status
	if (isset($_POST['submit']) && $_POST['submit'] == 'editStatus') {
		$query = "SELECT adminId FROM assignedproperties WHERE adminId = ?";
		$check = $mysqli->prepare($query);
		$check->bind_param("s",$adminId);
		$check->execute();
		$check->bind_result($adminId);
		$check->store_result();
		$numrows = $check->num_rows();

		if ($numrows == 0) {
			$isActive = $mysqli->real_escape_string($_POST['isActive']);

			$stmt = $mysqli->prepare("
								UPDATE
									admins
								SET
									isActive = ?
								WHERE
									adminId = ?
			");
			$stmt->bind_param('ss',
							   $isActive,
							   $adminId
			);
			$stmt->execute();
			$msgBox = alertBox($adminAccountStatusUpdMsg, "<i class='fa fa-check-square-o-sign'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($accountStatusUpdFailedMsg, "<i class='icon-warning-sign'></i>", "warning");
		}
	}

	// Email Admin
	if (isset($_POST['submit']) && $_POST['submit'] == 'sendEmail') {
		// Validation
        if($_POST['emailSubject'] == "") {
            $msgBox = alertBox($emailSubjectReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['emailText'] == "") {
            $msgBox = alertBox($emailTextReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Send out the email in HTML
			$emailSubject = htmlentities(clean($_POST['emailSubject']));
			$emailText = htmlentities(clean($_POST['emailText']));
			$adminEmail = htmlentities(clean($_POST['adminEmail']));

			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$businessEmail = $set['businessEmail'];

			$subject = $emailSubject;

			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<hr>';
			$message .= '<p>'.$emailText.'</p>';
			$message .= '<hr>';
			$message .= '<p>Thank you,<br>'.$adminFirstName.' '.$adminLastName.'</p>';
			$message .= '<p>'.$siteName.'</p>';
			$message .= '</body></html>';

			$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
			$headers .= "Reply-To: ".$businessEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($adminEmail, $subject, $message, $headers)) {
				$msgBox = alertBox($adminEmailSentMsg, "<i class='fa fa-check-square-o'></i>", "success");
				// Clear the form of Values
				$_POST['emailSubject'] = $_POST['emailText'] = '';
			}
		}
	}

	// Get Admin Data
    $query = "SELECT
				adminId,
				CASE superuser
					WHEN 0 THEN 'Normal User'
					WHEN 1 THEN 'Superuser'
				END AS superuser,
				CASE adminRole
					WHEN 0 THEN 'Supervisor'
					WHEN 1 THEN 'Landlord'
					WHEN 2 THEN 'Service Technician'
				END AS adminRole,
				adminEmail,
				password,
				adminFirstName,
				adminLastName,
				adminPhone,
				adminAltPhone,
				adminAddress,
				adminAvatar,
				isActive,
				CASE isActive
					WHEN 0 THEN 'Inactive'
					WHEN 1 THEN 'Active'
				END AS active
			FROM
				admins
			WHERE adminId = ".$adminId;
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving Admin Data failed. ' . mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Decrypt data for display
	if ($row['adminAddress'] != '') { $adminAddress = decryptIt($row['adminAddress']); } else { $adminAddress = ''; }
	if ($row['adminPhone'] != '') { $adminPhone = decryptIt($row['adminPhone']); } else { $adminPhone = ''; }
	if ($row['adminAltPhone'] != '') { $adminAltPhone = decryptIt($row['adminAltPhone']); } else { $adminAltPhone = ''; }

	// Set the selected state of dropdowns
	if ($row['isActive'] == '1') { $active = 'selected'; } else { $active = ''; }
	if ($row['isActive'] == '0') { $inactive = 'selected'; } else { $inactive = ''; }

	if ($row['superuser'] == 'Superuser') { $isSuper = 'selected'; } else { $isSuper = ''; }
	if ($row['superuser'] == 'Normal User') { $notSuper = 'selected'; } else { $notSuper = ''; }

	if ($row['adminRole'] == 'Supervisor') { $supervisor = 'selected'; } else { $supervisor = ''; }
	if ($row['adminRole'] == 'Landlord') { $landlord = 'selected'; } else { $landlord = ''; }
	if ($row['adminRole'] == 'Service Technician') { $tech = 'selected'; } else { $tech = ''; }

	// Get Admin's Currently Assigned Property
    $sqlStmt = "SELECT
					tenants.tenantId,
					tenants.propertyId,
					tenants.leaseId,
					tenants.tenantEmail,
					tenants.tenantFirstName,
					tenants.tenantLastName,
					tenants.tenantPhone,
					tenants.isActive,
					properties.propertyId,
					properties.propertyName,
					properties.propertyRate,
					DATE_FORMAT(leases.leaseEnd,'%M %d, %Y') AS leaseEnd,
					admins.adminId
				FROM
					tenants
					LEFT JOIN properties ON tenants.propertyId = properties.propertyId
					LEFT JOIN leases ON tenants.leaseId = leases.leaseId
					LEFT JOIN assignedproperties ON tenants.propertyId = assignedproperties.propertyId
					LEFT JOIN admins ON assignedproperties.adminId = admins.adminId
				WHERE
					admins.adminId = ".$adminId." AND
					tenants.isActive = 1 AND
					tenants.leaseId != 0";
	$results = mysqli_query($mysqli, $sqlStmt) or die('Error, retrieving Current Tenant Data failed. ' . mysqli_error());
?>
<div class="row">
	<div class="col-md-8">
		<h3 class="danger"><?php echo $adminAccountH3; ?></h3>

		<?php if ($msgBox) { echo $msgBox; } ?>

		<p class="lead">
			<img alt="Admin Avatar" src="../<?php echo $avatarDir.$row['adminAvatar']; ?>" class="avatar" />
			<?php echo clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?>
			<?php if($row['superuser'] == 'Superuser') { ?>
				<span class="accountLevel">[<?php echo $row['superuser'].', '.$row['adminRole']; ?>]</span><br />
			<?php } else { ?>
				<span class="accountLevel">[<?php echo $row['adminRole']; ?>]</span><br />
			<?php } ?>
			<?php echo clean($row['adminEmail']); ?><br />
			<?php echo $adminPhone; ?>
		</p>
		<p class="lead"><?php echo clean($adminAddress); ?></p>

		<div class="clearfix"></div>
		<hr />

		<p class="lead"><?php echo $adminAccountQuip; ?></p>
		<p><?php echo $adminAccountStatusNote; ?></p>
	</div>

	<div class="col-md-4">
        <div class="list-group">
			<a href="" class="list-group-item danger"><?php echo $adminSidebarTitle; ?></a>
			<a data-toggle="modal" href="#profileAvatar" class="list-group-item"><?php echo $updAdminAvatarLi; ?></a>
			<a data-toggle="modal" href="#editType" class="list-group-item">Update Admin Account Type</a>
			<a data-toggle="modal" href="#personalInfo" class="list-group-item"><?php echo $updAdminInfoLi; ?></a>
			<a data-toggle="modal" href="#accountEmail" class="list-group-item"><?php echo $updAdminEmailLi; ?></a>
			<a data-toggle="modal" href="#editPassword" class="list-group-item"><?php echo $updAdminPasswordLi; ?></a>
			<a data-toggle="modal" href="#editStatus" class="list-group-item"><?php echo $updAdminStatusLi; ?></a>
        </div>

		<a data-toggle="modal" href="#emailAdmin" class="btn btn-block btn-danger btn-icon"><i class="fa fa-envelope"></i> <?php echo $tab_email.' '.clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?></a>

		<?php if ($row['isActive'] == '0') { ?>
			<div class="alertMsg warning"><i class="fa fa-warning"></i> <?php echo $adminIsInactiveMsg; ?></div>
		<?php } ?>
	</div>
</div>

<?php if(mysqli_num_rows($results) > 0) { ?>
	<hr />
	<h3 class="danger"><?php echo clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?><?php echo $adminAssignedPropertiesH3; ?></h3>

	<table id="responsiveTable" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_property; ?></th>
			<th><?php echo $tab_tenant; ?></th>
			<th><?php echo $tab_email; ?></th>
			<th><?php echo $tab_phone; ?></th>
			<th><?php echo $tab_monthlyRate; ?></th>
			<th><?php echo $tab_leaseEndsOn; ?></th>
		</tr>
		<tbody class="table-hover">
		<?php
			while ($rows = mysqli_fetch_assoc($results)) {
				// Decrypt Tenant data
				if ($rows['tenantPhone'] != '') { $tenantPhone = decryptIt($rows['tenantPhone']); } else { $tenantPhone = ''; }
				// Format the Amounts
				$propertyRate = $currencySym.format_amount($rows['propertyRate'], 2);
		?>
			<tr>
				<td><a href="index.php?action=propertyInfo&propertyId=<?php echo $rows['propertyId']; ?>"><?php echo clean($rows['propertyName']); ?></a></td>
				<td><a href="index.php?action=tenantInfo&tenantId=<?php echo $rows['tenantId']; ?>"><?php echo clean($rows['tenantFirstName']).' '.clean($rows['tenantLastName']); ?></a></td>
				<td><?php echo clean($rows['tenantEmail']); ?></td>
				<td><?php echo $tenantPhone; ?></td>
				<td><?php echo $propertyRate; ?></td>
				<td><?php echo $rows['leaseEnd']; ?></td>
			</tr>
		<?php }	?>
		</tbody>
	</table>
<?php } ?>

<!-- -- REMOVE PROFILE AVATAR MODEL -- -->
<div class="modal fade" id="profileAvatar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $removeAvatarModalTitle; ?></h4>
			</div>

			<?php if ($row['adminAvatar'] != 'adminDefault.png') { ?>
				<div class="modal-body">
					<img alt="" src="../<?php echo $avatarDir.$row['adminAvatar']; ?>" class="avatar" />
					<p><?php echo $removeAvatarModalQuip; ?></p>
				</div>
				<div class="clearfix"></div>
				<div class="modal-footer">
					<a data-toggle="modal" href="#deleteAvatar" class="btn btn-danger btn-icon tool-tip" Title="Delete the Admin's Avatar Image"><i class="fa fa-times"></i> <?php echo $removeAvatarBtn; ?></a>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			<?php } else { ?>
				<div class="modal-body">
					<p class="lead"><?php echo $noAdminAvatar; ?></p>
				</div>
			<?php } ?>
        </div>
    </div>
</div>

<!-- -- REMOVE PROFILE AVATAR CONFIRMATION MODEL -- -->
<div class="modal fade" id="deleteAvatar" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="" method="post">
				<div class="modal-body">
					<p class="lead"><?php echo $removeAvatarConfModal.' '.clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?>?</p>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="removeAvatar" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- -- ACCOUNT TYPE MODEL -- -->
<div class="modal fade" id="editType" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $changeAdminTypeModalTitle; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="superuser"><?php echo $adminLevelField; ?></label>
						<select class="form-control" name="superuser">
							<option value="0" <?php echo $notSuper; ?>><?php echo $superuserNo; ?></option>
							<option value="1" <?php echo $isSuper; ?>><?php echo $superuserYes; ?></option>
						</select>
						<span class="help-block"><?php echo $adminLevelHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="adminRole"><?php echo $adminRoleField; ?></label>
						<select class="form-control" name="adminRole">
							<option value="0" <?php echo $supervisor; ?>><?php echo $adminRoleAdmin; ?></option>
							<option value="1" <?php echo $landlord; ?>><?php echo $adminRoleLandlord; ?></option>
							<option value="2" <?php echo $tech; ?>><?php echo $adminRoleTech; ?></option>
						</select>
						<span class="help-block"><?php echo $selectRoleHelper; ?></span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="editType" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- -- UPDATE PERSONAL INFO MODEL -- -->
<div class="modal fade" id="personalInfo" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $updateAdminPersInfoTitle; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
                        <label for="adminFirstName"><?php echo $firstNameField; ?></label>
                        <input type="text" class="form-control" name="adminFirstName" value="<?php echo clean($row['adminFirstName']); ?>" />
                    </div>
                    <div class="form-group">
                        <label for="adminLastName"><?php echo $lastNameField; ?></label>
                        <input type="text" class="form-control" name="adminLastName" value="<?php echo clean($row['adminLastName']); ?>" />
                    </div>
					<div class="form-group">
                        <label for="adminPhone"><?php echo $phoneField; ?></label>
                        <input type="text" class="form-control" name="adminPhone" id="adminPhone" value="<?php echo $adminPhone; ?>" />
                    </div>
					<div class="form-group">
                        <label for="adminAltPhone"><?php echo $altPhoneField; ?></label>
                        <input type="text" class="form-control" name="adminAltPhone" id="adminAltPhone" value="<?php echo $adminAltPhone; ?>" />
                    </div>
					<div class="form-group">
						<label for="adminAddress"><?php echo $addressField; ?></label>
						<textarea class="form-control" name="adminAddress" rows="3"><?php echo $adminAddress; ?></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="updateInfo" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- -- UPDATE ACCOUNT EMAIL MODEL -- -->
<div class="modal fade" id="accountEmail" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $updateAdminEmailTitle; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
                        <label for="adminEmail"><?php echo $emailAddressField; ?></label>
                        <input type="text" class="form-control" name="adminEmail" value="<?php echo clean($row['adminEmail']); ?>" />
						<span class="help-block"><?php echo $adminEmailHelper; ?></span>
                    </div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="updateEmail" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- -- UPDATE ACCOUNT PASSWORD MODEL -- -->
<div class="modal fade" id="editPassword" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $changeAdminPasswordTitle; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
                    <div class="form-group">
                        <label for="password"><?php echo $newPassField; ?></label>
                        <input type="text" class="form-control" name="password" id="password" value="" />
						<span class="help-block"><?php echo $adminNewPasswordHelper; ?></span>
                    </div>
					<div class="form-group">
                        <label for="password_r"><?php echo $confirmNewPassField; ?></label>
                        <input type="text" class="form-control" name="password_r" id="password_r" value="" />
						<span class="help-block"><?php echo $repeatPasswordHelper; ?></span>
                    </div>
				</div>
				<div class="modal-footer">
					<input name="passwordOld" id="passwordOld" value="<?php echo $row['password']; ?>" type="hidden">
					<button type="input" name="submit" value="updatePass" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- -- ACCOUNT STATUS MODEL -- -->
<div class="modal fade" id="editStatus" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $changeAdminStatusTitle; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<p><?php echo $changeAdminStatusQuip; ?></p>
					<div class="form-group">
						<label for="isActive"><?php echo $changeAdminStatusField; ?></label>
						<select class="form-control" id="isActive" name="isActive">
							<option value="0" <?php echo $inactive; ?>><?php echo $statusOptionInactive; ?></option>
							<option value="1" <?php echo $active; ?>><?php echo $statusOptionActive; ?></option>
						</select>
						<span class="help-block"><?php echo $changeAdminStatusHelper; ?></span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="editStatus" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- -- Email Admin Model -- -->
<div class="modal fade" id="emailAdmin" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $sendEmailModalTitle.' '.clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="emailSubject"><?php echo $subjectField; ?></label>
						<input type="text" class="form-control" name="emailSubject" value="<?php echo isset($_POST['emailSubject']) ? $_POST['emailSubject'] : ''; ?>" />
					</div>
					<div class="form-group">
						<label for="emailText"><?php echo $emailTextField; ?></label>
						<textarea class="form-control" name="emailText" rows="8"><?php echo isset($_POST['emailText']) ? $_POST['emailText'] : ''; ?></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<input name="adminEmail" value="<?php echo clean($row['adminEmail']); ?>" type="hidden">
					<button type="input" name="submit" value="sendEmail" class="btn btn-success btn-icon"><i class="fa fa-envelope"></i> <?php echo $sendEmailBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>