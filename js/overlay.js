jQuery(function ($) {
	/** ******************************
	 * Log In/Sign Up Overlay
	 ****************************** **/
	var full = $('#fullscreen');
	$(full).data('state','open');

	$('.signinup').click(function(e) {
		e.preventDefault();
		if ($(full).data('state') == 'open') {
			$(full).fadeIn(300);
			$(full).data('state','close');
		}
	});
	$('.signup-btn').click(function(e) {
		e.preventDefault();
		$('.signin-form').fadeOut(300, function() {
			$(this).hide;
		});
		$('.signup-form').delay(300).fadeIn("slow", function() {
			$(this).show;
		});
	});
	$('.signin-btn').click(function(e) {
		e.preventDefault();
		$('.signup-form, .password-form').fadeOut(300, function() {
			$(this).hide;
		});
		$('.signin-form').delay(300).fadeIn("slow", function() {
			$(this).show;
		});
	});
	$('.password-btn').click(function(e) {
		e.preventDefault();
		$('.signin-form').fadeOut(300, function() {
			$(this).hide;
		});
		$('.password-form').delay(300).fadeIn("slow", function() {
			$(this).show;
		});
	});
	
	$('.close-overlay').click(function(e) {
		e.preventDefault();
		if ($(full).data('state') == 'close') {
			$(full).fadeOut();
			$(full).data('state','open');
		}
	});
});