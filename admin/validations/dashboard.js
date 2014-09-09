$(document).ready(function() {
	var a = 0;

	$('[name="paymentDate"]').each(function() {
	  var cal1 = new Calendar({
			element: 'paymentDate['+a+']',
			months: 1,
			dateFormat: 'Y-m-d'
		});

		a++;
	});

});