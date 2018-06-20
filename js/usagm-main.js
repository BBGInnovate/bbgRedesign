(function($) {
$('document').ready(function() {

// KEEPS FEATURED MEDIA SCALED AT HD PROPORTIONS
function featuredMediaHD() {
	var hd_scale = 1.77778;
	var containerW = $('.page-post-featured-graphic').width();
	var dynHeight = containerW / hd_scale;
	$('.bbg-banner').width(containerW);
	$('.bbg-banner').height(dynHeight);
}
featuredMediaHD();

// KEEP PROFILE LIST FROM CHANGING SIDES ON RESIZE
function mgmtProfileSizing() {
	if ($('.mgmt-profile').length > 0) {
		var seniorProfile = $('.mgmt-profile');
		var profileHeights = new Array();

		seniorProfile.css('height', 'auto');
		$.each(seniorProfile, function() {
			profileHeights.push($(this).height())
		});
		var tallestProfileHeight = Math.max.apply(Math, profileHeights);
		seniorProfile.css('height', tallestProfileHeight);
		console.log(tallestProfileHeight);
	}
}
mgmtProfileSizing();



$(window).on('resize', function() {
	featuredMediaHD();
	mgmtProfileSizing();
});

}); // END READY
})(jQuery);