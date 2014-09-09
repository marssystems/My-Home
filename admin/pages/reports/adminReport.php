<?php
	$stacktable = 'true';

	// Get POST Data
	$adminType = $mysqli->real_escape_string($_POST['adminType']);
	if ($adminType == '1') {
		$isActive = "'0','1'";
		$reportType = $adminReport1;
	} else {
		$isActive = "'1'";
		$reportType = $adminReport2;
	}

	// Get Data
    $query  = "SELECT
                    adminId,
                    CASE superuser
						WHEN 0 THEN 'No'
						WHEN 1 THEN 'Yes'
					END AS superuser,
					CASE adminRole
						WHEN 0 THEN 'Administrator'
						WHEN 1 THEN 'Landlord'
					END AS adminRole,
                    adminEmail,
					adminFirstName,
					adminLastName,
					adminPhone,
					adminAltPhone,
					DATE_FORMAT(createDate,'%M %d, %Y') AS createDate,
					isActive,
					CASE isActive
						WHEN 0 THEN 'No'
						WHEN 1 THEN 'Yes'
					END AS active
                FROM
                    admins
				WHERE
					isActive IN (".$isActive.")
				ORDER BY
					isActive,
					adminId";
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
			<th><?php echo $tab_name; ?></th>
			<th><?php echo $tab_email; ?></th>
			<th><?php echo $tab_phone; ?></th>
			<th><?php echo $tab_altPhone; ?></th>
			<th><?php echo $tab_dateCreated; ?></th>
			<?php if ($adminType == '1') { ?>
				<th><?php echo $tab_isActive; ?></th>
			<?php }	?>
			<th><?php echo $tab_superUser; ?></th>
			<th><?php echo $tab_adminRole; ?></th>
		</tr>
		<tbody class="table-hover">
		<?php
			while ($row = mysqli_fetch_assoc($res)) {
				// Decrypt Admin data
				if ($row['adminPhone'] != '') { $adminPhone = decryptIt($row['adminPhone']); } else { $adminPhone = ''; }
				if ($row['adminAltPhone'] != '') { $adminAltPhone = decryptIt($row['adminAltPhone']); } else { $adminAltPhone = ''; }
		?>
			<tr>
				<td><?php echo clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?></td>
				<td><?php echo clean($row['adminEmail']); ?></td>
				<td><?php echo $adminPhone; ?></td>
				<td><?php echo $adminAltPhone; ?></td>
				<td><?php echo $row['createDate']; ?></td>
				<?php if ($adminType == '1') { ?>
					<td><?php echo $row['active']; ?></td>
				<?php }	?>
				<td><?php echo $row['superuser']; ?></td>
				<td><?php echo $row['adminRole']; ?></td>
			</tr>
		<?php }	?>
		</tbody>
	</table>
	<p><span class="reportTotal"><strong><?php echo $totalRecordsFound; ?></strong> <?php echo $totalRecs; ?></span></p>
<?php }	?>
<p class="reportFooter"><?php echo $reportCreatedOnDate.' '.date('l').", ".date('F jS Y')." at ".date('h:i:s A'); ?></p>