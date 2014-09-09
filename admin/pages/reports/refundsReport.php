<?php
	$stacktable = 'true';
	$viewAll = '';

	// Get POST Data
	if ($_POST['tenantId'] == 'all') {
		$viewAll = 'true';
	} else {
		$tenantId = $mysqli->real_escape_string($_POST['tenantId']);
	}

	if (!empty($_POST['refundsFromDate'])) {
		$fromDate = $mysqli->real_escape_string($_POST['refundsFromDate']);
		$fdate = date('F d, Y', strtotime($fromDate));
	}
	if (!empty($_POST['refundsToDate'])) {
		$toDate = $mysqli->real_escape_string($_POST['refundsToDate']);
		$tdate = date('F d, Y', strtotime($toDate));
	}

	// Get Data
	$sql = $select = $where = "";
	$select = "SELECT
					refunds.refundId,
					refunds.paymentId,
					refunds.propertyId,
					refunds.tenantId,
					refunds.refundDate,
					DATE_FORMAT(refunds.refundDate,'%M %d, %Y') AS dateRefunded,
					refunds.refundAmount,
					refunds.refundFor,
					DATE_FORMAT(payments.paymentDate,'%M %d, %Y') AS paymentDate,
					payments.paymentFor,
					tenants.propertyId,
					tenants.tenantFirstName,
					tenants.tenantLastName,
					properties.propertyName
				FROM
					refunds
					LEFT JOIN payments ON refunds.paymentId = payments.paymentId
					LEFT JOIN tenants ON refunds.tenantId = tenants.tenantId
					LEFT JOIN properties ON refunds.propertyId = properties.propertyId";

	if ($viewAll == 'true') {
		// All Tenants
		$where = sprintf("WHERE
							refunds.refundDate >= '".$fromDate."' AND refunds.refundDate <= '".$toDate."'
						ORDER BY
							refunds.paymentId,
							refunds.tenantId");
		$reportTitle = 'All Refunds Issued';
	} else {
		// Specific Tenant
		$where = sprintf("WHERE
							refunds.refundDate >= '".$fromDate."' AND refunds.refundDate <= '".$toDate."' AND
							refunds.tenantId = ".$tenantId."
						ORDER BY refunds.paymentId");
		$reportTitle = 'All Refunds Issued to a Specific Tenant';
	}

	$sql = sprintf("%s %s", $select, $where);
	$res = mysqli_query($mysqli, $sql) or die('Error, retrieving data failed. ' . mysqli_error());
	$totalRecs = mysqli_num_rows($res);
	
	// Get the Report Total
	$totals = $totalsel = $totalwhere = "";
	$totalsel = "SELECT
					SUM(refunds.refundAmount) AS totalRefunds
				FROM
					refunds
					LEFT JOIN payments ON refunds.paymentId = payments.paymentId
					LEFT JOIN tenants ON refunds.tenantId = tenants.tenantId
					LEFT JOIN properties ON refunds.propertyId = properties.propertyId";

	if ($viewAll == 'true') {
		// All Tenants
		$totalwhere = sprintf("WHERE
								refunds.refundDate >= '".$fromDate."' AND refunds.refundDate <= '".$toDate."'
							ORDER BY
								refunds.paymentId,
								refunds.tenantId");
	} else {
		// Specific Tenant
		$totalwhere = sprintf("WHERE
								refunds.refundDate >= '".$fromDate."' AND refunds.refundDate <= '".$toDate."' AND
								refunds.tenantId = ".$tenantId."
							ORDER BY refunds.paymentId");
	}

	$totals = sprintf("%s %s", $totalsel, $totalwhere);
	$results = mysqli_query($mysqli, $totals) or die('-2 ' . mysqli_error());
	$total = mysqli_fetch_assoc($results);
	// Format the Amount
	$reportTotal = $currencySym.format_amount($total['totalRefunds'], 2);
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
			<th><?php echo $tab_originalPaymentFor; ?></th>
			<th><?php echo $tab_originalPaymentDate; ?></th>
			<th><?php echo $refundDateField; ?></th>
			<th><?php echo $refundAmountField; ?></th>
		</tr>
		<tbody class="table-hover">
		<?php
			while ($row = mysqli_fetch_assoc($res)) {
				// Format the Amounts
				$refundAmount = $currencySym.format_amount($row['refundAmount'], 2);
		?>
			<tr>
				<td><?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?></td>
				<td><?php echo clean($row['propertyName']); ?></td>
				<td><?php echo clean($row['paymentFor']); ?></td>
				<td><?php echo $row['paymentDate']; ?></td>
				<td><?php echo $row['dateRefunded']; ?></td>
				<td><?php echo $refundAmount; ?></td>
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