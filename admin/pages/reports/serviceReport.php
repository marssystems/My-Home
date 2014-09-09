<?php
	$stacktable = 'true';
	$viewAll = '';

	// Get POST Data
	$requestType = $mysqli->real_escape_string($_POST['requestType']);
	if ($_POST['tenantId'] == 'all') {
		$viewAll = 'true';
	} else {
		$tenantId = $mysqli->real_escape_string($_POST['tenantId']);
	}

	if (!empty($_POST['serviceFromDate'])) {
		$fromDate = $mysqli->real_escape_string($_POST['serviceFromDate']);
		$fdate = date('F d, Y', strtotime($fromDate));
	}
	if (!empty($_POST['serviceToDate'])) {
		$toDate = $mysqli->real_escape_string($_POST['serviceToDate']);
		$tdate = date('F d, Y', strtotime($toDate));
	}

	// Get Data
	if ($requestType == '0') {
		// Only Active/Open Requests
		$sql = $select = $where = "";
		$select = "SELECT
						servicerequests.requestId,
						servicerequests.tenantId,
						servicerequests.leaseId,
						servicerequests.requestDate,
						DATE_FORMAT(servicerequests.requestDate,'%M %d, %Y') AS dateRequested,
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
						END AS currentStatus,
						servicerequests.requestTitle,
						tenants.tenantFirstName,
						tenants.tenantLastName,
						properties.propertyName
					FROM
						servicerequests
						LEFT JOIN tenants ON servicerequests.tenantId = tenants.tenantId
						LEFT JOIN properties ON tenants.propertyId = properties.propertyId";

		if ($viewAll == 'true') {
			// All Tenants
			$where = sprintf("WHERE
								servicerequests.requestStatus IN ('0', '1', '2') AND
								servicerequests.requestDate >= '".$fromDate."' AND servicerequests.requestDate <= '".$toDate."'
							ORDER BY servicerequests.tenantId");
		} else {
			// Specific Tenant
			$where = sprintf("WHERE
								servicerequests.requestStatus IN ('0', '1', '2') AND
								servicerequests.requestDate >= '".$fromDate."' AND servicerequests.requestDate <= '".$toDate."' AND
								servicerequests.tenantId = ".$tenantId."
							ORDER BY servicerequests.tenantId");
		}

		$sql = sprintf("%s %s", $select, $where);
		$res = mysqli_query($mysqli, $sql) or die('Error, retrieving Only Active/Open Requests failed. ' . mysqli_error());
		$totalRecs = mysqli_num_rows($res);
		$reportName = 'Currently Active/Open Service Requests';
	} else if ($requestType == '1') {
		// All Open/Active & Completed Requests
		$sql = $select = $where = "";
		$select = "SELECT
						servicerequests.requestId,
						servicerequests.tenantId,
						servicerequests.leaseId,
						servicerequests.requestDate,
						DATE_FORMAT(servicerequests.requestDate,'%M %d, %Y') AS dateRequested,
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
						END AS currentStatus,
						servicerequests.requestTitle,
						CASE serviceresolutions.needsFollowUp
							WHEN 0 THEN 'No'
							WHEN 1 THEN 'Yes'							
						END AS needsFollowUp,
						DATE_FORMAT(serviceresolutions.completeDate,'%M %d, %Y') AS completeDate,
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
								servicerequests.requestDate >= '".$fromDate."' AND servicerequests.requestDate <= '".$toDate."'
							ORDER BY servicerequests.tenantId");
		} else {
			// Specific Tenant
			$where = sprintf("WHERE
								servicerequests.requestDate >= '".$fromDate."' AND servicerequests.requestDate <= '".$toDate."' AND
								servicerequests.tenantId = ".$tenantId."
							ORDER BY servicerequests.tenantId");
		}

		$sql = sprintf("%s %s", $select, $where);
		$res = mysqli_query($mysqli, $sql) or die('Error, retrieving All Open/Active &amp; Completed Requests failed. ' . mysqli_error());
		$totalRecs = mysqli_num_rows($res);
		$reportName = 'Open/Active &amp; Completed Service Requests';
	} else {
		// Only Closed/Completed Requests
		$sql = $select = $where = "";
		$select = "SELECT
						servicerequests.requestId,
						servicerequests.tenantId,
						servicerequests.leaseId,
						servicerequests.requestDate,
						DATE_FORMAT(servicerequests.requestDate,'%M %d, %Y') AS dateRequested,
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
						END AS currentStatus,
						servicerequests.requestTitle,
						CASE serviceresolutions.needsFollowUp
							WHEN 0 THEN 'No'
							WHEN 1 THEN 'Yes'							
						END AS needsFollowUp,
						DATE_FORMAT(serviceresolutions.completeDate,'%M %d, %Y') AS completeDate,
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
								servicerequests.requestDate >= '".$fromDate."' AND servicerequests.requestDate <= '".$toDate."'
							ORDER BY servicerequests.tenantId");
		} else {
			// Specific Tenant
			$where = sprintf("WHERE
								servicerequests.requestStatus IN ('3', '4', '5') AND
								servicerequests.requestDate >= '".$fromDate."' AND servicerequests.requestDate <= '".$toDate."' AND
								servicerequests.tenantId = ".$tenantId."
							ORDER BY servicerequests.tenantId");
		}

		$sql = sprintf("%s %s", $select, $where);
		$res = mysqli_query($mysqli, $sql) or die('Error, retrieving Only Closed/Completed Requests failed. ' . mysqli_error());
		$totalRecs = mysqli_num_rows($res);
		$reportName = 'Closed/Completed Service Requests';
	}
?>
<h3 class="primary"><?php echo $reportTitleH3.' '.$reportName; ?></h3>

<?php if ($totalRecs < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noReportRecordsFoundMsg; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTable" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_requestTitle; ?></th>
			<th><?php echo $tab_dateRequested; ?></th>
			<th><?php echo $tab_priority; ?></th>
			<th><?php echo $tab_status; ?></th>
			<th><?php echo $tab_tenant; ?></th>
			<th><?php echo $tab_property; ?></th>
			<?php if ($requestType != '0') { ?>
				<th><?php echo $tab_dateCompleted; ?></th>
				<th><?php echo $servResLiNeedsFollowup; ?></th>
			<?php } ?>
		</tr>
		<tbody class="table-hover">
		<?php while ($row = mysqli_fetch_assoc($res)) { ?>
			<tr>
				<td><?php echo clean($row['requestTitle']); ?></td>
				<td><?php echo $row['dateRequested']; ?></td>
				<td><?php echo $row['requestPriority']; ?></td>
				<td><?php echo $row['currentStatus']; ?></td>
				<td><?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?></td>
				<td><?php echo clean($row['propertyName']); ?></td>
				<?php if ($requestType != '0') { ?>
					<td><?php echo $row['completeDate']; ?></td>
					<td><?php echo $row['needsFollowUp']; ?></td>
				<?php } ?>
			</tr>
		<?php }	?>
		</tbody>
	</table>
	<p><span class="reportTotal"><strong><?php echo $totalRecordsFound; ?></strong> <?php echo $totalRecs; ?></span></p>
<?php }	?>
<p class="reportFooter"><?php echo $reportCreatedOnDate.' '.date('l').", ".date('F jS Y')." at ".date('h:i:s A'); ?></p>