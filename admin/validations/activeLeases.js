$(document).ready(function() {
	var a = 0;

	$('[name="leaseStart"]').each(function() {
	  var cal1 = new Calendar({
			element: 'leaseStart['+a+']',
			months: 1,
			dateFormat: 'Y-m-d'
		});

		var cal2 = new Calendar({
			element: 'leaseEnd['+a+']',
			months: 1,
			dateFormat: 'Y-m-d'
		});

		a++;
	});

});