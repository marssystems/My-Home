<?php
	$stacktable = 'true';
	$hasLateRent = '';

	// Get the Current Month
    $currentMonth = date('F');
	// Get the Current Day
    $currentDay = date('d');
	// Get the Current Date
    $currentDate = date("Y-m-d");

	// Get Avatar Folder from Site Settings
	$avatarDir = $set['avatarFolder'];

	// Get Site Alert Data
    $alert  = "SELECT
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
					adminId = ".$_SESSION['adminId']." AND
					alertStart <= DATE_SUB(CURDATE(),INTERVAL 0 DAY) AND
					alertExpires >= DATE_SUB(CURDATE(),INTERVAL 0 DAY) AND
					isActive = 1
				ORDER BY
					orderDate DESC";
					
    $alertres = mysqli_query($mysqli, $alert) or die('Error, retrieving Alert Data failed. ' . mysqli_error());

	if ($leaseId != '0') {
		// Get Current Lease Info
		$lease = "
			SELECT
				tenants.propertyId,
				tenants.leaseId,
				properties.propertyName,
				properties.propertyRate,
				properties.latePenalty,
				CASE properties.petsAllowed
					WHEN 0 THEN 'No'
					WHEN 1 THEN 'Yes'
				END AS petsAllowed,
				leases.leaseTerm,
				leases.leaseStart,
				leases.leaseEnd,
				DATE_FORMAT(leases.leaseEnd,'%M %d, %Y') AS leaseEnds,
				leases.isClosed,
				assignedproperties.adminId,
				admins.adminFirstName,
				admins.adminLastName
			FROM
				tenants
				LEFT JOIN properties ON tenants.propertyId = properties.propertyId
				LEFT JOIN leases ON tenants.leaseId = leases.leaseId
				LEFT JOIN assignedproperties ON tenants.propertyId = assignedproperties.propertyId
				LEFT JOIN admins ON assignedproperties.adminId = admins.adminId
			WHERE
				tenants.tenantId = ".$_SESSION['tenantId']." AND
				leases.isClosed = 0";
		$leaseres = mysqli_query($mysqli, $lease) or die('Error, retrieving Current Lease Data failed. ' . mysqli_error());
		$col = mysqli_fetch_assoc($leaseres);

		// Format the Amounts
		$propertyRate = $currencySym.format_amount($col['propertyRate'], 2);
		$latePenalty = $currencySym.format_amount($col['latePenalty'], 2);

		// Get latest payment data
		$payment = "
			SELECT
				paymentId,
				tenantId,
				leaseId,
				hasRefund,
				DATE_FORMAT(paymentDate,'%M %d, %Y') AS paymentDate,
				UNIX_TIMESTAMP(paymentDate) AS orderDate,
				paymentAmount,
				paymentPenalty,
				paymentFor,
				paymentType,
				isRent,
				rentMonth
			FROM
				payments
			WHERE
				tenantId = ".$_SESSION['tenantId']." AND
				isRent = 1
			ORDER BY orderDate DESC
			LIMIT 1";
		$paymentres = mysqli_query($mysqli, $payment) or die('Error, retrieving Payment Data failed. ' . mysqli_error());
		$pay = mysqli_fetch_assoc($paymentres);

		// Format the Amounts
		$paymentAmount = $currencySym.format_amount($pay['paymentAmount'], 2);
		if ($pay['paymentPenalty'] != '') {
			$paymentPenalty = $currencySym.format_amount($pay['paymentPenalty'], 2);
		} else {
			$paymentPenalty = '';
		}
		$total = $pay['paymentAmount'] + $pay['paymentPenalty'];
		$totalPaid = $currencySym.format_amount($total, 2);

		// Check for Refunds
		if ($pay['hasRefund'] == '1') { $hasRefund = '<sup><i class="fa fa-asterisk tool-tip" title="'.$amountReflectRefund.'"></i></sup>'; } else { $hasRefund = ''; }

		// Check if the Tenant is late on current month's rent
		if ($currentDate > $col['leaseStart']) {
			$latecheck = "SELECT
							payments.isRent,
							payments.rentMonth,
							tenants.propertyId
						FROM
							payments
							LEFT JOIN tenants ON payments.tenantId = tenants.tenantId
						WHERE
							tenants.propertyId = ".$col['propertyId']." AND
							payments.isRent = 1 AND
							payments.rentMonth = '".$currentMonth."'";
			$lateres = mysqli_query($mysqli, $latecheck) or die('Error, retrieving Late Rent Data failed. ' . mysqli_error());
			if(mysqli_num_rows($lateres) < 1) {
				$hasLateRent = 'true';
			}
		} else {
			$hasLateRent = '';
		}

		// Get Service Request Data
		$service = "
			SELECT
				requestId,
				tenantId,
				leaseId,
				DATE_FORMAT(requestDate,'%M %d, %Y') AS requestDate,
				CASE requestPriority
					WHEN 0 THEN 'Normal'
					WHEN 1 THEN 'Important'
					WHEN 2 THEN 'Urgent'
				END AS requestPriority,
				CASE requestStatus
					WHEN 0 THEN 'Open'
					WHEN 1 THEN 'Work in Progress'
					WHEN 2 THEN 'Waiting for Parts'
					WHEN 3 THEN 'Completed/No Repair Needed'
					WHEN 4 THEN 'Completed Repair'
					WHEN 5 THEN 'Closed'
				END AS requestStatus,
				requestTitle,
				DATE_FORMAT(lastUpdated,'%M %d, %Y') AS lastUpdated
				FROM
					servicerequests
				WHERE
					requestStatus IN ('0', '1', '2') AND
					tenantId = ".$_SESSION['tenantId']."
				ORDER BY requestId";
		$serviceres = mysqli_query($mysqli, $service) or die('Error, retrieving Service Data failed. ' . mysqli_error());
	}

	// Get the Tenant's Avatar
	$avatar = "SELECT tenantAvatar FROM tenants WHERE tenantId = ".$_SESSION['tenantId'];
	$avatarres = mysqli_query($mysqli, $avatar) or die('Error, retrieving Tenant Avatar failed. ' . mysqli_error());
	$a = mysqli_fetch_assoc($avatarres);
