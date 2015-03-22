<?php
	$stacktable = 'true';
	$jsFile = 'dashboard';
	$datePicker = 'true';
	$count = 0;

	// Get the Current Month
    $currentMonth = date('F');
	// Get the Current Day
    $currentDay = date('d');

	// Get Avatar Folder from Site Settings
	$avatarDir = $set['avatarFolder'];

	// Record a Property Payment
	if (isset($_POST['submit']) && $_POST['submit'] == 'Record Payment') {
		if($_POST['paymentDate'] == "") {
			$msgBox = alertBox($paymentDateReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['paymentAmount'] == "") {
			$msgBox = alertBox($paymentAmountReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['paymentFor'] == "") {
			$msgBox = alertBox($paymentForReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['paymentType'] == "") {
			$msgBox = alertBox($paymentTypeReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			// Check if this is a Rental Payment
			if ($_POST['rentMonth'] == '') { $isRent = '0'; } else { $isRent = '1'; }
			// Set some variables
			$tenantId = $mysqli->real_escape_string($_POST['tenantId']);
			$leaseId = $mysqli->real_escape_string($_POST['leaseId']);
			$paymentDate = $mysqli->real_escape_string($_POST['paymentDate']);
			$paymentAmount = $mysqli->real_escape_string($_POST['paymentAmount']);
			$paymentPenalty = $mysqli->real_escape_string($_POST['paymentPenalty']);
			$paymentFor = $mysqli->real_escape_string($_POST['paymentFor']);
			$paymentType = $mysqli->real_escape_string($_POST['paymentType']);
			$rentMonth = $mysqli->real_escape_string($_POST['rentMonth']);
			$paymentNotes = htmlentities($_POST['paymentNotes']);

			$stmt = $mysqli->prepare("
								INSERT INTO
									payments(
										adminId,
										tenantId,
										leaseId,
										paymentDate,
										paymentAmount,
										paymentPenalty,
										paymentFor,
										paymentType,
										isRent,
										rentMonth,
										paymentNotes
									) VALUES (
										?,
										?,
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
			$stmt->bind_param('sssssssssss',
				$adminId,
				$tenantId,
				$leaseId,
				$paymentDate,
				$paymentAmount,
				$paymentPenalty,
				$paymentFor,
				$paymentType,
				$isRent,
				$rentMonth,
				$paymentNotes
			);
			$stmt->execute();
			$msgBox = alertBox($paymentSavedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			// Clear the form of Values
			$_POST['paymentDate'] = $_POST['paymentAmount'] = $_POST['paymentPenalty'] = $_POST['paymentFor'] = $_POST['paymentType'] = $_POST['paymentNotes'] = '';
			$stmt->close();
		}
	}

	// ----------------------------------------------------------------

	// Get Site Alert Data
    $alert = "SELECT
					isActive,
					alertTitle,
					alertText,
					DATE_FORMAT(alertDate,'%M %d, %Y') AS alertDate,
					UNIX_TIMESTAMP(alertDate) AS orderDate,
					alertStart,
					alertExpires
				FROM
					sitealerts
				WHERE
					alertStart <= DATE_SUB(CURDATE(),INTERVAL 0 DAY) AND
					alertExpires >= DATE_SUB(CURDATE(),INTERVAL 0 DAY) OR
					isActive = 1
				ORDER BY
					orderDate DESC";
    $alertres = mysqli_query($mysqli, $alert) or die('Error, retrieving Site Alert Data failed. ' . mysqli_error());

	// ----------------------------------------------------------------

	// Get latest payment data
    $payment = "SELECT
					payments.paymentId,
					payments.tenantId,
					payments.leaseId,
					payments.hasRefund,
					DATE_FORMAT(payments.paymentDate,'%M %d, %Y') AS paymentDate,
					UNIX_TIMESTAMP(payments.paymentDate) AS orderDate,
					payments.paymentAmount,
					payments.paymentPenalty,
					payments.isRent,
					payments.rentMonth,
					tenants.propertyId,
					tenants.tenantFirstName,
					tenants.tenantLastName,
					properties.propertyName
				FROM
					payments
					LEFT JOIN tenants ON payments.tenantId = tenants.tenantId
					LEFT JOIN properties ON tenants.propertyId = properties.propertyId
				WHERE
					payments.isRent = 1 AND
					payments.rentMonth = '".$currentMonth."'";
	$paymentres = mysqli_query($mysqli, $payment) or die('Error, retrieving Rent Payment Data failed. ' . mysqli_error());

	if(mysqli_num_rows($paymentres) > 0) {
		// Get the Totals
		$totals = "SELECT
					SUM(paymentAmount) AS totalPaid,
					SUM(paymentPenalty) AS totalFee
				FROM
					payments
				WHERE
					payments.isRent = 1 AND
					payments.rentMonth = '".$currentMonth."'";
		$total = mysqli_query($mysqli, $totals) or die('Error, retrieving Totals failed. ' . mysqli_error());
		$tot = mysqli_fetch_assoc($total);

		// Format the Amounts
		$totreceived = $tot['totalPaid'] + $tot['totalFee'];
		$totalReceived = $currencySym.format_amount($totreceived, 2);
	} else {
		$totalReceived = '';
	}

	// ----------------------------------------------------------------

	// Get Late Rent data
	// Note: This was a MAJOR Pain in the Arse -- No changes **Unless** you really know what you are doing.
	if($hasPaid = $mysqli->prepare("
								SELECT
									tenants.propertyId
								FROM
									tenants
									LEFT JOIN payments ON tenants.tenantId = payments.tenantId
								WHERE
									payments.rentMonth = ?"
	))
	$hasPaid->bind_param('s', $currentMonth);
	$hasPaid->execute();
	$hasPaid->bind_result($propertyId);
	$hasPaid->store_result();
    $totalrows = $hasPaid->num_rows;

	$propids = array();
	while($hasPaid->fetch()) {
		$propids[] = array(
			'propertyId' => $propertyId
		);
	}
	$hasPaid->close();

	// Get the Property ID list from the array
	foreach($propids as $v) $theIds[] = $v['propertyId'];

	if ($totalrows > 0) {
		$list = "'".implode("','",$theIds)."'";
	} else {
		$list = '0';
	}

	// Get the Property/Tenant info to display based on the array
	$today = date("Y-m-d");
	$latepay = "SELECT
					properties.propertyId,
					properties.propertyName,
					properties.propertyRate,
					properties.latePenalty,
					leases.leaseStart,
					tenants.tenantId,
					tenants.tenantFirstName,
					tenants.tenantLastName
				FROM
					properties
					LEFT JOIN leases ON properties.propertyId = leases.propertyId
					LEFT JOIN tenants ON properties.propertyId = tenants.propertyId
				WHERE
					properties.isLeased = 1 AND
					'".$today."' >= leases.leaseStart AND
					properties.propertyId NOT IN (".$list.")";
	$latepayres = mysqli_query($mysqli, $latepay) or die('Error, retrieving Late Tenants Data failed. ' . mysqli_error());

	// ----------------------------------------------------------------

	// Get current Tenants
	if ($superuser != '1') {
		$tenant = "SELECT
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
		$tenantres = mysqli_query($mysqli, $tenant) or die('Error, retrieving Current Tenant Data failed. ' . mysqli_error());
	} else {
		$tenant = "SELECT
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
					admins.adminId,
					admins.adminFirstName,
					admins.adminLastName
				FROM
					tenants
					LEFT JOIN properties ON tenants.propertyId = properties.propertyId
					LEFT JOIN leases ON tenants.leaseId = leases.leaseId
					LEFT JOIN assignedproperties ON tenants.propertyId = assignedproperties.propertyId
					LEFT JOIN admins ON assignedproperties.adminId = admins.adminId
				WHERE
					tenants.isActive = 1 AND
					tenants.leaseId != 0";
		$tenantres = mysqli_query($mysqli, $tenant) or die('Error, retrieving Current Tenant Data failed. ' . mysqli_error());
	}

	// ----------------------------------------------------------------

	// Get Unleased Properties
    $prop = "SELECT
					propertyId,
					propertyName,
					propertyAddress,
					isLeased,
					propertyRate,
					CASE petsAllowed
						WHEN 0 THEN 'No'
						WHEN 1 THEN 'Yes'
					END AS petsAllowed,
					propertyType,
					propertyStyle,
					propertySize,
					bedrooms,
					bathrooms,
					isArchived
				FROM
					properties
				WHERE
					isLeased = 0 AND
					isArchived = 0";
    $propres = mysqli_query($mysqli, $prop) or die('Error, retrieving Unleased Properties Data failed. ' . mysqli_error());

	// ----------------------------------------------------------------

	// Get Open Service Requests
	if ($superuser != '1') {
		$serv = "SELECT
					servicerequests.requestId,
					servicerequests.tenantId,
					servicerequests.leaseId,
					servicerequests.adminId,
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
					END AS status,
					servicerequests.requestTitle,
					DATE_FORMAT(servicerequests.lastUpdated,'%W, %M %e, %Y at %l:%i %p') AS lastUpdated,
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
					LEFT JOIN assignedproperties ON tenants.propertyId = assignedproperties.propertyId
					LEFT JOIN admins ON assignedproperties.adminId = admins.adminId
				WHERE
					admins.adminId = ".$adminId." AND
					servicerequests.requestStatus IN ('0','1','2')";
		$servres = mysqli_query($mysqli, $serv) or die('Error, retrieving Open Service Requests Data failed. ' . mysqli_error());
	} else {
		$serv = "SELECT
					servicerequests.requestId,
					servicerequests.tenantId,
					servicerequests.leaseId,
					servicerequests.adminId,
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
					END AS status,
					servicerequests.requestTitle,
					DATE_FORMAT(servicerequests.lastUpdated,'%W, %M %e, %Y at %l:%i %p') AS lastUpdated,
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
					LEFT JOIN assignedproperties ON tenants.propertyId = assignedproperties.propertyId
					LEFT JOIN admins ON assignedproperties.adminId = admins.adminId
				WHERE
					servicerequests.requestStatus IN ('0','1','2')";
		$servres = mysqli_query($mysqli, $serv) or die('Error, retrieving Open Service Requests Data failed. ' . mysqli_error());
	}

	// ----------------------------------------------------------------

	// Get Admin's Role & Avatar
	$a = "SELECT
			CASE adminRole
				WHEN 0 THEN 'Administrator'
				WHEN 1 THEN 'Landlord'
			END AS adminRole,
			adminAvatar
		FROM admins
		WHERE adminId = ".$adminId;
	$b = mysqli_query($mysqli, $a) or die('Error' . mysqli_error());
	$c = mysqli_fetch_assoc($b);
?>
<div class="row">
	<div class="col-md-6">
		<img alt="Admin Avatar" src="../<?php echo $avatarDir.$c['adminAvatar']; ?>" class="avatar" />
		<p class="lead welcomeMsg"><?php echo $welcomeMessage.' '.$c['adminRole'].' '.$adminFirstName.' '.$adminLastName; ?></p>
		<p><?php echo $welcomeMessageQuip; ?></p>
	</div>
	<div class="col-md-6">
		<?php while ($row = mysqli_fetch_assoc($alertres)) { ?>
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">
						<i class="fa fa-bullhorn"></i> <?php echo clean($row['alertTitle']); ?>
						<span class="floatRight">
							<?php echo $row['alertDate']; ?>
						</span>
					</h3>
				</div>
				<div class="panel-body">
					<?php echo nl2br(clean($row['alertText'])); ?>
				</div>
			</div>
		<?php } ?>
	</div>
</div>

<?php if ($msgBox) { echo $msgBox; } ?>

<?php
	// Late Rent
	if ($superuser == '1') {			// Only Superusers
		if ($currentDay > '5') {		// Only if over the 5 day grace period (shows on day 6 - server time)
			if(mysqli_num_rows($latepayres) > 0) {
?>
				<h3 class="success"><?php echo $lateRentH3.' '.$currentMonth; ?></h3>

				<table id="responsiveTable" class="large-only" cellspacing="0">
					<tr align="left" class="warning">
						<th><?php echo $tab_property; ?></th>
						<th><?php echo $tab_tenant; ?></th>
						<th><?php echo $tab_rentAmount; ?></th>
						<th><?php echo $tab_lateFee; ?></th>
						<th><?php echo $tab_totalDue; ?></th>
					</tr>
					<tbody class="table-hover">
<?php
					while ($late = mysqli_fetch_assoc($latepayres)) {
						// Format the Amounts
						$propertyRate = $currencySym.format_amount($late['propertyRate'], 2);
						$latePenalty = $currencySym.format_amount($late['latePenalty'], 2);
						$total = $late['propertyRate'] + $late['latePenalty'];
						$totalDue = $currencySym.format_amount($total, 2);
?>
						<tr>
							<td><a href="index.php?action=propertyInfo&propertyId=<?php echo $late['propertyId']; ?>"><?php echo clean($late['propertyName']); ?></a></td>
							<td><a href="index.php?action=tenantInfo&tenantId=<?php echo $late['tenantId']; ?>"><?php echo clean($late['tenantFirstName']).' '.clean($late['tenantLastName']); ?></a></td>
							<td><?php echo $propertyRate; ?></td>
							<td><?php echo $latePenalty; ?></td>
							<td class="amtDue tool-tip" title="Rent Amount + Late Fee"><?php echo $totalDue; ?></td>
						</tr>
			<?php
					}
			?>
				</tbody>
			</table>

			<hr />
<?php
			}
		}
?>

<h3 class="success"><?php echo $rentReceivedMonthH3.' '.$currentMonth; ?></h3>

<?php if(mysqli_num_rows($paymentres) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noRentReceived; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTableTwo" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_property; ?></th>
			<th><?php echo $tab_tenant; ?></th>
			<th><?php echo $tab_paymentDate; ?></th>
			<th><?php echo $tab_rentalMonth; ?></th>
			<th><?php echo $tab_amount; ?></th>
			<th><?php echo $tab_lateFeePaid; ?></th>
			<th><?php echo $tab_totalPaid; ?></th>
			<th></th>
		</tr>
		<tbody class="table-hover">
		<?php
			while ($pay = mysqli_fetch_assoc($paymentres)) {
				// Format the Amounts
				$paymentAmount = $currencySym.format_amount($pay['paymentAmount'], 2);
				if ($pay['paymentPenalty'] != '') { $paymentPenalty = $currencySym.format_amount($pay['paymentPenalty'], 2); } else { $paymentPenalty = ''; }
				$total = $pay['paymentAmount'] + $pay['paymentPenalty'];
				$totalPaid = $currencySym.format_amount($total, 2);

				// Check for Refunds
				if ($pay['hasRefund'] == '1') { $hasRefund = '<sup><i class="fa fa-asterisk tool-tip has-refund" title="'.$amountReflectRefund.'"></i></sup>'; } else { $hasRefund = ''; }
		?>
				<tr>
					<td><a href="index.php?action=propertyInfo&propertyId=<?php echo $pay['propertyId']; ?>"><?php echo clean($pay['propertyName']); ?></a></td>
					<td><a href="index.php?action=tenantInfo&tenantId=<?php echo $pay['tenantId']; ?>"><?php echo clean($pay['tenantFirstName']).' '.clean($pay['tenantLastName']); ?></a></td>
					<td><?php echo $pay['paymentDate']; ?></td>
					<td><?php echo $pay['rentMonth']; ?></td>
					<td><?php echo $paymentAmount.' '.$hasRefund; ?></td>
					<td><?php echo $paymentPenalty; ?></td>
					<td><?php echo $totalPaid." ".$hasRefund; ?></td>
					<td><a href="index.php?action=receipt&paymentId=<?php echo $pay['paymentId']; ?>" target="_blank"><i class="fa fa-print"></i> <?php echo $receiptBtn; ?></a></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<p><span class="reportTotal"><strong><?php echo $totalRentReceived.' '.$currentMonth; ?>:</strong> <?php echo $totalReceived; ?></span></p>
<?php
		}
	}

	if(mysqli_num_rows($tenantres) > 0) {
?>
	<hr />
	<h3 class="primary"><?php echo $currentTenantsH3; ?></h3>

	<table id="responsiveTableThree" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_tenant; ?></th>
			<th><?php echo $tab_email; ?></th>
			<th><?php echo $tab_phone; ?></th>
			<th><?php echo $tab_property; ?></th>
			<th><?php echo $tab_monthlyRate; ?></th>
			<th><?php echo $tab_leaseEndsOn; ?></th>
			<?php if ($superuser == '1') { ?>
				<th><?php echo $tab_landlord; ?></th>
			<?php }	?>
			<th></th>
		</tr>
		<tbody class="table-hover">
		<?php
			while ($ten = mysqli_fetch_assoc($tenantres)) {
				// Decrypt Tenant data
				if ($ten['tenantPhone'] != '') { $tenantPhone = decryptIt($ten['tenantPhone']); } else { $tenantPhone = ''; }
				// Format the Amounts
				$propertyRate = $currencySym.format_amount($ten['propertyRate'], 2);
		?>
			<tr>
				<td><a href="index.php?action=tenantInfo&tenantId=<?php echo $ten['tenantId']; ?>"><?php echo clean($ten['tenantFirstName']).' '.clean($ten['tenantLastName']); ?></a></td>
				<td><?php echo clean($ten['tenantEmail']); ?></td>
				<td><?php echo $tenantPhone; ?></td>
				<td><a href="index.php?action=propertyInfo&propertyId=<?php echo $ten['propertyId']; ?>"><?php echo clean($ten['propertyName']); ?></a></td>
				<td><?php echo $propertyRate; ?></td>
				<td><?php echo $ten['leaseEnd']; ?></td>
				<?php if ($superuser == '1') { ?>
					<td><?php echo clean($ten['adminFirstName']).' '.clean($ten['adminLastName']); ?></td>
				<?php }	?>
				<td><a data-toggle="modal" href="#recordPayment<?php echo $ten['tenantId']; ?>" class="tool-tip" title="Record a Payment received for this Tenant"><i class="fa fa-credit-card"></i></a></td>
			</tr>

			<!-- RECORD A PAYMENT RECEIVED MODAL -->
			<div class="modal fade" id="recordPayment<?php echo $ten['tenantId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header modal-success">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
							<h4 class="modal-title"><?php echo $recordPaymentBtn; ?></h4>
						</div>
						<form action="" method="post">
							<div class="modal-body">
								<div class="form-group">
									<label for="paymentDate"><?php echo $paymentDateField; ?></label>
									<input type="text" class="form-control" name="paymentDate" id="paymentDate[<?php echo $count; ?>]" value="<?php echo isset($_POST['paymentDate']) ? $_POST['paymentDate'] : '' ?>">
									<span class="help-block"><?php echo $paymentDateHelper; ?>.</span>
								</div>
								<div class="form-group">
									<label for="paymentAmount"><?php echo $paymentAmountField; ?></label>
									<input type="text" class="form-control" name="paymentAmount" value="<?php echo isset($_POST['paymentAmount']) ? $_POST['paymentAmount'] : '' ?>">
									<span class="help-block"><?php echo $paymentAmountHelper; ?></span>
								</div>
								<div class="form-group">
									<label for="paymentPenalty"><?php echo $lateFeeField; ?></label>
									<input type="text" class="form-control" name="paymentPenalty" value="<?php echo isset($_POST['paymentPenalty']) ? $_POST['paymentPenalty'] : '' ?>">
									<span class="help-block"><?php echo $lateFeeHelper; ?></span>
								</div>
								<div class="form-group">
									<label for="paymentFor"><?php echo $paymentForField; ?></label>
									<input type="text" class="form-control" name="paymentFor" value="<?php echo isset($_POST['paymentFor']) ? $_POST['paymentFor'] : '' ?>">
									<span class="help-block"><?php echo $paymentForHelper; ?></span>
								</div>
								<div class="form-group">
									<label for="paymentType"><?php echo $paymentTypeField; ?></label>
									<input type="text" class="form-control" name="paymentType" value="<?php echo isset($_POST['paymentType']) ? $_POST['paymentType'] : '' ?>">
									<span class="help-block"><?php echo $paymentTypeHelper; ?></span>
								</div>
								<div class="form-group">
									<label for="rentMonth"><?php echo $rentMonthField; ?></label>
									<select class="form-control" name="rentMonth">
										<option value=""><?php echo $monthNoneSelect; ?></option>
										<option value="<?php echo $monthJanuarySelect; ?>"><?php echo $monthJanuarySelect; ?></option>
										<option value="<?php echo $monthFebruarySelect; ?>"><?php echo $monthFebruarySelect; ?></option>
										<option value="<?php echo $monthMarchSelect; ?>"><?php echo $monthMarchSelect; ?></option>
										<option value="<?php echo $monthAprilSelect; ?>"><?php echo $monthAprilSelect; ?></option>
										<option value="<?php echo $monthMaySelect; ?>"><?php echo $monthMaySelect; ?></option>
										<option value="<?php echo $monthJuneSelect; ?>"><?php echo $monthJuneSelect; ?></option>
										<option value="<?php echo $monthJulySelect; ?>"><?php echo $monthJulySelect; ?></option>
										<option value="<?php echo $monthAugustSelect; ?>"><?php echo $monthAugustSelect; ?></option>
										<option value="<?php echo $monthSeptemberSelect; ?>"><?php echo $monthSeptemberSelect; ?></option>
										<option value="<?php echo $monthOctoberSelect; ?>"><?php echo $monthOctoberSelect; ?></option>
										<option value="<?php echo $monthNovemberSelect; ?>"><?php echo $monthNovemberSelect; ?></option>
										<option value="<?php echo $monthDecemberSelect; ?>"><?php echo $monthDecemberSelect; ?></option>
									</select>
									<span class="help-block"><?php echo $rentMonthHelper; ?></span>
								</div>
								<div class="form-group">
									<label for="paymentNotes"><?php echo $paymentNotesField; ?></label>
									<textarea class="form-control" name="paymentNotes" rows="2"><?php echo isset($_POST['paymentNotes']) ? $_POST['paymentNotes'] : '' ?></textarea>
									<span class="help-block"><?php echo $paymentNotesHelper; ?> <?php echo $htmlNotAllowed; ?></span>
								</div>
							</div>
							<div class="modal-footer">
								<input type="hidden" name="tenantId" value="<?php echo $ten['tenantId']; ?>">
								<input type="hidden" name="leaseId" value="<?php echo $ten['leaseId']; ?>">
								<button type="input" name="submit" value="Record Payment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $savePaymentBtn; ?></button>
								<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
							</div>
						</form>
					</div>
				</div>
			</div>
		<?php
			$count++;
			}
		?>
		</tbody>
	</table>
<?php
	}

	if(mysqli_num_rows($propres) > 0) {
?>
	<hr />
	<h3 class="info"><?php echo $availablePropertiesH3; ?></h3>

	<table id="responsiveTableFour" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_property; ?></th>
			<th><?php echo $tab_propertyType; ?></th>
			<th><?php echo $tab_address; ?></th>
			<th><?php echo $tab_monthlyRate; ?></th>
			<th><?php echo $tab_petsAllowed; ?></th>
			<th><?php echo $tab_propertySize; ?></th>
			<th><?php echo $tab_bedroomsBathrooms; ?></th>
		</tr>
		<tbody class="table-hover">
		<?php
			while ($prop = mysqli_fetch_assoc($propres)) {
				// Format the Amounts
				$propertyRate = $currencySym.format_amount($prop['propertyRate'], 2);
		?>
			<tr>
				<td><a href="index.php?action=propertyInfo&propertyId=<?php echo $prop['propertyId']; ?>"><?php echo clean($prop['propertyName']); ?></a></td>
				<td><?php echo clean($prop['propertyType']).' '.clean($prop['propertyStyle']); ?></td>
				<td><?php echo clean($prop['propertyAddress']); ?></td>
				<td><?php echo $propertyRate; ?></td>
				<td><?php echo $prop['petsAllowed']; ?></td>
				<td><?php echo clean($prop['propertySize']); ?></td>
				<td><?php echo $prop['bedrooms'].' / '.$prop['bathrooms']; ?></td>
			</tr>
		<?php }	?>
		</tbody>
	</table>
<?php
	}

	if(mysqli_num_rows($servres) > 0) {
?>
	<hr />
	<h3 class="warning"><?php echo $openServRequestsH3; ?></h3>

	<table id="responsiveTableFive" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_requestTitle; ?></th>
			<th><?php echo $tab_tenant; ?></th>
			<th><?php echo $tab_property; ?></th>
			<th><?php echo $tab_dateRequested; ?></th>
			<th><?php echo $tab_priority; ?></th>
			<th><?php echo $tab_status; ?></th>
			<th><?php echo $tab_lastUpdated; ?></th>
		</tr>
		<tbody class="table-hover">
		<?php while ($req = mysqli_fetch_assoc($servres)) { ?>
			<tr>
				<td><a href="index.php?action=viewRequest&requestId=<?php echo $req['requestId']; ?>"><?php echo clean($req['requestTitle']); ?></a></td>
				<td><a href="index.php?action=tenantInfo&tenantId=<?php echo $req['tenantId']; ?>"><?php echo clean($req['tenantFirstName']).' '.clean($req['tenantLastName']); ?></a></td>
				<td><a href="index.php?action=propertyInfo&propertyId=<?php echo $req['propertyId']; ?>"><?php echo clean($req['propertyName']); ?></a></td>
				<td><?php echo $req['requestDate']; ?></td>
				<td><?php echo $req['requestPriority']; ?></td>
				<td><?php echo $req['status']; ?></td>
				<td><?php echo $req['lastUpdated']; ?></td>
			</tr>
		<?php }	?>
		</tbody>
	</table>
<?php }	?>