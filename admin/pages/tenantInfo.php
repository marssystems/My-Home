<?php
	$tenantId = $_GET['tenantId'];
	$stacktable = 'true';
	$jsFile = 'tenantInfo';

    // Get the Max Upload Size allowed
    $maxUpload = (int)(ini_get('upload_max_filesize'));

	// Get Avatar Folder from Site Settings
	$avatarDir = $set['avatarFolder'];

	// Get Documents Folder from Site Settings
	$docUploadPath = $set['tenantDocsPath'];

	// Get the file types allowed from Site Settings
	$fileTypesAllowed = $set['fileTypesAllowed'];
	// Replace the commas with a comma space
	$uploadTypesAllowed = preg_replace('/,/', ', ', $fileTypesAllowed);

	// Remove Avatar Image
    if (isset($_POST['submit']) && $_POST['submit'] == 'removeAvatar') {
		// Get the Tenant's avatar url
		$sql = "SELECT tenantAvatar FROM tenants WHERE tenantId = ".$tenantId;
		$result = mysqli_query($mysqli, $sql) or die(mysqli_error());
		$r = mysqli_fetch_assoc($result);
		$avatarName = $r['tenantAvatar'];

		$filePath = '../'.$avatarDir.$avatarName;
		// Delete the Tenant's image from the server
		if (file_exists($filePath)) {
			unlink($filePath);

			// Update the Tenant record
			$tenantAvatar = 'tenantDefault.png';
			$stmt = $mysqli->prepare("
								UPDATE
									tenants
								SET
									tenantAvatar = ?
								WHERE
									tenantId = ?
			");
			$stmt->bind_param('ss',
							   $tenantAvatar,
							   $tenantId
			);
			$stmt->execute();
			$msgBox = alertBox($avatarRemovedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($avatarRemoveErrorMsg, "<i class='fa fa-warning'></i>", "warning");
		}
	}

	// Update Personal Info
	if (isset($_POST['submit']) && $_POST['submit'] == 'updateInfo') {
		// Validation
        if($_POST['tenantFirstName'] == "") {
            $msgBox = alertBox($firstNameReqVal, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['tenantLastName'] == "") {
            $msgBox = alertBox($lastNameReqVal, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$tenantFirstName = $mysqli->real_escape_string($_POST['tenantFirstName']);
			$tenantLastName = $mysqli->real_escape_string($_POST['tenantLastName']);

			// Encrypt sensitive data
			$tenantAddress = encryptIt($_POST['tenantAddress']);
			$tenantPhone = encryptIt($_POST['tenantPhone']);
			$tenantAltPhone = encryptIt($_POST['tenantAltPhone']);

			$stmt = $mysqli->prepare("
								UPDATE
									tenants
								SET
									tenantFirstName = ?,
									tenantLastName = ?,
									tenantAddress = ?,
									tenantPhone = ?,
									tenantAltPhone = ?
								WHERE
									tenantId = ?
			");
			$stmt->bind_param('ssssss',
								$tenantFirstName,
								$tenantLastName,
								$tenantAddress,
								$tenantPhone,
								$tenantAltPhone,
								$tenantId
			);
			$stmt->execute();
			$msgBox = alertBox($tenantPersonalInfoUpdMsg, "<i class='fa fa-check-square-o'></i>", "success");
			$stmt->close();
		}
	}

	// Update Tenant Notes
	if (isset($_POST['submit']) && $_POST['submit'] == 'updateNotes') {
		$tenantNotes = htmlentities($_POST['tenantNotes']);

		$stmt = $mysqli->prepare("
							UPDATE
								tenants
							SET
								tenantNotes = ?
							WHERE
								tenantId = ?
		");
		$stmt->bind_param('ss',
						   $tenantNotes,
						   $tenantId
		);
		$stmt->execute();
		$msgBox = alertBox($tenantNotesUpdatedMsg, "<i class='fa fa-check-square-o'></i>", "success");
		$stmt->close();
	}

	// Update Account Email
	if (isset($_POST['submit']) && $_POST['submit'] == 'updateEmail') {
		if($_POST['tenantEmail'] == "") {
            $msgBox = alertBox($tenantEmailReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$tenantEmail = $mysqli->real_escape_string($_POST['tenantEmail']);

			$stmt = $mysqli->prepare("
								UPDATE
									tenants
								SET
									tenantEmail = ?
								WHERE
									tenantId = ?
			");
			$stmt->bind_param('ss',
							   $tenantEmail,
							   $tenantId
			);
			$stmt->execute();
			$msgBox = alertBox($tenantEmailUpdatedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			$stmt->close();
		}
	}

	// Update Password
	if (isset($_POST['submit']) && $_POST['submit'] == 'updatePass') {
		if($_POST['password'] == '') {
			$msgBox = alertBox($tenantsNewPassReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password_r'] == '') {
			$msgBox = alertBox($tenantRepeatPassReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password'] != $_POST['password_r']) {
            $msgBox = alertBox($passwordMismatch, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			if(isset($_POST['password']) && $_POST['password'] != "") {
				$password = md5($_POST['password']);
			} else {
				$password = $_POST['passwordOld'];
			}

			$stmt = $mysqli->prepare("
								UPDATE
									tenants
								SET
									password = ?
								WHERE
									tenantId = ?
			");
			$stmt->bind_param('ss',
							   $password,
							   $tenantId
			);
			$stmt->execute();
			$msgBox = alertBox($passwordUpdatedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			$stmt->close();
		}
	}

	// Update Account Status
	if (isset($_POST['submit']) && $_POST['submit'] == 'editStatus') {

		// Check for a Leased Property
		$hasActive = '';
		$check = $mysqli->query("SELECT leaseId FROM tenants WHERE tenantId = ".$tenantId." AND leaseId != '0'");
		if ($check->num_rows) { $hasActive = 'true'; }

		// If an Active Lease is found
		if ($hasActive != '') {
			$msgBox = alertBox($activeLeaseFoundMsg, "<i class='fa fa-warning'></i>", "warning");
		} else {
			$isActive = $mysqli->real_escape_string($_POST['isActive']);
			$isArchived = $mysqli->real_escape_string($_POST['isArchived']);

			if ($isArchived == '1') {
				$today = date("Y-m-d");
			} else {
				$today = '';
			}

			$stmt = $mysqli->prepare("
								UPDATE
									tenants
								SET
									isActive = ?,
									isArchived = ?,
									archivedDate = ?
								WHERE
									tenantId = ?
			");
			$stmt->bind_param('ssss',
							   $isActive,
							   $isArchived,
							   $today,
							   $tenantId
			);
			$stmt->execute();
			$msgBox = alertBox($tenantAccountStatusUpdMsg, "<i class='fa fa-check-square-o'></i>", "success");
			$stmt->close();
		}

	}

	// Email Tenant
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
			$tenantEmail = htmlentities(clean($_POST['tenantEmail']));

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

			if (mail($tenantEmail, $subject, $message, $headers)) {
				$msgBox = alertBox($tenantEmailSentMsg, "<i class='fa fa-check-square-o'></i>", "success");
				// Clear the form of Values
				$_POST['emailSubject'] = $_POST['emailText'] = '';
			}

		}
	}

	// Upload Tenant Document
	if (isset($_POST['submit']) && $_POST['submit'] == 'Upload Document') {
		// Validation
        if ($_POST['docTitle'] == "") {
            $msgBox = alertBox($tenantDocTitleReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if ($_POST['docDesc'] == "") {
            $msgBox = alertBox($tenantDocDescReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Get the File Types allowed
			$fileExt = $set['fileTypesAllowed'];
			$allowed = preg_replace('/,/', ', ', $fileExt); // Replce the commas with a comma space (, )
			$ftypes = array($fileExt);
			$ftypes_data = explode( ',', $fileExt );

			// Check file type
			$ext = substr(strrchr(basename($_FILES['file']['name']), '.'), 1);
			if (!in_array($ext, $ftypes_data)) {
				$msgBox = alertBox($fileNotAcceptedMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else {
				// Get the Tenants Document Folder
				$tenantDocsFolder = $mysqli->real_escape_string($_POST['tenantDocsFolder']);

				// Rename the Document
				$documentName = htmlentities($_POST['docTitle']);

				// Replace any spaces with an underscore
				// And set to all lowercase
				$newName = str_replace(' ', '_', $documentName);
				$fileName = strtolower($newName);
				$fullName = $fileName;

				// set the upload path
				$documentUrl = basename($_FILES['file']['name']);

				// Get the files original Ext
				$extension = pathinfo($documentUrl, PATHINFO_EXTENSION);

				// Set the files name to the name set in the form
				// And add the original Ext
				$newDocumentName = $fullName.'.'.$extension;
				$movePath = '../'.$docUploadPath.$tenantDocsFolder.'/'.$newDocumentName;

				$docDesc = htmlentities($_POST['docDesc']);

				$stmt = $mysqli->prepare("
									INSERT INTO
										tenantdocs(
											tenantId,
											adminId,
											docTitle,
											docDesc,
											docUrl
										) VALUES (
											?,
											?,
											?,
											?,
											?
										)");
				$stmt->bind_param('sssss',
					$tenantId,
					$adminId,
					$documentName,
					$docDesc,
					$newDocumentName
				);

				if (move_uploaded_file($_FILES['file']['tmp_name'], $movePath)) {
					$stmt->execute();
					$msgBox = alertBox($documentUploadedMsg, "<i class='fa fa-check-square-o'></i>", "success");
					$stmt->close();
				} else {
					$msgBox = alertBox($documentUploadErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
				}
			}
		}
	}

	// Delete Tenant Document
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteDocument') {
		$docId = $mysqli->real_escape_string($_POST['deleteId']);

		// Get the document url
		$sql = "SELECT docUrl FROM tenantdocs WHERE docId = ".$docId;
		$result = mysqli_query($mysqli, $sql) or die(mysqli_error());
		$r = mysqli_fetch_assoc($result);
		$docUrl = $r['docUrl'];

		// Get the Properties Picture Folder
		$tenantDocsFolder = $mysqli->real_escape_string($_POST['tenantDocsFolder']);
		$filePath = '../'.$docUploadPath.$tenantDocsFolder.'/'.$docUrl;

		// Delete the picture from the server
		if (file_exists($filePath)) {
			unlink($filePath);

			// Delete the record
			$stmt = $mysqli->prepare("DELETE FROM tenantdocs WHERE docId = ?");
			$stmt->bind_param('s', $docId);
			$stmt->execute();

			$msgBox = alertBox($documentDeletedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($documentRemoveErrorMsg, "<i class='fa fa-minus-square-o'></i>", "warning");
		}
    }

	// Get Tenant Data
    $query = "SELECT
				tenantId,
                propertyId,
				leaseId,
				tenantDocsFolder,
				tenantEmail,
				password,
				tenantFirstName,
				tenantLastName,
				tenantAddress,
				tenantPhone,
				tenantAltPhone,
				tenantNotes,
				tenantPets,
				tenantAvatar,
				createDate,
				DATE_FORMAT(createDate,'%M %d, %Y') AS dateCreated,
				isActive,
				CASE isActive
					WHEN 0 THEN 'Inactive'
					WHEN 1 THEN 'Active'
				END AS active,
				isArchived,
				CASE isArchived
					WHEN 0 THEN 'Current'
					WHEN 1 THEN 'Archived'
				END AS archived,
				archivedDate,
				DATE_FORMAT(archivedDate,'%M %d, %Y') AS dateArchived
			FROM
				tenants
			WHERE tenantId = ".$tenantId;
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving Tenant Data failed. ' . mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Decrypt data for display
	if ($row['tenantAddress'] != '') { $tenantAddress = decryptIt($row['tenantAddress']); } else { $tenantAddress = ''; }
	if ($row['tenantPhone'] != '') { $tenantPhone = decryptIt($row['tenantPhone']); } else { $tenantPhone = ''; }
	if ($row['tenantAltPhone'] != '') { $tenantAltPhone = decryptIt($row['tenantAltPhone']); } else { $tenantAltPhone = ''; }

	if ($row['isActive'] == '1') { $active = 'selected'; } else { $active = ''; }
	if ($row['isActive'] == '0') { $inactive = 'selected'; } else { $inactive = ''; }

	if ($row['isArchived'] == '1') { $yes = 'selected'; } else { $yes = ''; }
	if ($row['isArchived'] == '0') { $no = 'selected'; } else { $no = ''; }

	// Get Property Data
	if ($superuser == '1') {
		// Get All
		$stmt  = "SELECT
					properties.propertyId,
					properties.createdBy,
					properties.propertyName,
					properties.propertyRate,
					properties.latePenalty,
					properties.isArchived,
					tenants.tenantId,
					leases.leaseId,
					leases.leaseTerm,
					DATE_FORMAT(leases.leaseStart,'%M %d, %Y') AS leaseStart,
					DATE_FORMAT(leases.leaseEnd,'%M %d, %Y') AS leaseEnd,
					assignedproperties.adminId,
					admins.adminFirstName,
					admins.adminLastName
				FROM
					properties
					LEFT JOIN tenants ON properties.propertyId = tenants.propertyId
					LEFT JOIN leases ON properties.propertyId = leases.propertyId
					LEFT JOIN assignedproperties ON properties.propertyId = assignedproperties.propertyId
					LEFT JOIN admins ON assignedproperties.adminId = admins.adminId
				WHERE
					tenants.tenantId = ".$tenantId." AND
					properties.isArchived = 0";
		$results = mysqli_query($mysqli, $stmt) or die('Error, retrieving Property Data failed. ' . mysqli_error());
		$rows = mysqli_fetch_assoc($results);
	} else {
		// Get Properties Assigned to the Admin/Landlord
		$stmt  = "SELECT
					properties.propertyId,
					properties.createdBy,
					properties.propertyName,
					properties.propertyRate,
					properties.latePenalty,
					properties.isArchived,
					tenants.tenantId,
					leases.leaseId,
					leases.leaseTerm,
					DATE_FORMAT(leases.leaseStart,'%M %d, %Y') AS leaseStart,
					DATE_FORMAT(leases.leaseEnd,'%M %d, %Y') AS leaseEnd,
					assignedproperties.adminId
				FROM
					properties
					LEFT JOIN tenants ON properties.propertyId = tenants.propertyId
					LEFT JOIN leases ON properties.propertyId = leases.propertyId
					LEFT JOIN assignedproperties ON properties.propertyId = assignedproperties.propertyId
				WHERE
					tenants.tenantId = ".$tenantId." AND
					properties.isArchived = 0 AND
					assignedproperties.adminId = ".$adminId;
		$results = mysqli_query($mysqli, $stmt) or die('Error, retrieving Property Data failed. ' . mysqli_error());
		$rows = mysqli_fetch_assoc($results);
	}

	// Format the Amounts
	$propertyRate = $currencySym.format_amount($rows['propertyRate'], 2);
	$latePenalty = $currencySym.format_amount($rows['latePenalty'], 2);

	// Get Tenant Documents
    $sqlStmt = "SELECT
					tenantdocs.docId,
					tenantdocs.tenantId,
					tenantdocs.adminId,
					tenantdocs.docTitle,
					tenantdocs.docDesc,
					tenantdocs.docUrl,
					DATE_FORMAT(tenantdocs.docDate,'%M %d, %Y') AS docDate,
					admins.adminFirstName,
					admins.adminLastName
				FROM
					tenantdocs
					LEFT JOIN admins ON tenantdocs.adminId = admins.adminId
				WHERE tenantdocs.tenantId = ".$tenantId."
				ORDER BY tenantdocs.docId";
    $result = mysqli_query($mysqli, $sqlStmt) or die('Error, retrieving Tenant Documents failed. ' . mysqli_error());
?>
<div class="row">
	<div class="col-md-8">
		<h3 class="primary"><?php echo $tenantAccountH3; ?></h3>

		<?php if ($msgBox) { echo $msgBox; } ?>

		<p class="lead">
			<img alt="Tenant Avatar" src="../<?php echo $avatarDir.$row['tenantAvatar']; ?>" class="avatar" />
			<?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?><br />
			<?php echo clean($row['tenantEmail']); ?><br />
			<?php echo $tenantPhone; ?>
		</p>
		<p class="lead"><?php echo clean($tenantAddress); ?></p>
		<div class="well well-sm padTop">
			<?php echo nl2br(clean($row['tenantNotes'])); ?>
		</div>

		<div class="clearfix"></div>
		<hr />

		<p class="lead"><?php echo $tenantAccountQuip; ?></p>
		<p><?php echo $tenantAccountStatusNote; ?></p>
	</div>

	<div class="col-md-4">
        <div class="list-group">
			<a href="" class="list-group-item primary"><?php echo $tenantSidebarTitle; ?></a>
			<a data-toggle="modal" href="#profileAvatar" class="list-group-item"><?php echo $updTenantAvatarLi; ?></a>
			<a data-toggle="modal" href="#personalInfo" class="list-group-item"><?php echo $updTenantInfoLi; ?></a>
			<a data-toggle="modal" href="#tenantNotes" class="list-group-item"><?php echo $updTenantNotesLi; ?></a>
			<a data-toggle="modal" href="#accountEmail" class="list-group-item"><?php echo $updTenantEmailLi; ?></a>
			<a data-toggle="modal" href="#editPassword" class="list-group-item"><?php echo $updTenantPasswordLi; ?></a>
			<a data-toggle="modal" href="#editStatus" class="list-group-item"><?php echo $updTenantStatusLi; ?></a>
        </div>

		<a data-toggle="modal" href="#emailTenant" class="btn btn-block btn-primary btn-icon"><i class="fa fa-envelope"></i> <?php echo $tab_email.' '.clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?></a>

		<?php if ($row['isArchived'] == '1') { ?>
			<div class="alertMsg info"><i class="fa fa-archive"></i> <?php echo $tenantIsArchivedMsg; ?></div>
		<?php }
			  if ($row['isActive'] == '0') { ?>
			<div class="alertMsg warning"><i class="fa fa-warning"></i> <?php echo $tenantIsInactiveMsg; ?></div>
		<?php } ?>
	</div>
</div>

<hr />

<h3 class="info"><?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']).$tenantsLeasedPropH3; ?></h3>

<?php if(mysqli_num_rows($results) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']).' '.$tenantNoLeasedPropMsg; ?>
	</div>
<?php } else { ?>
	<div class="row">
		<div class="col-md-6">
			<ul class="list-group">
				<li class="list-group-item"><?php echo $tab_property; ?>: <a href="index.php?action=propertyInfo&propertyId=<?php echo $rows['propertyId']; ?>"><?php echo clean($rows['propertyName']); ?></a></li>
				<li class="list-group-item"><?php echo $tab_rentAmount; ?>: <?php echo $propertyRate; ?></li>
				<li class="list-group-item"><?php echo $tab_lateFee; ?>: <?php echo $latePenalty; ?></li>
			</ul>
		</div>
		<div class="col-md-6">
			<ul class="list-group">
				<li class="list-group-item"><?php echo $tab_leaseTerm; ?>: <?php echo $rows['leaseTerm']; ?></li>
				<li class="list-group-item"><?php echo $tab_leaseStartsOn; ?>: <?php echo $rows['leaseStart']; ?></li>
				<li class="list-group-item"><?php echo $tab_leaseEndsOn; ?>: <?php echo $rows['leaseEnd']; ?></li>
			</ul>
		</div>
	</div>
<?php } ?>

<hr />

<h3 class="primary"><?php echo $tenantDocumentsH3.' '.clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?></h3>
<div class="row">
	<div class="col-md-10">
		<p class="lead"><?php echo $tenantDocQuip; ?></p>
	</div>
	<div class="col-md-2">
		<span class="floatRight">
			<a data-toggle="modal" href="#uploadDocuments" class="btn btn-primary btn-icon"><i class="fa fa-upload"></i> <?php echo $uploadTenantDocBtn; ?></a>
		</span>
	</div>
</div>

<?php if(mysqli_num_rows($result) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noDocsFoundMsg; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTableTwo" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_documentName; ?></th>
			<th><?php echo $tab_description; ?></th>
			<th><?php echo $tab_uploadedBy; ?></th>
			<th><?php echo $tab_dateUploaded; ?></th>
			<th></th>
		</tr>
		<tbody class="table-hover">
		<?php while ($cols = mysqli_fetch_assoc($result)) { ?>
			<tr>
				<td class="tool-tip" title="View Document">
					<a href="index.php?action=viewDocument&docId=<?php echo $cols['docId']; ?>"><?php echo clean($cols['docTitle']); ?></a>
				</td>
				<td><?php echo clean($cols['docDesc']); ?></td>
				<td><?php echo clean($cols['adminFirstName']).' '.clean($cols['adminLastName']); ?></td>
				<td><?php echo $cols['docDate']; ?></td>
				<td class="tool-tip" title="Delete Document">
					<a data-toggle="modal" href="#deleteDoc<?php echo $cols['docId']; ?>"><i class="fa fa-times"></i></a>
				</td>
			</tr>

			<!-- Delete Document Confirm Modal -->
			<div class="modal fade" id="deleteDoc<?php echo $cols['docId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<form action="" method="post">
							<div class="modal-body">
								<p class="lead"><?php echo $deleteDocumentConf; ?>
								</p>
							</div>
							<div class="modal-footer">
								<input name="deleteId" type="hidden" value="<?php echo $cols['docId']; ?>" />
								<input name="tenantDocsFolder" type="hidden" value="<?php echo $row['tenantDocsFolder']; ?>" />
								<button type="input" name="submit" value="deleteDocument" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
								<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
							</div>
						</form>
					</div>
				</div>
			</div>
		<?php } ?>
		</tbody>
	</table>
<?php } ?>

<!-- -- Remove Profile Avatar Model -- -->
<div class="modal fade" id="profileAvatar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $removeAvatarModalTitle; ?></h4>
			</div>

			<?php if ($row['tenantAvatar'] != 'tenantDefault.png') { ?>
				<div class="modal-body">
					<img alt="" src="../<?php echo $avatarDir.$row['tenantAvatar']; ?>" class="avatar" />
					<p><?php echo $removeAvatarQuip; ?></p>
				</div>
				<div class="clearfix"></div>
				<div class="modal-footer">
					<a data-toggle="modal" href="#deleteAvatar" class="btn btn-danger btn-icon tool-tip" Title="Delete the Tenant's Avatar Image"><i class="fa fa-times"></i> <?php echo $removeAvatarBtn; ?></a>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			<?php } else { ?>
				<div class="modal-body">
					<p class="lead"><?php echo $noAvatarUploadedQuip; ?></p>
				</div>
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
					<p class="lead"><?php echo $removeAvatarConfModal.' '.clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?>?</p>
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
				<h4 class="modal-title"><?php echo $updTenantInfoModalTitle; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
                        <label for="tenantFirstName"><?php echo $firstNameField; ?></label>
                        <input type="text" class="form-control" name="tenantFirstName" value="<?php echo clean($row['tenantFirstName']); ?>" />
                    </div>
                    <div class="form-group">
                        <label for="tenantLastName"><?php echo $lastNameField; ?></label>
                        <input type="text" class="form-control" name="tenantLastName" value="<?php echo clean($row['tenantLastName']); ?>" />
                    </div>
					<div class="form-group">
                        <label for="tenantPhone"><?php echo $phoneField; ?></label>
                        <input type="text" class="form-control" name="tenantPhone" id="tenantPhone" value="<?php echo $tenantPhone; ?>" />
                    </div>
					<div class="form-group">
                        <label for="tenantAltPhone"><?php echo $altPhoneField; ?></label>
                        <input type="text" class="form-control" name="tenantAltPhone" id="tenantAltPhone" value="<?php echo $tenantAltPhone; ?>" />
                    </div>
					<div class="form-group">
						<label for="tenantAddress"><?php echo $addressField; ?></label>
						<textarea class="form-control" name="tenantAddress" rows="3"><?php echo $tenantAddress; ?></textarea>
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

<!-- -- Update Tenant Notes Model -- -->
<div class="modal fade" id="tenantNotes" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $updTenantNotesModalTitle; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="tenantNotes"><?php echo $tenantInternalNotesField; ?></label>
						<textarea class="form-control" name="tenantNotes" rows="8"><?php echo clean($row['tenantNotes']); ?></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="updateNotes" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
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
				<h4 class="modal-title"><?php echo $updTenantEmailModalTitle; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
                        <label for="tenantEmail"><?php echo $tenantsEmailField; ?></label>
                        <input type="text" class="form-control" name="tenantEmail" value="<?php echo clean($row['tenantEmail']); ?>" />
						<span class="help-block"><?php echo $tenantEmailHelper; ?></span>
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

<!-- -- Update Account Password Model -- -->
<div class="modal fade" id="editPassword" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $updPasswordModalTitle; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
                    <div class="form-group">
                        <label for="password"><?php echo $newPassField; ?></label>
                        <input type="text" class="form-control" name="password" value="" />
						<span class="help-block"><?php echo $newTenantPasswordHelper; ?></span>
                    </div>
					<div class="form-group">
                        <label for="password_r"><?php echo $confirmNewPassField; ?></label>
                        <input type="text" class="form-control" name="password_r" value="" />
						<span class="help-block"><?php echo $newTenantPassRepeatHelper; ?></span>
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

<!-- -- Account Status Model -- -->
<div class="modal fade" id="editStatus" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $updTenantStatusModalTitle; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<p><small><?php echo $updTenantStatusQuip; ?></small></p>
					<div class="form-group">
						<label for="isActive"><?php echo $activeAccountField; ?></label>
						<select class="form-control" name="isActive">
							<option value="0" <?php echo $inactive; ?>><?php echo $statusOptionInactive; ?></option>
							<option value="1" <?php echo $active; ?>><?php echo $statusOptionActive; ?></option>
						</select>
						<span class="help-block"><?php echo $accountStatusHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="isArchived"><?php echo $archiveAccountField; ?></label>
						<select class="form-control" name="isArchived">
							<option value="0" <?php echo $no; ?>><?php echo $noBtn; ?></option>
							<option value="1" <?php echo $yes; ?>><?php echo $yesBtn; ?></option>
						</select>
						<span class="help-block"><?php echo $archiveAccountHelper; ?></span>
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

<!-- -- Email Tenant Model -- -->
<div class="modal fade" id="emailTenant" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $sendEmailModalTitle.' '.clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?></h4>
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
					<input name="tenantEmail" value="<?php echo clean($row['tenantEmail']); ?>" type="hidden">
					<button type="input" name="submit" value="sendEmail" class="btn btn-success btn-icon"><i class="fa fa-envelope"></i> <?php echo $sendEmailBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- -- UPLOAD TENANT DOCUMENT MODEL -- -->
<div class="modal fade" id="uploadDocuments" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $uploadTenantDocBtn; ?></h4>
			</div>
			<form enctype="multipart/form-data" action="" method="post">
				<div class="modal-body">
					<p>
						<small>
							<?php echo $allowedFileTypesQuip.' '.$uploadTypesAllowed; ?><br />
							<?php echo $maxUploadSize.' '.$maxUpload.'MB.'; ?>
						</small>
					</p>

					<div class="form-group">
						<label for="docTitle"><?php echo $propPictureTitle; ?></label>
						<input type="text" class="form-control" name="docTitle" value="">
						<span class="help-block"><?php echo $propFileTitleHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="docDesc"><?php echo $tab_formDescription; ?></label>
						<textarea class="form-control" name="docDesc" rows="2"></textarea>
						<span class="help-block"><?php echo $propFileDescHelper.' '.$htmlNotAllowed; ?></span>
					</div>
					<div class="form-group">
						<label for="file"><?php echo $selectPropFileField; ?></label>
						<input type="file" name="file">
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="tenantDocsFolder" value="<?php echo clean($row['tenantDocsFolder']); ?>">
					<button type="input" name="submit" value="Upload Document" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $uploadBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>