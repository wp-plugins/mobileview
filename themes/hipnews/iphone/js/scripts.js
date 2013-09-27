(function($){
$(document).ready(function(){


/* ===================================================================
  #FlexSlider
=================================================================== */
$('.flexslider').imagesLoaded(function(){
  $(this).flexslider({
    animation: 'slide',
    directionNav: false
  });
});

/* ===================================================================
  #Mobile Menu 
=================================================================== */
$('#top-sliding-menu').mmenu({
	counters: false,
	configuration: {
	  menuNodetype: 'div'
	}
});


/* Margin top for wrapper */

var headerHeight = $('.header').outerHeight(true);
$('.outer-wrapper').css('margin-top', headerHeight);

/* Welcome message */
$('#close-msg').click(function(){
	window.location.href = window.location.href;
});

});
})(jQuery);