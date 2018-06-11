(function($) {
$('document').ready(function() {

function featuredMediaHD() {
	// HD PROPORTIONS
	var containerW = $('.page-post-featured-graphic').width();
	var dynHeight = containerW / 1.77778;
	$('.bbg-banner').width(containerW);
	$('.bbg-banner').height(dynHeight);
	console.log(dynHeight);
}
featuredMediaHD();

$(window).on('resize', function() {
	featuredMediaHD();
});

}); // END READY
})(jQuery);