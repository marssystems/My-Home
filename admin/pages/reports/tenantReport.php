<?php
	$stacktable = 'true';

	// Get POST Data
	$includeInactive = $mysqli->real_escape_string($_POST['includeInactive']);
	if ($includeInactive == '1') {
		$isActive = "'0','1'";
		$reportType = $reportType1;
	} else {
		$isActive = "'1'";
		$reportType = $reportType2;
	}

	// Get Data
    $query  = "SELECT
                    tenants.tenantId,
                    tenants.propertyId,
                    tenants.tenantEmail,
                    tenants.tenantFirstName,
					tenants.tenantLastName,
					tenants.tenantAddress,
					tenants.tenantPhone,
					DATE_FORMAT(tenants.createDate,'%M %d, %Y') AS createDate,
					tenants.isActive,
					CASE tenants.isActive
						WHEN 0 THEN 'No'
						WHEN 1 THEN 'Yes'
					END AS active,
					properties.propertyName
                FROM
                    tenants
					LEFT JOIN properties ON tenants.propertyId = properties.propertyId
				WHERE
					tenants.isActive IN (".$isActive.")
				ORDER BY
					tenants.isActive,
					tenants.tenantId";
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving data failed. ' . mysqli_error());
	$totalRecs = mysqli_num_rows($res);

	$reportName = $reportType;
?>
<h3 class="primary"><?php echo $reportTitleH3.' '.$reportName; ?></h3>

<?php if ($totalRecs < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noReportRecordsFoundMsg; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTable" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_tenant; ?></th>
			<th><?php echo $tab_email; ?></th>
			<th><?php echo $tab_address; ?></th>
			<th><?php echo $tab_phone; ?></th>
			<th><?php echo $tab_dateCreated; ?></th>
			<?php if ($includeInactive == '1') { ?>
				<th><?php echo $tab_isActive; ?></th>
			<?php }	?>
			<th><?php echo $tab_property; ?></th>
		</tr>
		<tbody class="table-hover">
		<?php
			while ($row = mysqli_fetch_assoc($res)) {
				// Decrypt Tenant data
				if ($row['tenantAddress'] != '') { $tenantAddress = decryptIt($row['tenantAddress']); } else { $tenantAddress = ''; }
				if ($row['tenantPhone'] != '') { $tenantPhone = decryptIt($row['tenantPhone']); } else { $tenantPhone = ''; }
		?>
			<tr>
				<td><?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?></td>
				<td><?php echo clean($row['tenantEmail']); ?></td>
				<td><?php echo $tenantAddress; ?></td>
				<td><?php echo $tenantPhone; ?></td>
				<td><?php echo $row['createDate']; ?></td>
				<?php if ($includeInactive == '1') { ?>
					<td><?php echo $row['active']; ?></td>
				<?php }	?>
				<td><?php echo clean($row['propertyName']); ?></td>
			</tr>
		<?php }	?>
		</tbody>
	</table>
	<p><span class="reportTotal"><strong><?php echo $totalRecordsFound; ?></strong> <?php echo $totalRecs; ?></span></p>
<?php }	?>
<p class="reportFooter"><?php echo $reportCreatedOnDate.' '.date('l').", ".date('F jS Y')." at ".date('h:i:s A'); ?></p>