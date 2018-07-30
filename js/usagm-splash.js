(function($) {
$('document').ready(function() {
window.scrollTo(0,1)
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
				console.log('usagm');
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

var logoPageScroller = $('.splash-down');
var i = 0;
logoPageScroller.on('click', function() {
	var bbgCopyBottom = $('.logo-copy').offset().top + $('.logo-copy').outerHeight();
	if (i > 0) {
		bbgCopyBottom = bbgCopyBottom * 2;
	}
	$('html, body').animate({
		scrollTop: bbgCopyBottom
	}, 700);
	i++;
	console.log('x: ' + bbgCopyBottom);
});

$(window).on('resize scroll', function() {
	checkLogoCopyPos();
	checkUSAGMCopyPos();
});

}); // END READY
})(jQuery);