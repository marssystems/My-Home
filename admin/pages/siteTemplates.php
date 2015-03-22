<?php
	$stacktable = 'true';

	// Get Templates Folder from Site Settings
	$templatesPath = $set['templatesPath'];

    // Get the Max Upload Size allowed
    $maxUpload = (int)(ini_get('upload_max_filesize'));

	// Get the Upload file types allowed from Site Settings
	$fileTypesAllowed = $set['fileTypesAllowed'];
	// Replce the commas with a comma space
	$typesAllowed = preg_replace('/,/', ', ', $fileTypesAllowed);

	// Delete a Template
    if (isset($_POST['submit']) && $_POST['submit'] == 'deleteTemplate') {
		$templateId = $mysqli->real_escape_string($_POST['deleteId']);

		// Get the Template url
		$sql = "SELECT templateUrl FROM sitetemplates WHERE templateId = ".$templateId;
		$result = mysqli_query($mysqli, $sql) or die(mysqli_error());
		$r = mysqli_fetch_assoc($result);
		$templateUrl = $r['templateUrl'];
		$filePath = $templatesPath.$templateUrl;

		// Delete the Template from the server
		if (file_exists($filePath)) {
			unlink($filePath);

			// Delete the record
			$stmt = $mysqli->prepare("DELETE FROM sitetemplates WHERE templateId = ?");
			$stmt->bind_param('s', $templateId);
			$stmt->execute();

			$msgBox = alertBox($templateDeletedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($templateDeleteErrorMsg, "<i class='fa fa-minus-square-o'></i>", "warning");
		}
	}

	// Upload Template/Form
	if (isset($_POST['submit']) && $_POST['submit'] == 'uploadTemplate') {
		if($_POST['templateName'] == "") {
			$msgBox = alertBox($templateNameReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['templateDesc'] == "") {
			$msgBox = alertBox($templateDescReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			// Get the File Types allowed
			$fileExt = $set['fileTypesAllowed'];
			$allowed = preg_replace('/,/', ', ', $fileExt); // Replce the commas with a comma space (, )
			$ftypes = array($fileExt);
			$ftypes_data = explode( ',', $fileExt );

			// Check file type
			$ext = substr(strrchr(basename($_FILES['file']['name']), '.'), 1);
			if (!in_array($ext, $ftypes_data)) {
				$msgBox = alertBox($fileNotAcceptedMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else {
				// Rename the Document
				$templateName = htmlentities($_POST['templateName']);

				// Replace any spaces with an underscore
				// And set to all lowercase
				$newName = str_replace(' ', '_', $templateName);
				$fileName = strtolower($newName);
				$fullName = $fileName;

				// set the upload path
				$documentUrl = basename($_FILES['file']['name']);

				// Get the files original Ext
				$extension = pathinfo($documentUrl, PATHINFO_EXTENSION);

				// Set the files name to the name set in the form
				// And add the original Ext
				$newDocumentName = $fullName.'.'.$extension;
				$movePath = $templatesPath.'/'.$newDocumentName;

				$templateDesc = htmlentities($_POST['templateDesc']);

				$stmt = $mysqli->prepare("
									INSERT INTO
										sitetemplates(
											adminId,
											templateName,
											templateDesc,
											templateUrl
										) VALUES (
											?,
											?,
											?,
											?
										)");
				$stmt->bind_param('ssss',
					$adminId,
					$templateName,
					$templateDesc,
					$newDocumentName
				);

				if (move_uploaded_file($_FILES['file']['tmp_name'], $movePath)) {
					$stmt->execute();
					$msgBox = alertBox($fileUploadedMsg, "<i class='fa fa-check-square-o'></i>", "success");
					$stmt->close();
				} else {
					$msgBox = alertBox($fileUploadErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
				}
			}
		}
	}

	// Get Template Data
    $query  = "SELECT
                    sitetemplates.templateId,
                    sitetemplates.adminId,
                    sitetemplates.templateName,
					sitetemplates.templateDesc,
					sitetemplates.templateUrl,
                    DATE_FORMAT(sitetemplates.templateDate,'%M %d, %Y') AS templateDate,
					admins.adminFirstName,
					admins.adminLastName
                FROM
                    sitetemplates
					LEFT JOIN admins ON sitetemplates.adminId = admins.adminId
                ORDER BY sitetemplates.templateId";
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving Template Data failed. ' . mysqli_error());
?>
<h3 class="primary"><?php echo $templatesFormsH3; ?></h3>
<div class="row">
	<div class="col-md-8">
		<p class="lead"><?php echo $templatesFormsQuip; ?></p>
	</div>
	<div class="col-md-4">
		<a data-toggle="modal" href="#newUpload" class="btn btn-primary btn-icon floatRight"><i class="fa fa-upload"></i> <?php echo $uploadNewTemplateBtn; ?></a>
	</div>
</div>

<?php if ($msgBox) { echo $msgBox; } ?>

<?php if(mysqli_num_rows($res) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noTemplatesFoundMsg; ?>
	</div>
<?php } else { ?>
<table id="responsiveTableTwo" class="large-only" cellspacing="0">
	<tbody>
		<tr>
			<th>Template Name</th>
			<th>Uploaded By</th>
			<th>Date Uploaded</th>
			<th>Description</th>
			<?php if ($superuser == '1') { ?>
				<th></th>
			<?php } ?>
		</tr>
		<?php while ($row = mysqli_fetch_assoc($res)) { ?>
			<tr>
				<td><a href="index.php?action=viewTemplate&templateId=<?php echo $row['templateId']; ?>"><?php echo clean($row['templateName']); ?></a></td>
				<td><?php echo clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?></td>
				<td><?php echo $row['templateDate']; ?></td>
				<td><?php echo clean(ellipsis($row['templateDesc'])); ?></td>
				<?php if ($superuser == '1') { ?>
					<td class="tool-tip" title="Delete Template">
						<a data-toggle="modal" href="#deleteTemplate<?php echo $row['templateId']; ?>" class="btn btn-xs btn-link"><i class="fa fa-times"></i></a>
					</td>
				<?php } ?>
			</tr>

			<!-- DELETE TEMPLATE CONFIRM MODAL -->
			<div class="modal fade" id="deleteTemplate<?php echo $row['templateId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<form action="" method="post">
							<div class="modal-body">
								<p class="lead"><?php echo $deleteTemplateConf; ?>
								</p>
							</div>
							<div class="modal-footer">
								<input name="deleteId" type="hidden" value="<?php echo $row['templateId']; ?>" />
								<button type="input" name="submit" value="deleteTemplate" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
								<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
							</div>
						</form>
					</div>
				</div>
			</div>
		<?php } ?>
	</tbody>
</table>
<?php } ?>

<hr />

<h3 class="primary"><?php echo $premadeFormsH3; ?></h3>
<p class="lead"><?php echo $premadeFormsQuip; ?></p>
<p><?php echo $premadeFormsInst; ?></p>

<table id="responsiveTable" class="large-only" cellspacing="0">
	<tbody>
		<tr>
			<th><?php echo $tab_formName; ?></th>
			<th><?php echo $tab_formDescription; ?></th>
		</tr>
		<tr>
			<td><a href="/admin/templates/rentalApplication.pdf" target="_blank"><?php echo $applicationFormTitle; ?></a></td>
			<td><?php echo $applicationFormDesc; ?></td>
		</tr>
		<tr>
			<td><a href="/admin/templates/rentIncrease.pdf" target="_blank"><?php echo $rentIncreaseTitle; ?></a></td>
			<td><?php echo $rentIncreaseDesc; ?></td>
		</tr>
		<tr>
			<td><a href="/admin/templates/moveOut.pdf" target="_blank"><?php echo $moveOutReminderTitle; ?></a></td>
			<td><?php echo $moveOutReminderDesc; ?></td>
		</tr>
		<tr>
			<td><a href="/admin/templates/petAgreement.pdf" target="_blank"><?php echo $petAgreementTitle; ?></a></td>
			<td><?php echo $petAgreementDesc; ?></td>
		</tr>
		<tr>
			<td><a href="/admin/templates/newTenantInfo.pdf" target="_blank"><?php echo $importantInfoTitle; ?></a></td>
			<td><?php echo $importantInfoDesc; ?></td>
		</tr>
		<tr>
			<td><a href="/admin/templates/returnedCheck.pdf" target="_blank"><?php echo $returnedCheckTitle; ?></a></td>
			<td><?php echo $returnedCheckDesc; ?></td>
		</tr>
		<tr>
			<td><a href="/admin/templates/vacateRenew.pdf" target="_blank"><?php echo $vacateOrRenewTitle; ?></a></td>
			<td><?php echo $vacateOrRenewDesc; ?></td>
		</tr>
	</tbody>
</table>

<!-- -- UPLOAD A NEW TEMPLATE/FORM MODEL -- -->
<div class="modal fade" id="newUpload" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $uploadNewTemplateBtn; ?></h4>
			</div>
			<form enctype="multipart/form-data" action="" method="post">
				<div class="modal-body">
					<p><?php echo $uniqueTemplateNames; ?></p>
					<p>
						<small>
							<?php echo $allowedTemplateTypesQuip.' '.$typesAllowed; ?>
							<?php echo $maxUploadSize.' '.$maxUpload.'MB.'; ?>
						</small>
					</p>

					<div class="form-group">
                        <label for="templateName"><?php echo $templateNameField; ?></label>
                        <input type="text" class="form-control" name="templateName" value="<?php echo isset($_POST['templateName']) ? $_POST['templateName'] : ''; ?>" />
						<span class="help-block"><?php echo $templateNameHelper; ?></span>
                    </div>
					<div class="form-group">
						<label for="templateDesc"><?php echo $tab_formDescription; ?></label>
						<textarea class="form-control" name="templateDesc" rows="2"><?php echo isset($_POST['templateDesc']) ? $_POST['templateDesc'] : ''; ?></textarea>
						<span class="help-block"><?php echo $templateDescriptionHelper.' '.$htmlNotAllowed; ?></span>
					</div>
					<div class="form-group">
						<label for="file"><?php echo $selectPropFileField; ?></label>
						<input type="file" id="file" name="file">
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="uploadTemplate" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $uploadBtn; ?></button>
					<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
        </div>
    </div>
</div>