(function($) {
$('document').ready(function() {

var logoSrc = $('.logo-board img.logo').attr('src');
var imgFromPath = logoSrc.split("/");
var imgFile = imgFromPath[imgFromPath.length - 1];
var bbgLogoFileName = imgFromPath[imgFromPath.length - 1];
var usagmLogoFileName = "USAGM-BBG-logo-horiz-RGB-hires.png";
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
	var logoTopPad = ($('.logo-board').outerHeight() - $('.logo').outerHeight()) / 2;
	var logoDivTop = ($('#usagm-copy').offset().top - $('.logo-board').outerHeight() + logoTopPad);
	// var fixedReturnPos = $('#usagm-copy').offset().top - $('.logo-board').outerHeight();

	if (divBottom < windowBottom) {
		$('.logo').css({
			'position': 'absolute',
			'top': logoDivTop
		});
		fixedLogo = false;
	}
	else if (divBottom > windowBottom && fixedLogo == false) {
		$('.logo').css({
			'position': 'fixed',
			'top': '34%'
		});
		fixedLogo = true;
	}
}

$(window).on('resize scroll', function() {
	checkLogoCopyPos();
	checkUSAGMCopyPos();
});

}); // END READY
})(jQuery);