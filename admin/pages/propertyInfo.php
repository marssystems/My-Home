<?php
	$propertyId = $_GET['propertyId'];
	$allowAccess = '';
	$jsFile = 'propertyInfo';
	$datePicker = 'true';
	$tenantIsLate = '';

	// Get the Current Month
    $currentMonth = date('F');
	// Get the Current Day
    $currentDay = date('d');
	$today = date("M jS, Y");

	// Allow Superusers to access all properties
	if ($superuser == '1') { $allowAccess = 'true'; }
	
	$allowAccess = 'true'; 

    // Get the Max Upload Size allowed
    $maxUpload = (int)(ini_get('upload_max_filesize'));

	// Get Property Pictures Folder from Site Settings
	$propertyPicsPath = $set['propertyPicsPath'];

	// Get the Picture file types allowed from Site Settings
	$propertyPicTypes = $set['propertyPicTypes'];
	// Replace the commas with a comma space
	$pictureTypesAllowed = preg_replace('/,/', ', ', $propertyPicTypes);

	// Get Property Files Folder from Site Settings
	$uploadPath = $set['uploadPath'];

	// Get the file types allowed from Site Settings
	$fileTypesAllowed = $set['fileTypesAllowed'];
	// Replace the commas with a comma space
	$uploadTypesAllowed = preg_replace('/,/', ', ', $fileTypesAllowed);

	// Update Property Info
    if (isset($_POST['submit']) && $_POST['submit'] == 'Update Property') {
        // Validation
        if($_POST['propertyName'] == "") {
            $msgBox = alertBox($propertyNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['propertyDesc'] == "") {
            $msgBox = alertBox($propertyDescReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['propertyAddress'] == "") {
            $msgBox = alertBox($propertyAddressReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['propertyRate'] == "") {
            $msgBox = alertBox($monthlyRateReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['latePenalty'] == "") {
            $msgBox = alertBox($latePeneltyReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['propertyDeposit'] == "") {
            $msgBox = alertBox($depositAmountReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Set some variables
			$propertyName = $mysqli->real_escape_string($_POST['propertyName']);
			$propertyDesc = htmlentities($_POST['propertyDesc']);
			$propertyAddress = htmlentities($_POST['propertyAddress']);
			$propertyRate = $mysqli->real_escape_string($_POST['propertyRate']);
			$latePenalty = $mysqli->real_escape_string($_POST['latePenalty']);
			$propertyDeposit = $mysqli->real_escape_string($_POST['propertyDeposit']);
			$propertyNotes = htmlentities($_POST['propertyNotes']);

			$stmt = $mysqli->prepare("
								UPDATE
									properties
								SET
									propertyName = ?,
									propertyDesc = ?,
									propertyAddress = ?,
									propertyRate = ?,
									latePenalty = ?,
									propertyDeposit = ?,
									propertyNotes = ?
								WHERE
									propertyId = ?");
			$stmt->bind_param('ssssssss',
								$propertyName,
								$propertyDesc,
								$propertyAddress,
								$propertyRate,
								$latePenalty,
								$propertyDeposit,
								$propertyNotes,
								$propertyId
			);
			$stmt->execute();
			$msgBox = alertBox($propertyInfoUpdatedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			$stmt->close();
		}
	}

	// Upload Property Picture
	if (isset($_POST['submit']) && $_POST['submit'] == 'Upload Picture') {
		// Get the File Types allowed
		$fileExt = $set['propertyPicTypes'];
		$allowed = preg_replace('/,/', ', ', $fileExt); // Replce the commas with a comma space (, )
		$ftypes = array($fileExt);
		$ftypes_data = explode( ',', $fileExt );

		// Check file type
		$ext = substr(strrchr(basename($_FILES['file']['name']), '.'), 1);
		if (!in_array($ext, $ftypes_data)) {
			$msgBox = alertBox($pictureNotAcceptedMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			// Get the Properties Picture Folder
			$propertyFolder = $mysqli->real_escape_string($_POST['propertyFolder']);

			// Rename the Picture
			$pictureName = htmlentities($_POST['pictureName']);

			// Replace any spaces with an underscore
			// And set to all lower-case
			$newName = str_replace(' ', '_', $pictureName);
			$fileName = strtolower($newName);
			$fullName = $fileName;

			// set the upload path
			$pictureUrl = basename($_FILES['file']['name']);

			// Get the files original Ext
			$extension = pathinfo($pictureUrl, PATHINFO_EXTENSION);

			// Set the files name to the name set in the form
			// And add the original Ext
			$newPictureName = $fullName.'.'.$extension;
			$movePath = '../'.$propertyPicsPath.$propertyFolder.'/'.$newPictureName;

			$stmt = $mysqli->prepare("
								INSERT INTO
									propertypictures(
										propertyId,
										adminId,
										pictureName,
										pictureUrl
									) VALUES (
										?,
										?,
										?,
										?
									)");
			$stmt->bind_param('ssss',
				$propertyId,
				$adminId,
				$pictureName,
				$newPictureName
			);

			if (move_uploaded_file($_FILES['file']['tmp_name'], $movePath)) {
				$stmt->execute();
				$msgBox = alertBox($pictureUploadedMsg, "<i class='fa fa-check-square-o'></i>", "success");
				$stmt->close();
			} else {
				$msgBox = alertBox($pictureUploadErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
			}
		}
	}

	// Delete a Property Picture
    if (isset($_POST['submit']) && $_POST['submit'] == 'Delete Picture') {
		$pictureId = $mysqli->real_escape_string($_POST['pictureId']);

		// Get the picture url
		$sql = "SELECT pictureUrl FROM propertypictures WHERE pictureId = ".$pictureId;
		$result = mysqli_query($mysqli, $sql) or die(mysqli_error());
		$r = mysqli_fetch_assoc($result);
		$pictureUrl = $r['pictureUrl'];

		// Get the Properties Picture Folder
		$propertyFolder = $mysqli->real_escape_string($_POST['propertyFolder']);
		$filePath = '../'.$propertyPicsPath.$propertyFolder.'/'.$pictureUrl;

		// Delete the picture from the server
		if (file_exists($filePath)) {
			unlink($filePath);

			// Delete the record
			$stmt = $mysqli->prepare("DELETE FROM propertypictures WHERE pictureId = ?");
			$stmt->bind_param('s', $pictureId);
			$stmt->execute();

			$msgBox = alertBox($pictureRemovedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($pictureRemoveErrorMsg, "<i class='fa fa-minus-square-o'></i>", "warning");
		}
	}

	// Update Property Amenities
    if (isset($_POST['submit']) && $_POST['submit'] == 'Update Amenities') {
		// Set some variables
		$propertyType = $mysqli->real_escape_string($_POST['propertyType']);
		$propertyStyle = $mysqli->real_escape_string($_POST['propertyStyle']);
		$yearBuilt = $mysqli->real_escape_string($_POST['yearBuilt']);
		$propertySize = $mysqli->real_escape_string($_POST['propertySize']);
		$parking = $mysqli->real_escape_string($_POST['parking']);
		$heating = $mysqli->real_escape_string($_POST['heating']);
		$bedrooms = $mysqli->real_escape_string($_POST['bedrooms']);
		$bathrooms = $mysqli->real_escape_string($_POST['bathrooms']);
		$propertyAmenities = htmlentities($_POST['propertyAmenities']);

		$stmt = $mysqli->prepare("
							UPDATE
								properties
							SET
								propertyType = ?,
								propertyStyle = ?,
								yearBuilt = ?,
								propertySize = ?,
								parking = ?,
								heating = ?,
								bedrooms = ?,
								bathrooms = ?,
								propertyAmenities = ?
							WHERE
								propertyId = ?"
		);
		$stmt->bind_param('ssssssssss',
							$propertyType,
							$propertyStyle,
							$yearBuilt,
							$propertySize,
							$parking,
							$heating,
							$bedrooms,
							$bathrooms,
							$propertyAmenities,
							$propertyId
		);
		$stmt->execute();
		$msgBox = alertBox($propAmenitiesUpdatedMsg, "<i class='fa fa-check-square-o'></i>", "success");
		$stmt->close();
	}

	// Update Property HOA Info
    if (isset($_POST['submit']) && $_POST['submit'] == 'Update HOA') {
		// Set some variables
		$propertyHoa = $mysqli->real_escape_string($_POST['propertyHoa']);
		$hoaAddress = htmlentities($_POST['hoaAddress']);
		$hoaPhone = $mysqli->real_escape_string($_POST['hoaPhone']);
		$hoaFeeAmount = $mysqli->real_escape_string($_POST['hoaFeeAmount']);
		$hoaFeeSchedule = $mysqli->real_escape_string($_POST['hoaFeeSchedule']);

		$stmt = $mysqli->prepare("
							UPDATE
								properties
							SET
								propertyHoa = ?,
								hoaAddress = ?,
								hoaPhone = ?,
								hoaFeeAmount = ?,
								hoaFeeSchedule = ?
							WHERE
								propertyId = ?"
		);
		$stmt->bind_param('ssssss',
							$propertyHoa,
							$hoaAddress,
							$hoaPhone,
							$hoaFeeAmount,
							$hoaFeeSchedule,
							$propertyId
		);
		$stmt->execute();
		$msgBox = alertBox($propHoaUpdatedMsg, "<i class='fa fa-check-square-o'></i>", "success");
		$stmt->close();
	}

	// Update Property Listing Text
    if (isset($_POST['submit']) && $_POST['submit'] == 'Update Listing') {
		// Set some variables
		$propertyListing = htmlentities($_POST['propertyListing']);

		$stmt = $mysqli->prepare("
							UPDATE
								properties
							SET
								propertyListing = ?
							WHERE
								propertyId = ?"
		);
		$stmt->bind_param('ss',
							$propertyListing,
							$propertyId
		);
		$stmt->execute();
		$msgBox = alertBox($propListingUpdatedMsg, "<i class='fa fa-check-square-o'></i>", "success");
		$stmt->close();
	}

	// Assign Property to an Admin/Landlord
    if (isset($_POST['submit']) && $_POST['submit'] == 'Assign Property') {
		if($_POST['isManager'] != '0') {
			// There is allready an Admin/Landlord Assigned, so update the record
			if($_POST['adminId'] == "") {
				$msgBox = alertBox($selectAdminReq, "<i class='fa fa-times-circle'></i>", "danger");
			} else {
				// Set some variables
				$theAdmin = $mysqli->real_escape_string($_POST['adminId']);

				$stmt = $mysqli->prepare("
									UPDATE
										assignedproperties
									SET
										adminId = ?
									WHERE
										propertyId = ?"
				);
				$stmt->bind_param('ss',
									$theAdmin,
									$propertyId
				);
				$stmt->execute();
				$msgBox = alertBox($propAssignedMsg, "<i class='fa fa-check-square-o'></i>", "success");
				$stmt->close();
			}
		} else {
			// There is NOT an Admin/Landlord Assigned, so create a new record
			if($_POST['adminId'] == "") {
				$msgBox = alertBox($selectAdminReq, "<i class='fa fa-times-circle'></i>", "danger");
			} else {
				// Set some variables
				$theAdmin = $mysqli->real_escape_string($_POST['adminId']);

				$stmt = $mysqli->prepare("
									INSERT INTO
										assignedproperties(
											propertyId,
											adminId
										) VALUES (
											?,
											?
										)");
				$stmt->bind_param('ss',
					$propertyId,
					$theAdmin
				);
				$stmt->execute();
				$msgBox = alertBox($propAssignedMsg, "<i class='fa fa-check-square-o'></i>", "success");
				$stmt->close();
			}
		}
	}

	// Record a Property Payment
	if (isset($_POST['submit']) && $_POST['submit'] == 'Record Payment') {
		if($_POST['paymentDate'] == "") {
			$msgBox = alertBox($paymentDateReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['paymentAmount'] == "") {
			$msgBox = alertBox($paymentAmountReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['paymentFor'] == "") {
			$msgBox = alertBox($paymentForReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['paymentType'] == "") {
			$msgBox = alertBox($paymentTypeReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			// Check if this is a Rental Payment
			if ($_POST['rentMonth'] == '') { $isRent = '0'; } else { $isRent = '1'; }
			// Set some variables
			$tenantId = $mysqli->real_escape_string($_POST['tenantId']);
			$leaseId = $mysqli->real_escape_string($_POST['leaseId']);
			$paymentDate = $mysqli->real_escape_string($_POST['paymentDate']);
			$paymentAmount = $mysqli->real_escape_string($_POST['paymentAmount']);
			$paymentPenalty = $mysqli->real_escape_string($_POST['paymentPenalty']);
			$paymentFor = $mysqli->real_escape_string($_POST['paymentFor']);
			$paymentType = $mysqli->real_escape_string($_POST['paymentType']);
			$rentMonth = $mysqli->real_escape_string($_POST['rentMonth']);
			$paymentNotes = htmlentities($_POST['paymentNotes']);

			$stmt = $mysqli->prepare("
								INSERT INTO
									payments(
										adminId,
										tenantId,
										leaseId,
										paymentDate,
										paymentAmount,
										paymentPenalty,
										paymentFor,
										paymentType,
										isRent,
										rentMonth,
										paymentNotes
									) VALUES (
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?
									)");
			$stmt->bind_param('sssssssssss',
				$adminId,
				$tenantId,
				$leaseId,
				$paymentDate,
				$paymentAmount,
				$paymentPenalty,
				$paymentFor,
				$paymentType,
				$isRent,
				$rentMonth,
				$paymentNotes
			);
			$stmt->execute();
			$msgBox = alertBox($paymentSavedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			// Clear the form of Values
			$_POST['paymentDate'] = $_POST['paymentAmount'] = $_POST['paymentPenalty'] = $_POST['paymentFor'] = $_POST['paymentType'] = $_POST['paymentNotes'] = '';
			$stmt->close();
		}
	}

	// Update Resident
	if (isset($_POST['submit']) && $_POST['submit'] == 'Edit Resident') {
		if($_POST['residentName'] == "") {
			$msgBox = alertBox($residentNameReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['relation'] == "") {
			$msgBox = alertBox($residentRelationReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			// Set some variables
			$residentId = $mysqli->real_escape_string($_POST['residentId']);
			$residentName = $mysqli->real_escape_string($_POST['residentName']);
			$residentPhone = $mysqli->real_escape_string($_POST['residentPhone']);
			$residentEmail = $mysqli->real_escape_string($_POST['residentEmail']);
			$relation = $mysqli->real_escape_string($_POST['relation']);
			$residentNotes = htmlentities($_POST['residentNotes']);
			$isArchived = $mysqli->real_escape_string($_POST['isArchived']);
			if ($isArchived == '1') { $archivedDate = date("Y-m-d"); } else { $archivedDate = ''; }

			$stmt = $mysqli->prepare("
								UPDATE
									residents
								SET
									residentName = ?,
									residentPhone = ?,
									residentEmail = ?,
									relation = ?,
									residentNotes = ?,
									isArchived = ?,
									archivedDate = ?
								WHERE
									residentId = ?"
			);
			$stmt->bind_param('ssssssss',
									$residentName,
									$residentPhone,
									$residentEmail,
									$relation,
									$residentNotes,
									$isArchived,
									$archivedDate,
									$residentId
			);
			$stmt->execute();
			$msgBox = alertBox($residentUpdatedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			$stmt->close();
		}
	}

	// Add a New Resident
	if (isset($_POST['submit']) && $_POST['submit'] == 'Add Resident') {
		if($_POST['residentName'] == "") {
			$msgBox = alertBox($residentNameReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['relation'] == "") {
			$msgBox = alertBox($residentRelationReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			// Set some variables
			$tenantId = $mysqli->real_escape_string($_POST['tenantId']);
			$residentName = $mysqli->real_escape_string($_POST['residentName']);
			$residentPhone = $mysqli->real_escape_string($_POST['residentPhone']);
			$residentEmail = $mysqli->real_escape_string($_POST['residentEmail']);
			$relation = $mysqli->real_escape_string($_POST['relation']);
			$residentNotes = htmlentities($_POST['residentNotes']);

			$stmt = $mysqli->prepare("
								INSERT INTO
									residents(
										tenantId,
										residentName,
										residentPhone,
										residentEmail,
										relation,
										residentNotes
									) VALUES (
										?,
										?,
										?,
										?,
										?,
										?
									)");
			$stmt->bind_param('ssssss',
				$tenantId,
				$residentName,
				$residentPhone,
				$residentEmail,
				$relation,
				$residentNotes
			);
			$stmt->execute();
			$msgBox = alertBox($newResidentAddedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			// Clear the form of Values
			$_POST['residentName'] = $_POST['residentPhone'] = $_POST['residentEmail'] = $_POST['relation'] = $_POST['residentNotes'] = '';
			$stmt->close();
		}
	}

	// Upload Property File
	if (isset($_POST['submit']) && $_POST['submit'] == 'Upload File') {
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
			// Get the Properties Document Folder
			$propertyFolder = $mysqli->real_escape_string($_POST['propertyFolder']);

			// Rename the Document
			$documentName = htmlentities($_POST['fileName']);

			// Replace any spaces with an underscore
			// And set to all lowercase
			$newName = str_replace(' ', '_', $documentName);
			$fileName = strtolower($newName);
			$fullName = $fileName;

			// set the upload path
			$documentUrl = basename($_FILES['file']['name']);

			// Get the files original Ext
			$extension = pathinfo($documentUrl, PATHINFO_EXTENSION);

			// Set the files name to the name set in the form
			// And add the original Ext
			$newDocumentName = $fullName.'.'.$extension;
			$movePath = '../'.$uploadPath.$propertyFolder.'/'.$newDocumentName;

			$fileDesc = htmlentities($_POST['fileDesc']);

			$stmt = $mysqli->prepare("
								INSERT INTO
									propertyfiles(
										propertyId,
										adminId,
										fileName,
										fileDesc,
										fileUrl
									) VALUES (
										?,
										?,
										?,
										?,
										?
									)");
			$stmt->bind_param('sssss',
				$propertyId,
				$adminId,
				$documentName,
				$fileDesc,
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

	// Get Property Info
	$query = "
		SELECT
            propertyName,
			propertyDesc,
			propertyAddress,
			isLeased,
			propertyRate,
			latePenalty,
			propertyDeposit,
            CASE petsAllowed
                WHEN 0 THEN 'No'
                WHEN 1 THEN 'Yes'
            END AS petsAllowed,
			propertyNotes,
			propertyFolder,
			propertyAmenities,
			propertyListing,
			propertyType,
			propertyStyle,
			yearBuilt,
			propertySize,
			parking,
			heating,
			bedrooms,
			bathrooms,
			propertyHoa,
			hoaAddress,
			hoaPhone,
			hoaFeeAmount,
			hoaFeeSchedule,
			isArchived,
			DATE_FORMAT(dateArchived,'%M %d, %Y') AS dateArchived
		FROM
			properties
		WHERE
            propertyId = ".$propertyId;
	$res = mysqli_query($mysqli, $query) or die('Error, retrieving Property Data failed. ' . mysqli_error());
    $row = mysqli_fetch_assoc($res);

	// Format the Amounts
	$propertyRate = $currencySym.format_amount($row['propertyRate'], 2);
	$latePenalty = $currencySym.format_amount($row['latePenalty'], 2);
	$propertyDeposit = $currencySym.format_amount($row['propertyDeposit'], 2);
	$lateAmount = $row['propertyRate'] + $row['latePenalty'];
	$ifLate = $currencySym.format_amount($lateAmount, 2);
	if ($row['hoaFeeAmount'] != '') {
		$hoaFeeAmount = $currencySym.format_amount($row['hoaFeeAmount'], 2);
	} else {
		$hoaFeeAmount = '';
	}

	// Get Lease Data
	if ($row['isLeased'] == '1') {
		$lease = "
			SELECT
				leases.leaseId,
				leases.adminId,
				leases.propertyId,
				leases.leaseTerm,
				DATE_FORMAT(leases.leaseStart,'%d/%M/%Y') AS leaseStart,
				DATE_FORMAT(leases.leaseEnd,'%d/%M/%Y') AS leaseEnd,
				leases.leaseNotes,
				leases.isClosed,
				tenants.tenantId,
				tenants.tenantFirstName,
				tenants.tenantLastName
			FROM
				leases
				LEFT JOIN tenants ON leases.propertyId = tenants.propertyId
			WHERE
				leases.isClosed != 1 AND
				leases.propertyId = ".$propertyId;
		$leaseres = mysqli_query($mysqli, $lease) or die('Error, retrieving Property Lease Data failed. ' . mysqli_error());
		$rows = mysqli_fetch_assoc($leaseres);

		// Check if the Tenant is late on current month's rent
		$todayDate = date("Y-m-d");
		$latecheck1 = "SELECT
						tenants.leaseId,
						leases.leaseStart
					FROM
						tenants
						LEFT JOIN leases ON tenants.leaseId = leases.leaseId
					WHERE
						tenants.leaseId = ".$rows['leaseId']." AND
						'".$todayDate."' >= leases.leaseStart";
		$lateres1 = mysqli_query($mysqli, $latecheck1) or die('Error, retrieving Late Rent Data 1 failed. ' . mysqli_error());

		if (mysqli_num_rows($lateres1) > 0) {
			$latecheck2 = "SELECT
							payments.isRent,
							payments.rentMonth,
							tenants.leaseId,
							tenants.propertyId
						FROM
							payments
							LEFT JOIN tenants ON payments.tenantId = tenants.tenantId
						WHERE
							tenants.leaseId = ".$rows['leaseId']." AND
							payments.rentMonth = '".$currentMonth."'";
			$lateres = mysqli_query($mysqli, $latecheck2) or die('Error, retrieving Late Rent Data failed. ' . mysqli_error());
			if (mysqli_num_rows($lateres) > 0) { $tenantIsLate = 'false'; } else { $tenantIsLate = 'true'; }
		} else {
			$tenantIsLate = 'false';
		}

		// Get other Residents Data
		$resident  = "SELECT
						residentId,
						tenantId,
						residentName,
						residentPhone,
						residentEmail,
						relation,
						residentNotes,
						isArchived
					FROM
						residents
					WHERE
						tenantId = ".$rows['tenantId'];
		$residentres = mysqli_query($mysqli, $resident) or die('Error, retrieving Resident Data failed. ' . mysqli_error());

		// Get the Landlord Assigned to this Property
		$landlord = "
			SELECT
				assignedproperties.propertyId,
				assignedproperties.adminId,
				admins.adminFirstName,
				admins.adminLastName
			FROM
				assignedproperties
				LEFT JOIN admins ON assignedproperties.adminId = admins.adminId
			WHERE
				assignedproperties.propertyId = ".$propertyId;
		$landlordres = mysqli_query($mysqli, $landlord) or die('Error, retrieving Admin Access Data failed. ' . mysqli_error());
		$cols = mysqli_fetch_assoc($landlordres);

		// Only allow the Assigned admin/landlord access
		if ($cols['adminId'] == $adminId) { $allowAccess = 'true'; }
	} else {
		// The property is Unleased, allow all admins/landlords to access it
		$allowAccess = 'true';
	}

	// Check if there is an Admin/Landlord Assigned to the Property
	$managed = "SELECT 'X' FROM assignedproperties WHERE propertyId = ".$propertyId;
    $isManaged = mysqli_query($mysqli, $managed) or die(mysqli_error());
	$isManager = mysqli_num_rows($isManaged);

    // Get Property Pictures Data
    $pics  = "SELECT
                pictureId,
                propertyId,
                adminId,
				pictureName,
                pictureUrl,
				DATE_FORMAT(pictureDate,'%M %d, %Y') AS pictureDate
            FROM
                propertypictures
            WHERE
                propertyId = ".$propertyId."
			ORDER BY pictureId";
    $picsres = mysqli_query($mysqli, $pics) or die('Error, retrieving Property Piuctures failed. ' . mysqli_error());

    // Get Property Documents Data
    $docs  = "SELECT
                fileId,
                propertyId,
                adminId,
				fileName,
                fileDesc,
				DATE_FORMAT(fileDate,'%M %d, %Y') AS fileDate
            FROM
                propertyfiles
            WHERE
                propertyId = ".$propertyId."
            ORDER BY fileId DESC";
    $docsres = mysqli_query($mysqli, $docs) or die('Error, retrieving Property Documents failed. ' . mysqli_error());

	// Check Access Allowed
	if ($allowAccess == '') {
?>
<h3 class="danger"><?php echo $accessErrorH3; ?></h3>
<div class="alertMsg danger">
	<i class="fa fa-minus-square-o"></i> <?php echo $permissionDenied; ?>
</div>
<?php } else { ?>
	<div class="row">
		<div class="col-md-8">
			<h3 class="info"><?php echo $propertyDetailsH3; ?></h3>

			<?php if ($msgBox) { echo $msgBox; } ?>

			<p class="lead">
				<?php echo clean($row['propertyName']); ?><br />
				<?php echo clean($row['propertyAddress']); ?>
			</p>
			<p><?php echo clean($row['propertyDesc']); ?></p>

			<div class="row padTop">
				<div class="col-md-6">
					<ul class="list-group">
						<li class="list-group-item"><strong><?php echo $rentalMonthyRateLi; ?>:</strong> <?php echo $propertyRate; ?></li>
						<li class="list-group-item"><strong>Recargo Total:</strong> <?php echo $ifLate; ?></li>
					</ul>
				</div>
				<div class="col-md-6">
					<ul class="list-group">
						<li class="list-group-item"><strong><?php echo $rentalDepositAmtLi; ?>:</strong> <?php echo $propertyDeposit; ?></li>
						<li class="list-group-item"><strong><?php echo $rentalLateFeeLi; ?>:</strong> <?php echo $latePenalty; ?></li>
					</ul>
				</div>
			</div>

			<ul class="list-group">
				<li class="list-group-item"><strong><?php echo $propertyNoteLi; ?></strong> <?php echo clean($row['propertyNotes']); ?></li>
			</ul>

			<a data-toggle="modal" href="#editProperty" class="btn btn-default btn-sm btn-icon"><i class="fa fa-edit"></i> <?php echo $updatePropertyBtn; ?></a>

			<hr />

			<h3 class="info"><?php echo $propertyPicturesH3; ?></h3>
			<p class="clearfix"><?php echo $propertyPicturesQuip; ?></p>
			<?php if(mysqli_num_rows($picsres) > 0) { ?>
				<div class="gallery">
				<?php while ($pic = mysqli_fetch_assoc($picsres)) { ?>
					<a data-toggle="modal" href="#viewPicture<?php echo $pic['pictureId']; ?>">
						<img src="../<?php echo $propertyPicsPath.$row['propertyFolder'].'/'.$pic['pictureUrl']; ?>" />
					</a>
					<!-- -- VIEW PROPERTY PICTURE MODEL -- -->
					<div class="modal fade" id="viewPicture<?php echo $pic['pictureId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header picture">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
									<?php echo clean($pic['pictureName']); ?>
								</div>
								<div class="modal-body">
									<img src="../<?php echo $propertyPicsPath.$row['propertyFolder'].'/'.$pic['pictureUrl']; ?>" />
								</div>
								<div class="modal-footer">
									<a data-toggle="modal" href="#deletePicture<?php echo $pic['pictureId']; ?>" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-times"></i> <?php echo $deletePictureBtn; ?></a>
									<button type="button" class="btn btn-default btn-sm btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $closeBtn; ?></button>
								</div>
							</div>
						</div>
					</div>
					<!-- -- DELETE PICTURE CONFIRMATION MODEL -- -->
					<div class="modal fade" id="deletePicture<?php echo $pic['pictureId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<form action="" method="post">
									<div class="modal-body">
										<p class="lead"><?php echo $deletePictureConf; ?></p>
									</div>
									<div class="modal-footer">
										<input type="hidden" name="pictureId" value="<?php echo $pic['pictureId']; ?>">
										<input type="hidden" name="propertyFolder" value="<?php echo clean($row['propertyFolder']); ?>">
										<button type="input" name="submit" value="Delete Picture" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
										<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
									</div>
								</form>
							</div>
						</div>
					</div>
				<?php } ?>
				</div>
			<?php } else { ?>
				<p class="lead"><?php echo $noPicsUploaded; ?></p>
			<?php } ?>

			<div class="clearfix"></div>
			<p class="padTop"><a data-toggle="modal" href="#uploadPictures" class="btn btn-default btn-sm btn-icon"><i class="fa fa-upload"></i> <?php echo $uploadPicturesBtn; ?></a></p>

			<div class="clearfix"></div>
			<hr />

			<div class="panel-group" id="accordion">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#propertyAmenitiesAcc"><i class="fa fa-angle-right"></i> <?php echo $propertyAmenitiesH3; ?></a>
						</h4>
					</div>
					<div id="propertyAmenitiesAcc" class="panel-collapse collapse in">
						<div class="panel-body">
							<div class="row">
								<div class="col-md-6">
									<ul class="list-group">
										<li class="list-group-item"><strong><?php echo $propertyTypeLi; ?></strong> <?php echo clean($row['propertyType']); ?></li>
										<li class="list-group-item"><strong><?php echo $propertyStyleLi; ?></strong> <?php echo clean($row['propertyStyle']); ?></li>
										<li class="list-group-item"><strong><?php echo $yearBuiltLi; ?></strong> <?php echo clean($row['yearBuilt']); ?></li>
										<li class="list-group-item"><strong><?php echo $propertySizeLi; ?></strong> <?php echo clean($row['propertySize']); ?></li>
										<li class="list-group-item"><strong><?php echo $numberBedroomsLi; ?></strong> <?php echo clean($row['bedrooms']); ?></li>
										<li class="list-group-item"><strong><?php echo $numberBathroomsLi; ?></strong> <?php echo clean($row['bathrooms']); ?></li>
										<li class="list-group-item"><strong><?php echo $parkingLi; ?></strong> <?php echo clean($row['parking']); ?></li>
										<li class="list-group-item"><strong><?php echo $heatingLi; ?></strong> <?php echo clean($row['heating']); ?></li>
										<li class="list-group-item"><strong><?php echo $allowPetsLi; ?></strong> <?php echo $row['petsAllowed']; ?></li>
									</ul>

									<a data-toggle="modal" href="#editAmenities" class="btn btn-default btn-sm btn-icon"><i class="fa fa-edit"></i> <?php echo $updateAmenitiesBtn; ?></a>
								</div>
								<div class="col-md-6">
									<ul class="list-group">
										<li class="list-group-item"><strong><?php echo $propAmenitiesLi; ?></strong> <?php echo nl2br(clean($row['propertyAmenities'])); ?></li>
									</ul>

									<ul class="list-group">
										<li class="list-group-item"><strong><?php echo $hoaLi; ?></strong> <?php echo clean($row['propertyHoa']); ?></li>
										<li class="list-group-item"><strong><?php echo $hoaPhoneLi; ?></strong> <?php echo clean($row['hoaPhone']); ?></li>
										<li class="list-group-item"><strong><?php echo $hoaAddressLi; ?></strong> <?php echo nl2br(clean($row['hoaAddress'])); ?></li>
										<li class="list-group-item"><strong><?php echo $hoaFeeLi; ?></strong> <?php echo $hoaFeeAmount; ?></li>
										<li class="list-group-item"><strong><?php echo $hoaFeeScheduleLi; ?></strong> <?php echo clean($row['hoaFeeSchedule']); ?></li>
									</ul>

									<a data-toggle="modal" href="#editHoa" class="btn btn-default btn-sm btn-icon"><i class="fa fa-edit"></i> <?php echo $updatePropHoaInfoBtn; ?></a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#propertyListingAcc"><i class="fa fa-angle-right"></i> <?php echo $propertyListingH3; ?></a>
						</h4>
					</div>
					<div id="propertyListingAcc" class="panel-collapse collapse">
						<div class="panel-body">
							<p><?php echo nl2br(clean($row['propertyListing'])); ?></p>

							<a data-toggle="modal" href="#editListingText" class="btn btn-default btn-sm btn-icon padTop"><i class="fa fa-edit"></i> <?php echo $updateListingTextBtn; ?></a>
						</div>
					</div>
				</div>
			</div>

		</div>
		<div class="col-md-4">
			<?php if ($row['isArchived'] == '0') { ?>
				<h4 class="info"><?php echo $propertyLeaseH4; ?></h4>
				<?php
					if ($row['isLeased'] == '1') {
						if ($tenantIsLate == 'true') {
							if ($currentDay > '5') {
				?>
								<div class="alertMsg warning">
									<i class="fa fa-warning"></i> <?php echo $rentIsPastDueMsg; ?>
								</div>
				<?php
							}
						}
					}
				?>
				<?php if ($row['isLeased'] == '0') { ?>
					<div class="alertMsg default">
						<i class="fa fa-minus-square-o"></i> <?php echo $noLeaseMsg; ?>
					</div>
					<a href="index.php?action=leaseProperty&propertyId=<?php echo $propertyId; ?>" class="btn btn-primary btn-icon"><i class="fa fa-file"></i> <?php echo $leasePropertyBtn; ?></a>
				<?php } else { ?>
					<ul class="list-group">
						<li class="list-group-item">
							<strong><?php echo $currentTenant; ?></strong>
							<a href="index.php?action=tenantInfo&tenantId=<?php echo $rows['tenantId']; ?>"><?php echo clean($rows['tenantFirstName']).' '.clean($rows['tenantLastName']); ?></a><br />
							<strong><?php echo $leaseTerm; ?></strong> <?php echo clean($rows['leaseTerm']); ?><br />
							<small>
								<?php echo $rows['leaseStart']; ?> &mdash; <?php echo $rows['leaseEnd']; ?>
								<?php if ($rows['leaseNotes'] != '') { ?>
									<br />
									<strong><?php echo $leaseNotes; ?></strong> <?php echo clean($rows['leaseNotes']); ?>
								<?php } ?>
							</small>

						</li>
						<li class="list-group-item">
							<strong><?php echo $AssignedLandlord; ?></strong>
							<?php echo clean($cols['adminFirstName']).' '.clean($cols['adminLastName']); ?>
							<?php if ($superuser == '1') { ?>
								<a data-toggle="modal" href="#AssignProperty" class="btn btn-info btn-xs btn-icon floatRight"><i class="fa fa-edit"></i> <?php echo $AssignPropertyBtn; ?></a>
							<?php } ?>
						</li>
					</ul>
					<a data-toggle="modal" href="#recordPayment" class="btn btn-default btn-block btn-icon"><i class="fa fa-credit-card"></i> <?php echo $recordPaymentBtn; ?></a>
					<a href="index.php?action=viewLeasePayments&leaseId=<?php echo $rows['leaseId']; ?>" class="btn btn-default btn-block btn-icon"><i class="fa fa-money"></i> <?php echo $viewPaymentsBtn; ?></a>

					<hr />

					<h4 class="info"><?php echo $otherResidentsH4; ?></h4>
					<?php if(mysqli_num_rows($residentres) < 1) { ?>
						<dl class="accordion">
							<dt class="noneFound"><a><i class="fa fa-minus-square-o"></i> <?php echo $noResidentsFound; ?></a></dt>
						</dl>
					<?php } else { ?>
						<dl class="accordion">
							<?php while ($residents = mysqli_fetch_assoc($residentres)) { ?>
								<dt>
									<a><?php echo clean($residents['residentName']); ?>
										<span><?php echo $viewDeatilsLink; ?> <i class="fa fa-long-arrow-right"></i></span>
									</a>
								</dt>
								<dd class="hideIt">
									<p>
									<?php
										if ($residents['residentEmail'] != '') {
											echo clean($residents['residentEmail']);
										}
										if ($residents['residentPhone'] != '') {
											echo '<br />'.$residents['residentPhone'];
										}
									?>
									</p>
									<p class="updatedOn">
										<?php echo $relationToTenant.': '.clean($residents['relation']); ?>
									</p>
									<?php
										if ($residents['residentNotes'] != '') {
											echo '<p>'.clean($residents['residentNotes']).'</p>';
										}
									?>
									<p>
										<a data-toggle="modal" href="#editResident<?php echo $residents['residentId']; ?>" class="btn btn-info btn-sm"><?php echo $updateResidentBtn; ?>  <i class="fa fa-long-arrow-right"></i></a>
									</p>
								</dd>

								<!-- UPDATE RESIDENT MODAL -->
								<div class="modal fade" id="editResident<?php echo $residents['residentId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header modal-info">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
												<h4 class="modal-title"><?php echo $updateResidentBtn; ?></h4>
											</div>
											<form action="" method="post">
												<div class="modal-body">
													<div class="form-group">
														<label for="residentName"><?php echo $residentNameField; ?></label>
														<input type="text" class="form-control" name="residentName" value="<?php echo clean($residents['residentName']); ?>">
													</div>
													<div class="form-group">
														<label for="residentPhone"><?php echo $tab_phone; ?></label>
														<input type="text" class="form-control" name="residentPhone" id="residentPhone" value="<?php echo clean($residents['residentPhone']); ?>">
													</div>
													<div class="form-group">
														<label for="residentEmail"><?php echo $tab_email; ?></label>
														<input type="text" class="form-control" name="residentEmail" value="<?php echo $residents['residentEmail']; ?>">
													</div>
													<div class="form-group">
														<label for="relation"><?php echo $relationToTenant; ?></label>
														<input type="text" class="form-control" name="relation" value="<?php echo clean($residents['relation']); ?>">
													</div>
													<div class="form-group">
														<label for="residentNotes"><?php echo $residentNotesField; ?></label>
														<textarea class="form-control" name="residentNotes" rows="2"><?php echo clean($residents['residentNotes']); ?></textarea>
														<span class="help-block"><?php echo $residentNotesHelper.' '.$htmlNotAllowed; ?></span>
													</div>
													<div class="form-group">
														<label for="isArchived"><?php echo $archiveResidentField; ?></label>
														<select class="form-control" name="isArchived">
															<option value="0"><?php echo $OptionNo; ?></option>
															<option value="1"><?php echo $OptionYes; ?></option>
														</select>
														<span class="help-block"><?php echo $archiveResidentHelper; ?></span>
													</div>
												</div>
												<div class="modal-footer">
													<input type="hidden" name="residentId" value="<?php echo $residents['residentId']; ?>">
													<button type="input" name="submit" value="Edit Resident" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
													<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
												</div>
											</form>
										</div>
									</div>
								</div>
							<?php } ?>
						</dl>
					<?php } ?>
					<div class="clearfix"></div>
					<a data-toggle="modal" href="#newResident" class="btn btn-default btn-block btn-icon padTop"><i class="fa fa-user"></i> <?php echo $newResidentBtn; ?></a>

				<?php } ?>
			<?php } else { ?>
				<h4 class="info"><?php echo $propertyArchivedH4; ?></h4>
				<div class="alertMsg warning">
					<i class="fa fa-minus-square-o"></i> <?php echo $propertyArchivedMsg; ?>
				</div>
			<?php } ?>

			<hr />

			<h4 class="info"><?php echo $propertyFilesH4; ?></h4>
			<p><small><?php echo $propertyFilesQuip; ?></small></p>
			<?php if(mysqli_num_rows($docsres) < 1) { ?>
				<dl class="accordion">
					<dt class="noneFound"><a><i class="fa fa-minus-square-o"></i> <?php echo $noFilesUploaded; ?></a></dt>
				</dl>
			<?php
				} else {
					echo '<dl class="accordion">';
					while ($files = mysqli_fetch_assoc($docsres)) {
			?>
						<dt>
							<a><?php echo clean($files['fileName']); ?>
								<span><?php echo $viewDeatilsLink; ?> <i class="fa fa-long-arrow-right"></i></span>
							</a>
						</dt>
						<dd class="hideIt">
							<p><?php echo clean(ellipsis($files['fileDesc'])); ?></p>
							<p class="updatedOn">
								<?php echo $dateUploaded.': '.$files['fileDate']; ?>
							</p>
							<p>
								<a href="index.php?action=viewFile&fileId=<?php echo $files['fileId']; ?>" class="btn btn-info btn-sm"><?php echo $viewFileLink; ?>  <i class="fa fa-long-arrow-right"></i></a>
							</p>
						</dd>
			<?php
					}
					echo '</dl>';
				}
			?>
			<?php if ($row['isArchived'] == '0') { ?>
				<div class="clearfix"></div>
				<p class="padTop"><a data-toggle="modal" href="#uploadPropFile" class="btn btn-default btn-block btn-icon"><i class="fa fa-upload"></i> <?php echo $uploadPropFileBtn; ?></a></p>
			<?php } ?>

		</div>
	</div>

	<!-- UPDATE PROPERTY INFO MODAL -->
	<div class="modal fade" id="editProperty" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-info">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $updatePropertyBtn; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="propertyName"><?php echo $propertyNameField; ?></label>
							<input type="text" class="form-control" name="propertyName" value="<?php echo clean($row['propertyName']); ?>">
							<span class="help-block"><?php echo $propertyNameHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="propertyDesc"><?php echo $propertyDescField; ?></label>
							<textarea class="form-control" name="propertyDesc" rows="2"><?php echo clean($row['propertyDesc']); ?></textarea>
							<span class="help-block"><?php echo $propertyDescHelper.' '.$htmlNotAllowed; ?></span>
						</div>
						<div class="form-group">
							<label for="propertyAddress"><?php echo $propertyAddressField; ?></label>
							<textarea class="form-control" name="propertyAddress" rows="2"><?php echo clean($row['propertyAddress']); ?></textarea>
							<span class="help-block"><?php echo $htmlNotAllowed; ?></span>
						</div>
						<div class="form-group">
							<label for="propertyRate"><?php echo $propertyRateField; ?></label>
							<input type="text" class="form-control" name="propertyRate" value="<?php echo $row['propertyRate']; ?>">
							<span class="help-block"><?php echo $numberOnlyHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="latePenalty"><?php echo $latePeneltyField; ?></label>
							<input type="text" class="form-control" name="latePenalty" value="<?php echo $row['latePenalty']; ?>">
							<span class="help-block"><?php echo $latePeneltyHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="propertyDeposit"><?php echo $propertyDepositField; ?></label>
							<input type="text" class="form-control" name="propertyDeposit" value="<?php echo $row['propertyDeposit']; ?>">
							<span class="help-block"><?php echo $numberOnlyHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="propertyNotes"><?php echo $propertyNotesField; ?></label>
							<textarea class="form-control" name="propertyNotes" rows="2"><?php echo clean($row['propertyNotes']); ?></textarea>
							<span class="help-block"><?php echo $htmlNotAllowed; ?></span>
						</div>
					</div>
					<div class="modal-footer">
						<button type="input" name="submit" value="Update Property" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
						<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- -- UPLOAD PROPERTY PICTURES MODEL -- -->
	<div class="modal fade" id="uploadPictures" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-primary">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $uploadPicturesBtn; ?></h4>
				</div>
				<form enctype="multipart/form-data" action="" method="post">
					<div class="modal-body">
						<p>
							<small>
								<?php echo $allowedPictureTypesQuip.' '.$pictureTypesAllowed; ?><br />
								<?php echo $maxUploadSize.' '.$maxUpload.'MB.'; ?>
							</small>
						</p>

						<div class="form-group">
							<label for="pictureName"><?php echo $propPictureTitle; ?></label>
							<input type="text" class="form-control" name="pictureName" value="">
							<span class="help-block"><?php echo $propPictureTitleHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="file"><?php echo $selectPictureField; ?></label>
							<input type="file" name="file">
						</div>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="propertyFolder" id="propertyFolder" value="<?php echo clean($row['propertyFolder']); ?>">
						<button type="input" name="submit" value="Upload Picture" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $uploadBtn; ?></button>
						<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- UPDATE PROPERTY AMENITIES MODAL -->
	<div class="modal fade" id="editAmenities" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-info">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $updateAmenitiesBtn; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="propertyType"><?php echo $propertyTypeField; ?></label>
							<input type="text" class="form-control" name="propertyType" value="<?php echo clean($row['propertyType']); ?>">
							<span class="help-block"><?php echo $propertyTypeHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="propertyStyle"><?php echo $propertyStyleField; ?></label>
							<input type="text" class="form-control" name="propertyStyle" value="<?php echo clean($row['propertyStyle']); ?>">
							<span class="help-block"><?php echo $propertyStylehelper; ?></span>
						</div>
						<div class="form-group">
							<label for="yearBuilt"><?php echo $yearBuiltField; ?></label>
							<input type="text" class="form-control" name="yearBuilt" value="<?php echo $row['yearBuilt']; ?>">
						</div>
						<div class="form-group">
							<label for="propertySize"><?php echo $propertySizeField; ?></label>
							<input type="text" class="form-control" name="propertySize" value="<?php echo $row['propertySize']; ?>">
							<span class="help-block"><?php echo $propertySizeHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="parking"><?php echo $parkingField; ?></label>
							<input type="text" class="form-control" name="parking" value="<?php echo $row['parking']; ?>">
							<span class="help-block"><?php echo $parkingHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="heating"><?php echo $heatingTypeField; ?></label>
							<input type="text" class="form-control" name="heating" value="<?php echo $row['heating']; ?>">
						</div>
						<div class="form-group">
							<label for="bedrooms"><?php echo $numBedroomsField; ?></label>
							<input type="text" class="form-control" name="bedrooms" value="<?php echo $row['bedrooms']; ?>">
						</div>
						<div class="form-group">
							<label for="bathrooms"><?php echo $numBathroomsField; ?></label>
							<input type="text" class="form-control" name="bathrooms" value="<?php echo $row['bathrooms']; ?>">
						</div>
						<div class="form-group">
							<label for="propertyAmenities"><?php echo $propertyAmenitiesText; ?></label>
							<textarea class="form-control" name="propertyAmenities" rows="3"><?php echo clean($row['propertyAmenities']); ?></textarea>
							<span class="help-block"><?php echo $htmlNotAllowed; ?></span>
						</div>
					</div>
					<div class="modal-footer">
						<button type="input" name="submit" value="Update Amenities" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
						<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- UPDATE PROPERTY HOA MODAL -->
	<div class="modal fade" id="editHoa" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-info">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $updatePropHoaInfoBtn; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="propertyHoa"><?php echo $hoaNameField; ?></label>
							<input type="text" class="form-control" name="propertyHoa" value="<?php echo clean($row['propertyHoa']); ?>">
							<span class="help-block"><?php echo $hoaNameHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="hoaPhone"><?php echo $hoaPhoneField; ?></label>
							<input type="text" class="form-control" name="hoaPhone" id="hoaPhone" value="<?php echo clean($row['hoaPhone']); ?>">
						</div>
						<div class="form-group">
							<label for="hoaAddress"><?php echo $hoaAddressField; ?></label>
							<textarea class="form-control" name="hoaAddress" rows="2"><?php echo clean($row['hoaAddress']); ?></textarea>
							<span class="help-block"><?php echo $htmlNotAllowed; ?></span>
						</div>
						<div class="form-group">
							<label for="hoaFeeAmount"><?php echo $hoaFeeField; ?></label>
							<input type="text" class="form-control" name="hoaFeeAmount" value="<?php echo $row['hoaFeeAmount']; ?>">
							<span class="help-block"><?php echo $numberOnlyHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="hoaFeeSchedule"><?php echo $hoaFeeScheduleField; ?></label>
							<input type="text" class="form-control" name="hoaFeeSchedule" value="<?php echo $row['hoaFeeSchedule']; ?>">
							<span class="help-block"><?php echo $hoaFeeScheduleHelper; ?></span>
						</div>
					</div>
					<div class="modal-footer">
						<button type="input" name="submit" value="Update HOA" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
						<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- UPDATE PROPERTY LISTING TEXT MODAL -->
	<div class="modal fade" id="editListingText" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-info">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $updateListingTextBtn; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="propertyListing"><?php echo $propListingField; ?></label>
							<textarea class="form-control" name="propertyListing" rows="6"><?php echo clean($row['propertyListing']); ?></textarea>
							<span class="help-block"><?php echo $propListingHelper.' '.$htmlNotAllowed; ?></span>
						</div>
					</div>
					<div class="modal-footer">
						<button type="input" name="submit" value="Update Listing" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
						<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<?php if ($superuser == '1')  { ?>
		<!-- Assign LANDLORD MODAL -->
		<div class="modal fade" id="AssignProperty" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header modal-info">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
						<h4 class="modal-title"><?php echo $AssignPropertyBtn; ?></h4>
					</div>
					<form action="" method="post">
						<div class="modal-body">
							<div class="form-group">
								<label for="adminId"><?php echo $selectLandlordField; ?></label>
								<select class="form-control" name="adminId">
								<?php
									// Get the Admin List
									$getAdmin = "SELECT
													adminId,
													adminFirstName,
													adminLastName,
													isActive
												FROM
													admins
												WHERE
													isActive != 0
									";
									$getres = mysqli_query($mysqli, $getAdmin) or die(mysqli_error());
								?>
									<option value="">...</option>
									<?php while ($a = mysqli_fetch_assoc($getres)) { ?>
										<option value="<?php echo $a['adminId']; ?>"><?php echo clean($a['adminFirstName']).' '.clean($a['adminLastName']); ?></option>
									<?php } ?>
								</select>
								<span class="help-block"><?php echo $selectLandlordHelper; ?></span>
							</div>
						</div>
						<div class="modal-footer">
							<input type="hidden" name="isManager" value="<?php echo $isManager; ?>" />
							<button type="input" name="submit" value="Assign Property" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
							<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>
	<?php } ?>

	<!-- RECORD A PAYMENT RECEIVED MODAL -->
	<div class="modal fade" id="recordPayment" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-success">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $recordPaymentBtn; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="paymentDate"><?php echo $paymentDateField; ?></label>
							<input type="text" class="form-control" name="paymentDate" id="paymentDate" value="<?php echo isset($_POST['paymentDate']) ? $_POST['paymentDate'] : '' ?>">
							<span class="help-block"><?php echo $paymentDateHelper; ?>.</span>
						</div>
						<div class="form-group">
							<label for="paymentAmount"><?php echo $paymentAmountField; ?></label>
							<input type="text" class="form-control" name="paymentAmount" value="<?php echo isset($_POST['paymentAmount']) ? $_POST['paymentAmount'] : '' ?>">
							<span class="help-block"><?php echo $paymentAmountHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="paymentPenalty"><?php echo $lateFeeField; ?></label>
							<input type="text" class="form-control" name="paymentPenalty" value="<?php echo isset($_POST['paymentPenalty']) ? $_POST['paymentPenalty'] : '' ?>">
							<span class="help-block"><?php echo $lateFeeHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="paymentFor"><?php echo $paymentForField; ?></label>
							<input type="text" class="form-control" name="paymentFor" value="<?php echo isset($_POST['paymentFor']) ? $_POST['paymentFor'] : '' ?>">
							<span class="help-block"><?php echo $paymentForHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="paymentType"><?php echo $paymentTypeField; ?></label>
							<input type="text" class="form-control" name="paymentType" value="<?php echo isset($_POST['paymentType']) ? $_POST['paymentType'] : '' ?>">
							<span class="help-block"><?php echo $paymentTypeHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="rentMonth"><?php echo $rentMonthField; ?></label>
							<select class="form-control" name="rentMonth">
								<option value=""><?php echo $monthNoneSelect; ?></option>
								<option value="<?php echo $monthJanuarySelect; ?>"><?php echo $monthJanuarySelect; ?></option>
								<option value="<?php echo $monthFebruarySelect; ?>"><?php echo $monthFebruarySelect; ?></option>
								<option value="<?php echo $monthMarchSelect; ?>"><?php echo $monthMarchSelect; ?></option>
								<option value="<?php echo $monthAprilSelect; ?>"><?php echo $monthAprilSelect; ?></option>
								<option value="<?php echo $monthMaySelect; ?>"><?php echo $monthMaySelect; ?></option>
								<option value="<?php echo $monthJuneSelect; ?>"><?php echo $monthJuneSelect; ?></option>
								<option value="<?php echo $monthJulySelect; ?>"><?php echo $monthJulySelect; ?></option>
								<option value="<?php echo $monthAugustSelect; ?>"><?php echo $monthAugustSelect; ?></option>
								<option value="<?php echo $monthSeptemberSelect; ?>"><?php echo $monthSeptemberSelect; ?></option>
								<option value="<?php echo $monthOctoberSelect; ?>"><?php echo $monthOctoberSelect; ?></option>
								<option value="<?php echo $monthNovemberSelect; ?>"><?php echo $monthNovemberSelect; ?></option>
								<option value="<?php echo $monthDecemberSelect; ?>"><?php echo $monthDecemberSelect; ?></option>
							</select>
							<span class="help-block"><?php echo $rentMonthHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="paymentNotes"><?php echo $paymentNotesField; ?></label>
							<textarea class="form-control" name="paymentNotes" rows="2"><?php echo isset($_POST['paymentNotes']) ? $_POST['paymentNotes'] : '' ?></textarea>
							<span class="help-block"><?php echo $paymentNotesHelper; ?> <?php echo $htmlNotAllowed; ?></span>
						</div>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="tenantId" value="<?php echo $rows['tenantId']; ?>">
						<input type="hidden" name="leaseId" value="<?php echo $rows['leaseId']; ?>">
						<button type="input" name="submit" value="Record Payment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $savePaymentBtn; ?></button>
						<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- ADD A RESIDENT MODAL -->
	<div class="modal fade" id="newResident" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-info">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $newResidentBtn; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="residentName"><?php echo $residentNameField; ?></label>
							<input type="text" class="form-control" name="residentName" value="<?php echo isset($_POST['residentName']) ? $_POST['residentName'] : '' ?>">
						</div>
						<div class="form-group">
							<label for="residentPhone"><?php echo $tab_phone; ?></label>
							<input type="text" class="form-control" name="residentPhone" id="resident_phone" value="<?php echo isset($_POST['residentPhone']) ? $_POST['residentPhone'] : '' ?>">
						</div>
						<div class="form-group">
							<label for="residentEmail"><?php echo $tab_email; ?></label>
							<input type="text" class="form-control" name="residentEmail" value="<?php echo isset($_POST['residentEmail']) ? $_POST['residentEmail'] : '' ?>">
						</div>
						<div class="form-group">
							<label for="relation"><?php echo $relationToTenant; ?></label>
							<input type="text" class="form-control" name="relation" value="<?php echo isset($_POST['relation']) ? $_POST['relation'] : '' ?>">
						</div>
						<div class="form-group">
							<label for="residentNotes"><?php echo $residentNotesField; ?></label>
							<textarea class="form-control" name="residentNotes" rows="2"><?php echo isset($_POST['residentNotes']) ? $_POST['residentNotes'] : '' ?></textarea>
							<span class="help-block"><?php echo $residentNotesHelper.' '.$htmlNotAllowed; ?></span>
						</div>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="tenantId" value="<?php echo $rows['tenantId']; ?>">
						<button type="input" name="submit" value="Add Resident" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
						<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- -- UPLOAD PROPERTY DOCUMENTS MODEL -- -->
	<div class="modal fade" id="uploadPropFile" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-primary">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $uploadPropFileBtn; ?></h4>
				</div>
				<form enctype="multipart/form-data" action="" method="post">
					<div class="modal-body">
						<p>
							<?php echo $propertyFilesQuip; ?><br />
							<small>
								<?php echo $allowedFileTypesQuip.' '.$uploadTypesAllowed; ?><br />
								<?php echo $maxUploadSize.' '.$maxUpload.'MB.'; ?>
							</small>
						</p>

						<div class="form-group">
							<label for="fileName"><?php echo $propFileTitleField; ?></label>
							<input type="text" class="form-control" name="fileName" value="">
							<span class="help-block"><?php echo $propFileTitleHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="fileDesc"><?php echo $propFileDescField; ?></label>
							<textarea class="form-control" name="fileDesc" rows="2"></textarea>
							<span class="help-block"><?php echo $propFileDescHelper.' '.$htmlNotAllowed; ?></span>
						</div>
						<div class="form-group">
							<label for="file"><?php echo $selectPropFileField; ?></label>
							<input type="file" name="file">
						</div>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="propertyFolder" value="<?php echo clean($row['propertyFolder']); ?>">
						<button type="input" name="submit" value="Upload File" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $uploadBtn; ?></button>
						<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>

<?php } ?>