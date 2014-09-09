<?php
	$jsFile = 'propertyDetails';
	$hasLateRent = '';

	// Get the Current Month
    $currentMonth = date('F');
	// Get the Current Day
    $currentDay = date('d');
	// Get the Current Date
    $currentDate = date("Y-m-d");

	// Get Property Pictures Folder from Site Settings
	$propertyPicsPath = $set['propertyPicsPath'];

	// Get Current Property Info
	$lease = "
		SELECT
            tenants.propertyId,
            tenants.leaseId,
            properties.propertyName,
			properties.propertyDesc,
			properties.propertyAddress,
			properties.propertyRate,
			properties.latePenalty,
            CASE properties.petsAllowed
                WHEN 0 THEN 'No'
                WHEN 1 THEN 'Yes'
            END AS petsAllowed,
			properties.propertyFolder,
			properties.propertyAmenities,
			properties.propertyType,
			properties.propertyStyle,
			properties.yearBuilt,
			properties.propertySize,
			properties.parking,
			properties.heating,
			properties.bedrooms,
			properties.bathrooms,
			properties.propertyHoa,
			properties.hoaAddress,
			properties.hoaPhone,
			leases.leaseTerm,
            leases.leaseStart,
            DATE_FORMAT(leases.leaseEnd,'%M %d, %Y') AS leaseEnd,
            leases.isClosed,
			assignedproperties.adminId,
			admins.adminFirstName,
			admins.adminLastName
		FROM
			tenants
            LEFT JOIN properties ON tenants.propertyId = properties.propertyId
            LEFT JOIN leases ON tenants.leaseId = leases.leaseId
			LEFT JOIN assignedproperties ON tenants.propertyId = assignedproperties.propertyId
			LEFT JOIN admins ON assignedproperties.adminId = admins.adminId
		WHERE
            tenants.tenantId = ".$_SESSION['tenantId']." AND
            leases.isClosed = 0";
	$leaseres = mysqli_query($mysqli, $lease) or die('Error, retrieving Property Data failed. ' . mysqli_error());
    $row = mysqli_fetch_assoc($leaseres);

	// Format the Amounts
	$propertyRate = $currencySym.format_amount($row['propertyRate'], 2);
	$latePenalty = $currencySym.format_amount($row['latePenalty'], 2);

	// Check if the Tenant is late on current month's rent
	if ($currentDate > $row['leaseStart']) {
		$latecheck = "SELECT
						payments.isRent,
						payments.rentMonth,
						tenants.propertyId
					FROM
						payments
						LEFT JOIN tenants ON payments.tenantId = tenants.tenantId
					WHERE
						tenants.propertyId = ".$row['propertyId']." AND
						payments.isRent = 1 AND
						payments.rentMonth = '".$currentMonth."'";
		$lateres = mysqli_query($mysqli, $latecheck) or die('Error, retrieving Late Rent Data failed. ' . mysqli_error());
		if(mysqli_num_rows($lateres) < 1) {
			$hasLateRent = 'true';
		}
	} else {
		$hasLateRent = '';
	}

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
                propertyId = ".$row['propertyId']."
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
                propertyId = ".$row['propertyId']."
            ORDER BY fileId DESC";
    $docsres = mysqli_query($mysqli, $docs) or die('Error, retrieving Property Documents failed. ' . mysqli_error());

    // Get Resident Data
    $resident  = "SELECT
                residentName,
                residentPhone,
                residentEmail,
				relation
            FROM
                residents
            WHERE
                tenantId = ".$_SESSION['tenantId'];
    $residentres = mysqli_query($mysqli, $resident) or die('Error, retrieving Resident Data failed. ' . mysqli_error());

    // Get 5 latest payments data
    $payment = "
        SELECT
            paymentId,
			tenantId,
			leaseId,
			DATE_FORMAT(paymentDate,'%M %d, %Y') AS paymentDate,
			UNIX_TIMESTAMP(paymentDate) AS orderDate,
			paymentAmount,
			paymentPenalty,
			paymentFor,
			paymentType,
			isRent,
			rentMonth
		FROM
			payments
        WHERE
			tenantId = ".$_SESSION['tenantId']."
		ORDER BY orderDate DESC
		LIMIT 5";
	$paymentres = mysqli_query($mysqli, $payment) or die('Error, retrieving Payment Data failed. ' . mysqli_error());
