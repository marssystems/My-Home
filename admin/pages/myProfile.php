<?php
	$jsFile = 'myProfile';

	// Get Avatar Folder from Site Settings
	$avatarDir = $set['avatarFolder'];

	// Get the Avatar file types allowed from Site Settings
	$avatarTypes = $set['avatarTypes'];
	// Replce the commas with a comma space
	$avatarTypesAllowed = preg_replace('/,/', ', ', $avatarTypes);

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
			$defaultAvatar = 'adminDefault.png';
			$stmt = $mysqli->prepare("
								UPDATE
									admins
								SET
									adminAvatar = ?
								WHERE
									adminId = ?");
			$stmt->bind_param('ss',
							   $defaultAvatar,
							   $adminId);
			$stmt->execute();
			$msgBox = alertBox($avatarRemovedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($avatarRemoveErrorMsg, "<i class='fa fa-minus-square-o'></i>", "warning");
		}
	}

	// Upload Avatar Image
	if (isset($_POST['submit']) && $_POST['submit'] == 'updateAvatar') {
		// Get the File Types allowed
		$fileExt = $set['avatarTypes'];
		$allowed = preg_replace('/,/', ', ', $fileExt); // Replce the commas with a comma space (, )
		$ftypes = array($fileExt);
		$ftypes_data = explode( ',', $fileExt );

		// Check file type
		$ext = substr(strrchr(basename($_FILES['file']['name']), '.'), 1);
		if (!in_array($ext, $ftypes_data)) {
			$msgBox = alertBox($avatarNotAcceptedMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			// Rename the Admin's Avatar
			$avatarName = htmlentities($_POST['avatarName']);

			// Replace any spaces with an underscore
			// And set to all lowercase
			$newName = str_replace(' ', '_', $avatarName);
			$fileName = strtolower($newName);
			$fullName = $fileName;

			// set the upload path
			$avatarUrl = basename($_FILES['file']['name']);

			// Get the files original Ext
			$extension = pathinfo($avatarUrl, PATHINFO_EXTENSION);

			// Set the files name to the name set in the form
			// And add the original Ext
			$newAvatarName = $fullName.'.'.$extension;
			$movePath = '../'.$avatarDir.$newAvatarName;

			$stmt = $mysqli->prepare("
								UPDATE
									admins
								SET
									adminAvatar = ?
								WHERE
									adminId = ?");
			$stmt->bind_param('ss',
							   $newAvatarName,
							   $adminId);

			if (move_uploaded_file($_FILES['file']['tmp_name'], $movePath)) {
				$stmt->execute();
				$msgBox = alertBox($avatarUploadedMsg, "<i class='fa fa-check-square-o'></i>", "success");
				$stmt->close();
			} else {
				$msgBox = alertBox($avatarUploadErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
			}
		}
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
									adminId = ?");
			$stmt->bind_param('ssssss',
								$adminFirstName,
								$adminLastName,
								$adminAddress,
								$adminPhone,
								$adminAltPhone,
								$adminId);
			$stmt->execute();
			$msgBox = alertBox($personalInfoUpdatedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			$stmt->close();
		}
	}

	// Update Account Email
	if (isset($_POST['submit']) && $_POST['submit'] == 'updateEmail') {
		if($_POST['adminEmail'] == "") {
            $msgBox = alertBox($emailReqVal, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$adminEmail = $mysqli->real_escape_string($_POST['adminEmail']);

			$stmt = $mysqli->prepare("
								UPDATE
									admins
								SET
									adminEmail = ?
								WHERE
									adminId = ?");
			$stmt->bind_param('ss',
							   $adminEmail,
							   $adminId);
			$stmt->execute();
			$msgBox = alertBox($emailAddyUpdatedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			$stmt->close();
		}
	}

	// Update Password
	if (isset($_POST['submit']) && $_POST['submit'] == 'updatePass') {
		$currentPass = md5($_POST['currentpass']);
		if($_POST['currentpass'] == '') {
			$msgBox = alertBox($currentPassReqVal, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password'] == '') {
			$msgBox = alertBox($newPassReqVal, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password_r'] == '') {
			$msgBox = alertBox($typePassAgainReqVal, "<i class='fa fa-times-circle'></i>", "danger");
		} else if ($currentPass != $_POST['passwordOld']) {
			$msgBox = alertBox($currentPassInvalidReqVal, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password'] != $_POST['password_r']) {
            $msgBox = alertBox($newPassNotMatchReqVal, "<i class='fa fa-times-circle'></i>", "danger");
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
									adminId = ?");
			$stmt->bind_param('ss',
							   $password,
							   $adminId);
			$stmt->execute();
			$msgBox = alertBox($newPassSavedMsg, "<i class='icon-check-sign'></i>", "success");
			$stmt->close();
		}
	}

	// Get Admin Data
    $query = "SELECT
				adminId,
				superuser,
				adminRole,
				adminEmail,
                password,
				adminFirstName,
				adminLastName,
				adminPhone,
				adminAltPhone,
				adminAddress,
				adminAvatar AS avatar,
				createDate
			FROM
				admins
			WHERE adminId = ".$adminId;
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving Admin Data failed. ' . mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Decrypt data for display
	if ($row['adminAddress'] != '') { $adminAddress = decryptIt($row['adminAddress']); } else { $adminAddress = ''; }
	if ($row['adminPhone'] != '') { $adminPhone = decryptIt($row['adminPhone']); } else { $adminPhone = ''; }
	if ($row['adminAltPhone'] != '') { $adminAltPhone = decryptIt($row['adminAltPhone']); } else { $adminAltPhone = ''; }
?>
<div class="row">
	<div class="col-md-8">
		<h3 class="danger"><?php echo $myProfileH3; ?></h3>

		<?php if ($msgBox) { echo $msgBox; } ?>

		<p class="lead">
			<img alt="Admin Avatar" src="../<?php echo $avatarDir.$row['avatar']; ?>" class="avatar" />
			<?php echo $adminFirstName.' '.$adminLastName; ?><br />
			<?php echo $adminEmail; ?><br />
			<?php echo $adminPhone; ?>
		</p>
		<p class="lead"><?php echo clean($adminAddress); ?></p>
		<p><small class="light"><?php echo $myProfileQuip; ?></small></p>
	</div>

	<div class="col-md-4">
        <div class="list-group">
			<li class="list-group-item danger"><?php echo $listGroupAdminTitle; ?></li>
			<a data-toggle="modal" href="#profileAvatar" class="list-group-item"><?php echo $listGroupAdminAvatarLink; ?></a>
			<a data-toggle="modal" href="#personalInfo" class="list-group-item"><?php echo $listGroupAdminUpdateInfo; ?></a>
			<a data-toggle="modal" href="#accountEmail" class="list-group-item"><?php echo $listGroupAdminUpdateEmail; ?></a>
			<a data-toggle="modal" href="#editPassword" class="list-group-item"><?php echo $listGroupAdminUpdatePassword; ?></a>
        </div>
	</div>
</div>

<h3 class="danger"><?php echo $safePersonalInfoH3; ?></h3>
<p><?php echo $safePersonalInfo; ?></p>

<!-- -- Update Profile Avatar Model -- -->
<div class="modal fade" id="profileAvatar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $profileAvatarTitle; ?></h4>
			</div>

			<?php if ($row['avatar'] != 'adminDefault.png') { ?>
				<div class="modal-body">
					<img alt="" src="../<?php echo $avatarDir.$row['avatar']; ?>" class="avatar" />
					<p><?php echo $profileAvatarQuip; ?></p>
				</div>
				<div class="clearfix"></div>
				<div class="modal-footer">
					<a data-toggle="modal" href="#deleteAvatar" class="btn btn-danger btn-icon"><i class="fa fa-times"></i> <?php echo $removeAvatarBtn; ?></a>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-remove-sign"></i> <?php echo $cancelBtn; ?></button>
				</div>
			<?php } ?>

			<?php if ($row['avatar'] == 'adminDefault.png') { ?>
				<form enctype="multipart/form-data" action="" method="post">
					<div class="modal-body">
						<p class="lead"><?php echo $newAvatarUpload; ?></p>
						<p><?php echo $allowedAvatarTypesQuip.' '.$avatarTypesAllowed; ?></p>

						<div class="form-group">
							<label for="file"><?php echo $selectNewAvatar; ?></label>
							<input type="file" id="file" name="file">
							<p class="help-block"><?php echo $avatarMaxHight; ?></p>
						</div>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="avatarName" id="avatarName" value="<?php echo $row['adminFirstName'].'_'.$row['adminLastName']; ?>" />
						<button type="input" name="submit" value="updateAvatar" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $uploadBtn; ?></button>
						<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			<?php } ?>
        </div>
    </div>
</div>

<!-- -- Remove Profile Avatar Confirmation Model -- -->
<div class="modal fade" id="deleteAvatar" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="" method="post">
				<div class="modal-body">
					<p class="lead"><?php echo $removeAvatarConf; ?></p>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="removeAvatar" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- -- Update Personal Info Model -- -->
<div class="modal fade" id="personalInfo" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $personalInfoModalTitle; ?></h4>
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
					<button type="input" name="submit" value="updateInfo" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $updateBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- -- Update Account Email Model -- -->
<div class="modal fade" id="accountEmail" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $updateEmailModalTitle; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
                        <label for="adminEmail"><?php echo $emailAddressField; ?></label>
                        <input type="text" class="form-control" name="adminEmail" value="<?php echo clean($row['adminEmail']); ?>" />
						<span class="help-block"><?php echo $accountEmailHelper; ?></span>
                    </div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="updateEmail" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $updateBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- -- Update Account Password Model -- -->
<div class="modal fade" id="editPassword" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $updatePasswordModalTitle; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
                        <label for="currentpass"><?php echo $currentPassField; ?></label>
                        <input type="text" class="form-control" name="currentpass" value="" />
						<span class="help-block"><?php echo $currentPasswordHelper; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="password"><?php echo $newPassField; ?></label>
                        <input type="text" class="form-control" name="password" value="" />
						<span class="help-block"><?php echo $newPassword2Helper; ?></span>
                    </div>
					<div class="form-group">
                        <label for="password_r"><?php echo $confirmNewPassField; ?></label>
                        <input type="text" class="form-control" name="password_r" value="" />
						<span class="help-block"><?php echo $rnewPasswordHelper; ?></span>
                    </div>
				</div>
				<div class="modal-footer">
					<input name="passwordOld" id="passwordOld" value="<?php echo $row['password']; ?>" type="hidden">
					<button type="input" name="submit" value="updatePass" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $updateBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>