<?php

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
			propertyType,
			propertyStyle,
			propertySize,
			bedrooms,
			bathrooms,
			isArchived
		FROM
			properties
		WHERE
            createdBy = ".$_SESSION['adminId']." AND
			isLeased = 0 AND
			isArchived = 0";
	$res = mysqli_query($mysqli, $query) or die('Error, retrieving Property Data failed. ' . mysqli_error());
?>
<h3 class="primary"><?php echo $availablePropertiesH3; ?></h3>
<div class="row">
	<div class="col-md-10">
		<p class="lead"><?php echo $availablePropertiesQuip; ?></p>
	</div>
	<div class="col-md-2">
		<a href="<?php echo $set['installUrl']; ?>admin/templates/rentalApplication.pdf" target="_blank" class="btn btn-success btn-icon floatRight"><i class="fa fa-download"></i> <?php echo $downloadApplicationBtn; ?></a>
	</div>
</div>

<?php if(mysqli_num_rows($res) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noPropertiesAvailable; ?>
	</div>
	<p><?php echo $noPropertiesAvailableQuip; ?></p>
<?php
	} else {
?>
		<p><?php echo $availablePropertiesInst; ?></p>
		<div class="clearfix padTop"></div>
<?php
		while ($row = mysqli_fetch_assoc($res)) {
			// Format the Amounts
			$propertyRate = $currencySym.format_amount($row['propertyRate'], 2);
			$latePenalty = $currencySym.format_amount($row['latePenalty'], 2);
			$propertyDeposit = $currencySym.format_amount($row['propertyDeposit'], 2);
?>
			<div class="listBoxesRow">
				<h3 class="info">
					<a href="index.php?page=viewProperty&propertyId=<?php echo $row['propertyId']; ?>"><?php echo clean($row['propertyName']); ?></a>
					<span class="floatRight">
						<a href="index.php?page=viewProperty&propertyId=<?php echo $row['propertyId']; ?>"><small>View Property <i class="fa fa-long-arrow-right"></i></small></a>
					</span>
				</h3>
				<p class="lead"><?php echo clean($row['propertyAddress']); ?></p>
				<p><small><?php echo nl2br(clean($row['propertyDesc'])); ?></small></p>
				<div class="row">
					<div class="col-md-6">
						<ul class="list-unstyled">
							<li><strong><?php echo $tab_monthlyRent; ?>:</strong> <?php echo $propertyRate; ?></li>
							<li><strong><?php echo $feeForLateRent; ?>:</strong> <?php echo $latePenalty; ?></li>
							<li><strong><?php echo $tab_propertyDeposit; ?>:</strong> <?php echo $propertyDeposit; ?></li>
						</ul>
					</div>
					<div class="col-md-6">
						<ul class="list-unstyled">
							<li><strong><?php echo $propertyType; ?></strong> <?php echo clean($row['propertyStyle']); ?></li>
							<li><strong><?php echo $sizeOfProperty; ?>:</strong> <?php echo clean($row['propertySize']); ?></li>
							<li><strong><?php echo $bedroomsBathrooms; ?>:</strong> <?php echo clean($row['bedrooms']).' / '.clean($row['bathrooms']); ?></li>
						</ul>
					</div>
				</div>
			</div>
<?php
		}
	}
?>

<div class="clearfix"></div>