$(document).ready(function() {

	// Validate Phone Number Format
    $("#adminPhone, #adminAltPhone").keydown(function(event) {
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

});