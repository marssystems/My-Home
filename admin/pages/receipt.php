<?php
	$paymentId = $_GET['paymentId'];

	// Get Settings Data
	include ('../includes/settings.php');
	$set = mysqli_fetch_assoc($setRes);

	// Set Localization
	$local = $set['localization'];
	switch ($local) {
		case 'en':
			include ('language/en.php');
			break;
		case 'es':
			include ('language/es.php');
			break;
		case 'fr':
			include ('language/fr.php');
			break;
	}

	// Get Site Alert Data
    $alert  = "SELECT
					isActive,
					alertTitle,
					alertText,
					DATE_FORMAT(alertDate,'%M %d, %Y') AS alertDate,
					UNIX_TIMESTAMP(alertDate) AS orderDate,
					alertStart,
					alertExpires
				FROM
					sitealerts
				WHERE
					alertStart <= DATE_SUB(CURDATE(),INTERVAL 0 DAY) AND
					alertExpires >= DATE_SUB(CURDATE(),INTERVAL 0 DAY) OR
					onReceipt = 1
				ORDER BY
					orderDate DESC";
    $alertres = mysqli_query($mysqli, $alert) or die('Error, retrieving Alert Data failed. ' . mysqli_error());

	// Get All Payment Data
    $query  = "SELECT
                    payments.paymentId,
					payments.adminId,
                    payments.tenantId,
                    payments.leaseId,
					payments.hasRefund,
                    DATE_FORMAT(payments.paymentDate,'%M %d, %Y') AS paymentDate,
                    payments.paymentAmount,
                    payments.paymentPenalty,
                    payments.paymentFor,
                    payments.paymentType,
                    payments.isRent,
                    payments.rentMonth,
					payments.paymentNotes,
					tenants.tenantFirstName,
					tenants.tenantLastName,
                    tenants.tenantAddress,
                    tenants.tenantPhone
                FROM
                    payments
					LEFT JOIN tenants ON payments.tenantId = tenants.tenantId
                WHERE
                    payments.paymentId =".$paymentId;
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving Payment Data failed. ' . mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Get the Month the Payment was for (if it was a Rent Payment)
	$rentMonth = $row['rentMonth'];

	// Format the Amounts
	$paymentAmount = $currencySym.format_amount($row['paymentAmount'], 2);
	if ($row['paymentPenalty'] != '') {
		$paymentPenalty = $currencySym.format_amount($row['paymentPenalty'], 2);
	} else {
		$paymentPenalty = '';
	}
	$total = $row['paymentAmount'] + $row['paymentPenalty'];
	$totalPaid = $currencySym.format_amount($total, 2);

	// Decrypt data for display
	if ($row['tenantAddress'] != '') { $tenantAddress = decryptIt($row['tenantAddress']); } else { $tenantAddress = ''; }
	if ($row['tenantPhone'] != '') { $tenantPhone = decryptIt($row['tenantPhone']); } else { $tenantPhone = ''; }
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $set['siteName'].' &mdash; '.$headTitle; ?></title>

        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="../css/receipt.css">
        <!--[if lt IE 9]>
            <script src="../js/html5shiv.js"></script>
        <![endif]-->
	</head>
	<body>
		<header>
			<h1><?php echo $headTitle; ?></h1>
			<address>
				<p>
                    <strong><?php echo $set['siteName']; ?></strong><br />
                    <?php echo str_replace("\n","<br />", $set['businessAddress']); ?><br />
                    <?php echo $set['contactPhone']; ?>
                </p>
			</address>
			<span><img alt="" src="../images/logo.png"></span>
		</header>
		<article>
			<h1><?php echo $receivedFrom; ?></h1>
			<address>
				<p>
					<?php echo $row['tenantFirstName'].' '.$row['tenantLastName']; ?><br />
					<?php echo str_replace("\n","<br />", $tenantAddress);?><br />
					<?php echo $tenantPhone; ?>
				</p>
			</address>
			<table class="meta">
				<tr>
					<th><span><?php echo $receiptDate; ?></span></th>
					<td><span><?php echo date('F j, Y'); ?></span></td>
				</tr>
				<tr>
					<th><span><?php echo $paymentNum; ?></span></th>
					<td><span><?php echo $paymentId; ?></span></td>
				</tr>
				<tr>
					<th><span><?php echo $dateReceived; ?></span></th>
					<td><span><?php echo $row['paymentDate']; ?></span></td>
				</tr>
				<?php if ($row['isRent'] == '1') { ?>
					<tr>
						<th><span><?php echo $monthlyRent; ?></span></th>
						<td><span><?php echo $rentMonth; ?></span></td>
					</tr>
				<?php } ?>
			</table>
			<table class="inventory">
				<thead>
					<tr>
						<th class="type"><span><?php echo $descFor; ?></span></th>
						<th class="notes"><span><?php echo $payNotes; ?></span></th>
						<th class="paidBy"><span><?php echo $tab_paidBy; ?></span></th>
						<?php if ($row['isRent'] == '1') { ?>
							<th class="lateFee"><span><?php echo $lateFeeDue; ?></span></th>
						<?php } ?>
						<th class="payAmount"><span><?php echo $tab_amount; ?></span></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="type"><span><?php echo $row['paymentFor']; ?></span></td>
						<td class="notes"><span><?php echo $row['paymentNotes']; ?></span></td>
						<td class="paidBy"><span><?php echo $row['paymentType']; ?></span></td>
						<?php if ($row['isRent'] == '1') { ?>
							<td class="lateFee"><span><?php echo $paymentPenalty; ?></span></td>
						<?php } ?>
						<td class="payAmount"><span><?php echo $paymentAmount; ?></span></td>
					</tr>
				</tbody>
			</table>

			<table class="balance">
				<tr>
					<th><span><?php echo $amountDue; ?></span></th>
					<td><span><?php echo $paymentAmount; ?></span></td>
				</tr>
				<?php if ($row['isRent'] == '1') { ?>
					<tr>
						<th><span><?php echo $lateFeeDue; ?></span></th>
						<td><span><?php echo $paymentPenalty; ?></span></td>
					</tr>
				<tr>
					<th><span><?php echo $totalAmountDue; ?></span></th>
					<td><span><?php echo $totalPaid; ?></span></td>
				</tr>
				<?php } ?>
				<tr>
					<th class="strong"><span><?php echo $totalAmountPaid; ?></span></th>
					<td class="strong"><span><?php echo $totalPaid; ?></span></td>
				</tr>
			</table>
		</article>
		<aside>
			<h2><span><?php echo $receiptThankYou; ?></span></h2>
			<p>
				<?php while ($col = mysqli_fetch_assoc($alertres)) { ?>
					<?php echo nl2br(clean($col['alertText'])); ?>
				<?php } ?>
			</p>
		</aside>
	</body>
</html>