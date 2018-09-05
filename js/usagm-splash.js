(function($) {
$('document').ready(function() {

if ($('#usagm-splash-wrapper').length != 0) {
	// LIGHTBOX
	var lb = $('.lightbox-shell');
	var lbLinks = $('.lightbox-link');
	var lbLinksRefs = lbLinks.attr('href');
	var lbBg = $('<div class="lb-bg"></div>');
	var lbVideoBox = $('<div id="videoBox"></div>');
	var closeBu = $('<div class="close"><i class="far fa-times-circle"></i></div>');
	var closer = $('.close');

	lbVideoBox.append(closeBu);
	lbBg.append(lbVideoBox);

	var screenTop = 0;
	var screenHeight = 0;
	function setLightboxParams() {
		screenTop = $('html').scrollTop();
		screenHeight = $(window).height();
		lbBg.css({
			'top' : screenTop
		});
	}
	setLightboxParams();

	var refArr = [];
	lbLinks.each(function() {
		refArr.push($(this).attr('href'));
	});

	var i, curImg;

	lbLinks.on('click', function(e) {
		var curId = $(this).attr('id');
		var selImg = $(this);
		var selImgRef = selImg.attr('href');

		lbBg.show();
		closeBu.show();
		lbVideoBox.append(closeBu);
		$('body').prepend(lbBg);
		if (curId == "what-we-do") {
			lbVideoBox.append('<iframe width="560" height="315" src="https://www.youtube.com/embed/_j94Vc-8zyg?rel=0?playsinline=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>');
		}
		else if (curId == "who-we-are") {
			lbVideoBox.append('<iframe width="560" height="315" src="https://www.youtube.com/embed/z4XWcruGhNk?rel=0?playsinline=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>');
		}
		else if (curId == "ceo-message") {
			lbVideoBox.append('<iframe width="560" height="315" src="https://www.youtube.com/embed/eTNV0cnb6No?rel=0?playsinline=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>');
		}
	});

	// CONTROLS
	lbBg.on('click', function() {
		lbBg.hide();
		closeBu.hide();
		lbVideoBox.children().remove();
	});
	closeBu.click(function() {
		lbBg.hide();
		closeBu.hide();
		lbVideoBox.children().remove();
	});

	$(window).on('scroll', function() {
		setLightboxParams();
	});

} // END #usagm-splash-wrapper CHECK

}); // END READY
})(jQuery);