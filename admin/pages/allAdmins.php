<?php
	$stacktable = 'true';

	// Delete Admin
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteAdmin') {
		if ($_POST['deleteId'] != '1') {
			$stmt = $mysqli->prepare("DELETE FROM admins WHERE adminId = ?");
			$stmt->bind_param('s', $_POST['deleteId']);
			$stmt->execute();
			$stmt->close();
			$msgBox = alertBox($adminDeletedMsg, "<i class='fa fa-check-square-o'></i>", "success");
		} else {
			$msgBox = alertBox($adminDeleteFailMsg, "<i class='fa fa-times-circle'></i>", "danger");
		}
    }

	// Get Admin Data
    $query = "SELECT
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
					DATE_FORMAT(createDate,'%M %d, %Y') AS createDate,
					CASE isActive
						WHEN 0 THEN 'No'
						WHEN 1 THEN 'Yes'
					END AS isActive
				FROM
					admins";
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving Admin Data failed. ' . mysqli_error());

	if ($superuser != '1') {
?>
<h3 class="danger"><?php echo $accessErrorH3; ?></h3>
<div class="alertMsg danger">
	<i class="fa fa-minus-square-o"></i> <?php echo $permissionDenied; ?>
</div>
<?php } else { ?>
	<h3 class="danger"><?php echo $allAdminsH3; ?></h3>

	<?php if ($msgBox) { echo $msgBox; } ?>

	<table id="responsiveTable" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_name; ?></th>
			<th><?php echo $tab_superUser; ?></th>
			<th><?php echo $tab_adminRole; ?></th>
			<th><?php echo $tab_email; ?></th>
			<th><?php echo $tab_phone; ?></th>
			<th><?php echo $tab_dateCreated; ?></th>
			<th><?php echo $tab_isActive; ?></th>
			<th></th>
		</tr>
		<tbody class="table-hover">
		<?php
			while ($row = mysqli_fetch_assoc($res)) {
				if ($row['adminPhone'] != '') { $adminPhone = decryptIt($row['adminPhone']); } else { $adminPhone = ''; }
		?>
			<tr>
				<td><a href="index.php?action=adminInfo&adminId=<?php echo $row['adminId']; ?>"><?php echo clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?></a></td>
				<td><?php echo $row['superuser']; ?></td>
				<td><?php echo $row['adminRole']; ?></td>
				<td><?php echo $row['adminEmail']; ?></td>
				<td><?php echo $adminPhone; ?></td>
				<td><?php echo $row['createDate']; ?></td>
				<td><?php echo $row['isActive']; ?></td>
				<td><a data-toggle="modal" href="#deleteAdmin<?php echo $row['adminId']; ?>" class="btn btn-xs btn-link tool-tip" title="Delete Admin Account"><i class="fa fa-times"></i></a></td>
			</tr>

			<!-- Delete Admin Account Confirm Modal -->
			<div class="modal fade" id="deleteAdmin<?php echo $row['adminId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<form action="" method="post">
							<div class="modal-body">
								<p class="lead"><?php echo $deleteAdminConf; ?>
								</p>
							</div>
							<div class="modal-footer">
								<input name="deleteId" type="hidden" value="<?php echo $row['adminId']; ?>" />
								<button type="input" name="submit" value="deleteAdmin" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
								<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
							</div>
						</form>
					</div>
				</div>
			</div>
		<?php }	?>
		</tbody>
	</table>
<?php } ?>