<?php
	$requestId = $_GET['requestId'];

	// Get Avatar Folder from Site Settings
	$avatarDir = $set['avatarFolder'];

	// Edit Note
	if (isset($_POST['submit']) && $_POST['submit'] == 'saveEdits') {
		// Validation
        if($_POST['noteText'] == "") {
            $msgBox = alertBox($noteTextReqVal, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$noteText = htmlentities($_POST['noteText']);
			$noteId = $mysqli->real_escape_string($_POST['noteId']);

			$stmt = $mysqli->prepare("
                                UPDATE
                                    servicenotes
                                SET
									noteText = ?
                                WHERE
									noteId = ?");
            $stmt->bind_param('ss',
								$noteText,
								$noteId
			);
            $stmt->execute();
            $msgBox = alertBox($editServNoteUpdatedMsg, "<i class='fa fa-check-square-o'></i>", "success");
            $stmt->close();
		}
	}

	// Add New Note
    if (isset($_POST['submit']) && $_POST['submit'] == 'saveNote') {
        // Validation
        if($_POST['noteText'] == "") {
            $msgBox = alertBox($noteTextReqVal, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$propertyName = $mysqli->real_escape_string($_POST['propertyName']);
			$requestTitle = $mysqli->real_escape_string($_POST['requestTitle']);
			$noteText = htmlentities($_POST['noteText']);
			$today = date("Y-m-d H:i:s");

			// Add the Note
            $stmt = $mysqli->prepare("
                                INSERT INTO
                                    servicenotes(
                                        requestId,
                                        tenantId,
                                        adminId,
                                        noteText,
										noteDate
                                    ) VALUES (
                                        ?,
										?,
                                        0,
                                        ?,
										?
                                    )");
            $stmt->bind_param('ssss',
                $requestId,
				$tenantId,
                $noteText,
				$today
            );
            $stmt->execute();

			// Update the lastUpdated date
			$sqlstmt = $mysqli->prepare("
                                UPDATE
                                    servicerequests
                                SET
									lastUpdated = ?
                                WHERE
									requestId = ?");
            $sqlstmt->bind_param('ss',
								$today,
								$requestId
			);
            $sqlstmt->execute();
			$sqlstmt->close();

			// Send out the email in HTML
			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$businessEmail = $set['businessEmail'];

			$subject = 'A new Note has been added for the Service Request: '.$requestTitle;

			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<hr>';
			$message .= '<p>Property: '.$propertyName.'</p>';
			$message .= '<p>'.$noteText.'</p>';
			$message .= '<hr>';
			$message .= '<p>You can view and respond to this Service Request Note by logging in to your Admin account at '.$installUrl.'admin</p>';
			$message .= '<p>Thank you,<br>'.$siteName.'</p>';
			$message .= '</body></html>';

			$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
			$headers .= "Reply-To: ".$businessEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($allAdmins, $subject, $message, $headers)) {
				$msgBox = alertBox($newServNoteAddedMsg, "<i class='fa fa-check-square-o'></i>", "success");
				// Clear the form of Values
				$_POST['noteText'] = '';
			}
            $stmt->close();
		}
	}


	// Get Service Request Data
	$query = "
			SELECT
				servicerequests.requestId,
				servicerequests.tenantId,
				servicerequests.leaseId,
				servicerequests.adminId,
				DATE_FORMAT(servicerequests.requestDate,'%W, %M %e, %Y at %l:%i %p') AS requestDate,
				servicerequests.requestPriority,
				CASE servicerequests.requestPriority
					WHEN 0 THEN 'Normal'
					WHEN 1 THEN 'Important'
					WHEN 2 THEN 'Urgent'
				END AS priority,
				servicerequests.requestStatus,
				CASE servicerequests.requestStatus
					WHEN 0 THEN 'Open'
					WHEN 1 THEN 'Work in Progress'
					WHEN 2 THEN 'Waiting for Parts'
					WHEN 3 THEN 'Completed/No Repair Needed'
					WHEN 4 THEN 'Completed Repair'
					WHEN 5 THEN 'Closed'
				END AS status,
				servicerequests.requestTitle,
				servicerequests.requestDesc,
				DATE_FORMAT(servicerequests.lastUpdated,'%M %d, %Y') AS lastUpdated,
				tenants.leaseId,
				tenants.tenantFirstName,
				tenants.tenantLastName,
				properties.propertyName,
				admins.adminFirstName,
				admins.adminLastName
			FROM
				servicerequests
				LEFT JOIN tenants ON servicerequests.leaseId = tenants.leaseId
				LEFT JOIN properties ON tenants.propertyId = properties.propertyId
				LEFT JOIN admins ON servicerequests.adminId = admins.adminId
			WHERE
				servicerequests.requestId = ".$requestId;
	$res = mysqli_query($mysqli, $query) or die('Error, retrieving Service Data failed. ' . mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Check the current status
	if ($row['requestStatus'] == '0' || $row['requestStatus'] == '1' || $row['requestStatus'] == '2') {
		$requestOpen = 'true';
	} else {
		$requestOpen = 'false';
	}

	// Get who created the Request
	if ($row['adminId'] != '0') {
		$startedBy = clean($row['adminFirstName']).' '.clean($row['adminLastName']);
	} else {
		$startedBy = clean($row['tenantFirstName']).' '.clean($row['tenantLastName']);
	}

	if($requestOpen == 'false') {
		// Get the Resolution Data
		$qry = "SELECT
					serviceresolutions.resolutionId,
					serviceresolutions.requestId,
					serviceresolutions.tenantId,
					serviceresolutions.adminId,
					serviceresolutions.resolutionText,
					DATE_FORMAT(serviceresolutions.resolutionDate,'%M %d, %Y') AS resolutionDate,
					CASE serviceresolutions.needsFollowUp
						WHEN 0 THEN 'No'
						WHEN 1 THEN 'Yes'
					END AS followUp,
					serviceresolutions.followUpText,
					serviceresolutions.isComplete,
					DATE_FORMAT(serviceresolutions.completeDate,'%M %d, %Y') AS completeDate,
					admins.adminFirstName,
					admins.adminLastName,
					admins.adminAvatar
				FROM
					serviceresolutions
					LEFT JOIN admins ON serviceresolutions.adminId = admins.adminId
				WHERE
					serviceresolutions.requestId = ".$requestId;
		$results = mysqli_query($mysqli, $qry) or die('Error, retrieving Service Resolution failed. ' . mysqli_error());
		$col = mysqli_fetch_assoc($results);
	}

	// Get Service Request Notes Data
    $sqlStmt = "SELECT
                    servicenotes.noteId,
                    servicenotes.requestId,
					servicenotes.tenantId,
					servicenotes.adminId,
					servicenotes.noteText,
                    DATE_FORMAT(servicenotes.noteDate,'%W, %M %e, %Y at %l:%i %p') AS noteDate,
					admins.adminFirstName,
					admins.adminLastName,
					admins.adminAvatar,
					tenants.tenantFirstName,
					tenants.tenantLastName,
					tenants.tenantAvatar
                FROM
                    servicenotes
					LEFT JOIN admins ON servicenotes.adminId = admins.adminId
					LEFT JOIN tenants ON servicenotes.tenantId = tenants.tenantId
                WHERE
					servicenotes.requestId = ".$requestId."
				ORDER BY servicenotes.noteId";
	$result = mysqli_query($mysqli, $sqlStmt) or die('Error, retrieving Service Notes failed. ' . mysqli_error());

	// Check that the Tenant has not manipulated the URL
	$check = $row['tenantId'];
	if ($check == $tenantId) {
?>
	<h3 class="warning"><?php echo $viewRequestH3.' &mdash; '.$row['requestTitle']; ?></h3>
	<?php if ($requestOpen == 'true') { ?>
		<p class="lead"><?php echo $viewRequestOpenQuip; ?></p>
	<?php } else { ?>
		<p class="lead"><?php echo $viewRequestClosedQuip; ?></p>
	<?php } ?>

	<?php if ($msgBox) { echo $msgBox; } ?>

	<hr />

	<div class="row">
		<?php if ($requestOpen == 'true') { ?>
			<div class="col-md-12">
		<?php } else { ?>
			<div class="col-md-6">
		<?php } ?>
			<ul class="list-group">
				<li class="list-group-item warning"><?php echo $serviceReqLiTitle; ?></li>
				<li class="list-group-item"><?php echo $servReqLiDateRequested.' '.$row['requestDate']; ?></li>
				<li class="list-group-item"><?php echo $servReqLiReqBy.' '.$startedBy; ?></li>
				<li class="list-group-item"><?php echo $servReqLiProperty.' '.$row['propertyName']; ?></li>
				<li class="list-group-item"><?php echo $servReqLiPriority.' '.$row['priority']; ?></li>
				<li class="list-group-item"><?php echo $servReqLiStatus.' '.$row['status']; ?></li>
				<li class="list-group-item"><?php echo $servReqLiLastUpdate.' '.$row['lastUpdated']; ?></li>
				<li class="list-group-item"><?php echo $servReqLiRequest.'<br />'.nl2br(clean($row['requestDesc'])); ?></li>
			</ul>
		</div>
		<?php if ($requestOpen == 'false') { ?>
			<div class="col-md-6">
				<ul class="list-group">
					<li class="list-group-item info"><?php echo $servResolutionLiTitle; ?></li>
					<li class="list-group-item"><?php echo $servResLiCompletedBy.' '.clean($col['adminFirstName']).' '.clean($col['adminLastName']); ?></li>
					<li class="list-group-item"><?php echo $servResLiDateResolved.' '.$col['resolutionDate']; ?></li>
					<li class="list-group-item"><?php echo $servResLiComments.'<br />'.nl2br(clean($col['resolutionText'])); ?></li>
					<li class="list-group-item"><?php echo $servResLiNeedsFollowup.' '.$col['followUp']; ?></li>
					<li class="list-group-item"><?php echo $servResLiFollowUpComments.'<br />'.nl2br(clean($col['followUpText'])); ?></li>
					<li class="list-group-item"><?php echo $servResLiDateCompleted.' '.$col['completeDate']; ?></li>
				</ul>
			</div>
		<?php } ?>
	</div>

	<?php if (mysqli_num_rows($result) > 0) { ?>
			<hr />

			<h3><?php echo $serviceReqNotesH3; ?></h3>
		<?php
			while ($rows = mysqli_fetch_assoc($result)) {
				// Set some Variables
				if ($rows['adminId'] != '0') {
					$startedBy = $rows['adminFirstName'].' '.$rows['adminLastName'];
					$isEditable = '';
					$avatarUrl = $rows['adminAvatar'];
					$isAdmin = ' admin';
				} else {
					$startedBy = $rows['tenantFirstName'].' '.$rows['tenantLastName'];
					if ($requestOpen == 'true') {
						$isEditable = '<a data-toggle="modal" href="#editNote'.$rows['noteId'].'" class="note-edit-link label label-warning floatRight"><i class="fa fa-edit"></i> '.$editBtn.'</a>';
					} else {
						$isEditable = '';
					}
					$avatarUrl = $rows['tenantAvatar'];
					$isAdmin = '';
				}
		?>
				<div class="note-body<?php echo $isAdmin; ?>">
					<img alt="Avatar" src="<?php echo $avatarDir.$avatarUrl; ?>" class="avatar" />
					<div class="note-author"><?php echo $startedBy; ?></div>
					<div class="note-date"><?php echo $rows['noteDate']; ?><?php echo $isEditable; ?></div>
					<div class="note-text">
						<p><?php echo nl2br(clean($rows['noteText'])); ?></p>
					</div>
				</div>

				<!-- Edit Comment Modal -->
				<div class="modal fade" id="editNote<?php echo $rows['noteId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header modal-primary">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
								<h4 class="modal-title"><?php echo $editNoteModalTitle; ?></h4>
							</div>
							<form action="" method="post">
								<div class="modal-body">
									<div class="form-group">
										<label for="noteText"><?php echo $editNoteField; ?></label>
										<textarea class="form-control" name="noteText" id="noteText" rows="6"><?php echo clean($rows['noteText']); ?></textarea>
										<span class="help-block"><?php echo $htmlNotAllowedHelper; ?></span>
									</div>
								</div>
								<div class="modal-footer">
									<input name="noteId" type="hidden" value="<?php echo $rows['noteId']; ?>" />
									<button type="input" name="submit" value="saveEdits" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
									<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
								</div>
							</form>
						</div>
					</div>
				</div>
		<?php
			}
		}
	?>

	<hr />

	<?php if ($requestOpen == 'true') { ?>
		<p><a data-toggle="modal" href="#addNote" class="btn btn-info btn-icon"><i class="fa fa-comment"></i> <?php echo $addNoteBtn; ?></a></p>

		<!-- Add a New Note Modal -->
		<div class="modal fade" id="addNote" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header modal-info">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
						<h4 class="modal-title"><?php echo $addNoteBtn; ?></h4>
					</div>
					<form action="" method="post">
						<div class="modal-body">
							<div class="form-group form-group-modal">
								<label for="noteText"><?php echo $notesField; ?></label>
								<textarea class="form-control" name="noteText" id="noteText" rows="6"></textarea>
								<span class="help-block"><?php echo $htmlNotAllowedHelper; ?></span>
							</div>
						</div>
						<div class="modal-footer">
							<input name="requestId" type="hidden" value="<?php echo $requestId; ?>" />
							<input name="propertyName" type="hidden" value="<?php echo clean($row['propertyName']); ?>" />
							<input name="requestTitle" type="hidden" value="<?php echo clean($row['requestTitle']); ?>" />
							<button type="input" name="submit" value="saveNote" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
							<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>
	<?php } else { ?>
		<div class="alertMsg default">
			<i class="fa fa-minus-square-o"></i> <?php echo $notesClosedMsg; ?>
		</div>
	<?php }
} else {
?>
	<h3 class="primary"><?php echo $accessErrorH3; ?></h3>
	<div class="alertMsg warning"><i class="fa fa-ban"></i> <?php echo $permissionDenied; ?></div>
<?php } ?>