$(document).ready(function() {

	// Calendar Data Selector
	var cal1 = new Calendar({
		element: 'serviceFromDate',
		months: 1,
		dateFormat: 'Y-m-d'
	});
	var cal2 = new Calendar({
		element: 'serviceToDate',
		months: 1,
		dateFormat: 'Y-m-d'
	});
	var cal3 = new Calendar({
		element: 'serviceCostsFromDate',
		months: 1,
		dateFormat: 'Y-m-d'
	});
	var cal4 = new Calendar({
		element: 'serviceCostsToDate',
		months: 1,
		dateFormat: 'Y-m-d'
	});
	var cal5 = new Calendar({
		element: 'paymentsFromDate',
		months: 1,
		dateFormat: 'Y-m-d'
	});
	var cal6 = new Calendar({
		element: 'paymentsToDate',
		months: 1,
		dateFormat: 'Y-m-d'
	});
	var cal7 = new Calendar({
		element: 'refundsFromDate',
		months: 1,
		dateFormat: 'Y-m-d'
	});
	var cal8 = new Calendar({
		element: 'refundsToDate',
		months: 1,
		dateFormat: 'Y-m-d'
	});
	
	// Validate Report Date Fields before allowing the report to run
	$('#servReportBtn').click(function(e) {
		e.preventDefault();
		if ($("#serviceFromDate").val() == '') {
			result = 'The Service Request From Date Field is Required to run the report.';
			
			// Show the error Modal
			$('#errorModal').modal('show');
			$('.errorMsg').show().html(result);
			$("#serviceFromDate").addClass("highlightField");
			return false;
		} else if ($("#serviceToDate").val() == '') {
			result = 'The Service Request To Date Field is Required to run the report.';
			
			// Show the error Modal
			$('#errorModal').modal('show');
			$('.errorMsg').show().html(result);
			$("#serviceToDate").addClass("highlightField");
			return false;
		} else {
			// All fields validated - Submit the form
			$('#serviceReport').attr('action','index.php?action=serviceReport');
			$('#serviceReport').submit();
		}
	});
	
	$('#servCostsReportBtn').click(function(e) {
		e.preventDefault();
		if ($("#serviceCostsFromDate").val() == '') {
			result = 'The Service Request From Date Field is Required to run the report.';
			
			// Show the error Modal
			$('#errorModal').modal('show');
			$('.errorMsg').show().html(result);
			$("#serviceCostsFromDate").addClass("highlightField");
			return false;
		} else if ($("#serviceCostsToDate").val() == '') {
			result = 'The Service Request To Date Field is Required to run the report.';
			
			// Show the error Modal
			$('#errorModal').modal('show');
			$('.errorMsg').show().html(result);
			$("#serviceCostsToDate").addClass("highlightField");
			return false;
		} else {
			// All fields validated - Submit the form
			$('#serviceCostsReport').attr('action','index.php?action=serviceCostsReport');
			$('#serviceCostsReport').submit();
		}
	});
	
	$('#paymentsReportBtn').click(function(e) {
		e.preventDefault();
		if ($("#paymentsFromDate").val() == '') {
			result = 'The Payments From Date Field is Required to run the report.';
			
			// Show the error Modal
			$('#errorModal').modal('show');
			$('.errorMsg').show().html(result);
			$("#paymentsFromDate").addClass("highlightField");
			return false;
		} else if ($("#paymentsToDate").val() == '') {
			result = 'The Payments To Date Field is Required to run the report.';
			
			// Show the error Modal
			$('#errorModal').modal('show');
			$('.errorMsg').show().html(result);
			$("#paymentsToDate").addClass("highlightField");
			return false;
		} else {
			// All fields validated - Submit the form
			$('#paymentsReport').attr('action','index.php?action=paymentsReport');
			$('#paymentsReport').submit();
		}
	});
	
	$('#refundsReportBtn').click(function(e) {
		e.preventDefault();
		if ($("#refundsFromDate").val() == '') {
			result = 'The Refunds From Date Field is Required to run the report.';
			
			// Show the error Modal
			$('#errorModal').modal('show');
			$('.errorMsg').show().html(result);
			$("#refundsFromDate").addClass("highlightField");
			return false;
		} else if ($("#refundsToDate").val() == '') {
			result = 'The Refunds To Date Field is Required to run the report.';
			
			// Show the error Modal
			$('#errorModal').modal('show');
			$('.errorMsg').show().html(result);
			$("#refundsToDate").addClass("highlightField");
			return false;
		} else {
			// All fields validated - Submit the form
			$('#refundsReport').attr('action','index.php?action=refundsReport');
			$('#refundsReport').submit();
		}
	});

});