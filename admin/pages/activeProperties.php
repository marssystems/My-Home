<?php
	$stacktable = 'true';

	// Get the Property Pictures Folder Path from the Site Settings
	$propertyPicsPath = $set['propertyPicsPath'];

	// Get Property Files Folder from Site Settings
	$uploadPath = $set['uploadPath'];

	// Archive Property
	if (isset($_POST['submit']) && $_POST['submit'] == 'archiveProperty') {
		$archiveId = $mysqli->real_escape_string($_POST['archiveId']);
		$isArchived = '1';
		$today = date("Y-m-d");
		
		$stmt = $mysqli->prepare("
							UPDATE
								properties
							SET
								isArchived = ?,
								dateArchived = ?
							WHERE
								propertyId = ?
		");
		$stmt->bind_param('sss',
						   $isArchived,
						   $today,
						   $archiveId
		);
		$stmt->execute();
		$msgBox = alertBox($propertyArchivedMsg, "<i class='fa fa-check-square-o-sign'></i>", "success");
		$stmt->close();
    }
	
	// Delete Property
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteProperty') {
		$deleteId = $mysqli->real_escape_string($_POST['deleteId']);
		$folderUrl = $mysqli->real_escape_string($_POST['propertyFolder']);

		// Delete the Folders
		if (is_dir('../'.$propertyPicsPath.$folderUrl)) {
			rmdir('../'.$propertyPicsPath.$folderUrl);
		}
		if (is_dir('../'.$uploadPath.$folderUrl)) {
			rmdir('../'.$uploadPath.$folderUrl);

			// Allow the Delete
			$stmt = $mysqli->prepare("DELETE FROM properties WHERE propertyId = ?");
			$stmt->bind_param('s', $deleteId);
			$stmt->execute();
			$stmt->close();

			$msgBox = alertBox($propertyDeletedMsg, "<i class='fa fa-check-square-o'></i>", "success");
		}
    }

	// Get Leased Property Data
	//print_r($_SESSION);
	//DIE;
	/*
	Array ( [adminId] => 2 [superuser] => 1 [adminRole] => 1 [adminEmail] => gustavomenacba@hotmail.com [adminFirstName] => Gustavo [adminLastName] => Mena )
	*/
	if ($_SESSION['superuser'] != '1') {
		$lease = "SELECT
					properties.propertyId,
					properties.propertyName,
					properties.isLeased,
					properties.propertyRate,
					properties.latePenalty,
					CASE properties.petsAllowed
						WHEN 0 THEN 'No'
						WHEN 1 THEN 'Yes'
					END AS petsAllowed,
					tenants.tenantId,
					tenants.leaseId,
					tenants.tenantFirstName,
					tenants.tenantLastName,
					DATE_FORMAT(leases.leaseEnd,'%M %d, %Y') AS leaseEnd,
					admins.adminId,
					admins.adminFirstName,
					admins.adminLastName
				FROM
					properties
					LEFT JOIN tenants ON properties.propertyId = tenants.propertyId
					LEFT JOIN leases ON tenants.leaseId = leases.leaseId
					LEFT JOIN assignedproperties ON tenants.propertyId = assignedproperties.propertyId
					LEFT JOIN admins ON assignedproperties.adminId = admins.adminId
				WHERE
					admins.adminId = ".$_SESSION['adminId']." AND
					properties.isLeased = 1";
		$leaseres = mysqli_query($mysqli, $lease) or die('Error, retrieving Leased Property Data failed. ' . mysqli_error());
	} else {
		$lease = "SELECT
					properties.propertyId,
					properties.propertyName,
					properties.isLeased,
					properties.propertyRate,
					properties.latePenalty,
					CASE properties.petsAllowed
						WHEN 0 THEN 'No'
						WHEN 1 THEN 'Si'
					END AS petsAllowed,
					tenants.tenantId,
					tenants.leaseId,
					tenants.tenantFirstName,
					tenants.tenantLastName,
					DATE_FORMAT(leases.leaseEnd,'%M %d, %Y') AS leaseEnd,
					admins.adminId,
					admins.adminFirstName,
					admins.adminLastName
				FROM
					properties
					LEFT JOIN tenants ON properties.propertyId = tenants.propertyId
					LEFT JOIN leases ON tenants.leaseId = leases.leaseId
					LEFT JOIN assignedproperties ON tenants.propertyId = assignedproperties.propertyId
					LEFT JOIN admins ON assignedproperties.adminId = admins.adminId
				WHERE
					properties.isLeased = 1";
		$leaseres = mysqli_query($mysqli, $lease) or die('Error, retrieving Leased Property Data failed. ' . mysqli_error());
	}
	//die($lease);
	// Get Unleased Property Data
    $nolease = "SELECT
					propertyId,
					propertyName,
					propertyAddress,
					isLeased,
					propertyRate,
					CASE petsAllowed
						WHEN 0 THEN 'No'
						WHEN 1 THEN 'Yes'
					END AS petsAllowed,
					propertyFolder,
					propertyType,
					propertyStyle,
					propertySize,
					bedrooms,
					bathrooms
				FROM
					properties
				WHERE
					createdBy = ".$_SESSION['adminId']." AND
					isLeased = 0 AND
					isArchived = 0";
    $noleaseres = mysqli_query($mysqli, $nolease) or die('Error, retrieving Unleased Property Data failed. ' . mysqli_error());
