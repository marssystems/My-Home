<?php
    $jsFile = 'newPayment';
	$paid = '';
	$hasLateRent = '';

	// Get the Current Month
    $currentMonth = date('F');
	// Get the Current Day
    $currentDay = date('d');
	// Get the Current Date
    $currentDate = date("Y-m-d");

	// Get Current Property Info
	$lease = "
		SELECT
            tenants.propertyId,
            tenants.leaseId,
            properties.propertyName,
			properties.propertyRate,
			properties.latePenalty,
			leases.leaseTerm,
			leases.leaseStart,
            DATE_FORMAT(leases.leaseEnd,'%M %d, %Y') AS leaseEnd,
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
            tenants.tenantId = ".$_SESSION['tenantId'];
	$leaseres = mysqli_query($mysqli, $lease) or die('Error, retrieving Property Data failed. ' . mysqli_error());
    $row = mysqli_fetch_assoc($leaseres);

	// Format the Amounts
	$propertyRate = $currencySym.format_amount($row['propertyRate'], 2);
	$latePenalty = $currencySym.format_amount($row['latePenalty'], 2);
	$totalToPay	= $row['propertyRate'];
	$ifLate = $row['propertyRate'] + $row['latePenalty'];
	$lateTotal = $ifLate.'.00';
	
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
		
		if (mysqli_num_rows($lateres) < 1) {
			$hasLateRent = 'true';
			if ($currentDay > '5') {
				$totalToPay	= $lateTotal;
			} else {
				$totalToPay	= $row['propertyRate'];
			}
		}
	} else {
		$hasLateRent = '';
	}
?>
<h3 class="success"><?php echo clean($row['propertyName']).' '.$newPaymentH3; ?></h3>
<?php if ($set['enablePayments'] == '1') { ?>
	<p class="lead"><?php echo $newPaymentQuipPaypal; ?></p>
<?php } else { ?>
	<p class="lead"><?php echo $newPaymentQuipNoPaypal; ?></p>
<?php } ?>

<?php
	if ($hasLateRent == 'true') {
		if ($currentDay > '5') {
?>
			<div class="panel panel-warning padTop">
				<div class="panel-heading">
					<h4 class="panel-title"><i class="fa fa-warning"></i> <?php echo $rentIsPastDueMsg; ?></h4>
				</div>
				<div class="panel-body">
					<?php echo $rentIsPastDueQuip; ?>
				</div>
			</div>
<?php
		}
	}
?>

<div class="row">
	<div class="col-md-6">
		<div class="list-group padTop">
			<li class="list-group-item"><?php echo $monthlyRate.' '.$propertyRate; ?></li>
		</div>
	</div>
	<div class="col-md-6">
		<div class="list-group padTop">
			<li class="list-group-item"><?php echo $feeIfLate.' '.$latePenalty; ?></li>
		</div>
	</div>
</div>

<?php if ($set['enablePayments'] == '1') { ?>
	<h4 class="primary"><?php echo $payWithPaypal; ?></h4>
	<p class="lead"><?php echo $paymentAmount; ?></p>
	<p><?php echo $paymentAmountQuip; ?></p>

	<div class="errorNote"></div>

	<form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post" id="paypal">
		<div class="form-group">
			<label for="priceSet"><?php echo $paymentAmountField; ?></label>
			<input type="text" class="form-control" name="priceSet" id="priceSet" value="<?php echo $totalToPay; ?>" />
			<span class="help-block"><?php echo $paymentAmountHelper; ?></span>
		</div>
		<div class="form-group">
			<label for="pricePlusFee"><?php echo $totalAmountField; ?></label>
			<input type="text" class="form-control" name="pricePlusFee" id="pricePlusFee" readonly="readonly" />
			<span class="help-block"><?php echo $totalAmountHelper; ?></span>
		</div>

		<!-- Identify your business so that you can collect the payments. -->
		<input type="hidden" name="business" value="<?php echo $set['paypalEmail'];?>" />
		<input type="hidden" name="cmd" value="_xclick" />
		<!-- Specify details. -->
		<input type="hidden" name="item_name" value="<?php echo $set['paypalItemName'];?>" />
		<input type="hidden" name="item_number" value="Property: <?php echo $row['propertyName'];?>" />
		<input type="hidden" name="amount" value="" />
		<input type="hidden" name="currency_code" value="<?php echo $set['paypalCurrency'];?>" />
		<input type="hidden" name="no_shipping" value="0" />
		<!-- Include the PayPal Fee %. -->
		<input type="hidden" name="payFee" id="payFee" value="<?php echo $set['paypalFee'];?>" />
		<!-- Display the payment button. -->
		<p>
			<button type="input" name="submit" value="Paypal" class="btn btn-success btn-icon"><i class="fa fa-credit-card"></i> <?php echo $paypalBtn; ?></button>
			<input type="hidden" name="return" value="<?php echo $set['installUrl']; ?>index.php?page=paymentComplete&tenantId=<?php echo $_SESSION['tenantId']; ?>" />
		</p>
	</form>

	<hr />
<?php } ?>

<h4 class="primary"><?php echo $payByOther; ?></h4>
<?php if ($set['enablePayments'] == '1') { ?>
	<p class="lead"><?php echo $payByOtherQuip; ?></p>
<?php } ?>

<div class="list-group padTop">
	<li class="list-group-item"><?php echo $payableTo.' '.$set['businessName']; ?></li>
	<li class="list-group-item"><?php echo $mailTo.'<br />'.$set['businessName'].'<br />'.nl2br($set['businessAddress']); ?></li>
</div>

<hr />

<h4 class="primary"><?php echo $paymentQuestionsH3; ?></h4>
<p class="lead"><?php echo $paymentQuestionsQuip; ?></p>