<?php
	$requestId = $_GET['requestId'];
	$stacktable = 'true';
	$jsFile = 'viewRequest';
	$datePicker = 'true';

	// Get Avatar Folder from Site Settings
	$avatarDir = $set['avatarFolder'];

    // Edit/Update Original Service Request
    if (isset($_POST['submit']) && $_POST['submit'] == 'updateServRequest') {
		// Validation
        if($_POST['requestTitle'] == "") {
            $msgBox = alertBox($requestTitleReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['requestDesc'] == "") {
            $msgBox = alertBox($requestDescReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$requestPriority = $mysqli->real_escape_string($_POST['requestPriority']);
			$requestStatus = $mysqli->real_escape_string($_POST['requestStatus']);
			$requestTitle = $mysqli->real_escape_string($_POST['requestTitle']);
			$requestDesc = htmlentities($_POST['requestDesc']);

			$stmt = $mysqli->prepare("
								UPDATE
									servicerequests
								SET
									requestPriority = ?,
									requestStatus = ?,
									requestTitle = ?,
									requestDesc = ?
								WHERE
									requestId = ?
			");
			$stmt->bind_param('sssss',
								   $requestPriority,
								   $requestStatus,
								   $requestTitle,
								   $requestDesc,
								   $requestId
			);
			$stmt->execute();
			$msgBox = alertBox($serviceRequestUpdatedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			$stmt->close();
		}
    }

    // Add/Update Service Resolution
    if (isset($_POST['submit']) && $_POST['submit'] == 'saveResolution') {
		// Validation
		if($_POST['resolutionDate'] == "") {
            $msgBox = alertBox("Please select or type the Date this Service Request was completed.", "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['resolutionText'] == "") {
            $msgBox = alertBox("Please type the Resolution details.", "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$requestId = $mysqli->real_escape_string($_POST['requestId']);
			$tenantId = $mysqli->real_escape_string($_POST['tenantId']);
			$hasResolution = $mysqli->real_escape_string($_POST['hasResolution']);
			$resolutionText = htmlentities($_POST['resolutionText']);
			$resolutionDate = $mysqli->real_escape_string($_POST['resolutionDate']);
			$needsFollowUp = $mysqli->real_escape_string($_POST['needsFollowUp']);
			$followUpText = htmlentities($_POST['followUpText']);
			$isComplete = $mysqli->real_escape_string($_POST['isComplete']);
			$resolutionId = $mysqli->real_escape_string($_POST['resolutionId']);
			if ($isComplete == '1') { $today = date("Y-m-d"); } else { $today = ''; }

			if ($hasResolution == '1') {
				// There is all ready a Resolution Entry - so update it
				$stmt = $mysqli->prepare("
									UPDATE
										serviceresolutions
									SET
										adminId = ?,
										resolutionText = ?,
										resolutionDate = ?,
										needsFollowUp = ?,
										followUpText = ?,
										isComplete = ?,
										completeDate = ?
									WHERE
										resolutionId = ?
				");
				$stmt->bind_param('ssssssss',
										$adminId,
										$resolutionText,
										$resolutionDate,
										$needsFollowUp,
										$followUpText,
										$isComplete,
										$today,
										$resolutionId
				);
				$stmt->execute();
				$msgBox = alertBox("The Service Request Resolution has been updated.", "<i class='fa fa-check-square-o'></i>", "success");
				$stmt->close();
			} else {
				// There is NOT a Resolution Entry - so create it
				$stmt = $mysqli->prepare("
									INSERT INTO
										serviceresolutions(
											requestId,
											tenantId,
											adminId,
											resolutionText,
											resolutionDate,
											needsFollowUp,
											followUpText,
											isComplete,
											completeDate
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
										$requestId,
										$tenantId,
										$adminId,
										$resolutionText,
										$resolutionDate,
										$needsFollowUp,
										$followUpText,
										$isComplete,
										$today
				);
				$stmt->execute();
				$msgBox = alertBox("The Service Request Resolution has been saved.", "<i class='fa fa-check-square-o'></i>", "success");
				$stmt->close();
			}
		}
    }

	// Add a Service Expense
	if (isset($_POST['submit']) && $_POST['submit'] == 'saveExpense') {
		// Validation
        if($_POST['dateOfExpense'] == "") {
            $msgBox = alertBox($expenseDateReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['expenseName'] == "") {
            $msgBox = alertBox($expenseNameReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['expenseDesc'] == "") {
            $msgBox = alertBox($expenseDescReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['expenseCost'] == "") {
            $msgBox = alertBox($expenseCostReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$requestId = $mysqli->real_escape_string($_POST['requestId']);
			$tenantId = $mysqli->real_escape_string($_POST['tenantId']);
			$leaseId = $mysqli->real_escape_string($_POST['leaseId']);
			$vendorName = $mysqli->real_escape_string($_POST['vendorName']);
			$expenseName = $mysqli->real_escape_string($_POST['expenseName']);
			$expenseDesc = htmlentities($_POST['expenseDesc']);
			$expenseCost = $mysqli->real_escape_string($_POST['expenseCost']);
			$dateOfExpense = $mysqli->real_escape_string($_POST['dateOfExpense']);

			$stmt = $mysqli->prepare("
                                INSERT INTO
                                    serviceexpense(
                                        requestId,
                                        tenantId,
                                        leaseId,
                                        vendorName,
										expenseName,
										expenseDesc,
										expenseCost,
										dateOfExpense
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
                $requestId,
				$tenantId,
				$leaseId,
                $vendorName,
				$expenseName,
				$expenseDesc,
				$expenseCost,
				$dateOfExpense
            );
            $stmt->execute();
            $msgBox = alertBox($serviceExpenseSavedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			// Clear the form of Values
			$_POST['vendorName'] = $_POST['expenseName'] = $_POST['expenseDesc'] = $_POST['expenseCost'] = $_POST['dateOfExpense'] = '';
            $stmt->close();
		}
	}

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
									noteId = ?
			");
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
			$tenantId = $mysqli->real_escape_string($_POST['tenantId']);
			$tenantEmail = $mysqli->real_escape_string($_POST['tenantEmail']);
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
                                        ?,
                                        ?,
										?
                                    )");
            $stmt->bind_param('sssss',
                $requestId,
				$tenantId,
				$adminId,
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
			$message .= '<p>You can view and respond to this Service Request Note by logging in to your account <a href="http://'.$installUrl.'">HERE</a></p>';
			$message .= '<p>Thank you,<br>'.$siteName.'</p>';
			$message .= '</body></html>';

			$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
			$headers .= "Reply-To: ".$businessEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($tenantEmail, $subject, $message, $headers)) {
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
				DATE_FORMAT(servicerequests.lastUpdated,'%W, %M %e, %Y at %l:%i %p') AS lastUpdated,
				tenants.leaseId,
				tenants.tenantEmail,
				tenants.tenantFirstName,
				tenants.tenantLastName,
				properties.propertyId,
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
		$startedBy = '<a href="index.php?action=adminInfo&adminId='.$row['adminId'].'">'.clean($row['adminFirstName']).' '.clean($row['adminLastName']).'</a>';
	} else {
		$startedBy = '<a href="index.php?action=tenantInfo&tenantId='.$row['tenantId'].'">'.clean($row['tenantFirstName']).' '.clean($row['tenantLastName']).'</a>';
	}

	// Set the Select Fields to the proper value
	if ($row['requestPriority'] == '0') { $normal = 'selected'; } else { $normal = ''; }
	if ($row['requestPriority'] == '1') { $important = 'selected'; } else { $important = ''; }
	if ($row['requestPriority'] == '2') { $urgent = 'selected'; } else { $urgent = ''; }

	if ($row['requestStatus'] == '0') { $open = 'selected'; } else { $open = ''; }
	if ($row['requestStatus'] == '1') { $wip = 'selected'; } else { $wip = ''; }
	if ($row['requestStatus'] == '2') { $parts = 'selected'; } else { $parts = ''; }
	if ($row['requestStatus'] == '3') { $norepair = 'selected'; } else { $norepair = ''; }
	if ($row['requestStatus'] == '4') { $repaired = 'selected'; } else { $repaired = ''; }
	if ($row['requestStatus'] == '5') { $closed = 'selected'; } else { $closed = ''; }

	if($requestOpen == 'false') {
		// Get the Resolution Data
		$qry = "SELECT
					serviceresolutions.resolutionId,
					serviceresolutions.requestId,
					serviceresolutions.tenantId,
					serviceresolutions.adminId,
					serviceresolutions.resolutionText,
					serviceresolutions.resolutionDate,
					DATE_FORMAT(serviceresolutions.resolutionDate,'%M %d, %Y') AS dateResolved,
					CASE serviceresolutions.needsFollowUp
						WHEN 0 THEN 'No'
						WHEN 1 THEN 'Yes'
					END AS followUp,
					serviceresolutions.followUpText,
					serviceresolutions.isComplete,
					DATE_FORMAT(serviceresolutions.completeDate,'%M %d, %Y') AS completeDate,
					admins.adminFirstName,
					admins.adminLastName
				FROM
					serviceresolutions
					LEFT JOIN admins ON serviceresolutions.adminId = admins.adminId
				WHERE
					serviceresolutions.requestId = ".$requestId;
		$results = mysqli_query($mysqli, $qry) or die('Error, retrieving Service Resolution failed. ' . mysqli_error());
		$col = mysqli_fetch_assoc($results);

		if(mysqli_num_rows($results) > 0) { $hasResolution = '1'; } else { $hasResolution = '0'; }
		if ($col['followUp'] == 'No') { $nofollowup = 'selected'; } else { $nofollowup = ''; }
		if ($col['followUp'] == 'Yes') { $yesfollowup = 'selected'; } else { $yesfollowup = ''; }
		if ($col['isComplete'] == '0') { $nocomplete = 'selected'; } else { $nocomplete = ''; }
		if ($col['isComplete'] == '1') { $yescomplete = 'selected'; } else { $yescomplete = ''; }

		// Get the Service Cost Data
		$qryStmt = "SELECT
						expenseId,
						requestId,
						tenantId,
						leaseId,
						vendorName,
						expenseName,
						expenseDesc,
						expenseCost,
						DATE_FORMAT(dateOfExpense,'%M %d, %Y') AS dateOfExpense
					FROM
						serviceexpense
					WHERE
						requestId = ".$requestId;
		$qryres = mysqli_query($mysqli, $qryStmt) or die('Error, retrieving Service Requests Costs failed. ' . mysqli_error());

		// Get the Total of all Repair/Service Expenses for this Request
		$totals = "SELECT SUM(expenseCost) AS totalCosts FROM serviceexpense WHERE requestId = ".$requestId;
		$totalsres = mysqli_query($mysqli, $totals) or die('Error, retrieving Service Requests Costs failed. ' . mysqli_error());
		$total = mysqli_fetch_assoc($totalsres);
		// Format the Amount
		$totalServCost = $currencySym.format_amount($total['totalCosts'], 2);
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
?>
<h3 class="warning"><?php echo $viewRequestH3.' &mdash; '.$row['requestTitle']; ?></h3>
<?php if ($requestOpen == 'true') { ?>
	<p class="lead"><?php echo $viewRequestOpenQuip; ?></p>
	<p><?php echo $viewRequestInstructions; ?></p>
<?php } else { ?>
	<p class="lead"><?php echo $viewRequestClosedQuip; ?></p>
	<p><?php echo $viewClosedRequestInstructions; ?>
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
			<li class="list-group-item info"><?php echo $serviceReqLiTitle; ?></li>
			<li class="list-group-item"><?php echo $servReqLiDateRequested.': '.$row['requestDate']; ?></li>
			<li class="list-group-item"><?php echo $servReqLiReqBy.': '.$startedBy; ?></li>
			<li class="list-group-item"><?php echo $servReqLiProperty; ?>:
				<a href="index.php?action=propertyInfo&propertyId=<?php echo $row['propertyId']; ?>"><?php echo clean($row['propertyName']); ?></a>
			</li>
			<li class="list-group-item"><?php echo $servReqLiPriority.': '.$row['priority']; ?></li>
			<li class="list-group-item"><?php echo $servReqLiStatus.': '.$row['status']; ?></li>
			<li class="list-group-item"><?php echo $servReqLiLastUpdate.': '.$row['lastUpdated']; ?></li>
			<li class="list-group-item"><?php echo $servReqLiRequest.':<br />'.nl2br(clean($row['requestDesc'])); ?></li>
		</ul>
		<a data-toggle="modal" href="#updateRequest" class="btn btn-info btn-icon"><i class="fa fa-edit"></i> <?php echo $updateRequestBtn; ?></a>
	</div>

	<?php if ($requestOpen == 'false') { ?>
		<div class="col-md-6">
			<ul class="list-group">
				<li class="list-group-item warning"><?php echo $servResolutionLiTitle; ?></li>
				<li class="list-group-item"><?php echo $servResLiCompletedBy; ?>: <?php echo clean($col['adminFirstName']).' '.clean($col['adminLastName']); ?></li>
				<li class="list-group-item"><?php echo $servResLiDateResolved.': '.$col['dateResolved']; ?></li>
				<li class="list-group-item"><?php echo $servResLiComments.':<br />'.nl2br(clean($col['resolutionText'])); ?></li>
				<li class="list-group-item"><?php echo $servResLiNeedsFollowup.'? '.$col['followUp']; ?></li>
				<li class="list-group-item"><?php echo $servResLiFollowUpComments.':<br />'.nl2br(clean($col['followUpText'])); ?></li>
				<li class="list-group-item"><?php echo $servResLiDateCompleted.': '.$col['completeDate']; ?></li>
			</ul>
			<a data-toggle="modal" href="#reqResolution" class="btn btn-warning btn-icon"><i class="fa fa-wrench"></i> <?php echo $servResolutionBtn; ?></a>
		</div>
	<?php } ?>
</div>

<?php if ($requestOpen == 'false') { ?>
	<hr />

	<h3 class="success"><?php echo $serviceRequestCostsH3; ?></h3>
	<div class="row">
		<div class="col-md-10">
			<p class="lead"><?php echo $serviceRequestCostsQuip; ?></p>
			<p><?php echo $serviceRequestCostsNote; ?></p>
		</div>
		<div class="col-md-2">
			<a data-toggle="modal" href="#reqCosts" class="btn btn-success btn-icon floatRight"><i class="fa fa-money"></i> <?php echo $servCostsBtn; ?></a>
		</div>
	</div>

	<?php if(mysqli_num_rows($qryres) > 0) { ?>
		<table id="responsiveTable" class="large-only" cellspacing="0">
			<tr align="left">
				<th><?php echo $tab_expenseName; ?></th>
				<th><?php echo $tab_vendorName; ?></th>
				<th><?php echo $tab_expenseDesc; ?></th>
				<th><?php echo $tab_expenseCost; ?></th>
				<th><?php echo $tab_dateOfExpense; ?></th>
			</tr>
			<tbody class="table-hover">
			<?php
				while ($rows = mysqli_fetch_assoc($qryres)) {
					// Format the Amounts
					$expenseCost = $currencySym.format_amount($rows['expenseCost'], 2);
			?>
					<tr>
						<td><?php echo clean($rows['expenseName']); ?></td>
						<td><?php echo clean($rows['vendorName']); ?></td>
						<td><?php echo clean($rows['expenseDesc']); ?></td>
						<td><?php echo $expenseCost; ?></td>
						<td><?php echo $rows['dateOfExpense']; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<p><span class="reportTotal"><strong>Total Repair Costs:</strong> <?php echo $totalServCost; ?></span></p>
	<?php } ?>

<?php } ?>

<!-- UPDATE REQUEST MODAL -->
<div class="modal fade" id="updateRequest" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $updateRequestBtn; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="requestPriority"><?php echo $servicePriorityField; ?></label>
						<select class="form-control" id="requestPriority" name="requestPriority">
							<option value="0" <?php echo $normal; ?>><?php echo $normalSelect; ?></option>
							<option value="1" <?php echo $important; ?>><?php echo $importantSelect; ?></option>
							<option value="2" <?php echo $urgent; ?>><?php echo $urgenSelect; ?></option>
						</select>
					</div>
					<div class="form-group">
						<label for="requestStatus"><?php echo $serviceStatusField; ?></label>
						<select class="form-control" id="requestStatus" name="requestStatus">
							<option value="0" <?php echo $open; ?>><?php echo $statusSelectOpen; ?></option>
							<option value="1" <?php echo $wip; ?>><?php echo $statusSelectWIP; ?></option>
							<option value="2" <?php echo $parts; ?>><?php echo $statusSelectParts; ?></option>
							<option value="3" <?php echo $norepair; ?>><?php echo $statusSelectNoRepair; ?></option>
							<option value="4" <?php echo $repaired; ?>><?php echo $statusSelectRepaired; ?></option>
							<option value="5" <?php echo $closed; ?>><?php echo $statusSelectClosed; ?></option>
						</select>
					</div>
					<div class="form-group">
						<label for="requestTitle"><?php echo $serviceRequestField; ?></label>
						<input type="text" class="form-control" name="requestTitle" id="requestTitle" value="<?php echo clean($row['requestTitle']); ?>">
					</div>
					<div class="form-group">
						<label for="requestDesc"><?php echo $serviceRequestDesc; ?></label>
						<textarea class="form-control" name="requestDesc" id="requestDesc" rows="2"><?php echo clean($row['requestDesc']); ?></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="updateServRequest" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $updateBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- UPDATE/ADD RESOLUTION MODAL -->
<div class="modal fade" id="reqResolution" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-warning">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $servResolutionBtn; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="resolutionDate"><?php echo $resolutionDateField; ?></label>
						<input type="text" class="form-control" name="resolutionDate" id="resolutionDate" value="<?php echo $col['resolutionDate']; ?>">
						<span class="help-block"><?php echo $resolutionDateHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="resolutionText"><?php echo $resolutionTextField; ?></label>
						<textarea class="form-control" name="resolutionText" id="resolutionText" rows="4"><?php echo $col['resolutionText']; ?></textarea>
						<span class="help-block"><?php echo $resolutionTextHelper.' '.$htmlNotAllowed; ?></span>
					</div>
					<div class="form-group">
						<label for="needsFollowUp"><?php echo $needsFollowupField; ?></label>
						<select class="form-control" id="needsFollowUp" name="needsFollowUp">
							<option value="0" <?php echo $nofollowup; ?>><?php echo $OptionNo; ?></option>
							<option value="1" <?php echo $yesfollowup; ?>><?php echo $OptionYes; ?></option>
						</select>
						<span class="help-block"><?php echo $needsFollowupHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="followUpText"><?php echo $followupDescField; ?></label>
						<textarea class="form-control" name="followUpText" id="followUpText" rows="2"><?php echo $col['followUpText']; ?></textarea>
						<span class="help-block"><?php echo $followupDescHelper.' '.$htmlNotAllowed; ?></span>
					</div>
					<div class="form-group">
						<label for="isComplete"><?php echo $closeRequestField; ?></label>
						<select class="form-control" id="isComplete" name="isComplete">
							<option value="0" <?php echo $nocomplete; ?>><?php echo $OptionNo; ?></option>
							<option value="1" <?php echo $yescomplete; ?>><?php echo $OptionYes; ?></option>
						</select>
						<span class="help-block"><?php echo $closeRequestHelper; ?></span>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="hasResolution" value="<?php echo $hasResolution; ?>">
					<input type="hidden" name="resolutionId" value="<?php echo $col['resolutionId']; ?>">
					<input type="hidden" name="requestId" value="<?php echo $row['requestId']; ?>">
					<input type="hidden" name="tenantId" value="<?php echo $row['tenantId']; ?>">
					<button type="input" name="submit" value="saveResolution" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- UPDATE/ADD SERVICE COST MODAL -->
<div class="modal fade" id="reqCosts" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-success">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $servCostsBtn; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="dateOfExpense"><?php echo $tab_dateOfExpense; ?></label>
						<input type="text" class="form-control" name="dateOfExpense" id="dateOfExpense" value="<?php echo isset($_POST['dateOfExpense']) ? $_POST['dateOfExpense'] : ''; ?>">
						<span class="help-block"><?php echo $expenseDateHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="vendorName"><?php echo $tab_vendorName; ?></label>
						<input type="text" class="form-control" name="vendorName" id="vendorName" value="<?php echo isset($_POST['vendorName']) ? $_POST['vendorName'] : ''; ?>">
						<span class="help-block"><?php echo $vendorNameHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="expenseName"><?php echo $tab_expenseName; ?></label>
						<input type="text" class="form-control" name="expenseName" id="expenseName" value="<?php echo isset($_POST['expenseName']) ? $_POST['expenseName'] : ''; ?>">
						<span class="help-block"><?php echo $expenseNameHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="expenseDesc"><?php echo $tab_expenseDesc; ?></label>
						<textarea class="form-control" name="expenseDesc" id="expenseDesc" rows="2"><?php echo isset($_POST['expenseDesc']) ? $_POST['expenseDesc'] : ''; ?></textarea>
						<span class="help-block"><?php echo $expenseDescHelper.' '.$htmlNotAllowed; ?></span>
					</div>
					<div class="form-group">
						<label for="expenseCost"><?php echo $tab_expenseCost; ?></label>
						<input type="text" class="form-control" name="expenseCost" id="expenseCost" value="<?php echo isset($_POST['expenseCost']) ? $_POST['expenseCost'] : ''; ?>">
						<span class="help-block"><?php echo $expenseCostHelper.' '.$numbersOnly; ?></span>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="requestId" value="<?php echo $row['requestId']; ?>">
					<input type="hidden" name="tenantId" value="<?php echo $row['tenantId']; ?>">
					<input type="hidden" name="leaseId" value="<?php echo $row['leaseId']; ?>">
					<button type="input" name="submit" value="saveExpense" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php if (mysqli_num_rows($result) > 0) { ?>
		<hr />

		<h3><?php echo $serviceReqNotesH3; ?></h3>
	<?php
		while ($rows = mysqli_fetch_assoc($result)) {
			// Set some Variables
			if ($rows['adminId'] != '0') {
				$startedBy = $rows['adminFirstName'].' '.$rows['adminLastName'];
				$avatarUrl = $rows['adminAvatar'];
				$isAdmin = ' admin';
			} else {
				$startedBy = $rows['tenantFirstName'].' '.$rows['tenantLastName'];
				$avatarUrl = $rows['tenantAvatar'];
				$isAdmin = '';
			}
	?>
			<div class="note-body<?php echo $isAdmin; ?>">
				<img alt="Avatar" src="../<?php echo $avatarDir.$avatarUrl; ?>" class="avatar" />
				<div class="note-author"><?php echo $startedBy; ?></div>
				<div class="note-date"><?php echo $rows['noteDate']; ?>
					<?php if ($requestOpen == 'true') { ?>
						<a data-toggle="modal" href="#editNote<?php echo $rows['noteId']; ?>" class="note-edit-link label label-warning floatRight"><i class="fa fa-edit"></i> <?php echo $editBtn; ?></a>
					<?php } ?>
				</div>
				<div class="note-text">
					<p><?php echo nl2br(clean($rows['noteText'])); ?></p>
				</div>
			</div>

			<!-- Edit Comment Modal -->
			<div class="modal fade" id="editNote<?php echo $rows['noteId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header modal-primary">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
							<h4 class="modal-title"><?php echo $editNoteModalTitle; ?></h4>
						</div>
						<form action="" method="post">
							<div class="modal-body">
								<div class="form-group">
									<label for="noteText"><?php echo $editNoteField; ?></label>
									<textarea class="form-control" name="noteText" id="noteText" rows="6"><?php echo clean($rows['noteText']); ?></textarea>
									<span class="help-block"><?php echo $htmlNotAllowed; ?></span>
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
	<p><a data-toggle="modal" href="#addNote" class="btn btn-primary btn-icon"><i class="fa fa-comment"></i> <?php echo $addNoteBtn; ?></a></p>

	<!-- Add a New Note Modal -->
	<div class="modal fade" id="addNote" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-primary">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $addNoteBtn; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<div class="form-group form-group-modal">
							<label for="noteText"><?php echo $notesField; ?></label>
							<textarea class="form-control" name="noteText" rows="6"></textarea>
							<span class="help-block"><?php echo $htmlNotAllowed; ?></span>
						</div>
					</div>
					<div class="modal-footer">
						<input name="propertyName" type="hidden" value="<?php echo clean($row['propertyName']); ?>" />
						<input name="requestTitle" type="hidden" value="<?php echo clean($row['requestTitle']); ?>" />
						<input name="tenantId" type="hidden" value="<?php echo clean($row['tenantId']); ?>" />
						<input name="tenantEmail" type="hidden" value="<?php echo clean($row['tenantEmail']); ?>" />
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
<?php } ?>