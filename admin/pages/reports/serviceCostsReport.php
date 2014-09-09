<?php
	$stacktable = 'true';
	$viewAll = '';

	// Get POST Data
	if ($_POST['tenantId'] == 'all') {
		$viewAll = 'true';
	} else {
		$tenantId = $mysqli->real_escape_string($_POST['tenantId']);
	}

	if (!empty($_POST['serviceCostsFromDate'])) {
		$fromDate = $mysqli->real_escape_string($_POST['serviceCostsFromDate']);
		$fdate = date('F d, Y', strtotime($fromDate));
	}
	if (!empty($_POST['serviceCostsToDate'])) {
		$toDate = $mysqli->real_escape_string($_POST['serviceCostsToDate']);
		$tdate = date('F d, Y', strtotime($toDate));
	}

	// Get Data
	$sql = $select = $where = "";
	$select = "SELECT
					servicerequests.requestId,
					servicerequests.tenantId,
					servicerequests.leaseId,
					servicerequests.requestStatus,
					CASE servicerequests.requestStatus
						WHEN 0 THEN 'Open'
						WHEN 1 THEN 'Work in Progress'
						WHEN 2 THEN 'Waiting for Parts'
						WHEN 3 THEN 'Completed/No Repair Needed'
						WHEN 4 THEN 'Completed Repair'
						WHEN 5 THEN 'Closed'
					END AS currentStatus,
					servicerequests.requestTitle,
					serviceresolutions.completeDate,
					DATE_FORMAT(serviceresolutions.completeDate,'%M %d, %Y') AS dateCompleted,
					tenants.tenantFirstName,
					tenants.tenantLastName,
					properties.propertyName
				FROM
					servicerequests
					LEFT JOIN serviceresolutions ON servicerequests.requestId = serviceresolutions.requestId
					LEFT JOIN tenants ON servicerequests.tenantId = tenants.tenantId
					LEFT JOIN properties ON tenants.propertyId = properties.propertyId";

	if ($viewAll == 'true') {
		// All Tenants
		$where = sprintf("WHERE
							servicerequests.requestStatus IN ('3', '4', '5') AND
							serviceresolutions.completeDate >= '".$fromDate."' AND serviceresolutions.completeDate <= '".$toDate."'
						ORDER BY servicerequests.tenantId");
	} else {
		// Specific Tenant
		$where = sprintf("WHERE
							servicerequests.requestStatus IN ('3', '4', '5') AND
							serviceresolutions.completeDate >= '".$fromDate."' AND serviceresolutions.completeDate <= '".$toDate."' AND
							servicerequests.tenantId = ".$tenantId."
						ORDER BY servicerequests.tenantId");
	}

	$sql = sprintf("%s %s", $select, $where);
	$res = mysqli_query($mysqli, $sql) or die('-1 ' . mysqli_error());
	$totalRecs = mysqli_num_rows($res);
	
	// Get the Report Total
	$totals = $totalsel = $totalwhere = "";
	$totalsel = "SELECT
					SUM(serviceexpense.expenseCost) AS totalCosts
				FROM
					serviceexpense
					LEFT JOIN servicerequests ON serviceexpense.requestId = servicerequests.requestId
					LEFT JOIN serviceresolutions ON serviceexpense.requestId = serviceresolutions.requestId
					LEFT JOIN tenants ON serviceresolutions.tenantId = tenants.tenantId
					LEFT JOIN properties ON tenants.propertyId = properties.propertyId";

	if ($viewAll == 'true') {
		// All Tenants
		$totalwhere = sprintf("WHERE
								servicerequests.requestStatus IN ('3', '4', '5') AND
								serviceresolutions.completeDate >= '".$fromDate."' AND serviceresolutions.completeDate <= '".$toDate."'
							ORDER BY servicerequests.tenantId");
	} else {
		// Specific Tenant
		$totalwhere = sprintf("WHERE
								servicerequests.requestStatus IN ('3', '4', '5') AND
								serviceresolutions.completeDate >= '".$fromDate."' AND serviceresolutions.completeDate <= '".$toDate."' AND
								servicerequests.tenantId = ".$tenantId."
							ORDER BY servicerequests.tenantId");
	}

	$totals = sprintf("%s %s", $totalsel, $totalwhere);
	$results = mysqli_query($mysqli, $totals) or die('-2 ' . mysqli_error());
	$total = mysqli_fetch_assoc($results);
	// Format the Amount
	$reportTotal = $currencySym.format_amount($total['totalCosts'], 2);
?>
<h3 class="primary"><?php echo $reportTitleH3.' '.$report5Title; ?></h3>

<?php if ($totalRecs < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noReportRecordsFoundMsg; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTable" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_requestTitle; ?></th>
			<th><?php echo $tab_status; ?></th>
			<th><?php echo $servResLiDateCompleted; ?></th>
			<th><?php echo $tab_tenant; ?></th>
			<th><?php echo $tab_property; ?></th>
			<th><?php echo $tab_totalRepairCost; ?></th>
		</tr>
		<tbody class="table-hover">
		<?php
			while ($row = mysqli_fetch_assoc($res)) {
				$query = "SELECT SUM(expenseCost) AS totalCosts FROM serviceexpense WHERE requestId = ".$row['requestId'];
				$total = mysqli_query($mysqli, $query) or die('-1' . mysqli_error());
				$tot = mysqli_fetch_assoc($total);
				$totalCosts = $currencySym.format_amount($tot['totalCosts'], 2);
		?>
			<tr>
				<td><?php echo clean($row['requestTitle']); ?></td>
				<td><?php echo $row['currentStatus']; ?></td>
				<td><?php echo $row['dateCompleted']; ?></td>
				<td><?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?></td>
				<td><?php echo clean($row['propertyName']); ?></td>
				<td><?php echo $totalCosts; ?></td>
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