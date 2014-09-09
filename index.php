<?php
	// Check if install.php is present
	if(is_dir('install')) {
		header('Location: install/install.php');
	} else {
		session_start();
		if (!isset($_SESSION['tenantId'])) {
			header ('Location: login.php');
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
		include('config.php');

		// Get Settings Data
		include ('includes/settings.php');
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
		include('includes/functions.php');

		// Keep some Tenant data available
		$tenantId = $_SESSION['tenantId'];
		$propertyId = $_SESSION['propertyId'];
		$leaseId = $_SESSION['leaseId'];
		$tenantEmail = $_SESSION['tenantEmail'];
		$tenantFirstName = $_SESSION['tenantFirstName'];
		$tenantLastName = $_SESSION['tenantLastName'];

		// Link to the Page
		if (isset($_GET['page']) && $_GET['page'] == 'myProfile') {		        		// Account Profile
			$page = 'myProfile';
		} else if (isset($_GET['page']) && $_GET['page'] == 'availableProperties') {	// View Available Properties
			$page = "availableProperties";
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewProperty') {			// View Available Property Info
			$page = "viewProperty";
		} else if (isset($_GET['page']) && $_GET['page'] == 'propertyDetails') {		// View Property Info
			$page = "propertyDetails";
		} else if (isset($_GET['page']) && $_GET['page'] == 'myPayments') {		    	// View Payment History
			$page = "myPayments";
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewFile') {				// View Uploaded Property File
			$page = "viewFile";
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewDocument') {			// View Uploaded Tenant Document
			$page = "viewDocument";
		} else if (isset($_GET['page']) && $_GET['page'] == 'serviceRequests') {		// View All Service Requests
			$page = "serviceRequests";
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewRequest') {			// View Service Request
			$page = "viewRequest";
		} else if (isset($_GET['page']) && $_GET['page'] == 'contactRequests') {		// View All Contact Requests
			$page = "contactRequests";
		} else if (isset($_GET['page']) && $_GET['page'] == 'myProfile') {		    	// View Profile
			$page = "myProfile";
		} else if (isset($_GET['page']) && $_GET['page'] == 'myDocuments') {		    // View Uploaded Tenant Files
			$page = "myDocuments";
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewRentalPayments') {	    // View All Rental Payments
			$page = "viewRentalPayments";
		} else if (isset($_GET['page']) && $_GET['page'] == 'newPayment') {	    		// New Rental Payment
			$page = "newPayment";
		} else if (isset($_GET['page']) && $_GET['page'] == 'receipt') {		    	// View/Print Payment Receipt
			$page = "receipt";
		} else {																    	// Dashboard
			$page = 'dashboard';
		}

		if ($page != 'receipt') {
			include('includes/header.php');
		}

		if (file_exists('pages/'.$page.'.php')) {
			// Load Page
			include('pages/'.$page.'.php');
		} else {
			// Else Display the Error
			echo '
					<h3 class="danger">Error</h3>
					<div class="alertMsg warning">
						<i class="fa fa-warning"></i> The page "'.$page.'" could not be loaded.
					</div>
				';
		}

		if ($page != 'receipt') {
			include('includes/footer.php');
		}
	}

?>