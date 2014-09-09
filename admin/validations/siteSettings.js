$(document).ready(function() {

	// Validation - respond to the blur of fields
	$('#businessEmail').blur(function () {
		// Check a for a valid email
		var emailRegex = new RegExp(/^([\w\.\-]+)@([\w\-]+)((\.(\w){2,3})+)$/i);
        var emailAddress = $('#businessEmail').val();
        var emailIsValid = emailRegex.test(emailAddress);

        if (!emailIsValid) {
            $(this).addClass("highlightField");

			// Display an error
			result = '<div class="alertMsg danger">Please enter a valid email address.<a class="alert-close" href="#">x</a></div>';
			$('.errorNote').show().html(result);
			$('.alert-close').click(function(event) {
				event.preventDefault();
				$(this).parent().fadeOut("slow", function() {
					$(this).remove();
				});
			});

			// Clear the invalid email
			$('#businessEmail').val('');

			return(false);
        }
		return (true);
	});
	
	if ($("#enablePayments").val() === '1') {
		$('#paypalEmail').blur(function () {
			// Check a for a valid email
			var emailRegex = new RegExp(/^([\w\.\-]+)@([\w\-]+)((\.(\w){2,3})+)$/i);
			var emailAddress = $('#paypalEmail').val();
			var emailIsValid = emailRegex.test(emailAddress);

			if (!emailIsValid) {
				$(this).addClass("highlightField");

				// Display an error
				result = '<div class="alertMsg danger">Please enter a valid PayPal email address.<a class="alert-close" href="#">x</a></div>';
				$('.errorNote').show().html(result);
				$('.alert-close').click(function(event) {
					event.preventDefault();
					$(this).parent().fadeOut("slow", function() {
						$(this).remove();
					});
				});

				// Clear the invalid email
				$('#paypalEmail').val('');

				return(false);
			}
			return (true);
		});
	}

	// Validate Phone Number Format
    $("#businessPhone, #servicePhone, #contactPhone").keydown(function(event) {
    	// Allow only Numbers (Num Keys & Num Pad), Backspace, Delete, Space, Peren, Tab & Dash
		if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 57 ||
			event.keyCode == 48 || event.keyCode == 9 || event.keyCode == 173 ||
			event.keyCode == 32 || event.keyCode == 109 || event.keyCode == 96 ||
			event.keyCode == 97 || event.keyCode == 98 || event.keyCode == 99 ||
			event.keyCode == 100 || event.keyCode == 101 || event.keyCode == 102 ||
			event.keyCode == 103 || event.keyCode == 104 || event.keyCode == 105) {
    		return (true);
    	} else {
    		// Ensure that it is a number or stop the keypress
    		if (event.keyCode < 48 || event.keyCode > 57) {
				return (false);
    		}
    	}
    });

	// PayPal/Payment System options
	$("#enablePayments").change(function() {
		if ($('#enablePayments').val() !== '1') {
			// Hide if Disabled
			$('#paymentSystem').slideUp('slow');
		} else {
			// Show if Enabled
			$('#paymentSystem').slideDown('slow');
		}
	});

	// Hide PayPal options on Page Load if not Enabled
	if ($("#enablePayments").val() !== '1') {
		$('#paymentSystem').hide();
	}

});