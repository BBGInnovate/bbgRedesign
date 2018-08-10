(function($) {
$('document').ready(function() {

if ($('#usagm-splash-wrapper').length != 0) {
	var logoSrc = $('.logo-board img.logo').attr('src');
	var imgFromPath = logoSrc.split("/");
	var imgFile = imgFromPath[imgFromPath.length - 1];
	var bbgLogoFileName = imgFromPath[imgFromPath.length - 1];
	var usagmLogoFileName = "usagm-splash-logo.png";
	var usagmLogoPath = logoSrc.replace(imgFromPath[imgFromPath.length - 1], usagmLogoFileName);
	var bbgLogoFilepath = logoSrc.replace(imgFromPath[imgFromPath.length - 1], bbgLogoFileName);

	function getImgFiles() {
		var logoSrc = $('.logo-board img.logo').attr('src');
		var imgFromPath = logoSrc.split("/");
		var imgFileName = imgFromPath[imgFromPath.length - 1];
		return imgFileName;
	}

	function slideScroll() {
		var bbgCopyBottom = $('#bbg-copy').offset().top + $('#bbg-copy').outerHeight();
		var usagmCopyBottom = $('#usagm-copy').offset().top + $('#usagm-copy').outerHeight();
		var shifter;
		if ($(window).scrollTop() < $('#bbg-copy').offset().top) {
			shifter = bbgCopyBottom;
		}
		else {
			shifter = usagmCopyBottom;
		}
		$('html, body').animate({
			scrollTop: shifter
		}, 700);
	}

	// MOVE DOWN ARROW ON HOVER
	var logoPageScroller = $('.splash-down');
	logoPageScroller.hover(function() {
		$(this).animate({'padding-top': '5px'});
	}, function() {
		$(this).animate({'padding-top': '0'});
	});
	logoPageScroller.on('click', function() {
		slideScroll();
	});

	// PARALLAX BBG LOGO
	var topPos = Number($('#bbg-logo-container').offset().top);
	var maxPos = topPos + $('#bbg-logo-container').height();
	var minPos = topPos;
console.log(topPos);
	var posGroup = (maxPos - minPos) * 100;
	var newTopPos;

	function scrollLogoTopPos(elem) {
		topPos = Number(elem.offset().top);
		var bodyPc = ($(this).scrollTop() / $('body').height());
		newTopPos = ((bodyPc * posGroup) / 100) + minPos;
console.log(newTopPos);
		elem.css('top', newTopPos);
		// UPDATE VALUES
		topPos = elem.offset().top;
	}

	$(window).on('scroll', function() {
		scrollLogoTopPos($('#bbg-logo-container'));
	});

} // END #usagm-splash-wrapper CHECK

}); // END READY
})(jQuery);