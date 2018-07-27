(function($) {
$('document').ready(function() {

// KEEPS FEATURED MEDIA SCALED AT HD PROPORTIONS
function featuredMediaHD() {
	var hd_scale = 1.77778;
	var containerW = $('.page-featured-media').width();
	var dynHeight = containerW / hd_scale;
	$('.bbg-banner').width(containerW);
	$('.bbg-banner').height(dynHeight);
}
featuredMediaHD();
$(window).on('resize', function() {
	featuredMediaHD();
});

// KEEPS BACKGROUND IMAGES AND A CONSITENTS SIZE
function sizeBGimages() {
	var img_scale = 1.75;
	var containerW = $('.umbrella-bg-image').width();
	var dynHeight = containerW / img_scale;
	$('.umbrella-bg-image').height(dynHeight);
}
sizeBGimages();
$(window).on('resize', function() {
	sizeBGimages();
});

// KEEPS PROFILE LIST DIVS FROM CHANGING SIDES ON RESIZE
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
	}
}
mgmtProfileSizing();
$(window).on('resize', function() {
	mgmtProfileSizing();
});

if ($('.page-content').first().tagName == 'P') {
	console.log('paragraph');
	$('.page-content p').first().attr('class', 'lead-in');
}
$('.page-content').first().on('click', function() {
	if(this.tagName == 'p'){
		alert("It's a p!");
	}
})

}); // END READY
})(jQuery);