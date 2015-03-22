<?php
	// Add the New Service Request
    if (isset($_POST['submit']) && $_POST['submit'] == 'saveRequest') {
        // Validation
        if($_POST['requestTitle'] == "") {
            $msgBox = alertBox($requestTitleMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['requestDesc'] == "") {
            $msgBox = alertBox($requestDescMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Get the Tenants current Lease ID
			$theId = "SELECT
						properties.propertyId,
						properties.propertyName,
						leases.leaseId,
						tenants.tenantId
					FROM
						properties
						LEFT JOIN leases ON properties.propertyId = leases.propertyId
						LEFT JOIN tenants ON leases.leaseId = tenants.leaseId
					WHERE tenants.tenantId = ".$tenantId;
			$idres = mysqli_query($mysqli, $theId) or die('Error, retrieving Tenant Lease ID failed. ' . mysqli_error());
			$a = mysqli_fetch_assoc($idres);
			$propertyName = $a['propertyName'];
			$leaseId = $a['leaseId'];

			$requestTitle = $mysqli->real_escape_string($_POST['requestTitle']);
			$requestPriority = $mysqli->real_escape_string($_POST['requestPriority']);
			$requestDesc = htmlentities($_POST['requestDesc']);
			$today = date("Y-m-d H:i:s");
			$status = ('1');

			// Add the Request
            $stmt = $mysqli->prepare("
                                INSERT INTO
                                    servicerequests(
                                        tenantId,
										leaseId,
										requestDate,
                                        requestPriority,
										requestStatus,
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
				$today,
                $requestPriority,
				$status,
				$requestTitle,
				$requestDesc
            );
            if (!$stmt->execute()) {
					echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
					}

			// Send out the email in HTML
			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$businessEmail = $set['businessEmail'];

			$subject = 'A new Service Request has been added for the Property: '.$propertyName;

			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<hr>';
			$message .= '<p>Property: '.$propertyName.'</p>';
			$message .= '<p>'.$requestTitle.'</p>';
			$message .= '<p>'.$requestDesc.'</p>';
			$message .= '<hr>';
			$message .= '<p>You can view and respond to this request by logging in to your account <a href="'.$installUrl.'admin/">HERE</a></p>';
			$message .= '<p>Thank you,<br>'.$siteName.'</p>';
			$message .= '</body></html>';

			$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
			$headers .= "Reply-To: ".$businessEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($allAdmins, $subject, $message, $headers)) {
				$msgBox = alertBox($requestAddedMsg, "<i class='fa fa-check-square-o'></i>", "success");
				// Clear the form of Values
				$_POST['requestTitle'] = $_POST['requestDesc'] = '';
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
			DATE_FORMAT(servicerequests.requestDate,'%M %d, %Y') AS requestDate,
			CASE servicerequests.requestPriority
				WHEN 0 THEN 'Normal'
				WHEN 1 THEN 'Important'
				WHEN 2 THEN 'Urgent'
			END AS requestPriority,
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
			DATE_FORMAT(servicerequests.lastUpdated,'%M %d, %Y') AS lastUpdated,
			tenants.leaseId,
			properties.propertyName
			FROM
				servicerequests
				LEFT JOIN tenants ON servicerequests.leaseId = tenants.leaseId
				LEFT JOIN properties ON tenants.propertyId = properties.propertyId
			WHERE
				servicerequests.tenantId = ".$tenantId."
			ORDER BY requestId";
	$res = mysqli_query($mysqli, $query) or die('Error, retrieving Service Data failed. ' . mysqli_error());
?>
<h3 class="warning"><?php echo $serviceRequestsH3; ?></h3>
<div class="row">
	<div class="col-md-8">
		<p class="lead"><?php echo $serviceRequestsQuip; ?></p>
		<p><?php echo $serviceRequestsCompleted; ?></p>
	</div>
	<div class="col-md-4">
		<a data-toggle="modal" href="#newRequest" class="btn btn-primary btn-icon floatRight"><i class="fa fa-wrench"></i> <?php echo $newServiceRequestBtn; ?></a>
	</div>
</div>

<?php if ($msgBox) { echo $msgBox; } ?>

<?php if(mysqli_num_rows($res) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noServiceRequestsMsg; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTable" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_requestTitle; ?></th>
			<th><?php echo $tab_prop; ?></th>
			<th><?php echo $tab_dateRequested; ?></th>
			<th><?php echo $tab_priority; ?></th>
			<th><?php echo $tab_status; ?></th>
			<th><?php echo $tab_lastUpdated; ?></th>
			<th></th>
		</tr>
		<tbody class="table-hover">
		<?php
			while ($row = mysqli_fetch_assoc($res)) {
			if ($row['requestStatus'] == '3' || $row['requestStatus'] == '4' || $row['requestStatus'] == '5') {
				$isComplete = 'class="completed"';
			} else {
				$isComplete = '';
			}
		?>
				<tr <?php echo $isComplete; ?>>
					<td><?php echo clean($row['requestTitle']); ?></td>
					<td><?php echo clean($row['propertyName']); ?></td>
					<td><?php echo $row['requestDate']; ?></td>
					<td><?php echo $row['requestPriority']; ?></td>
					<td><?php echo $row['status']; ?></td>
					<td><?php echo $row['lastUpdated']; ?></td>
					<td><a href="index.php?page=viewRequest&requestId=<?php echo $row['requestId']; ?>"><?php echo $td_view; ?> <i class="fa fa-long-arrow-right"></i></a></td>
				</tr>
		<?php } ?>
		</tbody>
	</table>
<?php } ?>

<!-- -- New Service Request Model -- -->
<div class="modal fade" id="newRequest" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $newServiceRequestBtn; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
                        <label for="requestTitle"><?php echo $serviceRequestField; ?></label>
                        <input type="text" class="form-control" name="requestTitle" id="requestTitle" value="<?php echo isset($_POST['requestTitle']) ? $_POST['requestTitle'] : ''; ?>" />
						<span class="help-block"><?php echo $serviceTitleHelper; ?></span>
                    </div>
					<div class="form-group">
						<label for="requestPriority"><?php echo $tab_priority; ?></label>
						<select class="form-control" id="requestPriority" name="requestPriority">
							<option value="0" selected><?php echo $normalSelect; ?></option>
							<option value="1"><?php echo $importantSelect; ?></option>
							<option value="2"><?php echo $urgenSelect; ?></option>
						</select>
						<span class="help-block"><?php echo $servicePriorityHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="requestDesc"><?php echo $serviceRequestDesc; ?></label>
						<textarea class="form-control" name="requestDesc" id="requestDesc" rows="6"><?php echo isset($_POST['requestDesc']) ? $_POST['requestDesc'] : ''; ?></textarea>
						<p class="help-block"><?php echo $beDescriptiveHelper.' '.$htmlNotAllowedHelper; ?></p>
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="saveRequest" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>