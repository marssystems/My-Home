<?php
	$propertyId = $_GET['propertyId'];

	// Get Property Pictures Folder from Site Settings
	$propertyPicsPath = $set['propertyPicsPath'];

	// Get Current Property Info
	$query = "
		SELECT
			propertyId,
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
			propertyFolder,
			propertyAmenities,
			propertyType,
			propertyStyle,
			yearBuilt,
			propertySize,
			parking,
			heating,
			bedrooms,
			bathrooms,
			propertyHoa,
			isArchived
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

	// Check that the URL has not been minipulated
	if ($row['isLeased'] != '0' || $row['isArchived'] != '0') {
?>
<h3 class="danger"><?php echo $accessErrorH3; ?></h3>
<div class="alertMsg danger">
	<i class="fa fa-minus-square-o"></i> <?php echo $permissionDenied; ?>
</div>
<?php } else { ?>
	<div class="row">
		<div class="col-md-6">
			<h3 class="primary"><?php echo $viewPropertyH3; ?></h3>
			<p class="lead">
				<?php echo clean($row['propertyName']); ?><br />
				<?php echo clean($row['propertyAddress']); ?>
			</p>
			<p><?php echo clean($row['propertyDesc']); ?></p>

			<ul class="list-group">
				<li class="list-group-item"><strong><?php echo $tab_monthlyRent; ?>:</strong> <?php echo $propertyRate; ?></li>
				<li class="list-group-item"><strong><?php echo $feeForLateRent; ?>:</strong> <?php echo $latePenalty; ?></li>
				<li class="list-group-item"><strong><?php echo $tab_propertyDeposit; ?>:</strong> <?php echo $propertyDeposit; ?></li>
			</ul>

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
		</div>
		<div class="col-md-6">
			<h3 class="primary"><?php echo $propertyAmenitiesH3; ?></h3>
			<ul class="list-group">
				<li class="list-group-item"><strong><?php echo $propertyAmenities; ?></strong> <?php echo clean($row['propertyAmenities']); ?></li>
				<li class="list-group-item"><strong><?php echo $propertyType; ?></strong> <?php echo clean($row['propertyType']).' '.clean($row['propertyStyle']); ?></li>
				<li class="list-group-item"><strong><?php echo $yearBuilt; ?></strong> <?php echo clean($row['yearBuilt']); ?></li>
				<li class="list-group-item"><strong><?php echo $petsAreAllowed; ?></strong> <?php echo $row['petsAllowed']; ?></li>
				<li class="list-group-item"><strong><?php echo $propertySize; ?></strong> <?php echo clean($row['propertySize']); ?></li>
				<li class="list-group-item"><strong><?php echo $numberBedrooms; ?></strong> <?php echo clean($row['bedrooms']); ?></li>
				<li class="list-group-item"><strong><?php echo $numberBathrooms; ?></strong> <?php echo clean($row['bathrooms']); ?></li>
				<li class="list-group-item"><strong><?php echo $parking; ?></strong> <?php echo clean($row['parking']); ?></li>
				<li class="list-group-item"><strong><?php echo $heating; ?></strong> <?php echo clean($row['heating']); ?></li>
				<li class="list-group-item"><strong><?php echo $hoa; ?></strong> <?php echo clean($row['propertyHoa']); ?></li>
			</ul>
		</div>
	</div>

	<div class="row padTop">
		<div class="col-md-10">
			<p><?php echo $viewPropertyInst; ?></p>
		</div>
		<div class="col-md-2">
			<a href="<?php echo $set['installUrl']; ?>admin/templates/rentalApplication.pdf" target="_blank" class="btn btn-success btn-icon floatRight"><i class="fa fa-download"></i> <?php echo $downloadApplicationBtn; ?></a>
		</div>
	</div>
<?php } ?>