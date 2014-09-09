<?php
	$stacktable = 'true';

	// Get POST Data
	$leaseType = $mysqli->real_escape_string($_POST['leaseType']);
	if ($leaseType == '1') {
		// Open and Closed Leases
		$isClosed = "'0','1'";
		$reportType = $leaseReport2;
	} else {
		// Open Leases only
		$isClosed = "'0'";
		$reportType = $leaseReport1;
	}

	// Get Data
    $query  = "SELECT
                    leases.leaseId,
                    leases.propertyId,
                    leases.leaseTerm,
					DATE_FORMAT(leases.leaseStart,'%M %d, %Y') AS leaseStart,
					DATE_FORMAT(leases.leaseEnd,'%M %d, %Y') AS leaseEnd,
					leases.isClosed,
					CASE leases.isClosed
						WHEN 0 THEN 'Open'
						WHEN 1 THEN 'Closed'
					END AS closed,
					properties.propertyName,
					tenants.tenantFirstName,
					tenants.tenantLastName
                FROM
                    leases
					LEFT JOIN properties ON leases.propertyId = properties.propertyId
					LEFT JOIN tenants ON properties.propertyId = tenants.propertyId
				WHERE
					leases.isClosed IN (".$isClosed.")
				ORDER BY
					leases.leaseId,
					tenants.tenantId";
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving data failed. ' . mysqli_error());
	$totalRecs = mysqli_num_rows($res);
?>
<h3 class="primary"><?php echo $reportTitleH3.' '.$reportType; ?></h3>

<?php if ($totalRecs < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noReportRecordsFoundMsg; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTable" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_tenant; ?></th>
			<th><?php echo $tab_property; ?></th>
			<th><?php echo $tab_leaseTerm; ?></th>
			<th><?php echo $tab_leaseStartsOn; ?></th>
			<th><?php echo $tab_leaseEndsOn; ?></th>
			<th><?php echo $tab_status; ?></th>
		</tr>
		<tbody class="table-hover">
		<?php while ($row = mysqli_fetch_assoc($res)) { ?>
			<tr>
				<td><?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?></td>
				<td><?php echo clean($row['propertyName']); ?></td>
				<td><?php echo clean($row['leaseTerm']); ?></td>
				<td><?php echo $row['leaseStart']; ?></td>
				<td><?php echo $row['leaseEnd']; ?></td>
				<td><?php echo $row['closed']; ?></td>
			</tr>
		<?php }	?>
		</tbody>
	</table>
	<p><span class="reportTotal"><strong><?php echo $totalRecordsFound; ?></strong> <?php echo $totalRecs; ?></span></p>
<?php }	?>
<p class="reportFooter"><?php echo $reportCreatedOnDate.' '.date('l').", ".date('F jS Y')." at ".date('h:i:s A'); ?></p>