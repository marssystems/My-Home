<?php
	$stacktable = 'true';

	// Get Closed Service Requests
	$query = "SELECT
				servicerequests.requestId,
				servicerequests.tenantId,
				servicerequests.leaseId,
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
				properties.propertyId,
				properties.isArchived
			FROM
				servicerequests
				LEFT JOIN tenants ON servicerequests.leaseId = tenants.leaseId
				LEFT JOIN leases ON servicerequests.leaseId = leases.leaseId
				LEFT JOIN properties ON leases.propertyId = properties.propertyId
			WHERE
				servicerequests.adminId = ".$_SESSION['adminId']." AND
				servicerequests.requestStatus IN ('3','4','5') AND
				properties.isArchived = 1";
	$res = mysqli_query($mysqli, $query) or die('Error, retrieving Closed Service Requests Data failed. ' . mysqli_error());
?>
<h3 class="warning"><?php echo $archivedServRequestsH3; ?></h3>

<?php if ($msgBox) { echo $msgBox; } ?>

<?php if(mysqli_num_rows($res) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noArchivedRequestsMsg; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTableFive" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_requestTitle; ?></th>
			<th><?php echo $tab_dateRequested; ?></th>
			<th><?php echo $tab_priority; ?></th>
			<th><?php echo $tab_status; ?></th>
			<th><?php echo $tab_lastUpdated; ?></th>
		</tr>
		<tbody class="table-hover">
		<?php while ($row = mysqli_fetch_assoc($res)) { ?>
			<tr>
				<td><a href="index.php?action=viewRequest&requestId=<?php echo $row['requestId']; ?>"><?php echo clean($row['requestTitle']); ?></a></td>
				<td><?php echo $row['requestDate']; ?></td>
				<td><?php echo $row['requestPriority']; ?></td>
				<td><?php echo $row['status']; ?></td>
				<td><?php echo $row['lastUpdated']; ?></td>
			</tr>
		<?php }	?>
		</tbody>
	</table>
<?php }	?>