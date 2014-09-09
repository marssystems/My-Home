<?php
	// Get Settings Data
	$setSql  = "
		SELECT
			installUrl,
			localization,
			siteName,
			businessName,
			businessAddress,
			businessEmail,
			businessPhone,
			contactPhone,
			uploadPath,
			templatesPath,
			tenantDocsPath,
			fileTypesAllowed, 
			avatarFolder,
			avatarTypes,
			propertyPicsPath,
			propertyPicTypes,
			enablePayments,
			paypalCurrency,
			paymentCompleteMsg,
			paypalEmail,
			paypalItemName,
			paypalFee
		FROM
			sitesettings
	";
	$setRes = mysqli_query($mysqli, $setSql) or die('Error, retrieving Site Settings failed. ' . mysqli_error());
?>