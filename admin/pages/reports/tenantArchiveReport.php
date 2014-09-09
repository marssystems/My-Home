<?php
	$stacktable = 'true';

	// Get Data
    $query  = "SELECT
                    tenantId,
                    propertyId,
                    tenantEmail,
                    tenantFirstName,
					tenantLastName,
					tenantAddress,
					tenantPhone,
					DATE_FORMAT(createDate,'%M %d, %Y') AS createDate,
					isArchived,
					DATE_FORMAT(archivedDate,'%M %d, %Y') AS archivedDate
                FROM
                    tenants
				WHERE
					isArchived = '1'
				ORDER BY
					tenants.tenantId";
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving data failed. ' . mysqli_error());
	$totalRecs = mysqli_num_rows($res);
?>
<h3 class="primary"><?php echo $reportTitleH3.' '.$archivedTenantReportName; ?></h3>

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
			<th><?php echo $tab_dateArchived; ?></th>
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
				<td><?php echo $row['archivedDate']; ?></td>
			</tr>
		<?php }	?>
		</tbody>
	</table>
	<p><span class="reportTotal"><strong><?php echo $totalRecordsFound; ?></strong> <?php echo $totalRecs; ?></span></p>
<?php }	?>
<p class="reportFooter"><?php echo $reportCreatedOnDate.' '.date('l').", ".date('F jS Y')." at ".date('h:i:s A'); ?></p>