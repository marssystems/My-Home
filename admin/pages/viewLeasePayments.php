<?php
	$leaseId = $_GET['leaseId'];
	$stacktable = 'true';
	$jsFile = 'viewLeasePayments';
	$datePicker = 'true';

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

    // Get payment data
    $query = "
        SELECT
            payments.paymentId,
			payments.adminId,
			payments.tenantId,
			payments.leaseId,
            payments.hasRefund,
			DATE_FORMAT(payments.paymentDate,'%M %d, %Y') AS paymentDate,
			payments.paymentAmount,
			payments.paymentPenalty,
			payments.paymentFor,
			payments.paymentType,
			payments.isRent,
			payments.rentMonth,
			admins.adminFirstName,
			admins.adminLastName
		FROM
			payments
			LEFT JOIN admins ON payments.adminId = admins.adminId
        WHERE
			payments.leaseId = ".$leaseId;
	$res = mysqli_query($mysqli, $query) or die('Error, retrieving Payment Data failed. ' . mysqli_error());

	// Get the Property/Tenant info
	$sqlStmt = "SELECT
					tenants.tenantId,
					tenants.propertyId,
					tenants.leaseId,
					tenants.tenantFirstName,
					tenants.tenantLastName,
					properties.propertyName,
					properties.propertyRate,
					properties.latePenalty
				FROM
					tenants
					LEFT JOIN properties ON tenants.propertyId = properties.propertyId
				WHERE
					tenants.leaseId = ".$leaseId;
	$result = mysqli_query($mysqli, $sqlStmt) or die('Error, retrieving Property/Tenant Data failed. ' . mysqli_error());
	$rows = mysqli_fetch_assoc($result);
?>
<h3 class="success"><?php echo $rows['propertyName'].' &mdash; '.$allPaymentsH3; ?></h3>
<div class="row">
	<div class="col-md-8">
		<p class="lead">
			<?php echo $allPaymentsQuip; ?><br />
			<small>
				<?php echo $tab_tenant; ?>: <a href="index.php?action=tenantInfo&tenantId=<?php echo $rows['tenantId']; ?>"><?php echo clean($rows['tenantFirstName']).' '.clean($rows['tenantLastName']); ?></a>
			</small>
		</p>

		<?php if ($msgBox) { echo $msgBox; } ?>
	</div>
	<div class="col-md-4">
		<span class="floatRight">
			<a data-toggle="modal" href="#recordPayment" class="btn btn-success btn-icon"><i class="fa fa-credit-card"></i> <?php echo $newPaymentBtnLink; ?></a>
		</span>
	</div>
</div>

<?php if (mysqli_num_rows($res) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noPaymentsRecorded; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTable" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_paymentFor; ?></th>
			<th><?php echo $tab_paymentDate; ?></th>
			<th><?php echo $tab_rentalMonth; ?></th>
			<th><?php echo $tab_receivedBy; ?></th>
			<th><?php echo $tab_amount; ?></th>
			<th><?php echo $tab_lateFeePaid; ?></th>
			<th><?php echo $tab_totalPaid; ?></th>
			<th></th>
		</tr>
		<tbody class="table-hover">
		<?php
			while ($row = mysqli_fetch_assoc($res)) {
				// Format the Amounts
				$paymentAmount = $currencySym.format_amount($row['paymentAmount'], 2);
				if ($row['paymentPenalty'] != '') { $paymentPenalty = $currencySym.format_amount($row['paymentPenalty'], 2); } else { $paymentPenalty = ''; }
				$total = $row['paymentAmount'] + $row['paymentPenalty'];
				$totalPaid = $currencySym.format_amount($total, 2);
				// Check for Refunds
				if ($row['hasRefund'] == '1') { $hasRefund = '<sup><i class="fa fa-asterisk tool-tip has-refund" title="'.$amountReflectRefund.'"></i></sup>'; } else { $hasRefund = ''; }
		?>
				<tr>
					<td>
						<a href="index.php?action=viewPayment&paymentId=<?php echo $row['paymentId']; ?>"><?php echo clean($row['paymentFor']); ?></a>
					</td>
					<td><?php echo $row['paymentDate']; ?></td>
					<td><?php echo $row['rentMonth']; ?></td>
					<td><?php echo clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?></td>
					<td><?php echo $paymentAmount; ?></td>
					<td><?php echo $paymentPenalty; ?></td>
					<td><?php echo $totalPaid." ".$hasRefund; ?></td>
					<td class="tool-tip" title="<?php echo $viewPrintReceipt; ?>">
						<a href="index.php?action=receipt&paymentId=<?php echo $row['paymentId']; ?>" target="_blank"><i class="fa fa-print"></i></a>
					</td>
				</tr>
		<?php } ?>
		</tbody>
	</table>
<?php } ?>

<!-- RECORD A PAYMENT RECEIVED MODAL -->
<div class="modal fade" id="recordPayment" tabindex="-1" role="dialog" aria-hidden="true">
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
						<input type="text" class="form-control" name="paymentDate" id="paymentDate" value="<?php echo isset($_POST['paymentDate']) ? $_POST['paymentDate'] : '' ?>">
						<span class="help-block"><?php echo $paymentDateHelper; ?></span>
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
					<input type="hidden" name="tenantId" value="<?php echo $rows['tenantId']; ?>">
					<input type="hidden" name="leaseId" value="<?php echo $rows['leaseId']; ?>">
					<button type="input" name="submit" value="Record Payment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $savePaymentBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>