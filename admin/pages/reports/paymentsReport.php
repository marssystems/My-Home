<?php
	$stacktable = 'true';
	$viewAll = '';

	// Get POST Data
	$paymentType = $mysqli->real_escape_string($_POST['paymentType']);
	if ($_POST['tenantId'] == 'all') {
		$viewAll = 'true';
	} else {
		$tenantId = $mysqli->real_escape_string($_POST['tenantId']);
	}

	if (!empty($_POST['paymentsFromDate'])) {
		$fromDate = $mysqli->real_escape_string($_POST['paymentsFromDate']);
		$fdate = date('F d, Y', strtotime($fromDate));
	}
	if (!empty($_POST['paymentsToDate'])) {
		$toDate = $mysqli->real_escape_string($_POST['paymentsToDate']);
		$tdate = date('F d, Y', strtotime($toDate));
	}

	// Get Data
	if ($paymentType == '0') {
		// Only show Rent Payments
		$sql = $select = $where = "";
		$select = "SELECT
						payments.paymentId,
						payments.tenantId,
						payments.hasRefund,
						payments.paymentDate,
						DATE_FORMAT(payments.paymentDate,'%M %d, %Y') AS datePaid,
						payments.paymentAmount,
						payments.paymentPenalty,
						payments.paymentFor,
						payments.isRent,
						payments.rentMonth,
						tenants.propertyId,
						tenants.tenantFirstName,
						tenants.tenantLastName,
						properties.propertyName
					FROM
						payments
						LEFT JOIN tenants ON payments.tenantId = tenants.tenantId
						LEFT JOIN properties ON tenants.propertyId = properties.propertyId";

		if ($viewAll == 'true') {
			// All Tenants
			$where = sprintf("WHERE
								payments.isRent = 1 AND
								payments.paymentDate >= '".$fromDate."' AND payments.paymentDate <= '".$toDate."'
							ORDER BY
								payments.paymentId,
								payments.tenantId");
			$reportTitle = 'Rent Received from All Tenants';
		} else {
			// Specific Tenant
			$where = sprintf("WHERE
								payments.isRent = 1 AND
								payments.paymentDate >= '".$fromDate."' AND payments.paymentDate <= '".$toDate."' AND
								payments.tenantId = ".$tenantId."
							ORDER BY payments.paymentId");
			$reportTitle = 'Rent Received from a Specific Tenant';
		}

		$sql = sprintf("%s %s", $select, $where);
		$res = mysqli_query($mysqli, $sql) or die('Error, retrieving data failed. ' . mysqli_error());
		$totalRecs = mysqli_num_rows($res);
		
		// Get the Report Total
		$totals = $totalsel = $totalwhere = "";
		$totalsel = "SELECT
						SUM(payments.paymentAmount) AS totalPaid,
						SUM(payments.paymentPenalty) AS totalPenalty
					FROM
						payments
						LEFT JOIN tenants ON payments.tenantId = tenants.tenantId
						LEFT JOIN properties ON tenants.propertyId = properties.propertyId";

		if ($viewAll == 'true') {
			// All Tenants
			$totalwhere = sprintf("WHERE
									payments.isRent = 1 AND
									payments.paymentDate >= '".$fromDate."' AND payments.paymentDate <= '".$toDate."'
								ORDER BY
									payments.paymentId,
									payments.tenantId");
		} else {
			// Specific Tenant
			$totalwhere = sprintf("WHERE
									payments.isRent = 1 AND
									payments.paymentDate >= '".$fromDate."' AND payments.paymentDate <= '".$toDate."' AND
									payments.tenantId = ".$tenantId."
								ORDER BY payments.paymentId");
		}

		$totals = sprintf("%s %s", $totalsel, $totalwhere);
		$results = mysqli_query($mysqli, $totals) or die('-2 ' . mysqli_error());
		$total = mysqli_fetch_assoc($results);
		$theTotal = $total['totalPaid'] + $total['totalPenalty'];
		// Format the Amount
		$reportTotal = $currencySym.format_amount($theTotal, 2);
	} else {
		// Show all Payments
		$sql = $select = $where = "";
		$select = "SELECT
						payments.paymentId,
						payments.tenantId,
						payments.hasRefund,
						payments.paymentDate,
						DATE_FORMAT(payments.paymentDate,'%M %d, %Y') AS datePaid,
						payments.paymentAmount,
						payments.paymentPenalty,
						payments.paymentFor,
						payments.isRent,
						payments.rentMonth,
						tenants.propertyId,
						tenants.tenantFirstName,
						tenants.tenantLastName,
						properties.propertyName
					FROM
						payments
						LEFT JOIN tenants ON payments.tenantId = tenants.tenantId
						LEFT JOIN properties ON tenants.propertyId = properties.propertyId";

		if ($viewAll == 'true') {
			// All Tenants
			$where = sprintf("WHERE
								payments.paymentDate >= '".$fromDate."' AND payments.paymentDate <= '".$toDate."'
							ORDER BY
								payments.paymentId,
								payments.tenantId");
			$reportTitle = 'All Payments Received from All Tenants';
		} else {
			// Specific Tenant
			$where = sprintf("WHERE
								payments.paymentDate >= '".$fromDate."' AND payments.paymentDate <= '".$toDate."' AND
								payments.tenantId = ".$tenantId."
							ORDER BY payments.paymentId");
			$reportTitle = 'All Payments Received from a Specific Tenant';
		}

		$sql = sprintf("%s %s", $select, $where);
		$res = mysqli_query($mysqli, $sql) or die('Error, retrieving data failed. ' . mysqli_error());
		$totalRecs = mysqli_num_rows($res);
		
		// Get the Report Total
		$totals = $totalsel = $totalwhere = "";
		$totalsel = "SELECT
						SUM(payments.paymentAmount) AS totalPaid,
						SUM(payments.paymentPenalty) AS totalPenalty
					FROM
						payments
						LEFT JOIN tenants ON payments.tenantId = tenants.tenantId
						LEFT JOIN properties ON tenants.propertyId = properties.propertyId";

		if ($viewAll == 'true') {
			// All Tenants
			$totalwhere = sprintf("WHERE
									payments.paymentDate >= '".$fromDate."' AND payments.paymentDate <= '".$toDate."'
								ORDER BY
									payments.paymentId,
									payments.tenantId");
		} else {
			// Specific Tenant
			$totalwhere = sprintf("WHERE
									payments.paymentDate >= '".$fromDate."' AND payments.paymentDate <= '".$toDate."' AND
									payments.tenantId = ".$tenantId."
								ORDER BY payments.paymentId");
		}

		$totals = sprintf("%s %s", $totalsel, $totalwhere);
		$results = mysqli_query($mysqli, $totals) or die('-2 ' . mysqli_error());
		$total = mysqli_fetch_assoc($results);
		$theTotal = $total['totalPaid'] + $total['totalPenalty'];
		// Format the Amount
		$reportTotal = $currencySym.format_amount($theTotal, 2);
	}
?>
<h3 class="primary"><?php echo $reportTitleH3.' '.$reportTitle; ?></h3>

<?php if ($totalRecs < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noReportRecordsFoundMsg; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTable" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_tenant; ?></th>
			<th><?php echo $tab_property; ?></th>
			<th><?php echo $tab_paymentDate; ?></th>
			<th><?php echo $tab_paymentFor; ?></th>
			<th><?php echo $tab_rentalMonth; ?></th>
			<th><?php echo $tab_amount; ?></th>
			<th><?php echo $tab_lateFeePaid; ?></th>
			<th><?php echo $tab_totalPaid; ?></th>
		</tr>
		<tbody class="table-hover">
		<?php
			while ($row = mysqli_fetch_assoc($res)) {
				// Format the Amounts
				$paymentAmount = $currencySym.format_amount($row['paymentAmount'], 2);
				if ($row['paymentPenalty'] != '') { $paymentPenalty = $currencySym.format_amount($row['paymentPenalty'], 2); } else { $paymentPenalty = '';}
				$total = $row['paymentAmount'] + $row['paymentPenalty'];
				$totalPaid = $currencySym.format_amount($total, 2);
				// Check for Refunds
				if ($row['hasRefund'] == '1') { $hasRefund = '<sup><i class="fa fa-asterisk tool-tip has-refund" title="'.$amountReflectRefund.'"></i></sup>'; } else { $hasRefund = ''; }
		?>
			<tr>
				<td><?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?></td>
				<td><?php echo clean($row['propertyName']); ?></td>
				<td><?php echo $row['datePaid']; ?></td>
				<td><?php echo clean($row['paymentFor']); ?></td>
				<td><?php echo clean($row['rentMonth']); ?></td>
				<td><?php echo $paymentAmount; ?></td>
				<td><?php echo $paymentPenalty; ?></td>
				<td><?php echo $totalPaid.' '.$hasRefund; ?></td>
			</tr>
		<?php }	?>
		</tbody>
	</table>
	<p>
		<span class="reportTotal"><strong><?php echo $totalRecordsFound; ?></strong> <?php echo $totalRecs; ?></span>
		<span class="reportTotal floatRight"><strong><?php echo $reportTotals; ?></strong> <?php echo $reportTotal; ?></span>
	</p>
<?php }	?>
<p class="reportFooter"><?php echo $reportCreatedOnDate.' '.date('l').", ".date('F jS Y')." at ".date('h:i:s A'); ?></p>