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

// KEEPS BACKGROUND IMAGES A CONSISTENT SIZE
function sizeBGimages() {
	var img_scale = 1.77778;
	$.each($('.hd_scale'), function() {
		var containerW = $(this).width();
		var dynHeight = containerW / img_scale;
		$(this).height(dynHeight);
	});
}
sizeBGimages();
$(window).on('resize', function() {
	sizeBGimages();
});

function control_ribbon_height() {
	var ribbonHeight = $('.bbg__ribbon .main-content-container').outerHeight();
	$('.bbg__ribbon .side-content-container div').height(ribbonHeight)
}
if (($('.bbg__ribbon').length > 0) && $(window).width() > 1200) {
	control_ribbon_height();
}

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