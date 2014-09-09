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
	
});