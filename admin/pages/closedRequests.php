<?php
	$stacktable = 'true';

	// Get Closed Service Requests
	$query = "SELECT
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
					WHEN 3 THEN 'Completed/No Repair Needed'
					WHEN 4 THEN 'Completed Repair'
					WHEN 5 THEN 'Closed'
				END AS status,
				servicerequests.requestTitle,
				DATE_FORMAT(servicerequests.lastUpdated,'%M %d, %Y') AS lastUpdated,
				tenants.tenantFirstName,
				tenants.tenantLastName,
				properties.propertyId,
				properties.propertyName,
				assignedproperties.propertyId,
				admins.adminFirstName,
				admins.adminLastName
			FROM
				servicerequests
				LEFT JOIN tenants ON servicerequests.leaseId = tenants.leaseId
				LEFT JOIN properties ON tenants.propertyId = properties.propertyId
				LEFT JOIN assignedproperties ON tenants.propertyId = assignedproperties.propertyId
				LEFT JOIN admins ON assignedproperties.adminId = admins.adminId
			WHERE
				servicerequests.adminId = ".$_SESSION['adminId']." AND
				servicerequests.requestStatus IN ('3','4','5') AND
				properties.propertyId != ''";
	$res = mysqli_query($mysqli, $query) or die('Error, retrieving Closed Service Requests Data failed. ' . mysqli_error());
?>
<h3 class="warning"><?php echo $closedServRequestsH3; ?></h3>

<?php if ($msgBox) { echo $msgBox; } ?>

<?php if(mysqli_num_rows($res) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noClosedRequestsMsg; ?>
	</div>
<?php } else { ?>
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
		<?php while ($row = mysqli_fetch_assoc($res)) { ?>
			<tr>
				<td><a href="index.php?action=viewRequest&requestId=<?php echo $row['requestId']; ?>"><?php echo clean($row['requestTitle']); ?></a></td>
				<td><a href="index.php?action=tenantInfo&tenantId=<?php echo $row['tenantId']; ?>"><?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?></a></td>
				<td><a href="index.php?action=propertyInfo&propertyId=<?php echo $row['propertyId']; ?>"><?php echo clean($row['propertyName']); ?></a></td>
				<td><?php echo $row['requestDate']; ?></td>
				<td><?php echo $row['requestPriority']; ?></td>
				<td><?php echo $row['status']; ?></td>
				<td><?php echo $row['lastUpdated']; ?></td>
			</tr>
		<?php }	?>
		</tbody>
	</table>
<?php }	?>