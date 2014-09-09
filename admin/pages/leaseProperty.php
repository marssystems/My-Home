<?php
	$propertyId = $_GET['propertyId'];
	$jsFile = 'leaseProperty';
	$datePicker = 'true';
	$leaseCreated = '';
	
	// Step 1 - Create Lease
	if (isset($_POST['submit']) && $_POST['submit'] == 'Lease Property') {
		// Validation
		if($_POST['leaseTerm'] == "") {
            $msgBox = alertBox($leaseTermReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['leaseStart'] == "") {
            $msgBox = alertBox($startDateReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['leaseEnd'] == "") {
            $msgBox = alertBox($endDateReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Set some variables
			$leaseTerm = $mysqli->real_escape_string($_POST['leaseTerm']);
			$leaseStart = $mysqli->real_escape_string($_POST['leaseStart']);
			$leaseEnd = $mysqli->real_escape_string($_POST['leaseEnd']);
			$leaseNotes = htmlentities($_POST['leaseNotes']);

            $stmt = $mysqli->prepare("
                                INSERT INTO
                                    leases(
                                        adminId,
                                        propertyId,
                                        leaseTerm,
                                        leaseStart,
                                        leaseEnd,
										leaseNotes
                                    ) VALUES (
                                        ?,
                                        ?,
                                        ?,
                                        ?,
										?,
										?
                                    )");
            $stmt->bind_param('ssssss',
				$adminId,
                $propertyId,
                $leaseTerm,
                $leaseStart,
				$leaseEnd,
				$leaseNotes
            );
            $stmt->execute();
            $msgBox = alertBox($newLeaseCreatedMsg, "<i class='fa fa-check-square-o'></i>", "success");
            $leaseCreated = 'true';
            $stmt->close();
		}
	}
	
	// Step 2 - Assign Lease to a Tenant
	if (isset($_POST['submit']) && $_POST['submit'] == 'Assign Lease') {
		// Validation
        if($_POST['tenantId'] == "") {
            $msgBox = alertBox($tenantIdNameReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
            // Update the Property as Leased
            $setLeased = '1';
            $propsql = $mysqli->prepare("
                                UPDATE
                                    properties
                                SET
                                    isLeased = ?
                                WHERE
									propertyId = ?");
            $propsql->bind_param('ss',
							   $setLeased,
                               $propertyId
			);
            $propsql->execute();
            $propsql->close();

            // Update the Tenant to have a Leased Property
			$leaseId = $mysqli->real_escape_string($_POST['leaseId']);
			$tenantId = $mysqli->real_escape_string($_POST['tenantId']);
			
            $tenantsql = $mysqli->prepare("
                                UPDATE
                                    tenants
                                SET
                                    propertyId = ?,
                                    leaseId = ?
                                WHERE
									tenantId = ?");
            $tenantsql->bind_param('sss',
                                $propertyId,
                                $leaseId,
                                $tenantId
			);
            $tenantsql->execute();
            $msgBox = alertBox($tenantUpdatedMsg, "<i class='fa fa-check-square-o'></i>", "success");
            $leaseCreated = 'complete';
            $tenantsql->close();
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
			propertyDeposit
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

	// Get Tenant Data
    $sql = "SELECT
				tenantId,
				leaseId,
				tenantFirstName,
				tenantLastName,
				isArchived
			FROM
				tenants
			WHERE
                leaseId = 0 AND
				isActive = 1 AND
                isArchived = 0";
    $result = mysqli_query($mysqli, $sql) or die('Error, retrieving Tenant Data failed. ' . mysqli_error());

	// If the New Lease has been created
	if ($leaseCreated == 'true') {
		// Get New Lease Data
        $lease = "SELECT
                    leaseId,
                    propertyId,
                    leaseTerm,
                    leaseStart,
                    leaseEnd,
                    leaseNotes,
                    isClosed
                FROM
                    leases
                WHERE
                    isClosed = 0 AND
                    propertyId = ".$propertyId;
        $results = mysqli_query($mysqli, $lease) or die('Error, retrieving New Lease Data failed. ' . mysqli_error());
        $rows = mysqli_fetch_assoc($results);
    }

	if ($leaseCreated == 'complete') {
?>
	<h3 class="primary"><?php echo $propHasBeenLeasedH3; ?></h3>
	<div class="alertMsg success">
		<i class="fa fa-check-square-o"></i> <?php echo $propHasBeenLeasedMsg; ?>
	</div>
<?php
	} else {
		if ($row['isLeased'] != 0) { ?>
			<h3 class="primary"><?php echo $propAllReadyLeasedH3; ?></h3>
			<div class="alertMsg danger">
				<i class="fa fa-minus-square-o"></i> <?php echo $propAllReadyLeasedMsg; ?>
			</div>
<?php
		} else {
?>
			<h3 class="primary"><?php echo $leasePropertyH3.' '.$row['propertyName']; ?></h3>
			<p class="lead">
				<?php echo $leasePropertyQuip; ?>
			</p>

			<?php if ($msgBox) { echo $msgBox; } ?>

			<p><?php echo $leasePropertyInstructions; ?></p>

			<hr />

			<div class="row padTop">
				<div class="col-md-6">
					<p class="lead">
						<?php echo clean($row['propertyName']); ?><br />
						<?php echo clean($row['propertyAddress']); ?>
					</p>
					<p><?php echo clean($row['propertyDesc']); ?></p>
				</div>
				<div class="col-md-6">
					<ul class="list-group">
						<li class="list-group-item"><strong><?php echo $rentalMonthyRateLi; ?></strong> <?php echo $propertyRate; ?></li>
						<li class="list-group-item"><strong><?php echo $rentalLateFeeLi; ?></strong> <?php echo $latePenalty; ?></li>
						<li class="list-group-item"><strong><?php echo $rentalDepositAmtLi; ?></strong> <?php echo $propertyDeposit; ?></li>
					</ul>
				</div>
			</div>
			
			<?php if ($leaseCreated == '') { ?>
				<div class="panel panel-success">
					<div class="panel-heading">
						<h4 class="panel-title"><?php echo $step1Title; ?></h4>
					</div>
					<div class="panel-body">
						<?php if(mysqli_num_rows($result) < 1) { ?>
							<div class="alertMsg warning">
								<i class="fa fa-minus-square-o"></i> <?php echo $noTenantsAvailableMsg; ?>
							</div>
						<?php } else { ?>
							<form action="" method="post">
								<div class="form-group">
									<label for="leaseTerm"><?php echo $leaseTermField; ?></label>
									<input type="text" class="form-control" name="leaseTerm" value="">
									<span class="help-block"><?php echo $leaseTermHelper; ?></span>
								</div>
								<div class="form-group">
									<label for="leaseStart"><?php echo $leaseStartField; ?></label>
									<input type="text" class="form-control" name="leaseStart" id="leaseStart" value="">
									<span class="help-block"><?php echo $leaseStartHelper; ?></span>
								</div>
								<div class="form-group">
									<label for="leaseEnd"><?php echo $leaseEndField; ?></label>
									<input type="text" class="form-control" name="leaseEnd" id="leaseEnd" value="">
									<span class="help-block"><?php echo $leaseEndHelper; ?></span>
								</div>
								<div class="form-group">
									<label for="leaseNotes"><?php echo $leaseNotesField; ?></label>
									<textarea class="form-control" name="leaseNotes" rows="2"></textarea>
									<span class="help-block"><?php echo $leaseNotesHelper.' '.$htmlNotAllowed; ?></span>
								</div>
								<button type="input" name="submit" value="Lease Property" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $createLeaseBtn; ?></button>
							</form>
						<?php } ?>
					</div>
				</div>
			<?php } else { ?>
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h4 class="panel-title"><?php echo $step2Title; ?></h4>
					</div>
					<div class="panel-body">
						<p><?php echo $step2Quip; ?></p>
						<form action="" method="post">
							<div class="form-group">
								<label for="tenantId"><?php echo $tab_tenant; ?></label>
								<select class="form-control" name="tenantId">
									<option value="">...</option>
									<?php while ($col = mysqli_fetch_assoc($result)) { ?>
										<option value="<?php echo $col['tenantId']; ?>"><?php echo clean($col['tenantFirstName']).' '.clean($col['tenantLastName']); ?></option>
									<?php } ?>
								</select>
								<span class="help-block"><?php echo $selectTenantHelper; ?></span>
							</div>
							<input type="hidden" name="leaseId" value="<?php echo $rows['leaseId']; ?>">
							<button type="input" name="submit" value="Assign Lease" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $assignLeaseBtn; ?></button>
						</form>
					</div>
				</div>
			<?php } ?>
<?php
		}
	}
?>