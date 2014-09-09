<?php
	$stacktable = 'true';

    // Get payment data
    $query = "
        SELECT
            payments.paymentId,
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
			leases.propertyId,
			properties.propertyName
		FROM
			payments
			LEFT JOIN leases ON payments.leaseId = leases.leaseId
			LEFT JOIN properties ON leases.propertyId = properties.propertyId
        WHERE
			payments.tenantId = ".$_SESSION['tenantId']." AND
			payments.leaseId = ".$leaseId;
	$res = mysqli_query($mysqli, $query) or die('Error, retrieving Payment Data failed. ' . mysqli_error());
?>
<h3 class="success"><?php echo $allPaymentsH3; ?></h3>
<div class="row">
	<div class="col-md-8">
		<p class="lead"><?php echo $allPaymentsQuip; ?></p>
	</div>
	<div class="col-md-4">
		<a href="index.php?page=newPayment" class="btn btn-success btn-icon floatRight"><i class="fa fa-credit-card"></i> <?php echo $newPaymentBtnLink; ?></a>
	</div>
</div>

<?php if (mysqli_num_rows($res) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noPaymentsRecorded; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTable" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_paymentDate; ?></th>
			<th><?php echo $tab_propName; ?></th>
			<th><?php echo $tab_for; ?></th>
			<th><?php echo $tab_rentalMonth; ?></th>
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
				$paymentPenalty = $currencySym.format_amount($row['paymentPenalty'], 2);
				$total = $row['paymentAmount'] + $row['paymentPenalty'];
				$totalPaid = $currencySym.format_amount($total, 2);
				// Check for Refunds
				if ($row['hasRefund'] == '1') { $hasRefund = '<sup><i class="fa fa-asterisk tool-tip" title="'.$amountReflectRefund.'"></i></sup>'; } else { $hasRefund = ''; }
		?>
				<tr>
					<td><?php echo $row['paymentDate']; ?></td>
					<td><?php echo $row['propertyName']; ?></td>
					<td><?php echo clean($row['paymentFor']); ?></td>
					<td><?php echo $row['rentMonth']; ?></td>
					<td><?php echo $paymentAmount; ?></td>
					<td><?php echo $paymentPenalty; ?></td>
					<td><?php echo $totalPaid." ".$hasRefund; ?></td>
					<td><a href="index.php?page=receipt&paymentId=<?php echo $row['paymentId']; ?>" target="_blank"><i class="fa fa-print"></i> <?php echo $td_receipt; ?></a></td>
				</tr>
		<?php } ?>
		</tbody>
	</table>
<?php } ?>