<?php $msgBox = ''; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="My-Home - An easy to install, setup and use Rental Property Management web application built in HTML/CSS, PHP/MySQLi and jQuery">
	<meta name="author" content="Mars Systems International">
	<title><?php echo $set['siteName']; ?></title>

	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>

	<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<link href="css/custom.css" rel="stylesheet" type="text/css"/>
	<link href="css/extra.css" rel="stylesheet" type="text/css"/>
	<link href="css/flexslider.css" rel="stylesheet" type="text/css"/>
	<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
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
					<a href=""><img alt="<?php echo $set['siteName']; ?>" src="images/logo.png" title="Manchester Apartment Rentals" /></a>
				</div>
			</div>
			<div class="col-md-4 userInfo">
				<p class="textRight">
					<?php echo $welcomeMsg.', '.$tenantFirstName.' '.$tenantLastName; ?> <br />
					<?php echo $todayMsg.' '.date('l'). " the " .date('jS \of F, Y'); ?>
				</p>
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
				<ul class="nav navbar-nav">
					<li><a href="index.php"><?php echo $dashboardNav; ?></a></li>
					<?php if ($leaseId != '0') { ?>
						<li><a href="index.php?page=propertyDetails"><?php echo $propertyNav; ?></a></li>
						<li><a href="index.php?page=serviceRequests"><?php echo $serviceRequestsNav; ?></a></li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $myAccountNav; ?> <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="index.php?page=myProfile"><?php echo $myProfileNav; ?></a></li>
								<li><a href="index.php?page=myPayments"><?php echo $paymentHistoryNav; ?></a></li>
								<li><a href="index.php?page=myDocuments"><?php echo $tenantDocsNav; ?></a></li>
							</ul>
						</li>
					<?php } else { ?>
						<li><a href="index.php?page=myProfile"><?php echo $myProfileNav; ?></a></li>
					<?php } ?>
				</ul>

				<ul class="nav navbar-nav navbar-right">
					
					<li><a href="index.php?page=availableProperties"><?php echo $availablePropertiesNav; ?></a></li>
					<li><a href="index.php?page=contactRequests"><?php echo $contactRequestsNav; ?></a></li>
					<li><a data-toggle="modal" href="#signOut"><?php echo $signoutBtn; ?> <i class="fa fa-sign-out"></i></a></li>
				</ul>
			</div>
		</div>

		<!-- -- SIGN OUT MODEL -- -->
		<div class="modal fade" id="signOut" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body">
						<p class="lead"><?php echo $tenantFirstName.', '.$signoutConf; ?></p>
					</div>
					<div class="modal-footer">
						<a href="index.php?action=logout" class="btn btn-success btn-icon-alt"><?php echo $signoutBtn; ?> <i class="fa fa-sign-out"></i></a>
						<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</div>
			</div>
		</div>

		<div class="content">