<?php
	$stacktable = 'true';

	// Delete Lease
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteLease') {
		$stmt = $mysqli->prepare("DELETE FROM leases WHERE leaseId = ?");
		$stmt->bind_param('s', $_POST['deleteId']);
		$stmt->execute();
		$stmt->close();

		$msgBox = alertBox($leaseDeletedMsg, "<i class='fa fa-check-square-o'></i>", "success");
    }

	// Get Archived Lease Data
    $query = "SELECT
				leases.leaseId,
				leases.adminId,
				leases.propertyId,
				leases.leaseTerm,
				DATE_FORMAT(leases.leaseStart,'%M %d, %Y') AS leaseStart,
				DATE_FORMAT(leases.leaseEnd,'%M %d, %Y') AS leaseEnd,
				leases.isClosed,
				properties.propertyName,
				admins.adminFirstName,
				admins.adminLastName
			FROM
				leases
				LEFT JOIN properties ON leases.propertyId = properties.propertyId
				LEFT JOIN admins ON leases.adminId = admins.adminId
			WHERE
				leases.adminId = ".$_SESSION['adminId']." AND
				leases.isClosed = 1";
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving Archived Lease Data failed. ' . mysqli_error());
?>
<h3 class="primary"><?php echo $archivedLeasesH3; ?></h3>

<?php if ($msgBox) { echo $msgBox; } ?>

<?php if(mysqli_num_rows($res) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noArchivedLeasesMsg; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTableTwo" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_property; ?></th>
			<th><?php echo $tab_createdBy; ?></th>
			<th><?php echo $tab_leaseTerm; ?></th>
			<th><?php echo $tab_leaseStartsOn; ?></th>
			<th><?php echo $tab_leaseEndsOn; ?></th>
			<?php if ($superuser == '1') { ?>
				<th></th>
			<?php }	?>
		</tr>
		<tbody class="table-hover">
		<?php while ($row = mysqli_fetch_assoc($res)) { ?>
			<tr>
				<td><a href="index.php?action=propertyInfo&propertyId=<?php echo $row['propertyId']; ?>"><?php echo clean($row['propertyName']); ?></a></td>
				<td><?php echo clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?></td>
				<td><?php echo clean($row['leaseTerm']); ?></td>
				<td><?php echo $row['leaseStart']; ?></td>
				<td><?php echo $row['leaseEnd']; ?></td>
				<?php if ($superuser == '1') { ?>
					<td><a data-toggle="modal" href="#deleteLease<?php echo $row['leaseId']; ?>" class="btn btn-xs btn-link tool-tip" title="Delete Lease"><i class="fa fa-times"></i></a></td>
				<?php }	?>
			</tr>

			<?php if ($superuser == '1') { ?>
			<!-- Delete Lease Confirm Modal -->
			<div class="modal fade" id="deleteLease<?php echo $row['leaseId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<form action="" method="post">
							<div class="modal-body">
								<p class="lead"><?php echo $deleteLeaseConf; ?>
								</p>
							</div>
							<div class="modal-footer">
								<input name="deleteId" type="hidden" value="<?php echo $row['leaseId']; ?>" />
								<button type="input" name="submit" value="deleteLease" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
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