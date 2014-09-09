<?php
	$stacktable = 'true';

	// Delete a Service Request & all data relating to the Request
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteRequest') {
		$stmt1 = $mysqli->prepare("DELETE FROM servicerequests WHERE requestId = ?");
		$stmt1->bind_param('s', $_POST['deleteId']);
		$stmt1->execute();
		$stmt1->close();

		$stmt2 = $mysqli->prepare("DELETE FROM serviceresolutions WHERE requestId = ?");
		$stmt2->bind_param('s', $_POST['deleteId']);
		$stmt2->execute();
		$stmt2->close();

		$stmt3 = $mysqli->prepare("DELETE FROM serviceexpense WHERE requestId = ?");
		$stmt3->bind_param('s', $_POST['deleteId']);
		$stmt3->execute();
		$stmt3->close();

		$stmt4 = $mysqli->prepare("DELETE FROM servicenotes WHERE requestId = ?");
		$stmt4->bind_param('s', $_POST['deleteId']);
		$stmt4->execute();
		$stmt4->close();

		$msgBox = alertBox($requestDeletedMsg, "<i class='fa fa-check-square-o'></i>", "success");
    }

	// Get Open Service Requests
	if ($_SESSION['superuser'] != '1') {
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
						WHEN 0 THEN 'Open'
						WHEN 1 THEN 'Work in Progress'
						WHEN 2 THEN 'Waiting for Parts'
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
					admins.adminId = ".$_SESSION['adminId']." AND
					servicerequests.requestStatus IN ('0','1','2')";
		$res = mysqli_query($mysqli, $query) or die('Error, retrieving Open Service Requests Data failed. ' . mysqli_error());
	} else {
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
						WHEN 0 THEN 'Open'
						WHEN 1 THEN 'Work in Progress'
						WHEN 2 THEN 'Waiting for Parts'
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
					servicerequests.requestStatus IN ('0','1','2')";
		$res = mysqli_query($mysqli, $query) or die('Error, retrieving Open Service Requests Data failed. ' . mysqli_error());
	}
?>
<h3 class="warning"><?php echo $openServRequestsH3; ?></h3>

<?php if ($msgBox) { echo $msgBox; } ?>

<?php if(mysqli_num_rows($res) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noOpenRequestsMsg; ?>
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
			<?php if ($_SESSION['superuser'] == '1') { ?>
				<th></th>
			<?php }	?>
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
				<?php if ($_SESSION['superuser'] == '1') { ?>
					<td><a data-toggle="modal" href="#deleteRequest<?php echo $row['requestId']; ?>" class="btn btn-xs btn-link tool-tip" title="Delete this Request"><i class="fa fa-times"></i></a></td>
				<?php }	?>
			</tr>
			<?php if ($_SESSION['superuser'] == '1') { ?>
				<!-- DELETE SERVICE REQUEST CONFIRM MODAL -->
				<div class="modal fade" id="deleteRequest<?php echo $row['requestId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<form action="" method="post">
								<div class="modal-body">
									<p class="lead"><?php echo $deleteRequestConf; ?>
									</p>
								</div>
								<div class="modal-footer">
									<input name="deleteId" type="hidden" value="<?php echo $row['tenantId']; ?>" />
									<button type="input" name="submit" value="deleteRequest" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
									<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
								</div>
							</form>
						</div>
					</div>
				</div>
			<?php
			}
		}
		?>
		</tbody>
	</table>
<?php }	?>