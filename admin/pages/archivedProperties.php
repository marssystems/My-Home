<?php
	$stacktable = 'true';

	// Get the Property Pictures Folder Path from the Site Settings
	$propertyPicsPath = $set['propertyPicsPath'];

	// Get Property Files Folder from Site Settings
	$uploadPath = $set['uploadPath'];

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

	// Get Archived Property Data
    $query = "SELECT
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
					isArchived = 1";
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving Archived Property Data failed. ' . mysqli_error());
?>
<h3 class="info"><?php echo $archivedPropertiesH3; ?></h3>

<?php if ($msgBox) { echo $msgBox; } ?>

<?php if(mysqli_num_rows($res) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noArchivedPropertiesMsg; ?>
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
			<?php if ($superuser == '1') { ?>
				<th></th>
			<?php }	?>
		</tr>
		<tbody class="table-hover">
		<?php
			while ($row = mysqli_fetch_assoc($res)) {
				// Format the Amounts
				$propertyRate = $currencySym.format_amount($row['propertyRate'], 2);
		?>
			<tr>
				<td><a href="index.php?action=propertyInfo&propertyId=<?php echo $row['propertyId']; ?>"><?php echo clean($row['propertyName']); ?></a></td>
				<td><?php echo clean($row['propertyType']).' '.clean($row['propertyStyle']); ?></td>
				<td><?php echo clean($row['propertyAddress']); ?></td>
				<td><?php echo $propertyRate; ?></td>
				<td><?php echo $row['petsAllowed']; ?></td>
				<td><?php echo clean($row['propertySize']); ?></td>
				<td><?php echo $row['bedrooms'].' / '.$row['bathrooms']; ?></td>
				<?php if ($superuser == '1') { ?>
					<td><a data-toggle="modal" href="#deleteProperty<?php echo $row['propertyId']; ?>" class="btn btn-xs btn-link tool-tip" title="Delete Property"><i class="fa fa-times"></i></a></td>
				<?php }	?>
			</tr>

			<?php if ($superuser == '1') { ?>
			<!-- Delete Property Confirm Modal -->
			<div class="modal fade" id="deleteProperty<?php echo $row['propertyId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<form action="" method="post">
							<div class="modal-body">
								<p class="lead"><?php echo $deletePropertyConf; ?>
								</p>
							</div>
							<div class="modal-footer">
								<input name="deleteId" type="hidden" value="<?php echo $row['propertyId']; ?>" />
								<input name="propertyFolder" type="hidden" value="<?php echo $row['propertyFolder']; ?>" />
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