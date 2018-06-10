(function($) {
$('document').ready(function() {

function scaleHDvideo() {
	// HD PROPORTIONS
	var containerW = $('.page-post-featured-graphic').width();
	// var winW = $(window).width();
	var dynHeight = containerW / 1.77778;
	$('.bbg-banner').width(containerW);
	$('.bbg-banner').height(dynHeight);
	// $('#masthead').height($('.bbg-banner__section').outerHeight());
}
scaleHDvideo();

$(window).on('resize', function() {
	scaleHDvideo();
});

}); // END READY
})(jQuery);