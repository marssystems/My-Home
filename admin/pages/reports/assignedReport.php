<?php
	$stacktable = 'true';
	$viewAll = '';

	// Get POST Data
	if ($_POST['adminId'] == 'all') {
		$viewAll = 'true';
	} else {
		$adminId = $mysqli->real_escape_string($_POST['adminId']);
	}
	
	// Get the Data
	$sql = $select = $where = "";
	$select = "SELECT
					tenants.tenantId,
					tenants.propertyId,
					tenants.leaseId,
					tenants.tenantFirstName,
					tenants.tenantLastName,
					tenants.isActive,
					properties.propertyId,
					properties.propertyName,
					properties.propertyRate,
					leases.leaseTerm,
					DATE_FORMAT(leases.leaseEnd,'%M %d, %Y') AS leaseEnd,
					admins.adminId,
					admins.adminFirstName,
					admins.adminLastName
				FROM
					tenants
					LEFT JOIN properties ON tenants.propertyId = properties.propertyId
					LEFT JOIN leases ON tenants.leaseId = leases.leaseId
					LEFT JOIN assignedproperties ON tenants.propertyId = assignedproperties.propertyId
					LEFT JOIN admins ON assignedproperties.adminId = admins.adminId";

	if ($viewAll == 'true') {
		// All Admins
		$where = sprintf("WHERE
							tenants.isActive = 1 AND
							tenants.leaseId != 0
						ORDER BY admins.adminId");
		$reportName = 'All Assigned Properties';
	} else {
		// Specific Admin
		$where = sprintf("WHERE
							tenants.isActive = 1 AND
							tenants.leaseId != 0 AND
							admins.adminId = ".$adminId."
						ORDER BY admins.adminId");
		$reportName = 'Assigned Properties to a Specific Admin/Landlord';
	}

	$sql = sprintf("%s %s", $select, $where);
	$res = mysqli_query($mysqli, $sql) or die('Error, retrieving Data failed. ' . mysqli_error());
	$totalRecs = mysqli_num_rows($res);
?>
<h3 class="primary"><?php echo $reportTitleH3.' '.$reportName; ?></h3>

<?php if ($totalRecs < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noReportRecordsFoundMsg; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTable" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_adminLandlord; ?></th>
			<th><?php echo $tab_tenant; ?></th>
			<th><?php echo $tab_property; ?></th>
			<th><?php echo $tab_monthlyRate; ?></th>
			<th><?php echo $tab_leaseTerm; ?></th>
			<th><?php echo $tab_leaseEndsOn; ?></th>
		</tr>
		<tbody class="table-hover">
		<?php
			while ($row = mysqli_fetch_assoc($res)) {
				// Format the Amounts
				$propertyRate = $currencySym.format_amount($row['propertyRate'], 2);
		?>
				<tr>
					<td><?php echo clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?></td>
					<td><?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?></td>
					<td><?php echo $row['propertyName']; ?></td>
					<td><?php echo $propertyRate; ?></td>
					<td><?php echo clean($row['leaseTerm']); ?></td>
					<td><?php echo $row['leaseEnd']; ?></td>
				</tr>
		<?php }	?>
		</tbody>
	</table>
	<p><span class="reportTotal"><strong><?php echo $totalRecordsFound; ?></strong> <?php echo $totalRecs; ?></span></p>
<?php }	?>
<p class="reportFooter"><?php echo $reportCreatedOnDate.' '.date('l').", ".date('F jS Y')." at ".date('h:i:s A'); ?></p>