(function($) {
$('document').ready(function() {

function setBannerHeight() {
	// HD PROPORTIONS
	var winW = $(window).width();
	var dynHeight = winW / 1.77778;
	$('.bbg-banner').height(dynHeight);
	// $('#masthead').height($('.bbg-banner__section').outerHeight());
}
setBannerHeight();

$(window).on('resize', function() {
	setBannerHeight();
});

}); // END READY
})(jQuery);