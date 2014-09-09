<?php
	$stacktable = 'true';

	// Get POST Data
	$propertyType = $mysqli->real_escape_string($_POST['propertyType']);
	if ($propertyType == '0') {
		// Only Leased Properties
		$query  = "SELECT
						properties.propertyId,
						properties.propertyName,
						properties.propertyAddress,
						properties.isLeased,
						properties.propertyRate,
						properties.latePenalty,
						properties.propertyDeposit,
						CASE properties.petsAllowed
							WHEN 0 THEN 'No'
							WHEN 1 THEN 'Yes'
						END AS petsAllowed,
						properties.isArchived,
						tenants.tenantId,
						tenants.tenantFirstName,
						tenants.tenantLastName,
						admins.adminId,
						admins.adminFirstName,
						admins.adminLastName
					FROM
						properties
						LEFT JOIN tenants ON properties.propertyId = tenants.propertyId
						LEFT JOIN leases ON properties.propertyId = leases.propertyId
						LEFT JOIN assignedproperties ON tenants.propertyId = assignedproperties.propertyId
						LEFT JOIN admins ON assignedproperties.adminId = admins.adminId
					WHERE
						properties.isArchived = 0 AND
						properties.isLeased = 1
					ORDER BY
						properties.propertyId";
		$res = mysqli_query($mysqli, $query) or die('Error, retrieving Only Leased Properties failed. ' . mysqli_error());
		$totalRecs = mysqli_num_rows($res);
		$reportName = 'Currently Leased Properties';
	} else if ($propertyType == '1') {
		// Currently Leased &amp; Available to Rent Properties
		$query  = "SELECT
						propertyId,
						propertyName,
						propertyAddress,
						isLeased,
						CASE isLeased
							WHEN 0 THEN 'No'
							WHEN 1 THEN 'Yes'
						END AS leased,
						propertyRate,
						latePenalty,
						propertyDeposit,
						CASE petsAllowed
							WHEN 0 THEN 'No'
							WHEN 1 THEN 'Yes'
						END AS petsAllowed,
						isArchived
					FROM
						properties
					WHERE
						isArchived = 0 AND
						isLeased IN ('0', '1')
					ORDER BY
						propertyId";
		$res = mysqli_query($mysqli, $query) or die('Error, retrieving Currently Leased &amp; Available to Rent Properties failed. ' . mysqli_error());
		$totalRecs = mysqli_num_rows($res);
		$reportName = 'Currently Leased &amp; Available Properties';
	} else if ($propertyType == '2') {
		// Only Properties that are Available to Rent
		$query  = "SELECT
						propertyId,
						propertyName,
						propertyAddress,
						isLeased,
						propertyRate,
						latePenalty,
						propertyDeposit,
						CASE petsAllowed
							WHEN 0 THEN 'No'
							WHEN 1 THEN 'Yes'
						END AS petsAllowed,
						propertySize,
						bedrooms,
						bathrooms,
						isArchived
					FROM
						properties
					WHERE
						isArchived = 0 AND
						isLeased = 0
					ORDER BY
						propertyId";
		$res = mysqli_query($mysqli, $query) or die('Error, retrieving Properties that are Available to Rent failed. ' . mysqli_error());
		$totalRecs = mysqli_num_rows($res);
		$reportName = 'Available Properties';
	} else {
		// Only Archived Properties
		$query  = "SELECT
						propertyId,
						propertyName,
						propertyAddress,
						properties.propertyRate,
						properties.latePenalty,
						CASE petsAllowed
							WHEN 0 THEN 'No'
							WHEN 1 THEN 'Yes'
						END AS petsAllowed,
						isArchived,
						DATE_FORMAT(dateArchived,'%M %d, %Y') AS dateArchived
					FROM
						properties
					WHERE
						isArchived = 1
					ORDER BY
						propertyId";
		$res = mysqli_query($mysqli, $query) or die('Error, retrieving Archived Properties failed. ' . mysqli_error());
		$totalRecs = mysqli_num_rows($res);
		$reportName = 'Archived Properties';
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
			<th><?php echo $tab_property; ?></th>
			<th><?php echo $tab_address; ?></th>
			<th><?php echo $tab_monthlyRate; ?></th>
			<th><?php echo $rentalLateFeeLi; ?></th>
			<?php if ($propertyType == '0' || $propertyType == '1' || $propertyType == '2' ) { ?>
				<th><?php echo $depositAmountReq; ?></th>
			<?php } ?>
			<th><?php echo $tab_petsAllowed; ?></th>
			<?php if ($propertyType == '1') { ?>
				<th><?php echo $tab_isLeased; ?></th>
			<?php } else if ($propertyType == '2') { ?>
				<th><?php echo $tab_propertySize; ?></th>
				<th><?php echo $tab_bedroomsBathrooms; ?></th>
			<?php } else if ($propertyType == '0') { ?>
				<th><?php echo $tab_tenant; ?></th>
				<th><?php echo $tab_landlord; ?></th>
			<?php } ?>
		</tr>
		<tbody class="table-hover">
		<?php
			while ($row = mysqli_fetch_assoc($res)) {
				// Format the Amounts
				$propertyRate = $currencySym.format_amount($row['propertyRate'], 2);
				$latePenalty = $currencySym.format_amount($row['latePenalty'], 2);
				if ($propertyType != '3') {
					$propertyDeposit = $currencySym.format_amount($row['propertyDeposit'], 2);
				}
		?>
			<tr>
				<td><?php echo clean($row['propertyName']); ?></td>
				<td><?php echo clean($row['propertyAddress']); ?></td>
				<td><?php echo $propertyRate; ?></td>
				<td><?php echo $latePenalty; ?></td>
				<?php if ($propertyType != '3') { ?>
					<td><?php echo $propertyDeposit; ?></td>
				<?php } ?>
				<td><?php echo $row['petsAllowed']; ?></td>
				<?php if ($propertyType == '1') { ?>
					<td><?php echo $row['leased']; ?></td>
				<?php } else if ($propertyType == '2') { ?>
					<td><?php echo clean($row['propertySize']); ?></td>
					<td><?php echo $row['bedrooms'].' / '.$row['bathrooms']; ?></td>
				<?php } else if ($propertyType == '0') { ?>
					<td><?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?></td>
					<td><?php echo clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?></td>
				<?php } ?>
			</tr>
		<?php }	?>
		</tbody>
	</table>
	<p><span class="reportTotal"><strong><?php echo $totalRecordsFound; ?></strong> <?php echo $totalRecs; ?></span></p>
<?php }	?>
<p class="reportFooter"><?php echo $reportCreatedOnDate.' '.date('l').", ".date('F jS Y')." at ".date('h:i:s A'); ?></p>