?>
<h3 class="info"><?php echo $activeLeasePropertyH3; ?></h3>
<?php if ($_SESSION['superuser'] == '1') { ?>
	<p><?php echo $activePropertyQuip; ?></p>
<?php }	?>

<?php if ($msgBox) { echo $msgBox; } ?>

<?php if(mysqli_num_rows($leaseres) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noActivePropertiesMsg; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTable" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_property; ?></th>
			<th><?php echo $tab_rentAmount; ?></th>
			<th><?php echo $tab_lateFee; ?></th>
			<th><?php echo $tab_petsAllowed; ?></th>
			<th><?php echo $tab_tenant; ?></th>
			<th><?php echo $tab_leaseEndsOn; ?></th>
			<?php if ($_SESSION['superuser'] == '1') { ?>
				<th><?php echo $tab_landlord; ?></th>
			<?php }	?>
		</tr>
		<tbody class="table-hover">
	<?php
		while ($row = mysqli_fetch_assoc($leaseres)) {
			// Format the Amounts
			$propertyRate = $currencySym.format_amount($row['propertyRate'], 2);
			$latePenalty = $currencySym.format_amount($row['latePenalty'], 2);
	?>
			<tr>
				<td><a href="index.php?action=propertyInfo&propertyId=<?php echo $row['propertyId']; ?>"><?php echo clean($row['propertyName']); ?></a></td>
				<td><?php echo $propertyRate; ?></td>
				<td><?php echo $latePenalty; ?></td>
				<td><?php echo $row['petsAllowed']; ?></td>
				<td><a href="index.php?action=tenantInfo&tenantId=<?php echo $row['tenantId']; ?>"><?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?></a></td>
				<td><?php echo $row['leaseEnd']; ?></td>
				<?php if ($_SESSION['superuser'] == '1') { ?>
					<td><a href="index.php?action=adminInfo&adminId=<?php echo $row['adminId']; ?>"><?php echo clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?></a></td>
				<?php }	?>
			</tr>
		<?php }	?>
		</tbody>
	</table>
<?php }	?>

<hr />

<h3 class="info"><?php echo $activeNoLeasePropertysH3; ?></h3>

<?php if(mysqli_num_rows($noleaseres) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noAvailablePropertiesMsg; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTableTwo" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_property; ?></th>
			<th><?php echo $tab_propertyType; ?></th>
			<th><?php echo $tab_address; ?></th>
			<th><?php echo $tab_monthlyRate; ?></th>
			<th><?php echo $tab_petsAllowed; ?></th>
			<th><?php echo $tab_propertySize; ?></th>
			<th><?php echo $tab_bedroomsBathrooms; ?></th>
			<?php if ($_SESSION['superuser'] == '1') { ?>
				<th></th>
				<th></th>
			<?php }	?>
		</tr>
		<tbody class="table-hover">
		<?php
			while ($prop = mysqli_fetch_assoc($noleaseres)) {
				// Format the Amounts
				$propertyRate = $currencySym.format_amount($prop['propertyRate'], 2);
		?>
			<tr>
				<td><a href="index.php?action=propertyInfo&propertyId=<?php echo $prop['propertyId']; ?>"><?php echo clean($prop['propertyName']); ?></a></td>
				<td><?php echo clean($prop['propertyType']).' '.clean($prop['propertyStyle']); ?></td>
				<td><?php echo clean($prop['propertyAddress']); ?></td>
				<td><?php echo $propertyRate; ?></td>
				<td><?php echo $prop['petsAllowed']; ?></td>
				<td><?php echo clean($prop['propertySize']); ?></td>
				<td><?php echo $prop['bedrooms'].' / '.$prop['bathrooms']; ?></td>
				<?php if ($_SESSION['superuser'] == '1') { ?>
					<td class="tool-tip" title="Archive Property"><a data-toggle="modal" href="#archiveProperty<?php echo $prop['propertyId']; ?>" class="btn btn-xs btn-link"><i class="fa fa-archive"></i></a></td>
					<td class="tool-tip" title="Delete Property"><a data-toggle="modal" href="#deleteProperty<?php echo $prop['propertyId']; ?>" class="btn btn-xs btn-link"><i class="fa fa-times"></i></a></td>
				<?php }	?>
			</tr>

			<?php if ($_SESSION['superuser'] == '1') { ?>
			<!-- Archive Property Confirm Modal -->
			<div class="modal fade" id="archiveProperty<?php echo $prop['propertyId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<form action="" method="post">
							<div class="modal-body">
								<p class="lead"><?php echo $archivePropertyConf; ?>
								</p>
							</div>
							<div class="modal-footer">
								<input name="archiveId" type="hidden" value="<?php echo $prop['propertyId']; ?>" />
								<button type="input" name="submit" value="archiveProperty" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
								<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<!-- Delete Property Confirm Modal -->
			<div class="modal fade" id="deleteProperty<?php echo $prop['propertyId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<form action="" method="post">
							<div class="modal-body">
								<p class="lead"><?php echo $deletePropertyConf; ?>
								</p>
							</div>
							<div class="modal-footer">
								<input name="deleteId" type="hidden" value="<?php echo $prop['propertyId']; ?>" />
								<input name="propertyFolder" type="hidden" value="<?php echo $prop['propertyFolder']; ?>" />
								<button type="input" name="submit" value="deleteProperty" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
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