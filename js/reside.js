$(document).ready(function(){

	/** ******************************
    * Show Password on Focus
    ****************************** **/
	$("[type='password']").focus(function () {
        this.type = "text";
    }).blur(function () {
        this.type = "password";
    })
	
	/** ******************************
    * Alert Message Boxes
    ****************************** **/
    $('.alertMsg .alert-close').each(function() {
        $(this).click(function(event) {
            event.preventDefault();
            $(this).parent().fadeOut("slow", function() {
                $(this).css('diplay', 'none');
            });
        });
    });

    /** ******************************
	 * Tool-tip
	 ****************************** **/
    $('.tool-tip').hover(function(){
        // on Hover
        var title = $(this).attr('title');
        $(this).data('tipText', title).removeAttr('title');
        $('<p class="tooltips"></p>')
        .text(title)
        .appendTo('body')
        .fadeIn('slow');
    }, function() {
        // Hover out
        $(this).attr('title', $(this).data('tipText'));
        $('.tooltips').remove();
    }).mousemove(function(e) {
        var mousey = e.pageY + 0;
        var mousex = e.pageX + -20;
        $('.tooltips')
        .css({ top: mousey, left: mousex })
    });

});