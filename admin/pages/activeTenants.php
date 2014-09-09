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

	// Get Leased Tenant Data
	if ($_SESSION['superuser'] != '1') {
		$lease = "SELECT
					tenants.tenantId,
					tenants.propertyId,
					tenants.leaseId,
					tenants.tenantEmail,
					tenants.tenantFirstName,
					tenants.tenantLastName,
					tenants.tenantPhone,
					tenants.isActive,
					properties.propertyId,
					properties.propertyName,
					properties.propertyRate,
					DATE_FORMAT(leases.leaseEnd,'%M %d, %Y') AS leaseEnd,
					assignedproperties.propertyId,
					admins.adminId
				FROM
					tenants
					LEFT JOIN properties ON tenants.propertyId = properties.propertyId
					LEFT JOIN leases ON tenants.leaseId = leases.leaseId
					LEFT JOIN assignedproperties ON tenants.propertyId = assignedproperties.propertyId
					LEFT JOIN admins ON assignedproperties.adminId = admins.adminId
				WHERE
					admins.adminId = ".$_SESSION['adminId']." AND
					tenants.isActive = 1 AND
					tenants.leaseId != 0";
		$leaseres = mysqli_query($mysqli, $lease) or die('Error, retrieving Leased Tenant Data failed. ' . mysqli_error());
	} else {
		$lease = "SELECT
					tenants.tenantId,
					tenants.propertyId,
					tenants.leaseId,
					tenants.tenantEmail,
					tenants.tenantFirstName,
					tenants.tenantLastName,
					tenants.tenantPhone,
					tenants.isActive,
					properties.propertyId,
					properties.propertyName,
					properties.propertyRate,
					DATE_FORMAT(leases.leaseEnd,'%M %d, %Y') AS leaseEnd,
					assignedproperties.propertyId,
					admins.adminId,
					admins.adminFirstName,
					admins.adminLastName
				FROM
					tenants
					LEFT JOIN properties ON tenants.propertyId = properties.propertyId
					LEFT JOIN leases ON tenants.leaseId = leases.leaseId
					LEFT JOIN assignedproperties ON tenants.propertyId = assignedproperties.propertyId
					LEFT JOIN admins ON assignedproperties.adminId = admins.adminId
				WHERE
					tenants.isActive = 1 AND
					tenants.leaseId != 0";
		$leaseres = mysqli_query($mysqli, $lease) or die('Error, retrieving Leased Tenant Data failed. ' . mysqli_error());
	}

	// Get Unleased Tenant Data
    $nolease = "SELECT
					tenantId,
					propertyId,
					leaseId,
					tenantEmail,
					tenantFirstName,
					tenantLastName,
					tenantAddress,
					tenantPhone,
					tenantAltPhone,
					DATE_FORMAT(createDate,'%d/%m/%Y') AS createDate,
					isActive
				FROM
					tenants
				WHERE
					adminId = ".$_SESSION['adminId']." AND
					isActive = 1 AND
					leaseId = 0";
					//die($nolease);
    $noleaseres = mysqli_query($mysqli, $nolease) or die('Error, retrieving Unleased Tenant Data failed. ' . mysqli_error($mysqli));
?>
<h3 class="primary"><?php echo $activeLeaseTenantsH3; ?></h3>
<?php if ($_SESSION['superuser'] == '1') { ?>
	<p><?php echo $activeTenantsQuip; ?></p>
<?php }	?>

<?php if ($msgBox) { echo $msgBox; } ?>

<?php if(mysqli_num_rows($leaseres) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noActiveTenantsMsg; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTable" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_tenant; ?></th>
			<th><?php echo $tab_email; ?></th>
			<th><?php echo $tab_phone; ?></th>
			<th><?php echo $tab_property; ?></th>
			<th><?php echo $tab_monthlyRate; ?></th>
			<th><?php echo $tab_leaseEndsOn; ?></th>
			<?php if ($_SESSION['superuser'] == '1') { ?>
				<th><?php echo $tab_landlord; ?></th>
			<?php }	?>
		</tr>
		<tbody class="table-hover">
	<?php
		while ($row = mysqli_fetch_assoc($leaseres)) {
			// Decrypt data for display
			if ($row['tenantPhone'] != '') { $tenantPhone = decryptIt($row['tenantPhone']); } else { $tenantPhone = ''; }
			// Format the Amounts
			$propertyRate = $currencySym.format_amount($row['propertyRate'], 2);
	?>
			<tr>
				<td><a href="index.php?action=tenantInfo&tenantId=<?php echo $row['tenantId']; ?>"><?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?></a></td>
				<td><?php echo clean($row['tenantEmail']); ?></td>
				<td><?php echo $tenantPhone; ?></td>
				<td><a href="index.php?action=propertyInfo&propertyId=<?php echo $row['propertyId']; ?>"><?php echo clean($row['propertyName']); ?></a></td>
				<td><?php echo $propertyRate; ?></td>
				<td><?php echo clean($row['leaseEnd']); ?></td>
				<?php if ($_SESSION['superuser'] == '1') { ?>
					<td><a href="index.php?action=adminInfo&adminId=<?php echo $row['adminId']; ?>"><?php echo clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?></a></td>
				<?php }	?>
			</tr>
		<?php }	?>
		</tbody>
	</table>
<?php }	?>

<?php if(mysqli_num_rows($noleaseres) > 0) { ?>
	<hr />
	<h3 class="primary"><?php echo $activeNoLeaseTenantsH3; ?></h3>

	<table id="responsiveTable" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_tenant; ?></th>
			<th><?php echo $tab_email; ?></th>
			<th><?php echo $tab_address; ?></th>
			<th><?php echo $tab_phone; ?></th>
			<th><?php echo $tab_altPhone; ?></th>
			<th><?php echo $tab_dateCreated; ?></th>
			<?php if ($_SESSION['superuser'] == '1') { ?>
				<th></th>
			<?php }	?>
		</tr>
		<tbody class="table-hover">
	<?php
		while ($row = mysqli_fetch_assoc($noleaseres)) {
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
				<td><?php echo clean($row['createDate']); ?></td>
				<?php if ($_SESSION['superuser'] == '1') { ?>
					<td><a data-toggle="modal" href="#deleteTenant<?php echo $row['tenantId']; ?>" class="btn btn-xs btn-link tool-tip" title="Delete Tenant Account"><i class="fa fa-times"></i></a></td>
				<?php }	?>
			</tr>

			<?php if ($_SESSION['superuser'] == '1') { ?>
				<!-- Delete Tenant Account Confirm Modal -->
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