?>
<div class="row">
	<div class="col-md-6">
		<img alt="Tenant Avatar" src="<?php echo $avatarDir.$a['tenantAvatar']; ?>" class="avatar" />
		<p class="lead welcomeMsg"><?php echo $dashboardWelcome; ?></p>
		<p><?php echo $dashQuip; ?></p>
	</div>
	<div class="col-md-6">
		<?php
			if ($leaseId != '0') {
				if ($hasLateRent == 'true') {
					if ($currentDay > '5') {
		?>
						<div class="alertMsg warning">
							<i class="fa fa-warning"></i> <?php echo $rentIsPastDueMsg; ?>
						</div>
		<?php
					}
				}
			}
		?>

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

<h3 class="primary"><?php echo $currentLeaseH3; ?></h3>

<?php if ($leaseId == '0') { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noPropertyMsg; ?>
	</div>
	<p><?php echo $noLeasedProperty; ?></p>
<?php } else { ?>
	<p><?php echo $leaseQuip; ?></p>

	<table id="responsiveTable" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_propName; ?></th>
			<th><?php echo $tab_monthlyRent; ?></th>
			<th><?php echo $tab_lateFee; ?></th>
			<th><?php echo $tab_petsAllowed; ?></th>
			<th><?php echo $tab_leaseTerm; ?></th>
			<th><?php echo $tab_landlord; ?></th>
			<th><?php echo $tab_leaseEnds; ?></th>
		</tr>
		<tbody class="table-hover">
			<tr>
				<td><?php echo $col['propertyName']; ?></td>
				<td><?php echo $propertyRate; ?></td>
				<td><?php echo $latePenalty; ?></td>
				<td><?php echo $col['petsAllowed']; ?></td>
				<td><?php echo clean($col['leaseTerm']); ?></td>
				<td><?php echo $col['adminFirstName'].' '.$col['adminLastName']; ?></td>
				<td><?php echo $col['leaseEnds']; ?></td>
			</tr>
		</tbody>
	</table>

	<hr />

	<h3 class="success"><?php echo $lastpaymentH3; ?></h3>
	<?php if(mysqli_num_rows($paymentres) < 1) { ?>
		<div class="alertMsg default">
			<i class="fa fa-minus-square-o"></i> <?php echo $noPaymentMsg; ?>
		</div>
	<?php } else { ?>
		<p><?php echo $paymentQuip; ?></p>

		<table id="responsiveTableTwo" class="large-only" cellspacing="0">
			<tr align="left">
				<th><?php echo $tab_paymentDate; ?></th>
				<th><?php echo $tab_paidBy; ?></th>
				<th><?php echo $tab_amount; ?></th>
				<th><?php echo $tab_lateFeePaid; ?></th>
				<th><?php echo $tab_for; ?></th>
				<th><?php echo $tab_rentalMonth; ?></th>
				<th><?php echo $tab_totalPaid; ?></th>
				<th></th>
			</tr>
			<tbody class="table-hover">
				<tr>
					<td><?php echo $pay['paymentDate']; ?></td>
					<td><?php echo $pay['paymentType']; ?></td>
					<td><?php echo $paymentAmount; ?></td>
					<td><?php echo $paymentPenalty; ?></td>
					<td><?php echo clean($pay['paymentFor']); ?></td>
					<td><?php echo $pay['rentMonth']; ?></td>
					<td><?php echo $totalPaid." ".$hasRefund; ?></td>
					<td><a href="index.php?page=receipt&paymentId=<?php echo $pay['paymentId']; ?>" target="_blank"><i class="fa fa-print"></i> <?php echo $td_receipt; ?></a></td>
				</tr>
			</tbody>
		</table>
	<?php } ?>

	<?php if(mysqli_num_rows($serviceres) > 0) { ?>
		<hr />

		<h3 class="warning"><?php echo $openServReqH3; ?></h3>
		<p><?php echo $openServReqQuip; ?></p>

		<table id="responsiveTableThree" class="large-only" cellspacing="0">
			<tr align="left">
				<th><?php echo $tab_request; ?></th>
				<th><?php echo $tab_dateRequested; ?></th>
				<th><?php echo $tab_priority; ?></th>
				<th><?php echo $tab_status; ?></th>
				<th><?php echo $tab_lastUpdated; ?></th>
				<th></th>
			</tr>
			<tbody class="table-hover">
			<?php while ($serv = mysqli_fetch_assoc($serviceres)) { ?>
				<tr>
					<td><?php echo $serv['requestTitle']; ?></td>
					<td><?php echo $serv['requestDate']; ?></td>
					<td><?php echo $serv['requestPriority']; ?></td>
					<td><?php echo clean($serv['requestStatus']); ?></td>
					<td><?php echo $serv['lastUpdated']; ?></td>
					<td><a href="index.php?page=viewRequest&requestId=<?php echo $serv['requestId']; ?>"><?php echo $td_view; ?> <i class="fa fa-long-arrow-right"></i></a></td>
				</tr>
			<?php }	?>
			</tbody>
		</table>
	<?php } ?>

<?php } ?>