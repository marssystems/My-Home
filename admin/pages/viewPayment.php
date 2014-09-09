<?php
	$paymentId = $_GET['paymentId'];
	$jsFile = 'viewPayment';
	$datePicker = 'true';

	// Issue a Refund
	if (isset($_POST['submit']) && $_POST['submit'] == 'Issue Refund') {
		if($_POST['refundDate'] == "") {
			$msgBox = alertBox($refundDateReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['refundAmount'] == "") {
			$msgBox = alertBox($refundAmountReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['refundFor'] == "") {
			$msgBox = alertBox($refundForReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			// Set some variables
			$tenantId = $mysqli->real_escape_string($_POST['tenantId']);
			$leaseId = $mysqli->real_escape_string($_POST['leaseId']);
			$propertyId = $mysqli->real_escape_string($_POST['propertyId']);
			$refundDate = $mysqli->real_escape_string($_POST['refundDate']);
			$refundAmount = $mysqli->real_escape_string($_POST['refundAmount']);
			$refundFor = $mysqli->real_escape_string($_POST['refundFor']);
			$refundNotes = htmlentities($_POST['refundNotes']);

			// Get the original Payment Amount
			$originalAmt = $mysqli->real_escape_string($_POST['originalAmt']);
			$newAmt = $originalAmt - $refundAmount;
			$newPaymentAmt = $newAmt.'.00';
			$hasRefund = '1';

			$stmt = $mysqli->prepare("
								INSERT INTO
									refunds(
										paymentId,
										propertyId,
										leaseId,
										tenantId,
										refundDate,
										refundAmount,
										refundFor,
										refundedBy,
										refundNotes
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
				$paymentId,
				$propertyId,
				$leaseId,
				$tenantId,
				$refundDate,
				$refundAmount,
				$refundFor,
				$adminId,
				$refundNotes
			);
			$stmt->execute();
			$stmt->close();

			// Update the Original Payment
			$upd = $mysqli->prepare("
                                UPDATE
                                    payments
                                SET
									hasRefund = ?,
									paymentAmount = ?
                                WHERE
									paymentId = ?");
            $upd->bind_param('sss',
								$hasRefund,
								$newPaymentAmt,
								$paymentId
			);
            $upd->execute();

			$msgBox = alertBox($refundIssuedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			// Clear the form of Values
			$_POST['refundDate'] = $_POST['refundAmount'] = $_POST['refundFor'] = $_POST['refundNotes'] = '';
			$upd->close();
		}
	}

	// Update Payment
	if (isset($_POST['submit']) && $_POST['submit'] == 'Update Payment') {
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
			if ($_POST['rentMonth'] == '...') { $isRent = '0'; } else { $isRent = '1'; }
			// Set some variables
			$paymentDate = $mysqli->real_escape_string($_POST['paymentDate']);
			$paymentAmount = $mysqli->real_escape_string($_POST['paymentAmount']);
			$paymentPenalty = $mysqli->real_escape_string($_POST['paymentPenalty']);
			$paymentFor = $mysqli->real_escape_string($_POST['paymentFor']);
			$paymentType = $mysqli->real_escape_string($_POST['paymentType']);
			$rentMonth = $mysqli->real_escape_string($_POST['rentMonth']);
			$paymentNotes = htmlentities($_POST['paymentNotes']);

			$stmt = $mysqli->prepare("
								UPDATE
									payments
								SET
									paymentDate = ?,
									paymentAmount = ?,
									paymentPenalty = ?,
									paymentFor = ?,
									paymentType = ?,
									isRent = ?,
									rentMonth = ?,
									paymentNotes = ?
								WHERE
									paymentId = ?
			");
			$stmt->bind_param('sssssssss',
								   $paymentDate,
								   $paymentAmount,
								   $paymentPenalty,
								   $paymentFor,
								   $paymentType,
								   $isRent,
								   $rentMonth,
								   $paymentNotes,
								   $paymentId
			);
			$stmt->execute();
			$msgBox = alertBox($paymentUpdatedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			$stmt->close();
		}
	}

	// Email Receipt
	if (isset($_POST['submit']) && $_POST['submit'] == 'sendReceipt') {
		// Validation
        if($_POST['emailSubject'] == "") {
            $msgBox = alertBox($emailSubjectReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {

			// Send out the email in HTML
			$emailSubject = $mysqli->real_escape_string($_POST['emailSubject']);
			$emailNote = htmlentities(clean($_POST['emailNote']));
			$tenantEmail = $mysqli->real_escape_string($_POST['tenantEmail']);
			$datePaid = $mysqli->real_escape_string($_POST['paymentDate']);
			$paymentAmount = $mysqli->real_escape_string($_POST['paymentAmount']);
			$paymentPenalty = $mysqli->real_escape_string($_POST['paymentPenalty']);
			$paymentFor = $mysqli->real_escape_string($_POST['paymentFor']);
			$paymentType = $mysqli->real_escape_string($_POST['paymentType']);
			$paymentNotes = htmlentities($_POST['paymentNotes']);

			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$businessEmail = $set['businessEmail'];

			$subject = $emailSubject;

			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<hr>';
			$message .= '<p>'.$emailNote.'</p>';
			$message .= '<hr>';
			$message .= '<h3>Payment Receipt</h3>';
			$message .= '<p>
							Payment Date: '.$datePaid.'<br />
							Payment Amount: '.$paymentAmount.'<br />
							Late Fee Paid: '.$paymentPenalty.'<br />
							Payment For: '.$paymentFor.'<br />
							Payment Form: '.$paymentType.'<br />
							Payment Notes: '.$paymentNotes.'<br />
						</p>';
			$message .= '<hr>';
			$message .= '<p>Thank you,<br>'.$adminFirstName.' '.$adminLastName.'</p>';
			$message .= '<p>'.$siteName.'</p>';
			$message .= '</body></html>';

			$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
			$headers .= "Reply-To: ".$businessEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($tenantEmail, $subject, $message, $headers)) {
				$msgBox = alertBox($emailReceiptSentMsg, "<i class='fa fa-check-square-o'></i>", "success");
				// Clear the form of Values
				$_POST['emailSubject'] = $_POST['emailNote'] = '';
			}

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
			payments.paymentDate,
			DATE_FORMAT(payments.paymentDate,'%M %d, %Y') AS datePaid,
			payments.paymentAmount,
			payments.paymentPenalty,
			payments.paymentFor,
			payments.paymentType,
			payments.isRent,
			payments.rentMonth,
			payments.paymentNotes,
			admins.adminFirstName,
			admins.adminLastName,
			tenants.tenantId,
			tenants.propertyId,
			tenants.leaseId,
			tenants.tenantEmail,
			tenants.tenantFirstName,
			tenants.tenantLastName,
			properties.propertyName,
			properties.propertyRate,
			properties.latePenalty
		FROM
			payments
			LEFT JOIN admins ON payments.adminId = admins.adminId
			LEFT JOIN tenants ON payments.tenantId = tenants.tenantId
			LEFT JOIN properties ON tenants.propertyId = properties.propertyId
        WHERE
			payments.paymentId = ".$paymentId;
	$res = mysqli_query($mysqli, $query) or die('Error, retrieving Payment Data failed. ' . mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Format the Amounts
	$paymentAmount = $currencySym.format_amount($row['paymentAmount'], 2);
	if ($row['paymentPenalty'] != '') { $paymentPenalty = $currencySym.format_amount($row['paymentPenalty'], 2); } else { $paymentPenalty = ''; }
	$total = $row['paymentAmount'] + $row['paymentPenalty'];
	$totalPaid = $currencySym.format_amount($total, 2);

    // Get the value for rentMonth and set it as "selected" in the drop-down
    if ($row['rentMonth'] == "January") { $january = 'selected'; } else { $january = ''; }
    if ($row['rentMonth'] == "February") { $february = 'selected'; } else { $february = ''; }
    if ($row['rentMonth'] == "March") { $march = 'selected'; } else { $march = ''; }
    if ($row['rentMonth'] == "April") { $april = 'selected'; } else { $april = ''; }
    if ($row['rentMonth'] == "May") { $may = 'selected'; } else { $may = ''; }
    if ($row['rentMonth'] == "June") { $june = 'selected'; } else { $june = ''; }
    if ($row['rentMonth'] == "July") { $july = 'selected'; } else { $july = ''; }
    if ($row['rentMonth'] == "August") { $august = 'selected'; } else { $august = ''; }
    if ($row['rentMonth'] == "September") { $september = 'selected'; } else { $september = ''; }
    if ($row['rentMonth'] == "October") { $october = 'selected'; } else { $october = ''; }
    if ($row['rentMonth'] == "November") { $november = 'selected'; } else { $november = ''; }
    if ($row['rentMonth'] == "December") { $december = 'selected'; } else { $december = ''; }


	// Check for Refunds
	if ($row['hasRefund'] == '1') { $hasRefund = '<sup><i class="fa fa-asterisk tool-tip has-refund" title="'.$amountReflectRefund.'"></i></sup>'; } else { $hasRefund = ''; }

    // Get refund data
    $sqlStmt = "
        SELECT
            refunds.refundId,
			refunds.paymentId,
			DATE_FORMAT(refunds.refundDate,'%M %d, %Y') AS refundDate,
			refunds.refundAmount,
			refunds.refundFor,
			refunds.refundedBy,
			refunds.refundNotes,
			admins.adminId,
			admins.adminFirstName,
			admins.adminLastName
		FROM
			refunds
			LEFT JOIN admins ON refunds.refundedBy = admins.adminId
        WHERE
			paymentId = ".$paymentId;
	$results = mysqli_query($mysqli, $sqlStmt) or die('Error, retrieving Refund Data failed. ' . mysqli_error());
	$rows = mysqli_fetch_assoc($results);

	// Format the Amounts
	$refundAmount = $currencySym.format_amount($rows['refundAmount'], 2);
?>
<h3 class="success"><?php echo $row['propertyName'].' &mdash; '.$paymentDetailsH3; ?></h3>
<?php
	if ($superuser == '1') {
		if ($row['hasRefund'] != '1') {
?>
	<div class="row">
		<div class="col-md-10">
			<p class="lead"><?php echo $refundQuip; ?></p>
		</div>
		<div class="col-md-2">
			<span class="floatRight">
				<a data-toggle="modal" href="#issueRefund" class="btn btn-warning btn-icon"><i class="fa fa-money"></i> <?php echo $issueRefundBtn; ?></a>
			</span>
		</div>
	</div>
<?php
		}
	}
?>

<?php if ($msgBox) { echo $msgBox; } ?>

<div class="row padTop">
	<div class="col-md-6">
		<ul class="list-group">
			<li class="list-group-item">
				<?php echo $tab_tenant; ?>: <a href="index.php?action=tenantInfo&tenantId=<?php echo $row['tenantId']; ?>"><?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?></a>
			</li>
			<li class="list-group-item"><?php echo $tab_property; ?>: <a href="index.php?action=propertyInfo&propertyId=<?php echo $row['propertyId']; ?>"><?php echo clean($row['propertyName']); ?></a></li>
			<li class="list-group-item">
				<?php echo $tab_receivedBy; ?>: <?php echo clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?>
			</li>
			<li class="list-group-item"><?php echo $tab_paymentDate.': '.$row['datePaid']; ?></li>
			<li class="list-group-item"><?php echo $paymentForField.': '.$row['paymentFor']; ?></li>
		</ul>
	</div>
	<div class="col-md-6">
		<ul class="list-group">
			<li class="list-group-item"><?php echo $paymentTypeField.': '.$row['paymentType']; ?></li>
			<li class="list-group-item"><?php echo $paymentAmountField.': '.$paymentAmount.' '.$hasRefund; ?></li>
			<li class="list-group-item"><?php echo $tab_lateFeePaid.': '.$paymentPenalty; ?></li>
			<li class="list-group-item"><?php echo $tab_totalPaid.': '.$totalPaid; ?></li>
			<?php if ($row['isRent'] == '1') { ?>
				<li class="list-group-item"><?php echo $rentMonthField.': '.$row['rentMonth']; ?></li>
			<?php } ?>
		</ul>
	</div>
</div>

<ul class="list-group">
	<li class="list-group-item"><?php echo $paymentNotesField.': '.nl2br(clean($row['paymentNotes'])); ?></li>
</ul>

<a data-toggle="modal" href="#editPayment" class="btn btn-primary btn-icon"><i class="fa fa-edit"></i> <?php echo $updatePaymentBtn; ?></a>
<a href="index.php?action=receipt&paymentId=<?php echo $row['paymentId']; ?>" target="_blank" class="btn btn-info btn-icon"><i class="fa fa-print"></i> <?php echo $viewPrintReceipt; ?></a>
<a data-toggle="modal" href="#emailReceipt" class="btn btn-success btn-icon"><i class="fa fa-envelope"></i> <?php echo $emailReceiptBtn; ?></a>

<?php if (mysqli_num_rows($results) > 0) { ?>
	<hr />

	<h3 class="success"><?php echo $refundIssuedH3; ?></h3>

	<div class="row padTop">
		<div class="col-md-6">
			<ul class="list-group">
				<li class="list-group-item"><?php echo $refundDateField.': '.$rows['refundDate']; ?></li>
				<li class="list-group-item"><?php echo $refundAmountField.': '.$refundAmount; ?></li>
			</ul>
		</div>
		<div class="col-md-6">
			<ul class="list-group">
				<li class="list-group-item"><?php echo $refundForField.': '.$rows['refundFor']; ?></li>
				<li class="list-group-item">
					<?php echo $refundIssuedBy; ?>: <?php echo clean($rows['adminFirstName']).' '.clean($rows['adminLastName']); ?>
				</li>
			</ul>
		</div>
	</div>

	<ul class="list-group">
		<li class="list-group-item"><?php echo $refundNotesField.': '.nl2br(clean($rows['refundNotes'])); ?></li>
	</ul>
<?php } ?>

<!-- ISSUE REFUND MODAL -->
<div class="modal fade" id="issueRefund" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-warning">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $issueRefundBtn; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="refundDate"><?php echo $refundDateField; ?></label>
						<input type="text" class="form-control" name="refundDate" id="refundDate" value="<?php echo isset($_POST['refundDate']) ? $_POST['refundDate'] : '' ?>">
						<span class="help-block"><?php echo $refundDateHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="refundAmount"><?php echo $refundAmountField; ?></label>
						<input type="text" class="form-control" name="refundAmount" value="<?php echo isset($_POST['refundAmount']) ? $_POST['refundAmount'] : '' ?>">
						<span class="help-block"><?php echo $numberOnlyHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="refundFor"><?php echo $refundForField; ?></label>
						<input type="text" class="form-control" name="refundFor" value="<?php echo isset($_POST['refundFor']) ? $_POST['refundFor'] : '' ?>">
						<span class="help-block"><?php echo $refundForHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="refundNotes"><?php echo $refundNotesField; ?></label>
						<textarea class="form-control" name="refundNotes" rows="2"><?php echo isset($_POST['refundNotes']) ? $_POST['refundNotes'] : '' ?></textarea>
						<span class="help-block"><?php echo $htmlNotAllowed; ?></span>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="propertyId" value="<?php echo $row['propertyId']; ?>">
					<input type="hidden" name="leaseId" value="<?php echo $row['leaseId']; ?>">
					<input type="hidden" name="tenantId" value="<?php echo $row['tenantId']; ?>">
					<input type="hidden" name="originalAmt" value="<?php echo $row['paymentAmount']; ?>">
					<button type="input" name="submit" value="Issue Refund" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $issueTheRefundBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- UPDATE PAYMENT MODAL -->
<div class="modal fade" id="editPayment" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-success">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $updatePaymentBtn; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="paymentDate"><?php echo $paymentDateField; ?></label>
						<input type="text" class="form-control" name="paymentDate" id="paymentDate" value="<?php echo $row['paymentDate']; ?>">
						<span class="help-block"><?php echo $paymentDateHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="paymentAmount"><?php echo $paymentAmountField; ?></label>
						<input type="text" class="form-control" name="paymentAmount" value="<?php echo clean($row['paymentAmount']); ?>">
						<span class="help-block"><?php echo $paymentAmountHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="paymentPenalty"><?php echo $lateFeeField; ?></label>
						<input type="text" class="form-control" name="paymentPenalty" value="<?php echo clean($row['paymentPenalty']); ?>">
						<span class="help-block"><?php echo $lateFeeHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="paymentFor"><?php echo $paymentForField; ?></label>
						<input type="text" class="form-control" name="paymentFor" value="<?php echo clean($row['paymentFor']); ?>">
						<span class="help-block"><?php echo $paymentForHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="paymentType"><?php echo $paymentTypeField; ?></label>
						<input type="text" class="form-control" name="paymentType" value="<?php echo clean($row['paymentType']); ?>">
						<span class="help-block"><?php echo $paymentTypeHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="rentMonth"><?php echo $rentMonthField; ?></label>
						<select class="form-control" name="rentMonth">
							<option value="..."><?php echo $monthNoneSelect; ?></option>
							<option value="<?php echo $monthJanuarySelect; ?>" <?php echo $january; ?>><?php echo $monthJanuarySelect; ?></option>
							<option value="<?php echo $monthFebruarySelect; ?>" <?php echo $february; ?>><?php echo $monthFebruarySelect; ?></option>
							<option value="<?php echo $monthMarchSelect; ?>" <?php echo $march; ?>><?php echo $monthMarchSelect; ?></option>
							<option value="<?php echo $monthAprilSelect; ?>" <?php echo $april; ?>><?php echo $monthAprilSelect; ?></option>
							<option value="<?php echo $monthMaySelect; ?>" <?php echo $may; ?>><?php echo $monthMaySelect; ?></option>
							<option value="<?php echo $monthJuneSelect; ?>" <?php echo $june; ?>><?php echo $monthJuneSelect; ?></option>
							<option value="<?php echo $monthJulySelect; ?>" <?php echo $july; ?>><?php echo $monthJulySelect; ?></option>
							<option value="<?php echo $monthAugustSelect; ?>" <?php echo $august; ?>><?php echo $monthAugustSelect; ?></option>
							<option value="<?php echo $monthSeptemberSelect; ?>" <?php echo $september; ?>><?php echo $monthSeptemberSelect; ?></option>
							<option value="<?php echo $monthOctoberSelect; ?>" <?php echo $october; ?>><?php echo $monthOctoberSelect; ?></option>
							<option value="<?php echo $monthNovemberSelect; ?>" <?php echo $november; ?>><?php echo $monthNovemberSelect; ?></option>
							<option value="<?php echo $monthDecemberSelect; ?>" <?php echo $december; ?>><?php echo $monthDecemberSelect; ?></option>
						</select>
						<span class="help-block"><?php echo $rentMonthHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="paymentNotes"><?php echo $paymentNotesField; ?></label>
						<textarea class="form-control" name="paymentNotes" rows="2"><?php echo clean($row['paymentNotes']); ?></textarea>
						<span class="help-block"><?php echo $paymentNotesHelper; ?> <?php echo $htmlNotAllowed; ?></span>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="tenantId" value="<?php echo $rows['tenantId']; ?>">
					<input type="hidden" name="leaseId" value="<?php echo $rows['leaseId']; ?>">
					<button type="input" name="submit" value="Update Payment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $updatePaymentBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- -- EMAIL RECEIPT MODEL -- -->
<div class="modal fade" id="emailReceipt" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $emailReceiptBtn; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="emailSubject"><?php echo $subjectField; ?></label>
						<input type="text" class="form-control" name="emailSubject" value="<?php echo $emailReceiptDefaultSubject; ?>" />
					</div>
					<div class="form-group">
						<label for="emailNote"><?php echo $emailNotesField; ?></label>
						<textarea class="form-control" name="emailNote" rows="4"><?php echo isset($_POST['emailNote']) ? $_POST['emailNote'] : ''; ?></textarea>
						<span class="help-block"><?php echo $emailNotesHelper; ?> <?php echo $htmlNotAllowedAlt; ?></span>
					</div>
				</div>
				<div class="modal-footer">
					<input name="tenantEmail" value="<?php echo clean($row['tenantEmail']); ?>" type="hidden">
					<input name="paymentDate" value="<?php echo $row['datePaid']; ?>" type="hidden">
					<input name="paymentAmount" value="<?php echo $paymentAmount; ?>" type="hidden">
					<input name="paymentPenalty" value="<?php echo clean($row['paymentPenalty']); ?>" type="hidden">
					<input name="paymentFor" value="<?php echo clean($row['paymentFor']); ?>" type="hidden">
					<input name="paymentType" value="<?php echo clean($row['paymentType']); ?>" type="hidden">
					<input name="paymentNotes" value="<?php echo clean($row['paymentNotes']); ?>" type="hidden">
					<button type="input" name="submit" value="sendReceipt" class="btn btn-success btn-icon"><i class="fa fa-envelope"></i> <?php echo $sendEmailBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>