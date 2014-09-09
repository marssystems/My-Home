<?php
	// Check if install.php is present
	if(is_dir('../install')) {
		header('Location: ../install/install.php');
	} else {
		session_start();
		if(!isset($_SESSION['adminId'])) {
			header('Location: login.php');
			exit;
		}
		
		// Logout
		if (isset($_GET['action'])) {
			$action = $_GET['action'];
			if ($action == 'logout') {
				session_destroy();
				header('Location: login.php');
			}
		}

		// Access DB Info
		include('../config.php');

		// Get Settings Data
		include ('../includes/settings.php');
		$set = mysqli_fetch_assoc($setRes);

		// Set Localization
		$local = $set['localization'];
		switch ($local) {
			case 'ar':		include ('language/ar.php');		break;
			case 'bg':		include ('language/bg.php');		break;
			case 'ce':		include ('language/ce.php');		break;
			case 'cs':		include ('language/cs.php');		break;
			case 'da':		include ('language/da.php');		break;
			case 'en':		include ('language/en.php');		break;
			case 'en-ca':	include ('language/en-ca.php');		break;
			case 'en-gb':	include ('language/en-gb.php');		break;
			case 'es':		include ('language/es.php');		break;
			case 'fr':		include ('language/fr.php');		break;
			case 'hr':		include ('language/hr.php');		break;
			case 'hu':		include ('language/hu.php');		break;
			case 'hy':		include ('language/hy.php');		break;
			case 'id':		include ('language/id.php');		break;
			case 'it':		include ('language/it.php');		break;
			case 'ja':		include ('language/ja.php');		break;
			case 'ko':		include ('language/ko.php');		break;
			case 'nl':		include ('language/nl.php');		break;
			case 'pt':		include ('language/pt.php');		break;
			case 'ro':		include ('language/ro.php');		break;
			case 'th':		include ('language/th.php');		break;
			case 'vi':		include ('language/vi.php');		break;
			case 'yue':		include ('language/yue.php');		break;
		}

		// Include Functions
		include('../includes/functions.php');

		// Keep some Admin data available
		$adminId = $_SESSION["adminId"];
		$superuser = $_SESSION["superuser"];
		$adminRole = $_SESSION["adminRole"];
		$adminEmail = $_SESSION["adminEmail"];
		$adminFirstName = $_SESSION["adminFirstName"];
		$adminLastName = $_SESSION["adminLastName"];

		// Link to the Page
		if (isset($_GET['action']) && $_GET['action'] == 'myProfile') {					// Admin Profile
			$page = "myProfile";
		} else if (isset($_GET['action']) && $_GET['action'] == 'adminInfo') {			// Admin Profile Info
			$page = "adminInfo";
		} else if (isset($_GET['action']) && $_GET['action'] == 'activeTenants') {		// All Active tenants
			$page = "activeTenants";
		} else if (isset($_GET['action']) && $_GET['action'] == 'inactiveTenants') {	// All Inactive tenants
			$page = "inactiveTenants";
		} else if (isset($_GET['action']) && $_GET['action'] == 'archivedTenants') {	// All Archived tenants
			$page = "archivedTenants";
		} else if (isset($_GET['action']) && $_GET['action'] == 'tenantInfo') {			// Tenant Info
			$page = "tenantInfo";
		} else if (isset($_GET['action']) && $_GET['action'] == 'viewDocument') {		// View a Tenant Document
			$page = "viewDocument";
		} else if (isset($_GET['action']) && $_GET['action'] == 'activeProperties') {	// All Active Properties
			$page = "activeProperties";
		} else if (isset($_GET['action']) && $_GET['action'] == 'archivedProperties') {	// All Archived Properties
			$page = "archivedProperties";
		} else if (isset($_GET['action']) && $_GET['action'] == 'propertyInfo') {		// Property Info
			$page = "propertyInfo";
		} else if (isset($_GET['action']) && $_GET['action'] == 'viewLeasePayments') {	// View All Lease Payments
			$page = "viewLeasePayments";
		} else if (isset($_GET['action']) && $_GET['action'] == 'viewPayment') {		// View a Lease Payment
			$page = "viewPayment";
		} else if (isset($_GET['action']) && $_GET['action'] == 'receipt') {			// View/Print Receipt
			$page = "receipt";
		} else if (isset($_GET['action']) && $_GET['action'] == 'leaseProperty') {		// Lease a Property
			$page = "leaseProperty";
		} else if (isset($_GET['action']) && $_GET['action'] == 'activeLeases') {		// All Active Leases
			$page = "activeLeases";
		} else if (isset($_GET['action']) && $_GET['action'] == 'archivedLeases') {		// All Archived leases
			$page = "archivedLeases";
		} else if (isset($_GET['action']) && $_GET['action'] == 'activeRequests') {		// All Active Service Requests
			$page = "activeRequests";
		} else if (isset($_GET['action']) && $_GET['action'] == 'closedRequests') {		// Closed Service Requests
			$page = "closedRequests";
		} else if (isset($_GET['action']) && $_GET['action'] == 'archivedRequests') {	// Archived Service Requests
			$page = "archivedRequests";
		} else if (isset($_GET['action']) && $_GET['action'] == 'viewFile') {			// View a Property File/Document
			$page = "viewFile";
		} else if (isset($_GET['action']) && $_GET['action'] == 'viewRequest') {		// View a Service Request
			$page = "viewRequest";
		} else if (isset($_GET['action']) && $_GET['action'] == 'allAdmins') {			// All Admins
			$page = "allAdmins";
		} else if (isset($_GET['action']) && $_GET['action'] == 'siteAlerts') {			// Site Alerts
			$page = "siteAlerts";
		} else if (isset($_GET['action']) && $_GET['action'] == 'reports') {			// Site Reports
			$page = "reports";
		} else if (isset($_GET['action']) && $_GET['action'] == 'siteTemplates') {		// Uploaded Templates & Forms
			$page = "siteTemplates";
		} else if (isset($_GET['action']) && $_GET['action'] == 'viewTemplate') {		// View an Uploaded Template/Form
			$page = "viewTemplate";
		} else if (isset($_GET['action']) && $_GET['action'] == 'emailAllTenants') {	// Email All Active Tenants
			$page = "emailAllTenants";
		} else if (isset($_GET['action']) && $_GET['action'] == 'siteSettings') {		// Site Settings
			$page = "siteSettings";
		} 
		// Report Pages
		else if (isset($_GET['action']) && $_GET['action'] == 'tenantReport') {
			$page = "reports/tenantReport";
		} else if (isset($_GET['action']) && $_GET['action'] == 'tenantArchiveReport') {
			$page = "reports/tenantArchiveReport";
		} else if (isset($_GET['action']) && $_GET['action'] == 'propertyReport') {
			$page = "reports/propertyReport";
		} else if (isset($_GET['action']) && $_GET['action'] == 'serviceReport') {
			$page = "reports/serviceReport";
		} else if (isset($_GET['action']) && $_GET['action'] == 'serviceCostsReport') {
			$page = "reports/serviceCostsReport";
		} else if (isset($_GET['action']) && $_GET['action'] == 'paymentsReport') {
			$page = "reports/paymentsReport";
		} else if (isset($_GET['action']) && $_GET['action'] == 'refundsReport') {
			$page = "reports/refundsReport";
		} else if (isset($_GET['action']) && $_GET['action'] == 'leaseReport') {
			$page = "reports/leaseReport";
		} else if (isset($_GET['action']) && $_GET['action'] == 'adminReport') {
			$page = "reports/adminReport";
		} else if (isset($_GET['action']) && $_GET['action'] == 'assignedReport') {
			$page = "reports/assignedReport";
		}
		// END Reports
		else {																			// Admin Dashboard
			$page = 'dashboard';
		}

		// Load the Header for all pages except
		if ($page != "receipt") {
			include('includes/header.php');
		}

		if (file_exists('pages/'.$page.'.php')) {
			// Load Page
			include('pages/'.$page.'.php');
		} else {
			// Else Display Error
			echo '
					<h3 class="danger">Error</h3>
					<div class="alertMsg warning">
						<i class="fa fa-warning"></i> The page "'.$page.'" could not be loaded.
					</div>
				';
		}

		// Load the footer for all pages except
		if ($page != "receipt") {
			include('includes/footer.php');
		}
	}

?>