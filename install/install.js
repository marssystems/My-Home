$(document).ready(function() {

	// Validate emails
	$('#businessEmail').blur(function () {
		// Check a for a valid email
		var emailRegex = new RegExp(/^([\w\.\-]+)@([\w\-]+)((\.(\w){2,3})+)$/i);
		var emailAddress = $('#businessEmail').val();
		var emailIsValid = emailRegex.test(emailAddress);

		if (!emailIsValid) {
			$(this).addClass("empty");

			// Display an error
			result = '<div class="alertMsg warning"><i class="fa fa-warning"></i> Please enter a valid Office/Support Email address.</div>';
			$('.settingsNote').show().html(result);

			// Clear the invalid email
			$('#businessEmail').val('');

			return(false);
		}
		return (true);
	});
	
	$('#adminEmail').blur(function () {
		// Check a for a valid email
		var emailRegex = new RegExp(/^([\w\.\-]+)@([\w\-]+)((\.(\w){2,3})+)$/i);
		var emailAddress = $('#adminEmail').val();
		var emailIsValid = emailRegex.test(emailAddress);

		if (!emailIsValid) {
			$(this).addClass("empty");

			// Display an error
			result = '<div class="alertMsg warning"><i class="fa fa-warning"></i> Please enter a valid Admin Email address.</div>';
			$('.adminNote').show().html(result);

			// Clear the invalid email
			$('#adminEmail').val('');

			return(false);
		}
		return (true);
	});

	// Validate Phone Number Format
	$("#businessPhone").keydown(function(event) {
		// Allow only Numbers (Num Keys & Num Pad), Backspace, Delete, Space, Peren, Tab & Dash
		if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 57 ||
			event.keyCode == 48 || event.keyCode == 9 || event.keyCode == 173 ||
			event.keyCode == 32 || event.keyCode == 109 || event.keyCode == 96 ||
			event.keyCode == 97 || event.keyCode == 98 || event.keyCode == 99 ||
			event.keyCode == 100 || event.keyCode == 101 || event.keyCode == 102 ||
			event.keyCode == 103 || event.keyCode == 104 || event.keyCode == 105) {
			return (true);
		} else {
			// Ensure that it is a number or stop the key-press
			if (event.keyCode < 48 || event.keyCode > 57) {
				return (false);
			}
		}
	});

});