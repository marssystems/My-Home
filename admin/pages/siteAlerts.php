<?php
	$jsFile = 'siteAlerts';
	$stacktable = 'true';
	$datePicker = 'true';

	// Delete Alert
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteAlert') {
		$stmt = $mysqli->prepare("DELETE FROM sitealerts WHERE alertId = ?");
		$stmt->bind_param('s', $_POST['deleteId']);
		$stmt->execute();
		$stmt->close();

		$msgBox = alertBox($alertDeletedMsg, "<i class='fa fa-check-square-o'></i>", "success");
    }

	// Add New Site Alert
    if (isset($_POST['submit']) && $_POST['submit'] == 'saveAlert') {
		if($_POST['alertTitle'] == "") {
            $msgBox = alertBox($alertTitleMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['alertText'] == "") {
            $msgBox = alertBox($alertTextMsg, "<i class='fa fa-times-circle'></i>", "danger");
        }  else {
			$isActive = $mysqli->real_escape_string($_POST['isActive']);
			$onReceipt = $mysqli->real_escape_string($_POST['onReceipt']);
			$alertTitle = $mysqli->real_escape_string($_POST['alertTitle']);
			$alertText = htmlentities($_POST['alertText']);
			$alertStart = $mysqli->real_escape_string($_POST['alertStart']);
			$alertExpires = $mysqli->real_escape_string($_POST['alertExpires']);
			$today = date("Y-m-d");

			$stmt = $mysqli->prepare("
								INSERT INTO
									sitealerts(
										adminId,
										isActive,
										onReceipt,
										alertTitle,
										alertText,
										alertDate,
										alertStart,
										alertExpires
									) VALUES (
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?
									)");
			$stmt->bind_param('ssssssss',
								$_SESSION['adminId'],
								$isActive,
								$onReceipt,
								$alertTitle,
								$alertText,
								$today,
								$alertStart,
								$alertExpires
			);
			$stmt->execute();
			$msgBox = alertBox($newAlertSavedMsg, "<i class='fa fa-check-square-o'></i>", "success");

			// Clear the form of Values
			$_POST['alertTitle'] = $_POST['alertStart'] = $_POST['alertExpires'] = $_POST['alertText'] = '';
			$stmt->close();
		}
	}

	// Edit Site Alert
    if (isset($_POST['submit']) && $_POST['submit'] == 'updateAlert') {
		if($_POST['alertTitle'] == "") {
            $msgBox = alertBox($alertTitleMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['alertText'] == "") {
            $msgBox = alertBox($alertTextMsg, "<i class='fa fa-times-circle'></i>", "danger");
        }  else {
			$isActive = $mysqli->real_escape_string($_POST['isActive']);
			$onReceipt = $mysqli->real_escape_string($_POST['onReceipt']);
			$alertTitle = $mysqli->real_escape_string($_POST['alertTitle']);
			$alertText = htmlentities($_POST['alertText']);
			$alertStart = $mysqli->real_escape_string($_POST['startsOn']);
			$alertExpires = $mysqli->real_escape_string($_POST['endsOn']);
			$alertId = $mysqli->real_escape_string($_POST['alertId']);

			$stmt = $mysqli->prepare("
								UPDATE
									sitealerts
								SET
									isActive = ?,
									onReceipt = ?,
									alertTitle = ?,
									alertText = ?,
									alertStart = ?,
									alertExpires = ?
								WHERE
									alertId = ?");
			$stmt->bind_param('sssssss',
							   $isActive,
							   $onReceipt,
							   $alertTitle,
							   $alertText,
							   $alertStart,
							   $alertExpires,
							   $alertId
			);
			$stmt->execute();
			$msgBox = alertBox($alertUpdatedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			$stmt->close();
		}
	}

	// Get Site Alert Data
    $query = "SELECT
				sitealerts.alertId,
				sitealerts.adminId,
				CASE sitealerts.isActive
					WHEN 0 THEN 'No'
					WHEN 1 THEN 'Yes'
				END AS isActive,
				CASE sitealerts.onReceipt
					WHEN 0 THEN 'No'
					WHEN 1 THEN 'Yes'
				END AS onReceipt,
				sitealerts.alertTitle,
				sitealerts.alertText,
				sitealerts.alertStart,
				DATE_FORMAT(sitealerts.alertStart,'%M %d, %Y') AS starts,
				sitealerts.alertExpires,
				DATE_FORMAT(sitealerts.alertExpires,'%M %d, %Y') AS ends,
				admins.adminFirstName,
				admins.adminLastName
			FROM
				sitealerts
				LEFT JOIN admins ON sitealerts.adminId = admins.adminId";
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving Site Alert Data failed. ' . mysqli_error());
?>
<h3 class="primary"><?php echo $siteAlertsH3; ?></h3>
<div class="row">
	<div class="col-md-8">
		<p class="lead">
			<?php echo $siteAlertsQuip; ?><br />
			<small><?php echo $siteAlertsInst; ?></small>
		</p>
	</div>
	<div class="col-md-4">
		<a data-toggle="modal" href="#newAlert" class="btn btn-primary btn-icon floatRight"><i class="fa fa-plus"></i> <?php echo $newSiteAlertBtn; ?></a>
	</div>
</div>

<?php if ($msgBox) { echo $msgBox; } ?>

<?php if(mysqli_num_rows($res) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noSiteAlertsMsg; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTable" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_Alert; ?></th>
			<th><?php echo $tab_alertText; ?></th>
			<th><?php echo $tab_createdBy; ?></th>
			<th><?php echo $tab_isActive; ?></th>
			<th><?php echo $tab_printOnReceipt; ?></th>
			<th><?php echo $tab_dateStarts; ?></th>
			<th><?php echo $tab_dateEnds; ?></th>
			<th></th>
		</tr>
		<tbody class="table-hover">
	<?php
		while ($row = mysqli_fetch_assoc($res)) {
			if ($row['isActive'] == 'Yes') { $isActive = 'selected'; } else { $isActive = ''; }
			if ($row['onReceipt'] == 'Yes') { $onReceipt = 'selected'; } else { $onReceipt = ''; }
			if ($row['alertStart'] != '0000-00-00') { $alertStart = $row['alertStart']; } else { $alertStart = ''; }
			if ($row['alertExpires'] != '0000-00-00') { $alertExpires = $row['alertExpires']; } else { $alertExpires = ''; }
	?>
			<tr>
				<td><a data-toggle="modal" href="#editAlert<?php echo $row['alertId']; ?>"><?php echo clean($row['alertTitle']); ?></a></td>
				<td><?php echo ellipsis($row['alertText']); ?></td>
				<td><?php echo clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?></td>
				<td><?php echo $row['isActive']; ?></td>
				<td><?php echo $row['onReceipt']; ?></td>
				<td><?php echo $row['starts']; ?></td>
				<td><?php echo $row['ends']; ?></td>
				<td><a data-toggle="modal" href="#deleteAlert<?php echo $row['alertId']; ?>" class="btn btn-xs btn-link tool-tip" title="Delete Site Alert"><i class="fa fa-times"></i></a></td>
			</tr>

			<!-- Edit an Alert Modal -->
			<div class="modal fade" id="editAlert<?php echo $row['alertId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header modal-primary">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
							<h4 class="modal-title"><?php echo $editAlertModalTitle; ?></h4>
						</div>
						<form action="" method="post">
							<div class="modal-body">
								<p><small><?php echo $alertDatesInstructions; ?></small></p>

								<div class="form-group">
									<label for="alertTitle"><?php echo $alertTitleField; ?></label>
									<input type="text" class="form-control" name="alertTitle" id="alertTitle" value="<?php echo clean($row['alertTitle']); ?>">
								</div>
								<div class="form-group">
									<label for="isActive"><?php echo $alertStatusFeild; ?></label>
									<select class="form-control" id="isActive" name="isActive">
										<option value="0"><?php echo $statusOptionInactive; ?></option>
										<option value="1" <?php echo $isActive; ?>><?php echo $statusOptionActive; ?></option>
									</select>
									<span class="help-block"><?php echo $alertStatusHelper; ?></span>
								</div>
								<div class="form-group">
									<label for="onReceipt"><?php echo $invoicePrintField; ?></label>
									<select class="form-control" id="onReceipt" name="onReceipt">
										<option value="0"><?php echo $OptionNo; ?></option>
										<option value="1" <?php echo $OptionYes; ?>><?php echo $OptionYes; ?></option>
									</select>
									<span class="help-block"><?php echo $invoicePrintHelper; ?></span>
								</div>
								<div class="form-group">
									<label for="startsOn"><?php echo $alertStartField; ?></label>
									<input type="text" class="form-control" name="startsOn" id="startsOn" value="<?php echo $alertStart; ?>">
									<span class="help-block"><?php echo $alertStarHelper; ?></span>
								</div>
								<div class="form-group">
									<label for="endsOn"><?php echo $alertEndField; ?></label>
									<input type="text" class="form-control" name="endsOn" id="endsOn" value="<?php echo $alertExpires; ?>">
									<span class="help-block"><?php echo $alertEndHelper; ?></span>
								</div>
								<div class="form-group form-group-modal">
									<label for="alertText"><?php echo $alertTextField; ?></label>
									<textarea class="form-control" name="alertText" id="alertText" rows="3"><?php echo $row['alertText']; ?></textarea>
								</div>
							</div>
							<div class="modal-footer">
								<input type="hidden" name="alertId" id="alertId" value="<?php echo $row['alertId']; ?>">
								<button type="input" name="submit" value="updateAlert" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
								<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<!-- Delete Alert Confirm Modal -->
			<div class="modal fade" id="deleteAlert<?php echo $row['alertId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<form action="" method="post">
							<div class="modal-body">
								<p class="lead"><?php echo $deleteAlertConf; ?>
								</p>
							</div>
							<div class="modal-footer">
								<input name="deleteId" type="hidden" value="<?php echo $row['alertId']; ?>" />
								<button type="input" name="submit" value="deleteAlert" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
								<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
							</div>
						</form>
					</div>
				</div>
			</div>
		<?php }	?>
		</tbody>
	</table>
<?php }	?>

<!-- Create a New Alert Modal -->
<div class="modal fade" id="newAlert" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $newAlertModalTitle; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<p><small><?php echo $alertDatesInstructions; ?></small></p>

					<div class="form-group">
						<label for="alertTitle"><?php echo $alertTitleField; ?></label>
						<input type="text" class="form-control" name="alertTitle" id="alertTitle" value="<?php echo isset($_POST['alertTitle']) ? $_POST['alertTitle'] : ''; ?>">
					</div>
					<div class="form-group">
						<label for="isActive"><?php echo $alertStatusFeild; ?></label>
						<select class="form-control" id="isActive" name="isActive">
							<option value="0"><?php echo $statusOptionInactive; ?></option>
							<option value="1" selected><?php echo $statusOptionActive; ?></option>
						</select>
						<span class="help-block"><?php echo $alertStatusHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="onReceipt"><?php echo $invoicePrintField; ?></label>
						<select class="form-control" id="onReceipt" name="onReceipt">
							<option value="0" selected><?php echo $OptionNo; ?></option>
							<option value="1"><?php echo $OptionYes; ?></option>
						</select>
						<span class="help-block"><?php echo $invoicePrintHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="alertStart"><?php echo $alertStartField; ?></label>
						<input type="text" class="form-control" name="alertStart" id="alertStart" value="<?php echo isset($_POST['alertStart']) ? $_POST['alertStart'] : ''; ?>">
						<span class="help-block"><?php echo $alertStarHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="alertExpires"><?php echo $alertEndField; ?></label>
						<input type="text" class="form-control" name="alertExpires" id="alertExpires" value="<?php echo isset($_POST['alertExpires']) ? $_POST['alertExpires'] : ''; ?>">
						<span class="help-block"><?php echo $alertEndHelper; ?></span>
					</div>
					<div class="form-group form-group-modal">
						<label for="alertText"><?php echo $alertTextField; ?></label>
						<textarea class="form-control" name="alertText" id="alertText" rows="3"><?php echo isset($_POST['alertText']) ? $_POST['alertText'] : ''; ?></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="saveAlert" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $createAlertBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>