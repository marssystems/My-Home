<?php
	$stacktable = 'true';

	// Delete Tenant Account
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteTenant') {
		$stmt = $mysqli->prepare("DELETE FROM tenants WHERE tenantId = ?");
		$stmt->bind_param('s', $_POST['deleteId']);
		$stmt->execute();
		$stmt->close();

		$msgBox = alertBox($tenantDeletedMsg, "<i class='fa fa-check-square-o'></i>", "success");
    }

	// Get Archived Tenant Data
    $query = "SELECT
					tenantId,
					propertyId,
					leaseId,
					tenantEmail,
					tenantFirstName,
					tenantLastName,
					tenantAddress,
					tenantPhone,
					tenantAltPhone,
					isActive,
					isArchived,
					DATE_FORMAT(archivedDate,'%M %d, %Y') AS archivedDate
				FROM
					tenants
				WHERE
					adminId = ".$_SESSION['adminId']." AND
					isActive = 0 AND
					isArchived = 1";
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving Unleased Tenant Data failed. ' . mysqli_error());
?>
<h3 class="primary"><?php echo $archivedTenantsH3; ?></h3>

<?php if ($msgBox) { echo $msgBox; } ?>

<?php if(mysqli_num_rows($res) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noArchedTenantsMsg; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTable" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_tenant; ?></th>
			<th><?php echo $tab_email; ?></th>
			<th><?php echo $tab_address; ?></th>
			<th><?php echo $tab_phone; ?></th>
			<th><?php echo $tab_altPhone; ?></th>
			<th><?php echo $tab_dateArchived; ?></th>
			<?php if ($superuser == '1') { ?>
				<th></th>
			<?php }	?>
		</tr>
		<tbody class="table-hover">
	<?php
		while ($row = mysqli_fetch_assoc($res)) {
			// Decrypt data for display
			if ($row['tenantAddress'] != '') { $tenantAddress = decryptIt($row['tenantAddress']); } else { $tenantAddress = ''; }
			if ($row['tenantPhone'] != '') { $tenantPhone = decryptIt($row['tenantPhone']); } else { $tenantPhone = ''; }
			if ($row['tenantAltPhone'] != '') { $tenantAltPhone = decryptIt($row['tenantAltPhone']); } else { $tenantAltPhone = ''; }
	?>
			<tr>
				<td><a href="index.php?action=tenantInfo&tenantId=<?php echo $row['tenantId']; ?>"><?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?></a></td>
				<td><?php echo clean($row['tenantEmail']); ?></td>
				<td><?php echo $tenantAddress; ?></td>
				<td><?php echo $tenantPhone; ?></td>
				<td><?php echo $tenantAltPhone; ?></td>
				<td><?php echo clean($row['archivedDate']); ?></td>
				<?php if ($superuser == '1') { ?>
					<td><a data-toggle="modal" href="#deleteTenant<?php echo $row['tenantId']; ?>" class="btn btn-xs btn-link tool-tip" title="Delete Tenant Account"><i class="fa fa-times"></i></a></td>
				<?php }	?>
			</tr>

			<?php if ($superuser == '1') { ?>
				<!-- DELETE TENANT ACCOUNT CONFIRM MODAL -->
				<div class="modal fade" id="deleteTenant<?php echo $row['tenantId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<form action="" method="post">
								<div class="modal-body">
									<p class="lead"><?php echo $deleteTenantConf; ?>
									</p>
								</div>
								<div class="modal-footer">
									<input name="deleteId" type="hidden" value="<?php echo $row['tenantId']; ?>" />
									<button type="input" name="submit" value="deleteTenant" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
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