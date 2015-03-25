<?php
	// Check if install.php is present
	if(is_dir('install')) {
		header("Location: install/install.php");
	} else {
		// Access DB Info
		include('config.php');

		// Get Settings Data
		include ('includes/settings.php');
		$set = mysqli_fetch_assoc($setRes);

		// Set Localization
		$local = $set['localization'];
		switch ($local) {
			case 'en-gb':
				include ('language/en-gb.php');
				break;
			case 'es':
				include ('language/es.php');
				break;
			case 'fr':
				include ('language/fr.php');
				break;
		}

		// Include Functions
		include('includes/functions.php');

		$activeAccount = '';
		$nowActive = '';

		if((isset($_GET['tenantEmail']) && !empty($_GET['tenantEmail'])) && (isset($_GET['hash']) && !empty($_GET['hash']))) {
			// Set some variables
			$tenantEmail = $mysqli->real_escape_string($_GET['tenantEmail']);
			$hash = $mysqli->real_escape_string($_GET['hash']);

			// Check to see if there is an account that matches the link
			$check1 = $mysqli->query("SELECT
										tenantEmail,
										hash,
										isActive
									FROM
										tenants
									WHERE
										tenantEmail = '".$tenantEmail."' AND
										hash = '".$hash."' AND
										isActive = 0
			");
			$match = mysqli_num_rows($check1);
			
			// Check if account has all ready been activated
			$check2 = $mysqli->query("SELECT 'X' FROM tenants WHERE tenantEmail = '".$tenantEmail."' AND hash = '".$hash."' AND isActive = 1");
			if ($check2->num_rows) {
				$activeAccount = 'true';
			}

			// Match found, update the Tenant's account to active
			if ($match > 0) {
				$isActive = '1';

				$stmt = $mysqli->prepare("
									UPDATE
										tenants
									SET
										isActive = ?
									WHERE
										tenantEmail = ?");
				$stmt->bind_param('ss',
								   $isActive,
								   $tenantEmail);
				$stmt->execute();
				$nowActive = 'true';
				$stmt->close();
			}
		}
	?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php echo $set['siteName'].' &mdash; '.$tenantLogin; ?></title>

		<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>

		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/custom.css" rel="stylesheet">
		<link href="css/extra.css" rel="stylesheet">
		<link href="css/font-awesome.min.css" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="js/html5shiv.js"></script>
			<script src="js/respond.js"></script>
		<![endif]-->
	</head>

	<body>
		<div class="container">
			<div class="row">
				<div class="col-md-8">
					<div class="logo">
						<a href=""><img alt="<?php echo $set['siteName']; ?>" src="images/logo.png" /></a>
					</div>
				</div>
				<div class="col-md-4 userInfo">
					<p class="textRight"><?php echo $todayMsg.' '.date('l'). " the " .date('jS \of F, Y'); ?></p>
				</div>
			</div>

			<div class="navbar navbar-default">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>

				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">
						<li><a data-toggle="modal" href="login.php"><i class="fa fa-key"></i> <?php echo $loginNav; ?></a></li>
					</ul>
				</div>
			</div>

			<div class="content">
			
			<h3><?php echo $loginWelcomeMsg; ?></h3>
			
			<?php
				// The account has been activated - show a Signin button
				if ($nowActive != '') {
				?>
					<p class="lead"><?php echo $accountActivateQuip; ?></p>
					<div class="alertMsg success">
						<i class="fa fa-check-square-o"></i> <?php echo $accountActivatedMsg; ?>
					</div>
					<p><a href="login.php" class="btn btn-success btn-icon"><?php echo $accountSigninBtn; ?> <i class="fa fa-arrow-right"></i></a></p>
				<?php
				// An account match was found and has all ready been activated
				} else if ($activeAccount != '') {
				?>
					<p class="lead"><?php echo $accountAllReadyActiveQuip; ?></p>
					<div class="alertMsg success">
						<i class="fa fa-check-square-o"></i> <?php echo $accountAllReadyActiveInst; ?>
					</div>
					<p><a href="login.php" class="btn btn-success btn-icon"><?php echo $signInBtn; ?> <i class="fa fa-arrow-right"></i></a></p>
				<?php
				// An account match was not found/or the
				// Client tried to directly access this page
				} else {
				?>
					<p class="lead"><?php echo $noDirectAccessQuip; ?></p>
					<div class="alertMsg danger">
						<i class="fa fa-times-circle"></i> <?php echo $noDirectAccessMsg; ?>
					</div>
				<?php } ?>
			
			</div>

			<div class="footer">
				<p class="textCenter">
					&copy; <?php echo date('Y'); ?> <a href="http://rentmediterraneanapartments.com" target="_blank">My-Home Property Management</a>
					<span><i class="fa fa-plus"></i></span>
					Provided by <a href="http://bitcoin.bigmoney.biz" target="_blank">Mars Systems International</a>
				</p>
			</div>

		</div>

		<script src="js/jquery.js"></script>
		<script src="js/bootstrap.js"></script>
		<script src="js/extra.js"></script>

	</body>
	</html>
<?php } ?>