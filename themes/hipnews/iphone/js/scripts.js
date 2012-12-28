(function($){
$(document).ready(function(){

/* ===================================================================
  #Mobile Menu Toggle
=================================================================== */
$('.collapse-toggle').click(function(e){
  e.preventDefault();
  var $el = $(this),
      $navCollapse = $('.nav-collapse');

  // If collapsed
  if( $el.hasClass('collapsed') ) {
    $navCollapse.height( $navCollapse.children().outerHeight(true) );
    $el.removeClass('collapsed');
  } else {
    $navCollapse.height(0);
    $el.addClass('collapsed');
  }
});


/* ===================================================================
  #FlexSlider
=================================================================== */
$('.flexslider').imagesLoaded(function(){
  $(this).flexslider({
    animation: 'slide',
    directionNav: false
  });
});


});
})(jQuery);