?>
<div class="row">
	<div class="col-md-8">
		<h3 class="primary"><?php echo $propertyDetailsH3; ?></h3>
		<p class="lead">
			<?php echo clean($row['propertyName']); ?><br />
			<?php echo clean($row['propertyAddress']); ?>
		</p>

		<p><?php echo clean($row['propertyDesc']); ?></p>

		<hr />

		<h3 class="primary"><?php echo $propertyPicturesH3; ?></h3>
		<?php if(mysqli_num_rows($picsres) > 0) { ?>
			<div class="gallery">
			<?php while ($pic = mysqli_fetch_assoc($picsres)) { ?>
				<a data-toggle="modal" href="#viewPicture<?php echo $pic['pictureId']; ?>">
					<img src="<?php echo $propertyPicsPath.$row['propertyFolder'].'/'.$pic['pictureUrl']; ?>" />
				</a>
				<!-- -- View Property Picture Model -- -->
				<div class="modal fade" id="viewPicture<?php echo $pic['pictureId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header picture">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
								<?php echo clean($pic['pictureName']); ?>
							</div>
							<div class="modal-body">
								<img src="<?php echo $propertyPicsPath.$row['propertyFolder'].'/'.$pic['pictureUrl']; ?>" />
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
			</div>
		<?php } ?>

		<div class="clearfix"></div>
		<hr />

		<h3 class="primary"><?php echo $propertyAmenitiesH3; ?></h3>
		<div class="row">
			<div class="col-md-6">
				<ul class="list-group">
					<li class="list-group-item"><strong><?php echo $propertyType; ?></strong> <?php echo clean($row['propertyType']).' '.clean($row['propertyStyle']); ?></li>
					<li class="list-group-item"><strong><?php echo $petsAreAllowed; ?></strong> <?php echo clean($row['petsAllowed']); ?></li>
					<li class="list-group-item"><strong><?php echo $yearBuilt; ?></strong> <?php echo clean($row['yearBuilt']); ?></li>
					<li class="list-group-item"><strong><?php echo $propertySize; ?></strong> <?php echo clean($row['propertySize']); ?></li>
					<li class="list-group-item"><strong><?php echo $numberBedrooms; ?></strong> <?php echo clean($row['bedrooms']); ?></li>
					<li class="list-group-item"><strong><?php echo $numberBathrooms; ?></strong> <?php echo clean($row['bathrooms']); ?></li>
					<li class="list-group-item"><strong><?php echo $parking; ?></strong> <?php echo clean($row['parking']); ?></li>
					<li class="list-group-item"><strong><?php echo $heating; ?></strong> <?php echo clean($row['heating']); ?></li>
				</ul>
			</div>
			<div class="col-md-6">
				<ul class="list-group">
					<li class="list-group-item"><strong><?php echo $propertyAmenities; ?></strong> <?php echo clean($row['propertyAmenities']); ?></li>
				</ul>

				<ul class="list-group">
					<li class="list-group-item"><strong><?php echo $hoa; ?></strong> <?php echo clean($row['propertyHoa']); ?></li>
					<li class="list-group-item"><strong><?php echo $hoaPhone; ?></strong> <?php echo clean($row['hoaPhone']); ?></li>
					<li class="list-group-item"><strong><?php echo $hoaAddress; ?></strong> <?php echo clean($row['hoaAddress']); ?></li>
				</ul>
			</div>
		</div>

	</div>
	<div class="col-md-4">
		<h4 class="primary"><?php echo $propertyFiles_sb; ?></h4>
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
							<?php echo $tab_dateUploaded.': '.$files['fileDate']; ?>
						</p>
						<p>
							<a href="index.php?page=viewFile&fileId=<?php echo $files['fileId']; ?>" class="btn btn-info btn-sm btn-icon"><?php echo $viewFileLink; ?>  <i class="fa fa-long-arrow-right"></i></a>
						</p>
					</dd>
		<?php
				}
				echo '</dl>';
			}
		?>

		<?php if(mysqli_num_rows($residentres) > 0) { ?>
			<div class="clearfix"></div>
			<hr />

			<h4 class="primary"><?php echo $propertyResidents_sb; ?></h4>
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
							<?php echo $relationToTenant.' '.$residents['relation']; ?>
						</p>
					</dd>
				<?php } ?>
			</dl>
		<?php } ?>

		<div class="clearfix"></div>
		<hr />

		<h4 class="primary"><?php echo $propertyPayments_sb; ?></h4>
		<?php
			if ($hasLateRent == 'true') {
				if ($currentDay > '5') {
		?>
					<div class="alertMsg warning">
						<i class="fa fa-warning"></i> <?php echo $rentIsPastDueMsg; ?>
					</div>
		<?php
				}
			}
		?>

		<?php if(mysqli_num_rows($paymentres) < 1) { ?>
			<dl class="accordion">
				<dt class="noneFound"><a><i class="fa fa-minus-square-o"></i> <?php echo $noPaymentMsg; ?></a></dt>
			</dl>
		<?php
			} else {
				echo '<dl class="accordion">';
				while ($pay = mysqli_fetch_assoc($paymentres)) {
					// Format the Amounts
					$paymentAmount = $currencySym.format_amount($pay['paymentAmount'], 2);
					$paymentPenalty = $currencySym.format_amount($pay['paymentPenalty'], 2);
					// Get the Total Paid
					$total = $pay['paymentAmount'] + $pay['paymentPenalty'];
					$totalPaid = $currencySym.format_amount($total, 2);
		?>
					<dt>
						<a><?php echo clean($pay['paymentFor']); ?>
							<span><?php echo $viewDeatilsLink; ?> <i class="fa fa-long-arrow-right"></i></span>
						</a>
					</dt>
					<dd class="hideIt">
						<p>
							<?php echo $amountPaid_sb.' '.$paymentAmount; ?><br />
							<?php echo $feesPaid_sb.' '.$paymentPenalty; ?><br />
							<?php echo $totalPaid_sb.' '.$totalPaid; ?>
						</p>
						<p class="updatedOn">
							<?php echo $paymentDate_sb.' '.$pay['paymentDate']; ?>
						</p>
						<p>
							<a href="index.php?page=receipt&paymentId=<?php echo $pay['paymentId']; ?>" target="_blank" class="btn btn-info btn-sm btn-icon"><?php echo $viewReciptLabel; ?>  <i class="fa fa-long-arrow-right"></i></a>
						</p>
					</dd>
		<?php
				}
				echo '</dl>';
			}
		?>
		<a href="index.php?page=viewRentalPayments" class="btn btn-block btn-icon btn-primary padTop"><i class="fa fa-money"></i> <?php echo $viewRentalPaymentsBtn; ?></a>
		<a href="index.php?page=newPayment" class="btn btn-block btn-icon btn-success padTop"><i class="fa fa-credit-card"></i> <?php echo $newPaymentBtnLink; ?></a>
	</div>
</div>