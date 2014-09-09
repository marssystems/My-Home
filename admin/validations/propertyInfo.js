$(document).ready(function() {

	/** ******************************
	 * Sidebar Lists
	 ****************************** **/
    var allPanels = $('.accordion > dd').hide();
    $('.accordion > dt > a').click(function () {
        $this = $(this);
        $target = $this.parent().next();
        if (!$target.hasClass('active')) {
            allPanels.removeClass('active').slideUp();
            $target.addClass('active').slideDown();
        } else {
            $target.removeClass('active').slideUp();
        }
        return false;
    });
	
	// Validate Phone Number Format
    $("#resident_phone").keydown(function(event) {
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
	
	// Calander Data Selector
	var cal1 = new Calendar({
		element: 'paymentDate',
		months: 1,
		dateFormat: 'Y-m-d'
	});
	
});