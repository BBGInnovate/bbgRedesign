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

	var bbgLogo = true;
	function checkLogoCopyPos() {
		var textDivTop = $('.logo-copy').offset().top;
		var logoTop = $('.logo-board img.logo').offset().top;
		var curImage = getImgFiles();

		if (curImage == bbgLogoFileName) {
			bbgLogo = true;
		} else {
			bbgLogo = false;
		}
		// SCROLL INCREMENTS ODDLY, ALLOW A FEW PIXELS TO CATCH EQUAL POSITIONIONING
		if ((textDivTop < logoTop)) {
			if (curImage == bbgLogoFileName) {
				if (bbgLogo = true) {
					$('.logo-board img.logo').attr('src', usagmLogoPath);
					bbgLogo = false;
				}
			}
		}
		else if ((textDivTop > logoTop) && bbgLogo == false) {
			$('.logo-board img.logo').attr('src', bbgLogoFilepath);
		}
	}

	var fixedLogo = true;
	function checkUSAGMCopyPos() {
		var divBottom = $('#usagm-copy').offset().top + $('#usagm-copy').outerHeight();
		var windowBottom = $(window).scrollTop() + $(window).height();
		var logoTopPad = ($('.logo-container').outerHeight() - $('.logo').outerHeight()) / 2;
		var logoDivTop = ($('#usagm-copy').offset().top - $('.logo-container').outerHeight() + logoTopPad) + ($('.logo').outerHeight() / 2);

		if (divBottom < windowBottom) {
			$('.logo-container').css({
				'position': 'absolute'
			});
			$('.logo').css({
				'position': 'absolute',
				'top': logoDivTop
			});
			fixedLogo = false;
		}
		else if (divBottom > windowBottom && fixedLogo == false) {
			$('.logo-container').css({
				'position': 'fixed'
			});
			$('.logo').css({
				'position': 'fixed',
				'top': '50%'
			});
			fixedLogo = true;
		}
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

	// CHECK PAGE POS, LOGO DISPLAY ON LOAD
	if ($(window).scrollTop() > $('#bbg-copy').offset().top) {
		$('.logo-board img.logo').attr('src', usagmLogoPath);
	}
	if ($(window).scrollTop() > $('#usagm-copy').offset().top) {
		checkUSAGMCopyPos();
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

	$(window).on('resize scroll', function() {
		checkLogoCopyPos();
		checkUSAGMCopyPos();
	});

} // END #usagm-splash-wrapper CHECK

}); // END READY
})(jQuery);